<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Workflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkflowController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $workflows = Workflow::with(['steps.approverRole', 'steps.approverUser', 'conditions'])
            ->where('company_id', $companyId)
            ->latest('id')
            ->paginate(15);

        return view('admin.pages.workflows.index', compact('workflows'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;

        $roles = Role::where('company_id', $companyId)->orderBy('name')->get();
        $users = User::where('company_id', $companyId)->orderBy('name')->get();

        $modules = $this->modules();

        return view('admin.pages.workflows.create', compact('roles', 'users', 'modules'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'module' => ['required', 'string', 'max:100'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('workflows')->where(fn ($q) => $q
                    ->where('company_id', $companyId)
                    ->where('module', $request->module)
                ),
            ],
            'is_active' => ['nullable', 'boolean'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.step_order' => ['required', 'integer', 'min:1'],
            'steps.*.approver_type' => ['required', Rule::in(['manager', 'role', 'user'])],
            'steps.*.approver_role_id' => ['nullable', 'integer'],
            'steps.*.approver_user_id' => ['nullable', 'integer'],
            'steps.*.min_approvals' => ['required', 'integer', 'min:1'],
            'rules' => ['nullable', 'array'],
        ]);

        DB::transaction(function () use ($request, $companyId) {
            $workflow = Workflow::create([
                'company_id' => $companyId,
                'module' => $request->module,
                'name' => $request->name,
                'is_active' => $request->boolean('is_active', true),
            ]);

            foreach (collect($request->steps)->sortBy('step_order') as $step) {
                $workflow->steps()->create([
                    'company_id' => $companyId,
                    'step_order' => $step['step_order'],
                    'approver_type' => $step['approver_type'],
                    'approver_role_id' => $step['approver_type'] === 'role' ? ($step['approver_role_id'] ?? null) : null,
                    'approver_user_id' => $step['approver_type'] === 'user' ? ($step['approver_user_id'] ?? null) : null,
                    'min_approvals' => $step['min_approvals'] ?? 1,
                ]);
            }

            $cleanRules = collect($request->rules ?? [])
                ->filter(fn ($rule) => filled($rule['field'] ?? null) && filled($rule['operator'] ?? null))
                ->values()
                ->all();

            if (!empty($cleanRules)) {
                $workflow->conditions()->create([
                    'company_id' => $companyId,
                    'rules' => $cleanRules,
                ]);
            }
        });

        return redirect()->route('admin.workflows.index')
            ->with('success', 'Workflow created successfully.');
    }

    public function edit(Workflow $workflow)
    {
        $this->authorizeWorkflow($workflow);

        $companyId = Auth::user()->company_id;

        $workflow->load(['steps', 'conditions']);
        $roles = Role::where('company_id', $companyId)->orderBy('name')->get();
        $users = User::where('company_id', $companyId)->orderBy('name')->get();
        $modules = $this->modules();

        return view('admin.pages.workflows.edit', compact('workflow', 'roles', 'users', 'modules'));
    }

    public function update(Request $request, Workflow $workflow)
    {
        $this->authorizeWorkflow($workflow);
        $companyId = Auth::user()->company_id;

        $request->validate([
            'module' => ['required', 'string', 'max:100'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('workflows')->ignore($workflow->id)->where(fn ($q) => $q
                    ->where('company_id', $companyId)
                    ->where('module', $request->module)
                ),
            ],
            'is_active' => ['nullable', 'boolean'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.step_order' => ['required', 'integer', 'min:1'],
            'steps.*.approver_type' => ['required', Rule::in(['manager', 'role', 'user'])],
            'steps.*.approver_role_id' => ['nullable', 'integer'],
            'steps.*.approver_user_id' => ['nullable', 'integer'],
            'steps.*.min_approvals' => ['required', 'integer', 'min:1'],
            'rules' => ['nullable', 'array'],
        ]);

        DB::transaction(function () use ($request, $workflow, $companyId) {
            $workflow->update([
                'module' => $request->module,
                'name' => $request->name,
                'is_active' => $request->boolean('is_active', true),
            ]);

            $workflow->steps()->delete();

            foreach (collect($request->steps)->sortBy('step_order') as $step) {
                $workflow->steps()->create([
                    'company_id' => $companyId,
                    'step_order' => $step['step_order'],
                    'approver_type' => $step['approver_type'],
                    'approver_role_id' => $step['approver_type'] === 'role' ? ($step['approver_role_id'] ?? null) : null,
                    'approver_user_id' => $step['approver_type'] === 'user' ? ($step['approver_user_id'] ?? null) : null,
                    'min_approvals' => $step['min_approvals'] ?? 1,
                ]);
            }

            $workflow->conditions()->delete();

            $cleanRules = collect($request->rules ?? [])
                ->filter(fn ($rule) => filled($rule['field'] ?? null) && filled($rule['operator'] ?? null))
                ->values()
                ->all();

            if (!empty($cleanRules)) {
                $workflow->conditions()->create([
                    'company_id' => $companyId,
                    'rules' => $cleanRules,
                ]);
            }
        });

        return redirect()->route('admin.workflows.index')
            ->with('success', 'Workflow updated successfully.');
    }

    public function destroy(Workflow $workflow)
    {
        $this->authorizeWorkflow($workflow);

        if ($workflow->approvalRequests()->exists()) {
            return back()->with('error', 'Workflow cannot be deleted because approval requests already exist.');
        }

        $workflow->delete();

        return redirect()->route('admin.workflows.index')
            ->with('success', 'Workflow deleted successfully.');
    }

    protected function authorizeWorkflow(Workflow $workflow): void
    {
        abort_unless($workflow->company_id === Auth::user()->company_id, 403);
    }

    protected function modules(): array
    {
        return [
            'leave_request' => 'Leave Request',
            'attendance_request' => 'Attendance Correction',
            'hr_request' => 'HR Request',
            'resignation' => 'Resignation',
            'transfer' => 'Transfer',
            'loan' => 'Loan / Advance',
            'payroll_run' => 'Payroll Run',
        ];
    }
}
