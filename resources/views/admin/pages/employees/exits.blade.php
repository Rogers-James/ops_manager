@extends('admin.layouts.layout')

@section('title', 'Admin - Employee Exits')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">HR /</span> Employee Exits
        </h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Exit Requests</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExit">
                    <i class="bx bx-plus me-1"></i> Add Exit
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Resignation Date</th>
                            <th>Last Working Day</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($exits as $i => $x)
                            <tr>
                                <td>{{ $exits->firstItem() + $i }}</td>

                                <td>
                                    <strong>{{ $x->employee->employee_code ?? '' }}</strong><br>
                                    <small>{{ $x->employee->first_name }} {{ $x->employee->last_name }}</small>
                                </td>

                                <td>{{ optional($x->resignation_date)->format('Y-m-d') }}</td>
                                <td>{{ optional($x->last_working_day)->format('Y-m-d') }}</td>

                                <td>
                                    @php
                                        $statusMap = [
                                            'submitted' => 'info',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'withdrawn' => 'warning',
                                        ];
                                    @endphp
                                    <span class="badge bg-label-{{ $statusMap[$x->status] ?? 'secondary' }}">
                                        {{ strtoupper($x->status) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#viewExit"
                                                data-employee="{{ $x->employee->employee_code }} - {{ $x->employee->first_name }} {{ $x->employee->last_name }}"
                                                data-resignation="{{ optional($x->resignation_date)->format('Y-m-d') }}"
                                                data-last="{{ optional($x->last_working_day)->format('Y-m-d') }}"
                                                data-reason="{{ $x->reason ?? '-' }}"
                                                data-status="{{ strtoupper($x->status) }}">
                                                <i class="bx bx-show me-1"></i> View
                                            </button>

                                            <a class="dropdown-item"
                                                href="{{ route('admin.exits.clearance.show', $x->id) }}">
                                                <i class="bx bx-folder-open me-1"></i> Clearance
                                            </a>

                                            @if ($x->status === 'submitted')
                                                <form method="POST" action="{{ route('admin.exits.status', $x->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button class="dropdown-item text-success"
                                                        onclick="return confirm('Approve this exit request?')">
                                                        <i class="bx bx-check-circle me-1"></i> Approve
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.exits.status', $x->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button class="dropdown-item text-danger"
                                                        onclick="return confirm('Reject this exit request?')">
                                                        <i class="bx bx-x-circle me-1"></i> Reject
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($x->status === 'submitted')
                                                <form method="POST" action="{{ route('admin.exits.status', $x->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="withdrawn">
                                                    <button class="dropdown-item text-warning"
                                                        onclick="return confirm('Withdraw this exit request?')">
                                                        <i class="bx bx-undo me-1"></i> Withdraw
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No exit requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $exits->links() }}
            </div>
        </div>
    </div>

    {{-- Add Exit Modal --}}
    <div class="modal fade" id="addExit" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.exits.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Exit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Employee</label>
                        <select class="form-select" name="employee_id" required>
                            <option value="">-- Select --</option>
                            @foreach ($employees as $e)
                                <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>
                                    {{ $e->employee_code }} - {{ $e->first_name }} {{ $e->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Resignation Date</label>
                        <input type="date" class="form-control" name="resignation_date" required
                            value="{{ old('resignation_date', date('Y-m-d')) }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Last Working Day</label>
                        <input type="date" class="form-control" name="last_working_day" required
                            value="{{ old('last_working_day', date('Y-m-d')) }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" name="reason" rows="2">{{ old('reason') }}</textarea>
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
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Save Exit</button>
                </div>
            </form>
        </div>
    </div>

    {{-- View Exit Modal --}}
    <div class="modal fade" id="viewExit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Exit Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Employee:</strong> <span id="vxEmployee"></span></p>
                    <p><strong>Status:</strong> <span id="vxStatus"></span></p>
                    <p><strong>Resignation Date:</strong> <span id="vxResignation"></span></p>
                    <p><strong>Last Working Day:</strong> <span id="vxLast"></span></p>
                    <p><strong>Reason:</strong> <span id="vxReason"></span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewModal = document.getElementById('viewExit');
            viewModal.addEventListener('show.bs.modal', function(e) {
                const btn = e.relatedTarget;

                document.getElementById('vxEmployee').innerText = btn.getAttribute('data-employee');
                document.getElementById('vxStatus').innerText = btn.getAttribute('data-status');
                document.getElementById('vxResignation').innerText = btn.getAttribute('data-resignation');
                document.getElementById('vxLast').innerText = btn.getAttribute('data-last');
                document.getElementById('vxReason').innerText = btn.getAttribute('data-reason');
            });

            @if ($errors->any())
                new bootstrap.Modal(document.getElementById('addExit')).show();
            @endif
        });
    </script>


@endsection
