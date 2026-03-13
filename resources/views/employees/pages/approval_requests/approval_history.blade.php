@extends('admin.layouts.layout')

@section('title', 'Admin - Approval History')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-4">Approval History</h4>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select">
                            <option value="">All</option>
                            <option value="approved" {{ request('action') === 'approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="rejected" {{ request('action') === 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                            <option value="returned" {{ request('action') === 'returned' ? 'selected' : '' }}>Returned
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Module</label>
                        <input type="text" name="module" class="form-control" value="{{ request('module') }}"
                            placeholder="leave_request">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('admin.approval_history.index') }}" class="btn btn-outline-dark">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Module</th>
                            <th>Step</th>
                            <th>Action</th>
                            <th>Actor</th>
                            <th>Comments</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($actions as $action)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.approval_requests.show', $action->approvalRequest) }}">
                                        #{{ $action->approval_request_id }}
                                    </a>
                                </td>
                                <td>{{ ucwords(str_replace('_', ' ', $action->approvalRequest->workflow->module ?? '-')) }}
                                </td>
                                <td>{{ $action->step_order }}</td>
                                <td>
                                    <span
                                        class="badge bg-label-{{ $action->action === 'approved' ? 'success' : ($action->action === 'returned' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($action->action) }}
                                    </span>
                                </td>
                                <td>{{ $action->actor->name ?? '-' }}</td>
                                <td>{{ $action->comments ?: '-' }}</td>
                                <td>{{ $action->created_at->format('d M Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No approval history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                {{ $actions->links() }}
            </div>
        </div>
    </div>
@endsection
