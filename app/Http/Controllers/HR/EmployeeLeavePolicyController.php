<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeavePolicy;
use App\Models\EmployeeLeavePolicy; // assignment table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmployeeLeavePolicyController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('first_name')->get(['id','employee_code','first_name','last_name']);
        $policies = LeavePolicy::with('leaveType')->orderBy('name')->get();

        $assignments = EmployeeLeavePolicy::with(['employee','policy.leaveType'])
            ->latest()->paginate(15);

        return view('admin.pages.leaves.employee_leave_policies', compact('employees','policies','assignments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_policy_id' => 'required|exists:leave_policies,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        try {
            EmployeeLeavePolicy::create([
                'employee_id' => $data['employee_id'],
                'leave_policy_id' => $data['leave_policy_id'],
                'effective_from' => $data['effective_from'],
                'effective_to' => $data['effective_to'] ?? null,
            ]);

            return back()->with('success', 'Policy assigned to employee.');
        } catch (Throwable $e) {
            Log::error('EmployeeLeavePolicy store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to assign policy.');
        }
    }

    public function update(Request $request, EmployeeLeavePolicy $assignment)
    {
        $data = $request->validate([
            'leave_policy_id' => 'required|exists:leave_policies,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        try {
            $assignment->update([
                'leave_policy_id' => $data['leave_policy_id'],
                'effective_from' => $data['effective_from'],
                'effective_to' => $data['effective_to'] ?? null,
            ]);

            return back()->with('success', 'Assignment updated.');
        } catch (Throwable $e) {
            Log::error('EmployeeLeavePolicy update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update assignment.');
        }
    }

    public function destroy(EmployeeLeavePolicy $assignment)
    {
        try {
            $assignment->delete();
            return back()->with('success', 'Assignment deleted.');
        } catch (Throwable $e) {
            Log::error('EmployeeLeavePolicy delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete assignment.');
        }
    }
}
