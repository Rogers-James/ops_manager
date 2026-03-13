@extends('admin.layouts.layout')

@section('title', 'Admin - Approval Requests Details')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Approval Request #{{ $approvalRequest->id }}</h4>
            <a href="{{ route('admin.approval_requests.index') }}" class="btn btn-outline-dark">Back</a>
        </div>

        <div class="row">
            <div class="col-lg-5 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Request Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Workflow:</strong> {{ $approvalRequest->workflow->name ?? '-' }}</p>
                        <p><strong>Module:</strong>
                            {{ ucwords(str_replace('_', ' ', $approvalRequest->workflow->module ?? '-')) }}</p>
                        <p><strong>Request Type:</strong> {{ class_basename($approvalRequest->request_type) }}</p>
                        <p><strong>Request ID:</strong> {{ $approvalRequest->request_id }}</p>
                        <p><strong>Current Step:</strong> {{ $approvalRequest->current_step }}</p>
                        <p>
                            <strong>Status:</strong>
                            <span
                                class="badge bg-label-{{ $approvalRequest->status === 'pending' ? 'warning' : ($approvalRequest->status === 'approved' ? 'success' : 'danger') }}">
                                {{ ucfirst($approvalRequest->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Workflow Steps</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($approvalRequest->workflow->steps->sortBy('step_order') as $step)
                            <div class="mb-3">
                                <div class="fw-semibold">Step {{ $step->step_order }}</div>
                                <div class="text-muted">
                                    {{ ucfirst($step->approver_type) }}
                                    @if ($step->approver_type === 'role')
                                        - {{ $step->approverRole->name ?? '-' }}
                                    @elseif($step->approver_type === 'user')
                                        - {{ $step->approverUser->name ?? '-' }}
                                    @endif
                                </div>
                                <small class="text-muted">Min approvals: {{ $step->min_approvals }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-7 mb-4">
                @if ($approvalRequest->status === 'pending')
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Take Action</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.approval_requests.approve', $approvalRequest) }}" method="POST"
                                class="mb-3">
                                @csrf
                                <label class="form-label">Approval Comment</label>
                                <textarea name="comment" class="form-control mb-2" rows="3"></textarea>
                                <button class="btn btn-success">Approve</button>
                            </form>

                            <form action="{{ route('admin.approval_requests.reject', $approvalRequest) }}" method="POST"
                                class="mb-3">
                                @csrf
                                <label class="form-label">Rejection Reason</label>
                                <textarea name="comment" class="form-control mb-2" rows="3" required></textarea>
                                <button class="btn btn-danger">Reject</button>
                            </form>

                            <form action="{{ route('admin.approval_requests.return', $approvalRequest) }}" method="POST">
                                @csrf
                                <label class="form-label">Return Comment</label>
                                <textarea name="comment" class="form-control mb-2" rows="3" required></textarea>
                                <button class="btn btn-warning">Return to Previous Step</button>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Approval Timeline</h5>
                    </div>
                    <div class="card-body">
                        @forelse($approvalRequest->actions->sortByDesc('id') as $action)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <div class="fw-semibold">
                                        Step {{ $action->step_order }} - {{ ucfirst($action->action) }}
                                    </div>
                                    <small class="text-muted">{{ $action->created_at->format('d M Y h:i A') }}</small>
                                </div>
                                <div class="text-muted">{{ $action->actor->name ?? '-' }}</div>
                                <div>{{ $action->comments ?: '-' }}</div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No actions yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
