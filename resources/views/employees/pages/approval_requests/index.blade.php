@extends('admin.layouts.layout')

@section('title', 'Admin - Approval Requests')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-4">Approval Requests</h4>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
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
                        <a href="{{ route('admin.approval_requests.index') }}" class="btn btn-outline-dark">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Module</th>
                            <th>Request</th>
                            <th>Current Step</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvalRequests as $item)
                            <tr>
                                <td>#{{ $item->id }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $item->workflow->module ?? '-')) }}</td>
                                <td>{{ class_basename($item->request_type) }} #{{ $item->request_id }}</td>
                                <td>{{ $item->current_step }}</td>
                                <td>
                                    <span
                                        class="badge bg-label-{{ $item->status === 'pending' ? 'warning' : ($item->status === 'approved' ? 'success' : 'danger') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>{{ $item->created_at->format('d M Y h:i A') }}</td>
                                <td>
                                    <a href="{{ route('admin.approval_requests.show', $item) }}"
                                        class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No approval requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                {{ $approvalRequests->links() }}
            </div>
        </div>
    </div>
@endsection
