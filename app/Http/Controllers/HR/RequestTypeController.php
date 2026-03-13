<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HrRequestType;
use App\Models\Workflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RequestTypeController extends Controller
{
    public function index()
    {
        $workflows = Workflow::where('module', 'hr_requests')->orWhere('module', 'hr_request')->orderBy('name')->get();
        $requestTypes = HrRequestType::with('workflow')->latest()->paginate(15);

        return view('admin.pages.operations.request_types', compact('requestTypes', 'workflows'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:hr_request_types,code',
            'workflow_id' => 'nullable|exists:workflows,id',
        ]);

        try {
            HrRequestType::create([
                'company_id' => null,
                'name' => $data['name'],
                'code' => $data['code'],
                'workflow_id' => $data['workflow_id'] ?? null,
            ]);

            return back()->with('success', 'HR request type created successfully.');
        } catch (Throwable $e) {
            Log::error('HrRequestType store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create request type.');
        }
    }

    public function update(Request $request, HrRequestType $hrRequestType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:hr_request_types,code,' . $hrRequestType->id,
            'workflow_id' => 'nullable|exists:workflows,id',
        ]);

        try {
            $hrRequestType->update([
                'name' => $data['name'],
                'code' => $data['code'],
                'workflow_id' => $data['workflow_id'] ?? null,
            ]);

            return back()->with('success', 'HR request type updated successfully.');
        } catch (Throwable $e) {
            Log::error('HrRequestType update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update request type.');
        }
    }

    public function destroy(HrRequestType $hrRequestType)
    {
        try {
            $hrRequestType->delete();
            return back()->with('success', 'HR request type deleted successfully.');
        } catch (Throwable $e) {
            Log::error('HrRequestType delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete request type.');
        }
    }
}
