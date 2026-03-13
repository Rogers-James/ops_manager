<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Models\Employee;
use App\Models\HrRequest;
use App\Models\HrRequestType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class HRRequestController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::orderBy('first_name')->get(['id','employee_code','first_name','last_name']);
        $requestTypes = HrRequestType::with('workflow')->orderBy('name')->get();

        $query = HrRequest::with(['employee', 'type.workflow', 'approvalRequest'])->latest();

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('hr_request_type_id')) {
            $query->where('hr_request_type_id', $request->hr_request_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.pages.operations.requests', compact('employees', 'requestTypes', 'requests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'hr_request_type_id' => 'required|exists:hr_request_types,id',
            'payload_subject' => 'nullable|string|max:255',
            'payload_reason' => 'nullable|string|max:1000',
            'payload_meta' => 'nullable|string|max:2000',
        ]);

        try {
            DB::beginTransaction();

            $type = HrRequestType::findOrFail($data['hr_request_type_id']);

            $payload = [
                'subject' => $data['payload_subject'] ?? null,
                'reason' => $data['payload_reason'] ?? null,
                'meta' => $data['payload_meta'] ?? null,
            ];

            $hrRequest = HrRequest::create([
                'company_id' => null,
                'hr_request_type_id' => $data['hr_request_type_id'],
                'employee_id' => $data['employee_id'],
                'payload' => $payload,
                'status' => $type->workflow_id ? 'submitted' : 'draft',
            ]);

            if ($type->workflow_id) {
                ApprovalRequest::create([
                    'company_id' => null,
                    'workflow_id' => $type->workflow_id,
                    'request_type' => HrRequest::class,
                    'request_id' => $hrRequest->id,
                    'current_step' => 1,
                    'status' => 'pending',
                ]);
            }

            DB::commit();
            return back()->with('success', 'HR request created successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('HrRequest store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create HR request.');
        }
    }

    public function show(HrRequest $hrRequest)
    {
        $hrRequest->load(['employee', 'type.workflow', 'approvalRequest.actions']);
        return view('admin.pages.operations.request_show', compact('hrRequest'));
    }

    public function updateStatus(Request $request, HrRequest $hrRequest)
    {
        $data = $request->validate([
            'status' => 'required|in:draft,submitted,approved,rejected,cancelled',
        ]);

        try {
            DB::beginTransaction();

            $hrRequest->update([
                'status' => $data['status'],
            ]);

            if ($hrRequest->approvalRequest) {
                if ($data['status'] === 'approved') {
                    $hrRequest->approvalRequest->update(['status' => 'approved']);
                } elseif ($data['status'] === 'rejected') {
                    $hrRequest->approvalRequest->update(['status' => 'rejected']);
                }
            }

            DB::commit();
            return back()->with('success', 'Request status updated successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('HrRequest status update failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to update request status.');
        }
    }

    public function destroy(HrRequest $hrRequest)
    {
        try {
            $hrRequest->delete();
            return back()->with('success', 'HR request deleted successfully.');
        } catch (Throwable $e) {
            Log::error('HrRequest delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete request.');
        }
    }
}
