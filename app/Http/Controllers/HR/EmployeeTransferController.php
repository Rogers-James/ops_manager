<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeTransfer;
use App\Models\Grade;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmployeeTransferController extends Controller
{
    public function index()
    {
        $transfers = EmployeeTransfer::with(['employee', 'fromDepartment', 'toDepartment', 'fromDesignation', 'toDesignation'])
            ->latest()->paginate(15);

        $employees = Employee::orderBy('first_name')->get(['id', 'employee_code', 'first_name', 'last_name']);
        $departments = Department::orderBy('name')->get();
        $designations = Designation::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $grades = Grade::orderBy('rank')->get();
        $costCenters = CostCenter::orderBy('name')->get();
        $managers = Employee::orderBy('first_name')->get(['id', 'employee_code', 'first_name', 'last_name']);

        return view('admin.pages.employees.transfers', compact(
            'transfers',
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
            'employee_id'      => 'required|exists:employees,id',
            'effective_date'   => 'required|date',
            'reason'           => 'nullable|string|max:500',

            'to_location_id'   => 'nullable|exists:locations,id',
            'to_department_id' => 'nullable|exists:departments,id',
            'to_designation_id' => 'nullable|exists:designations,id',
            'to_grade_id'      => 'nullable|exists:grades,id',
            'to_cost_center_id' => 'nullable|exists:cost_centers,id',
            'to_manager_id'    => 'nullable|exists:employees,id',
        ]);

        try {
            DB::beginTransaction();

            $companyId = 1;

            $employee = Employee::with('employment')->findOrFail($data['employee_id']);
            $emp = $employee->employment; // snapshot from current employment

            EmployeeTransfer::create([
                'company_id' => $companyId,
                'employee_id' => $employee->id,

                'from_location_id' => $emp->location_id ?? null,
                'from_department_id' => $emp->department_id ?? null,
                'from_designation_id' => $emp->designation_id ?? null,
                'from_grade_id' => $emp->grade_id ?? null,
                'from_cost_center_id' => $emp->cost_center_id ?? null,
                'from_manager_id' => $emp->manager_id ?? null,

                'to_location_id' => $data['to_location_id'] ?? null,
                'to_department_id' => $data['to_department_id'] ?? null,
                'to_designation_id' => $data['to_designation_id'] ?? null,
                'to_grade_id' => $data['to_grade_id'] ?? null,
                'to_cost_center_id' => $data['to_cost_center_id'] ?? null,
                'to_manager_id' => $data['to_manager_id'] ?? null,

                'effective_date' => $data['effective_date'],
                'reason' => $data['reason'] ?? null,
                'status' => 'submitted',
            ]);

            DB::commit();
            return back()->with('success', 'Transfer request created.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Transfer store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong while creating transfer.');
        }
    }

    public function updateStatus(Request $request, EmployeeTransfer $transfer)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,rejected,cancelled',
        ]);

        try {
            DB::beginTransaction();

            $transfer->update(['status' => $data['status']]);

            // If approved => update employee_employment to new values
            if ($data['status'] === 'approved') {
                $employment = $transfer->employee->employment;

                $employment->update([
                    'location_id'    => $transfer->to_location_id ?? $employment->location_id,
                    'department_id'  => $transfer->to_department_id ?? $employment->department_id,
                    'designation_id' => $transfer->to_designation_id ?? $employment->designation_id,
                    'grade_id'       => $transfer->to_grade_id ?? $employment->grade_id,
                    'cost_center_id' => $transfer->to_cost_center_id ?? $employment->cost_center_id,
                    'manager_id'     => $transfer->to_manager_id ?? $employment->manager_id,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Transfer status updated.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Transfer status update failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while updating transfer.');
        }
    }
}
