<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::orderBy('first_name')->get(['id','employee_code','first_name','last_name']);
        $leaveTypes = LeaveType::orderBy('name')->get();

        $query = LeaveRequest::with(['employee','leaveType'])
            ->latest();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('employee_id')) $query->where('employee_id', $request->employee_id);

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.pages.leaves.leave_requests', compact('requests','employees','leaveTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            // compute days (simple). Later: exclude weekends/holidays by profile.
            $days = Carbon::parse($data['start_date'])->diffInDays(Carbon::parse($data['end_date'])) + 1;

            LeaveRequest::create([
                'employee_id' => $data['employee_id'],
                'leave_type_id' => $data['leave_type_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'days' => $days,
                'reason' => $data['reason'] ?? null,
                'status' => 'pending',
            ]);

            return back()->with('success', 'Leave request created.');
        } catch (Throwable $e) {
            Log::error('LeaveRequest store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create request.');
        }
    }

    public function updateStatus(Request $request, LeaveRequest $leaveRequest)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,rejected,cancelled,pending',
            'admin_note' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // If approving now (and was not approved before) => deduct from balance
            if ($data['status'] === 'approved' && $leaveRequest->status !== 'approved') {

                $days = (int)($leaveRequest->days ?? 0);

                $balance = LeaveBalance::firstOrCreate([
                    'employee_id' => $leaveRequest->employee_id,
                    'leave_type_id' => $leaveRequest->leave_type_id,
                ], [
                    'balance' => 0,
                    'used' => 0,
                ]);

                // Deduct balance and increase used
                $balance->update([
                    'balance' => ($balance->balance - $days),
                    'used' => ($balance->used + $days),
                ]);
            }

            // If previously approved but changing away from approved => rollback balance
            if ($leaveRequest->status === 'approved' && $data['status'] !== 'approved') {

                $days = (int)($leaveRequest->days ?? 0);

                $balance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                    ->where('leave_type_id', $leaveRequest->leave_type_id)
                    ->first();

                if ($balance) {
                    $balance->update([
                        'balance' => ($balance->balance + $days),
                        'used' => max(0, ($balance->used - $days)),
                    ]);
                }
            }

            $leaveRequest->update([
                'status' => $data['status'],
                'admin_note' => $data['admin_note'] ?? null,
            ]);

            DB::commit();
            return back()->with('success', 'Request status updated.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('LeaveRequest status update failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to update status.');
        }
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        try {
            $leaveRequest->delete();
            return back()->with('success', 'Request deleted.');
        } catch (Throwable $e) {
            Log::error('LeaveRequest delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete request.');
        }
    }
}
