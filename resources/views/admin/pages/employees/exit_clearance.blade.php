@extends('admin.layouts.layout')

@section('title', 'Admin - Exit Clearance')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">HR / Exits /</span> Clearance
        </h4>

        {{-- Summary --}}
        <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="mb-1">
                        {{ $resignation->employee->employee_code }} -
                        {{ $resignation->employee->first_name }} {{ $resignation->employee->last_name }}
                    </h5>
                    <div class="text-muted">
                        Resignation: {{ optional($resignation->resignation_date)->format('Y-m-d') }} |
                        Last Day: {{ optional($resignation->last_working_day)->format('Y-m-d') }}
                    </div>
                </div>

                <div class="text-end">
                    <span class="badge bg-label-info">Exit: {{ strtoupper($resignation->status) }}</span>
                    <span class="badge bg-label-secondary ms-1">Clearance: {{ strtoupper($clearance->status) }}</span>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Final Settlement --}}
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Final Settlement</h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.exits.final_settlement.save', $resignation->id) }}">
                            @csrf

                            <div class="mb-2">
                                <label class="form-label">Final Amount</label>
                                <input type="number" step="0.01" class="form-control" name="amount" required
                                    value="{{ old('amount', $finalSettlement->amount ?? 0) }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Status</label>
                                @php
                                    $sel = old('status', $finalSettlement->status ?? 'pending');
                                    $statuses = ['pending', 'approved', 'paid', 'hold'];
                                @endphp
                                <select class="form-select" name="status" required>
                                    @foreach ($statuses as $s)
                                        <option value="{{ $s }}" {{ $sel === $s ? 'selected' : '' }}>
                                            {{ strtoupper($s) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if (isset($payrollRuns) && $payrollRuns)
                                <div class="mb-2">
                                    <label class="form-label">Payroll Run (optional)</label>
                                    <select class="form-select" name="payroll_run_id">
                                        <option value="">-- None --</option>
                                        @foreach ($payrollRuns as $pr)
                                            <option value="{{ $pr->id }}"
                                                {{ (string) old('payroll_run_id', $finalSettlement->payroll_run_id ?? '') === (string) $pr->id ? 'selected' : '' }}>
                                                #{{ $pr->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <button class="btn btn-primary btn-sm">Save Settlement</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tasks --}}
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Asset Handover & Document Clearance</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTask">
                            <i class="bx bx-plus me-1"></i> Add Task
                        </button>
                    </div>

                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Module</th>
                                    <th>Task</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $t)
                                    <tr>
                                        <td><span class="badge bg-label-secondary">{{ strtoupper($t->module) }}</span></td>
                                        <td>
                                            <strong>{{ $t->title }}</strong>
                                            @if ($t->notes)
                                                <br><small class="text-muted">{{ $t->notes }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php $map = ['pending'=>'info','approved'=>'success','rejected'=>'danger']; @endphp
                                            <span class="badge bg-label-{{ $map[$t->status] ?? 'secondary' }}">
                                                {{ strtoupper($t->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($t->status === 'pending')
                                                <form class="d-inline" method="POST"
                                                    action="{{ route('admin.exits.clearance.task.status', $t->id) }}">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button class="btn btn-sm btn-success"
                                                        onclick="return confirm('Approve this task?')">Approve</button>
                                                </form>

                                                <form class="d-inline" method="POST"
                                                    action="{{ route('admin.exits.clearance.task.status', $t->id) }}">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Reject this task?')">Reject</button>
                                                </form>
                                            @else
                                                <small class="text-muted">Done</small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3">No clearance tasks.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @php
                        $pendingCount = $tasks->where('status', 'pending')->count();
                        $notApproved = $tasks->where('status', '!=', 'approved')->count();
                    @endphp

                    <div class="card-body pt-0">
                        <form method="POST" action="{{ route('admin.exits.clearance.status', $resignation->id) }}">
                            @csrf @method('PUT')

                            <input type="hidden" name="status" value="cleared">

                            <button class="btn btn-primary" {{ $notApproved > 0 ? 'disabled' : '' }}>
                                Mark as Cleared
                            </button>

                            @if ($notApproved > 0)
                                <small class="text-muted ms-2">Approve all tasks first.</small>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Task Modal --}}
    <div class="modal fade" id="addTask" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.exits.clearance.task.add') }}">
                @csrf

                <input type="hidden" name="exit_clearance_id" value="{{ $clearance->id }}">

                <div class="modal-header">
                    <h5 class="modal-title">Add Clearance Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Module</label>
                        <select name="module" class="form-select" required>
                            <option value="hr">HR</option>
                            <option value="it">IT</option>
                            <option value="finance">Finance</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Task Title</label>
                        <input name="title" class="form-control" required placeholder="e.g. Return Laptop">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
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
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>


@endsection
