@extends('admin.layouts.layout')

@section('title', 'Admin - Reuqest ')

@section('main-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Operations / HR Requests /</span> Requests</h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRequest">
                <i class="bx bx-plus me-1"></i> Create Request
            </button>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form class="row g-2" method="GET">
                    <div class="col-md-4">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($employees as $e)
                                <option value="{{ $e->id }}"
                                    {{ request('employee_id') == $e->id ? 'selected' : '' }}>
                                    {{ $e->employee_code }} - {{ $e->first_name }} {{ $e->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Request Type</label>
                        <select name="request_type_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($requestTypes as $t)
                                <option value="{{ $t->id }}"
                                    {{ request('request_type_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            @foreach (['open', 'pending', 'approved', 'rejected', 'closed', 'cancelled'] as $st)
                                <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                                    {{ ucfirst($st) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Workflow</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $i => $r)
                            <tr>
                                <td>{{ $requests->firstItem() + $i }}</td>
                                <td>{{ $r->employee->employee_code ?? '' }} - {{ $r->employee->first_name ?? '' }}
                                    {{ $r->employee->last_name ?? '' }}</td>
                                <td>{{ $r->requestType->name ?? '-' }}</td>
                                <td>{{ $r->subject }}</td>
                                <td>{{ $r->requestType->workflow->name ?? '-' }}</td>
                                <td><span class="badge bg-label-info">{{ strtoupper($r->status) }}</span></td>
                                <td>{{ $r->requested_date ? \Carbon\Carbon::parse($r->requested_date)->format('Y-m-d') : '-' }}
                                </td>
                                <td class="d-flex gap-1">
                                    <a class="btn btn-sm btn-outline-primary"
                                        href="{{ route('admin.requests.show', $r) }}">
                                        <i class="bx bx-show"></i>
                                    </a>

                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#statusRequest-{{ $r->id }}">
                                        <i class="bx bx-check-circle"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.requests.destroy', $r) }}"
                                        onsubmit="return confirm('Delete this request?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="statusRequest-{{ $r->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.requests.status', $r) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Request Status</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <strong>{{ $r->subject }}</strong><br>
                                                {{ $r->employee->first_name ?? '' }} {{ $r->employee->last_name ?? '' }}
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select" required>
                                                    @foreach (['open', 'pending', 'approved', 'rejected', 'closed', 'cancelled'] as $st)
                                                        <option value="{{ $st }}"
                                                            {{ $r->status === $st ? 'selected' : '' }}>
                                                            {{ ucfirst($st) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Admin Note</label>
                                                <textarea name="admin_note" class="form-control" rows="3">{{ $r->admin_note }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $requests->links() }}</div>
        </div>
    </div>

    <div class="modal fade" id="addRequest" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.requests.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create HR Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}">{{ $e->employee_code }} - {{ $e->first_name }}
                                        {{ $e->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Request Type</label>
                            <select name="request_type_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($requestTypes as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Subject</label>
                        <input class="form-control" name="subject" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Details</label>
                        <textarea name="details" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Requested Date</label>
                        <input type="date" class="form-control" name="requested_date" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
