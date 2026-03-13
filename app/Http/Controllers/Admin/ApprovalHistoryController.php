<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalHistoryController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = ApprovalAction::with([
            'approvalRequest.workflow',
            'approvalRequest.request',
            'actor',
        ])->where('company_id', $companyId);

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('module')) {
            $query->whereHas('approvalRequest.workflow', fn ($q) => $q->where('module', $request->module));
        }

        $actions = $query->latest('id')->paginate(20)->withQueryString();

        return view('admin.pages.approval_requests.approval_history', compact('actions'));
    }
}
