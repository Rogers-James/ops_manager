<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\LeavePolicy;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class LeavePolicyController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::orderBy('name')->get();
        $policies = LeavePolicy::with('leaveType')->latest()->paginate(15);

        return view('admin.pages.leaves.leave_policies', compact('policies', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'leave_type_id' => 'required|exists:leave_types,id',
            'yearly_quota' => 'required|integer|min:0|max:366',
            'accrual_method' => 'required|in:none,monthly,yearly',
            'accrual_rate' => 'nullable|numeric|min:0|max:31', // days per month or per year
            'allow_negative' => 'nullable|boolean',
            'max_negative' => 'nullable|integer|min:0|max:366',
            'min_notice_days' => 'nullable|integer|min:0|max:365',
            'max_consecutive_days' => 'nullable|integer|min:0|max:366',
        ]);

        try {
            LeavePolicy::create([
                'name' => $data['name'],
                'leave_type_id' => $data['leave_type_id'],
                'yearly_quota' => $data['yearly_quota'],
                'accrual_method' => $data['accrual_method'],
                'accrual_rate' => $data['accrual_rate'] ?? null,
                'allow_negative' => (bool)($data['allow_negative'] ?? false),
                'max_negative' => $data['max_negative'] ?? null,
                'min_notice_days' => $data['min_notice_days'] ?? 0,
                'max_consecutive_days' => $data['max_consecutive_days'] ?? null,
            ]);

            return back()->with('success', 'Leave policy created.');
        } catch (Throwable $e) {
            Log::error('LeavePolicy store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create policy.');
        }
    }

    public function update(Request $request, LeavePolicy $leavePolicy)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'leave_type_id' => 'required|exists:leave_types,id',
            'yearly_quota' => 'required|integer|min:0|max:366',
            'accrual_method' => 'required|in:none,monthly,yearly',
            'accrual_rate' => 'nullable|numeric|min:0|max:31',
            'allow_negative' => 'nullable|boolean',
            'max_negative' => 'nullable|integer|min:0|max:366',
            'min_notice_days' => 'nullable|integer|min:0|max:365',
            'max_consecutive_days' => 'nullable|integer|min:0|max:366',
        ]);

        try {
            $leavePolicy->update([
                'name' => $data['name'],
                'leave_type_id' => $data['leave_type_id'],
                'yearly_quota' => $data['yearly_quota'],
                'accrual_method' => $data['accrual_method'],
                'accrual_rate' => $data['accrual_rate'] ?? null,
                'allow_negative' => (bool)($data['allow_negative'] ?? false),
                'max_negative' => $data['max_negative'] ?? null,
                'min_notice_days' => $data['min_notice_days'] ?? 0,
                'max_consecutive_days' => $data['max_consecutive_days'] ?? null,
            ]);

            return back()->with('success', 'Leave policy updated.');
        } catch (Throwable $e) {
            Log::error('LeavePolicy update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update policy.');
        }
    }

    public function destroy(LeavePolicy $leavePolicy)
    {
        try {
            $leavePolicy->delete();
            return back()->with('success', 'Leave policy deleted.');
        } catch (Throwable $e) {
            Log::error('LeavePolicy delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete policy.');
        }
    }
}
