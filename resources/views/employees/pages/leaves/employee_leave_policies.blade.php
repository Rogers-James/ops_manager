@extends('admin.layouts.layout')

@section('title', 'Admin - Employee Leave Policies')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Leave Management /</span> Employee Leave Policies</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignPolicy">
                <i class="bx bx-plus me-1"></i> Assign Policy
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Policy</th>
                            <th>Leave Type</th>
                            <th>Effective From</th>
                            <th>Effective To</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($assignments as $i => $a)
                            <tr>
                                <td>{{ $assignments->firstItem() + $i }}</td>
                                <td>
                                    <strong>{{ $a->employee->employee_code ?? '' }}</strong> -
                                    {{ $a->employee->first_name ?? '' }} {{ $a->employee->last_name ?? '' }}
                                </td>
                                <td>{{ $a->policy->name ?? '-' }}</td>
                                <td>{{ $a->policy->leaveType->name ?? '-' }}</td>
                                <td>{{ optional($a->effective_from)->format('Y-m-d') ?? $a->effective_from }}</td>
                                <td>{{ $a->effective_to ? \Carbon\Carbon::parse($a->effective_to)->format('Y-m-d') : '-' }}
                                </td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editAssign-{{ $a->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.employee_leave_policies.destroy', $a) }}"
                                        onsubmit="return confirm('Delete this assignment?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Assignment --}}
                            <div class="modal fade" id="editAssign-{{ $a->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.employee_leave_policies.update', $a) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="__modal" value="editAssign-{{ $a->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Assignment</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                Employee: <strong>{{ $a->employee->employee_code ?? '' }} -
                                                    {{ $a->employee->first_name ?? '' }}
                                                    {{ $a->employee->last_name ?? '' }}</strong>
                                            </div>

                                            <div class="row g-2">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Policy</label>
                                                    <select name="leave_policy_id" class="form-select" required>
                                                        @foreach ($policies as $p)
                                                            <option value="{{ $p->id }}"
                                                                {{ (string) old('leave_policy_id', $a->leave_policy_id) === (string) $p->id ? 'selected' : '' }}>
                                                                {{ $p->name }} ({{ $p->leaveType->name ?? '-' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">Effective From</label>
                                                    <input type="date" class="form-control" name="effective_from"
                                                        required
                                                        value="{{ old('effective_from', \Carbon\Carbon::parse($a->effective_from)->format('Y-m-d')) }}">
                                                </div>

                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">Effective To</label>
                                                    <input type="date" class="form-control" name="effective_to"
                                                        value="{{ old('effective_to', $a->effective_to ? \Carbon\Carbon::parse($a->effective_to)->format('Y-m-d') : '') }}">
                                                </div>
                                            </div>

                                            @if ($errors->any() && old('__modal') === "editAssign-$a->id")
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
                                            <button class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No assignments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $assignments->links() }}</div>
        </div>
    </div>

    {{-- Assign Policy Modal --}}
    <div class="modal fade" id="assignPolicy" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.employee_leave_policies.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="assignPolicy">

                <div class="modal-header">
                    <h5 class="modal-title">Assign Policy to Employee</h5>
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
                            <label class="form-label">Policy</label>
                            <select name="leave_policy_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($policies as $p)
                                    <option value="{{ $p->id }}"
                                        {{ (string) old('leave_policy_id') === (string) $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->leaveType->name ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Effective From</label>
                            <input type="date" class="form-control" name="effective_from" required
                                value="{{ old('effective_from', date('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Effective To</label>
                            <input type="date" class="form-control" name="effective_to"
                                value="{{ old('effective_to') }}">
                        </div>
                    </div>

                    @if ($errors->any() && old('__modal') === 'assignPolicy')
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
                    <button class="btn btn-primary">Assign</button>
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
