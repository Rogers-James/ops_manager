<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeEmployment;
use App\Models\Grade;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['employment'])->latest()->paginate(15);

        // master data for Add Employee modal dropdowns
        $departments  = Department::orderBy('name')->get();
        $designations = Designation::orderBy('name')->get();
        $locations    = Location::orderBy('name')->get();
        $grades       = Grade::orderBy('name')->get();        // if exists
        $costCenters  = CostCenter::orderBy('name')->get();   // if exists

        // managers list (employees)
        $managers = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'employee_code']);

        return view('admin.pages.employees.index', compact(
            'employees',
            'departments',
            'designations',
            'locations',
            'grades',
            'costCenters',
            'managers'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'     => 'required|string|max:60',
            'last_name'      => 'nullable|string|max:60',
            'email'          => 'nullable|email|max:120|unique:employees,email',
            'phone'          => 'nullable|string|max:30',
            'dob'            => 'nullable|date',
            'gender'         => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'status'         => 'required|in:active,inactive',

            'joining_date'      => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern,daily_wage',

            'location_id'       => 'nullable|exists:locations,id',
            'department_id'     => 'nullable|exists:departments,id',
            'designation_id'    => 'nullable|exists:designations,id',
            'grade_id'          => 'nullable|exists:grades,id',
            'cost_center_id'    => 'nullable|exists:cost_centers,id',
            'manager_id'        => 'nullable|exists:employees,id',
        ]);

        try {
            DB::beginTransaction();

            $employee = Employee::create([
                'employee_code'  => 'TEMP',
                'first_name'     => $data['first_name'],
                'last_name'      => $data['last_name'] ?? null,
                'email'          => $data['email'] ?? null,
                'phone'          => $data['phone'] ?? null,
                'dob'            => $data['dob'] ?? null,
                'gender'         => $data['gender'] ?? null,
                'marital_status' => $data['marital_status'] ?? null,
                'status'         => $data['status'],
            ]);

            $employee->update([
                'employee_code' => 'EMP-' . str_pad((string) $employee->id, 6, '0', STR_PAD_LEFT),
            ]);

            EmployeeEmployment::create([
                'employee_id'      => $employee->id,
                'joining_date'     => $data['joining_date'],
                'employment_type'  => $data['employment_type'] ?? null,
                'location_id'      => $data['location_id'] ?? null,
                'department_id'    => $data['department_id'] ?? null,
                'designation_id'   => $data['designation_id'] ?? null,
                'grade_id'         => $data['grade_id'] ?? null,
                'cost_center_id'   => $data['cost_center_id'] ?? null,
                'manager_id'       => $data['manager_id'] ?? null,
            ]);

            DB::commit();
            return back()->with('success', 'Employee created successfully!');
        } catch (QueryException $e) {
            DB::rollBack();

            Log::error('Employee save failed (DB)', [
                'message'   => $e->getMessage(),
                'errorInfo' => $e->errorInfo,
                'sql'       => method_exists($e, 'getSql') ? $e->getSql() : null,
                'bindings'  => method_exists($e, 'getBindings') ? $e->getBindings() : null,
            ]);

            // show a friendly message
            if (($e->errorInfo[1] ?? null) == 1062) {
                return back()->withInput()->with('error', 'Duplicate record found (email/employee code).');
            }

            return back()->withInput()->with('error', 'Database error while saving employee. Check logs.');
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Employee save failed (Throwable)', [
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Something went wrong. Check logs.');
        }
    }

    public function profilePage($empId)
    {
        $employee = Employee::findorFail($empId)->with('employment')->first();
        // $employee->load('employment');

        $departments = Department::orderBy('name')->get();
        $designations = Designation::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $grades = Grade::orderBy('rank')->get();
        $managers = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'employee_code']);
        $costCenters = CostCenter::orderBy('name')->get(); // if exists

        // dd($employee);
        // simple role logic (adjust to your system)
        $role = Auth::user()->roles->pluck('slug')->toArray();

        $canEditEmployment = in_array('admin', $role) || in_array('hr', $role);
        $canEditStatus = $canEditEmployment;
        $canUploadPhoto = true; // allow both, or make only self

        $password = Str::random(8);
        User::create([
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'email' => $employee->email,
            'password' => $password,
            'employee_id' => $employee->id,
        ]);

        return view('admin.pages.employees.profile', compact(
            'employee',
            'departments',
            'designations',
            'locations',
            'grades',
            'managers',
            'costCenters',
            'canEditEmployment',
            'canEditStatus',
            'canUploadPhoto'
        ));
    }

    public function updateProfile(Request $request, Employee $employee)
    {
        $data = $request->validate([
            // employees table
            'first_name'     => 'required|string|max:60',
            'last_name'      => 'nullable|string|max:60',
            'email'          => 'nullable|email|max:120|unique:employees,email,' . $employee->id,
            'phone'          => 'nullable|string|max:30',
            'dob'            => 'nullable|date',
            'gender'         => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',

            // status (admin/hr only - if you want always allow, keep it)
            'status'         => 'nullable|in:active,inactive',

            // employment (optional fields)
            'joining_date'    => 'nullable|date',
            'employment_type' => 'nullable|in:full_time,part_time,contract,intern,daily_wage',

            'location_id'     => 'nullable|exists:locations,id',
            'department_id'   => 'nullable|exists:departments,id',
            'designation_id'  => 'nullable|exists:designations,id',
            'grade_id'        => 'nullable|exists:grades,id',
            'cost_center_id'  => 'nullable|exists:cost_centers,id',
            'manager_id'      => 'nullable|exists:employees,id',
        ]);

        try {
            DB::beginTransaction();

            // 1) update employees table
            $employee->update([
                'first_name'     => $data['first_name'],
                'last_name'      => $data['last_name'] ?? null,
                'email'          => $data['email'] ?? null,
                'phone'          => $data['phone'] ?? null,
                'dob'            => $data['dob'] ?? null,
                'gender'         => $data['gender'] ?? null,
                'marital_status' => $data['marital_status'] ?? null,
                // update status only if it exists in request (so employee can't change it unless you allow)
                'status'         => $request->has('status') ? $data['status'] : $employee->status,
            ]);

            // 2) employment record (create if missing)
            $employmentData = [
                'joining_date'    => $data['joining_date'] ?? null,
                'employment_type' => $data['employment_type'] ?? null,
                'location_id'     => $data['location_id'] ?? null,
                'department_id'   => $data['department_id'] ?? null,
                'designation_id'  => $data['designation_id'] ?? null,
                'grade_id'        => $data['grade_id'] ?? null,
                'cost_center_id'  => $data['cost_center_id'] ?? null,
                'manager_id'      => $data['manager_id'] ?? null,
            ];

            // If you want: only update employment when admin/hr allowed
            // if(!$canEditEmployment) { unset employment fields }

            $employee->employment()->updateOrCreate(
                ['employee_id' => $employee->id],
                $employmentData
            );

            DB::commit();
            return back()->with('success', 'Profile updated successfully!');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Employee profile update DB error', [
                'message' => $e->getMessage(),
                'errorInfo' => $e->errorInfo,
            ]);
            return back()->withInput()->with('error', 'Database error while updating profile.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Employee profile update error', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong while updating profile.');
        }
    }

    public function updatePhoto(Request $request, Employee $employee)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:800', // 800KB
        ]);

        try {
            $file = $request->file('photo');

            // Delete old photo if exists
            if ($employee->photo_path && Storage::disk('public')->exists($employee->photo_path)) {
                Storage::disk('public')->delete($employee->photo_path);
            }

            $ext = $file->getClientOriginalExtension();
            $filename = 'photo_' . time() . '.' . $ext;

            // store: storage/app/public/employees/{EMP-000001}/photo/photo_123.jpg
            $empFolder = $employee->employee_code ?: ('EMP-' . $employee->id);
            $path = $file->storeAs("employees/{$empFolder}/photo", $filename, 'public');

            $employee->update([
                'photo_path' => $path
            ]);

            return back()->with('success', 'Photo updated successfully!');
        } catch (Throwable $e) {
            Log::error('Employee photo update error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while updating photo.');
        }
    }
}
