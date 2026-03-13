<?php

namespace App\Services;

use App\Models\ApprovalAction;
use App\Models\ApprovalRequest;
use App\Models\Workflow;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApprovalEngineService
{
    public function start(object $requestModel, string $module): ?ApprovalRequest
    {
        $workflow = Workflow::with(['steps' => fn ($q) => $q->orderBy('step_order')])
            ->where('company_id', $requestModel->company_id)
            ->where('module', $module)
            ->where('is_active', true)
            ->first();

        if (!$workflow || $workflow->steps->isEmpty()) {
            return null;
        }

        $firstStep = $workflow->steps->first();

        return ApprovalRequest::create([
            'company_id'    => $requestModel->company_id,
            'workflow_id'   => $workflow->id,
            'request_type'  => get_class($requestModel),
            'request_id'    => $requestModel->id,
            'current_step'  => $firstStep->step_order,
            'status'        => 'pending',
        ]);
    }

    public function approve(ApprovalRequest $approvalRequest, User $actor, ?string $comment = null): void
    {
        DB::transaction(function () use ($approvalRequest, $actor, $comment) {
            $approvalRequest->load(['workflow.steps', 'request']);

            $currentStep = $approvalRequest->workflow->steps
                ->sortBy('step_order')
                ->firstWhere('step_order', $approvalRequest->current_step);

            if (!$currentStep) {
                throw ValidationException::withMessages([
                    'approval' => 'Current workflow step not found.',
                ]);
            }

            $this->ensureActorCanAct($approvalRequest, $currentStep, $actor);

            $alreadyApproved = ApprovalAction::where('approval_request_id', $approvalRequest->id)
                ->where('step_order', $currentStep->step_order)
                ->where('acted_by', $actor->id)
                ->where('action', 'approved')
                ->exists();

            if ($alreadyApproved) {
                throw ValidationException::withMessages([
                    'approval' => 'You already approved this step.',
                ]);
            }

            ApprovalAction::create([
                'company_id'           => $approvalRequest->company_id,
                'approval_request_id'  => $approvalRequest->id,
                'step_order'           => $currentStep->step_order,
                'acted_by'             => $actor->id,
                'action'               => 'approved',
                'comments'             => $comment,
            ]);

            $approvedCount = ApprovalAction::where('approval_request_id', $approvalRequest->id)
                ->where('step_order', $currentStep->step_order)
                ->where('action', 'approved')
                ->count();

            if ($approvedCount < max(1, (int) $currentStep->min_approvals)) {
                return;
            }

            $nextStep = $approvalRequest->workflow->steps
                ->sortBy('step_order')
                ->firstWhere('step_order', '>', $currentStep->step_order);

            if ($nextStep) {
                $approvalRequest->update([
                    'current_step' => $nextStep->step_order,
                    'status'       => 'pending',
                ]);
                return;
            }

            $approvalRequest->update([
                'status' => 'approved',
            ]);

            $this->syncTargetStatus($approvalRequest, 'approved');
        });
    }

    public function reject(ApprovalRequest $approvalRequest, User $actor, string $comment): void
    {
        DB::transaction(function () use ($approvalRequest, $actor, $comment) {
            $approvalRequest->load(['workflow.steps', 'request']);

            $currentStep = $approvalRequest->workflow->steps
                ->sortBy('step_order')
                ->firstWhere('step_order', $approvalRequest->current_step);

            if (!$currentStep) {
                throw ValidationException::withMessages([
                    'approval' => 'Current workflow step not found.',
                ]);
            }

            $this->ensureActorCanAct($approvalRequest, $currentStep, $actor);

            ApprovalAction::create([
                'company_id'           => $approvalRequest->company_id,
                'approval_request_id'  => $approvalRequest->id,
                'step_order'           => $currentStep->step_order,
                'acted_by'             => $actor->id,
                'action'               => 'rejected',
                'comments'             => $comment,
            ]);

            $approvalRequest->update([
                'status' => 'rejected',
            ]);

            $this->syncTargetStatus($approvalRequest, 'rejected');
        });
    }

    public function returnBack(ApprovalRequest $approvalRequest, User $actor, string $comment): void
    {
        DB::transaction(function () use ($approvalRequest, $actor, $comment) {
            $approvalRequest->load(['workflow.steps']);

            $currentStep = $approvalRequest->workflow->steps
                ->sortBy('step_order')
                ->firstWhere('step_order', $approvalRequest->current_step);

            if (!$currentStep) {
                throw ValidationException::withMessages([
                    'approval' => 'Current workflow step not found.',
                ]);
            }

            $this->ensureActorCanAct($approvalRequest, $currentStep, $actor);

            ApprovalAction::create([
                'company_id'           => $approvalRequest->company_id,
                'approval_request_id'  => $approvalRequest->id,
                'step_order'           => $currentStep->step_order,
                'acted_by'             => $actor->id,
                'action'               => 'returned',
                'comments'             => $comment,
            ]);

            $previousStep = $approvalRequest->workflow->steps
                ->sortBy('step_order')
                ->where('step_order', '<', $approvalRequest->current_step)
                ->last();

            $approvalRequest->update([
                'current_step' => $previousStep?->step_order ?? 1,
                'status'       => 'pending',
            ]);
        });
    }

    protected function syncTargetStatus(ApprovalRequest $approvalRequest, string $status): void
    {
        $model = $approvalRequest->request;

        if ($model && in_array('status', $model->getFillable(), true)) {
            $model->update(['status' => $status]);
        }
    }

    protected function ensureActorCanAct(ApprovalRequest $approvalRequest, $step, User $actor): void
    {
        if ($step->approver_type === 'user') {
            if ((int) $step->approver_user_id !== (int) $actor->id) {
                throw ValidationException::withMessages([
                    'approval' => 'You are not assigned to approve this step.',
                ]);
            }
            return;
        }

        if ($step->approver_type === 'role') {
            $hasRole = method_exists($actor, 'roles')
                ? $actor->roles()->where('roles.id', $step->approver_role_id)->exists()
                : false;

            if (!$hasRole) {
                throw ValidationException::withMessages([
                    'approval' => 'You do not have the required role for this approval.',
                ]);
            }
            return;
        }

        if ($step->approver_type === 'manager') {
            $target = $approvalRequest->request;
            $managerUserId = optional(optional(optional($target)->employee)->employment)->manager_id;

            if ((int) $managerUserId !== (int) $actor->id) {
                throw ValidationException::withMessages([
                    'approval' => 'Only the reporting manager can approve this step.',
                ]);
            }
        }
    }
}
