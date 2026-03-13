<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class LeaveBalanceController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::orderBy('first_name')->get(['id','employee_code','first_name','last_name']);
        $leaveTypes = LeaveType::orderBy('name')->get();

        $query = LeaveBalance::with(['employee','leaveType'])->latest();

        if ($request->filled('employee_id')) $query->where('employee_id', $request->employee_id);
        if ($request->filled('leave_type_id')) $query->where('leave_type_id', $request->leave_type_id);

        $balances = $query->paginate(20)->withQueryString();

        return view('admin.pages.leaves.leave_balances', compact('balances','employees','leaveTypes'));
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'amount' => 'required|integer|min:-366|max:366',
            'note' => 'nullable|string|max:250',
        ]);

        try {
            $balance = LeaveBalance::firstOrCreate([
                'employee_id' => $data['employee_id'],
                'leave_type_id' => $data['leave_type_id'],
            ], [
                'balance' => 0,
                'used' => 0,
            ]);

            $balance->update([
                'balance' => $balance->balance + (int)$data['amount'],
            ]);

            return back()->with('success', 'Leave balance adjusted.');
        } catch (Throwable $e) {
            Log::error('LeaveBalance adjust failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to adjust balance.');
        }
    }
}
