<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ShiftGroup;
use App\Models\ShiftGroupAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ShiftAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $companyId = 1;

        $employees = Employee::orderBy('first_name')->get(['id','employee_code','first_name','last_name']);
        $shiftGroups = ShiftGroup::orderBy('name')->get();

        $assignments = ShiftGroupAssignment::with(['employee', 'group'])
            // ->where('company_id', $companyId)
            ->latest()
            ->paginate(15);

        return view('admin.pages.shifts.shift_assignments', compact('employees', 'shiftGroups', 'assignments'));
    }

    public function store(Request $request)
    {
        $companyId = 1;

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_group_id' => 'required|exists:shift_groups,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        try {
            ShiftGroupAssignment::create([
                // 'company_id' => $companyId,
                'employee_id' => $data['employee_id'],
                'shift_group_id' => $data['shift_group_id'],
                'effective_from' => $data['effective_from'],
                'effective_to' => $data['effective_to'] ?? null,
            ]);

            return back()->with('success', 'Shift assigned.');
        } catch (Throwable $e) {
            Log::error('ShiftAssignment store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to assign shift.');
        }
    }

    public function update(Request $request, ShiftGroupAssignment $assignment)
    {
        $data = $request->validate([
            'shift_group_id' => 'required|exists:shift_groups,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        try {
            $assignment->update([
                'shift_group_id' => $data['shift_group_id'],
                'effective_from' => $data['effective_from'],
                'effective_to' => $data['effective_to'] ?? null,
            ]);

            return back()->with('success', 'Shift assignment updated.');
        } catch (Throwable $e) {
            Log::error('ShiftAssignment update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update assignment.');
        }
    }

    public function destroy(ShiftGroupAssignment $assignment)
    {
        try {
            $assignment->delete();
            return back()->with('success', 'Assignment deleted.');
        } catch (Throwable $e) {
            Log::error('ShiftAssignment delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete assignment.');
        }
    }
}
