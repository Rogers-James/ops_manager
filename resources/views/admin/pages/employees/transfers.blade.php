@extends('admin.layouts.layout')

@section('title', 'Admin - Transfers')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">HR /</span> Employee Transfers
        </h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Transfers</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTransfer">
                    <i class="bx bx-plus me-1"></i> Add Transfer
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Effective</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($transfers as $i => $t)
                            <tr>
                                <td>{{ $transfers->firstItem() + $i }}</td>

                                <td>
                                    <strong>{{ $t->employee->employee_code ?? '' }}</strong><br>
                                    <small>{{ $t->employee->first_name }} {{ $t->employee->last_name }}</small>
                                </td>

                                <td>
                                    <small class="text-muted">
                                        {{ $t->fromDepartment->name ?? '-' }} /
                                        {{ $t->fromDesignation->name ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    <small class="text-muted">
                                        {{ $t->toDepartment->name ?? '-' }} /
                                        {{ $t->toDesignation->name ?? '-' }}
                                    </small>
                                </td>

                                <td>{{ optional($t->effective_date)->format('Y-m-d') }}</td>

                                <td>
                                    @php
                                        $statusMap = [
                                            'draft' => 'secondary',
                                            'submitted' => 'info',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'cancelled' => 'warning',
                                        ];
                                    @endphp
                                    <span class="badge bg-label-{{ $statusMap[$t->status] ?? 'secondary' }}">
                                        {{ strtoupper($t->status) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#viewTransfer"
                                                data-employee="{{ $t->employee->employee_code }} - {{ $t->employee->first_name }} {{ $t->employee->last_name }}"
                                                data-effective="{{ optional($t->effective_date)->format('Y-m-d') }}"
                                                data-reason="{{ $t->reason ?? '-' }}"
                                                data-from="{{ ($t->fromDepartment->name ?? '-') . ' / ' . ($t->fromDesignation->name ?? '-') }}"
                                                data-to="{{ ($t->toDepartment->name ?? '-') . ' / ' . ($t->toDesignation->name ?? '-') }}"
                                                data-status="{{ strtoupper($t->status) }}">
                                                <i class="bx bx-show me-1"></i> View
                                            </button>

                                            @if ($t->status === 'submitted')
                                                <form method="POST"
                                                    action="{{ route('admin.transfers.status', $t->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button class="dropdown-item text-success"
                                                        onclick="return confirm('Approve this transfer?')">
                                                        <i class="bx bx-check-circle me-1"></i> Approve
                                                    </button>
                                                </form>

                                                <form method="POST"
                                                    action="{{ route('admin.transfers.status', $t->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button class="dropdown-item text-danger"
                                                        onclick="return confirm('Reject this transfer?')">
                                                        <i class="bx bx-x-circle me-1"></i> Reject
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No transfers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $transfers->links() }}
            </div>
        </div>
    </div>

    {{-- Add Transfer Modal --}}
    <div class="modal fade" id="addTransfer" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.transfers.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Employee</label>
                            <select class="form-select" name="employee_id" required>
                                <option value="">-- Select --</option>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}"
                                        {{ old('employee_id') == $e->id ? 'selected' : '' }}>
                                        {{ $e->employee_code }} - {{ $e->first_name }} {{ $e->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Effective Date</label>
                            <input type="date" class="form-control" name="effective_date" required
                                value="{{ old('effective_date', date('Y-m-d')) }}">
                        </div>
                    </div>

                    <hr class="my-2">

                    <h6 class="mb-2">Transfer To</h6>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Department</label>
                            <select name="to_department_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($departments as $d)
                                    <option value="{{ $d->id }}"
                                        {{ old('to_department_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Designation</label>
                            <select name="to_designation_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($designations as $d)
                                    <option value="{{ $d->id }}"
                                        {{ old('to_designation_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Location</label>
                            <select name="to_location_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($locations as $l)
                                    <option value="{{ $l->id }}"
                                        {{ old('to_location_id') == $l->id ? 'selected' : '' }}>
                                        {{ $l->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Grade</label>
                            <select name="to_grade_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($grades as $g)
                                    <option value="{{ $g->id }}"
                                        {{ old('to_grade_id') == $g->id ? 'selected' : '' }}>
                                        {{ $g->name }} {{ $g->rank ? '(Rank ' . $g->rank . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Cost Center</label>
                            <select name="to_cost_center_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($costCenters as $cc)
                                    <option value="{{ $cc->id }}"
                                        {{ old('to_cost_center_id') == $cc->id ? 'selected' : '' }}>
                                        {{ $cc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Manager</label>
                            <select name="to_manager_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($managers as $m)
                                    <option value="{{ $m->id }}"
                                        {{ old('to_manager_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->employee_code }} - {{ $m->first_name }} {{ $m->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="2">{{ old('reason') }}</textarea>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger mt-2 mb-0">
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
                    <button type="submit" class="btn btn-primary">Save Transfer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- View Transfer Modal --}}
    <div class="modal fade" id="viewTransfer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Employee:</strong> <span id="vtEmployee"></span></p>
                    <p><strong>Status:</strong> <span id="vtStatus"></span></p>
                    <p><strong>Effective Date:</strong> <span id="vtEffective"></span></p>
                    <p><strong>From:</strong> <span id="vtFrom"></span></p>
                    <p><strong>To:</strong> <span id="vtTo"></span></p>
                    <p><strong>Reason:</strong> <span id="vtReason"></span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewModal = document.getElementById('viewTransfer');

            viewModal.addEventListener('show.bs.modal', function(e) {
                const btn = e.relatedTarget;

                document.getElementById('vtEmployee').innerText = btn.getAttribute('data-employee');
                document.getElementById('vtStatus').innerText = btn.getAttribute('data-status');
                document.getElementById('vtEffective').innerText = btn.getAttribute('data-effective');
                document.getElementById('vtFrom').innerText = btn.getAttribute('data-from');
                document.getElementById('vtTo').innerText = btn.getAttribute('data-to');
                document.getElementById('vtReason').innerText = btn.getAttribute('data-reason');
            });

            @if ($errors->any())
                new bootstrap.Modal(document.getElementById('addTransfer')).show();
            @endif
        });
    </script>

@endsection
