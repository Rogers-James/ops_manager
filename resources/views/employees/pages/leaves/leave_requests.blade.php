@extends('admin.layouts.layout')

@section('title', 'Admin - Leave Requests')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Leave Management /</span> Leave Requests</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRequest">
                <i class="bx bx-plus me-1"></i> Create Request
            </button>
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($employees as $e)
                                <option value="{{ $e->id }}" {{ request('employee_id') == $e->id ? 'selected' : '' }}>
                                    {{ $e->employee_code }} - {{ $e->first_name }} {{ $e->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            @foreach (['pending', 'approved', 'rejected', 'cancelled'] as $s)
                                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <a class="btn btn-outline-secondary w-100"
                            href="{{ route('admin.leave_requests.index') }}">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Dates</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($requests as $i => $r)
                            @php
                                $badge = match ($r->status) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'cancelled' => 'secondary',
                                    default => 'warning',
                                };
                            @endphp
                            <tr>
                                <td>{{ $requests->firstItem() + $i }}</td>
                                <td>
                                    <strong>{{ $r->employee->employee_code ?? '' }}</strong> -
                                    {{ $r->employee->first_name ?? '' }} {{ $r->employee->last_name ?? '' }}
                                </td>
                                <td>{{ $r->leaveType->name ?? '-' }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($r->start_date)->format('Y-m-d') }}
                                    to
                                    {{ \Carbon\Carbon::parse($r->end_date)->format('Y-m-d') }}
                                </td>
                                <td><span class="badge bg-label-primary">{{ $r->days ?? '-' }}</span></td>
                                <td><span class="badge bg-label-{{ $badge }}">{{ ucfirst($r->status) }}</span></td>
                                <td>{{ \Illuminate\Support\Str::limit($r->reason, 30) ?? '-' }}</td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#statusReq-{{ $r->id }}">
                                        <i class="bx bx-check-circle"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.leave_requests.destroy', $r) }}"
                                        onsubmit="return confirm('Delete this request?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Update Status Modal --}}
                            <div class="modal fade" id="statusReq-{{ $r->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.leave_requests.status', $r) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="__modal" value="statusReq-{{ $r->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Request Status</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <div><strong>Employee:</strong> {{ $r->employee->first_name ?? '' }}
                                                    {{ $r->employee->last_name ?? '' }}</div>
                                                <div><strong>Type:</strong> {{ $r->leaveType->name ?? '-' }}</div>
                                                <div><strong>Days:</strong> {{ $r->days ?? '-' }}</div>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select" required>
                                                    @foreach (['pending', 'approved', 'rejected', 'cancelled'] as $s)
                                                        <option value="{{ $s }}"
                                                            {{ old('status', $r->status) === $s ? 'selected' : '' }}>
                                                            {{ ucfirst($s) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Admin Note</label>
                                                <textarea name="admin_note" class="form-control" rows="3">{{ old('admin_note', $r->admin_note ?? '') }}</textarea>
                                            </div>

                                            @if ($errors->any() && old('__modal') === "statusReq-$r->id")
                                                <div class="alert alert-danger mb-0">
                                                    <ul class="mb-0">
                                                        @foreach ($errors->all() as $e)
                                                            <li>{{ $e }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary">Update</button>
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

    {{-- Add Request Modal --}}
    <div class="modal fade" id="addRequest" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.leave_requests.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addRequest">

                <div class="modal-header">
                    <h5 class="modal-title">Create Leave Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}"
                                        {{ (string) old('employee_id') === (string) $e->id ? 'selected' : '' }}>
                                        {{ $e->employee_code }} - {{ $e->first_name }} {{ $e->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Leave Type</label>
                            <select name="leave_type_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($leaveTypes as $t)
                                    <option value="{{ $t->id }}"
                                        {{ (string) old('leave_type_id') === (string) $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" required
                                value="{{ old('start_date', date('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" required
                                value="{{ old('end_date', date('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
                    </div>

                    @if ($errors->any() && old('__modal') === 'addRequest')
                        <div class="alert alert-danger mb-0">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($errors->any() && old('__modal'))
                new bootstrap.Modal(document.getElementById(@json(old('__modal')))).show();
            @endif
        });
    </script>

@endsection
