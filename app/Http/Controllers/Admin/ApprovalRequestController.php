<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Services\ApprovalEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalRequestController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = ApprovalRequest::with([
            'workflow',
            'actions.actor',
            'request',
        ])->where('company_id', $companyId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('module')) {
            $query->whereHas('workflow', fn ($q) => $q->where('module', $request->module));
        }

        $approvalRequests = $query->latest('id')->paginate(15)->withQueryString();

        return view('admin.pages.approval_requests.index', compact('approvalRequests'));
    }

    public function show(ApprovalRequest $approvalRequest)
    {
        $this->authorizeRequest($approvalRequest);

        $approvalRequest->load([
            'workflow.steps.approverRole',
            'workflow.steps.approverUser',
            'actions.actor',
            'request',
        ]);

        return view('admin.pages.approval_requests.show', compact('approvalRequest'));
    }

    public function approve(Request $request, ApprovalRequest $approvalRequest, ApprovalEngineService $engine)
    {
        $this->authorizeRequest($approvalRequest);

        $request->validate([
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $engine->approve($approvalRequest, Auth::user(), $request->comment);

        return back()->with('success', 'Request approved successfully.');
    }

    public function reject(Request $request, ApprovalRequest $approvalRequest, ApprovalEngineService $engine)
    {
        $this->authorizeRequest($approvalRequest);

        $request->validate([
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $engine->reject($approvalRequest, Auth::user(), $request->comment);

        return back()->with('success', 'Request rejected successfully.');
    }

    public function returnBack(Request $request, ApprovalRequest $approvalRequest, ApprovalEngineService $engine)
    {
        $this->authorizeRequest($approvalRequest);

        $request->validate([
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $engine->returnBack($approvalRequest, Auth::user(), $request->comment);

        return back()->with('success', 'Request returned to previous step.');
    }

    protected function authorizeRequest(ApprovalRequest $approvalRequest): void
    {
        abort_unless($approvalRequest->company_id === Auth::user()->company_id, 403);
    }
}
