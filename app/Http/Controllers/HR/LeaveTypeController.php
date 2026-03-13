<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::latest()->paginate(15);
        return view('admin.pages.leaves.leave_types', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'code' => 'nullable|string|max:20|unique:leave_types,code',
            'is_paid' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
            'max_per_year' => 'nullable|integer|min:0|max:366',
            'carry_forward' => 'nullable|boolean',
            'carry_forward_limit' => 'nullable|integer|min:0|max:366',
        ]);

        try {
            LeaveType::create([
                'name' => $data['name'],
                'code' => $data['code'] ?? null,
                'is_paid' => (bool)($data['is_paid'] ?? true),
                'requires_approval' => (bool)($data['requires_approval'] ?? true),
                'max_per_year' => $data['max_per_year'] ?? null,
                'carry_forward' => (bool)($data['carry_forward'] ?? false),
                'carry_forward_limit' => $data['carry_forward_limit'] ?? null,
            ]);

            return back()->with('success', 'Leave type created.');
        } catch (Throwable $e) {
            Log::error('LeaveType store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create leave type.');
        }
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'code' => 'nullable|string|max:20|unique:leave_types,code,' . $leaveType->id,
            'is_paid' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
            'max_per_year' => 'nullable|integer|min:0|max:366',
            'carry_forward' => 'nullable|boolean',
            'carry_forward_limit' => 'nullable|integer|min:0|max:366',
        ]);

        try {
            $leaveType->update([
                'name' => $data['name'],
                'code' => $data['code'] ?? null,
                'is_paid' => (bool)($data['is_paid'] ?? true),
                'requires_approval' => (bool)($data['requires_approval'] ?? true),
                'max_per_year' => $data['max_per_year'] ?? null,
                'carry_forward' => (bool)($data['carry_forward'] ?? false),
                'carry_forward_limit' => $data['carry_forward_limit'] ?? null,
            ]);

            return back()->with('success', 'Leave type updated.');
        } catch (Throwable $e) {
            Log::error('LeaveType update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update leave type.');
        }
    }

    public function destroy(LeaveType $leaveType)
    {
        try {
            $leaveType->delete();
            return back()->with('success', 'Leave type deleted.');
        } catch (Throwable $e) {
            Log::error('LeaveType delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete leave type.');
        }
    }
}
