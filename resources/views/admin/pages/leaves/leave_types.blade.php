@extends('admin.layouts.layout')

@section('title', 'Admin - Leave Types')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Leave Management /</span> Leave Types</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLeaveType">
                <i class="bx bx-plus me-1"></i> Add Leave Type
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Code</th>
                            <th>Paid</th>
                            <th>Approval</th>
                            <th>Max/Year</th>
                            <th>Carry Forward</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($leaveTypes as $i => $t)
                            <tr>
                                <td>{{ $leaveTypes->firstItem() + $i }}</td>
                                <td><strong>{{ $t->name }}</strong></td>
                                <td>{{ $t->code ?? '-' }}</td>
                                <td><span
                                        class="badge bg-label-{{ $t->is_paid ?? true ? 'success' : 'secondary' }}">{{ $t->is_paid ?? true ? 'Paid' : 'Unpaid' }}</span>
                                </td>
                                <td><span
                                        class="badge bg-label-{{ $t->requires_approval ?? true ? 'primary' : 'secondary' }}">{{ $t->requires_approval ?? true ? 'Required' : 'No' }}</span>
                                </td>
                                <td>{{ $t->max_per_year ?? '-' }}</td>
                                <td>
                                    @if ($t->carry_forward ?? false)
                                        <span class="badge bg-label-info">Yes ({{ $t->carry_forward_limit ?? '∞' }})</span>
                                    @else
                                        <span class="badge bg-label-secondary">No</span>
                                    @endif
                                </td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editLeaveType-{{ $t->id }}"><i
                                            class="bx bx-edit-alt"></i></button>
                                    <form method="POST" action="{{ route('admin.leave_types.destroy', $t) }}"
                                        onsubmit="return confirm('Delete this leave type?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit --}}
                            <div class="modal fade" id="editLeaveType-{{ $t->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.leave_types.update', $t) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="__modal" value="editLeaveType-{{ $t->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Leave Type</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row g-2">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Name</label>
                                                    <input class="form-control" name="name"
                                                        value="{{ old('name', $t->name) }}" required>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Code</label>
                                                    <input class="form-control" name="code"
                                                        value="{{ old('code', $t->code) }}">
                                                </div>
                                            </div>

                                            <div class="row g-2">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Max per year</label>
                                                    <input class="form-control" type="number" name="max_per_year"
                                                        value="{{ old('max_per_year', $t->max_per_year) }}">
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Carry forward limit</label>
                                                    <input class="form-control" type="number" name="carry_forward_limit"
                                                        value="{{ old('carry_forward_limit', $t->carry_forward_limit) }}">
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Options</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_paid"
                                                            value="1"
                                                            {{ old('is_paid', (int) ($t->is_paid ?? 1)) ? 'checked' : '' }}>
                                                        <label class="form-check-label">Paid</label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="requires_approval" value="1"
                                                            {{ old('requires_approval', (int) ($t->requires_approval ?? 1)) ? 'checked' : '' }}>
                                                        <label class="form-check-label">Requires approval</label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="carry_forward"
                                                            value="1"
                                                            {{ old('carry_forward', (int) ($t->carry_forward ?? 0)) ? 'checked' : '' }}>
                                                        <label class="form-check-label">Carry forward</label>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($errors->any() && old('__modal') === "editLeaveType-$t->id")
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
                                <td colspan="8" class="text-center py-4">No leave types found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $leaveTypes->links() }}</div>
        </div>
    </div>

    {{-- Add --}}
    <div class="modal fade" id="addLeaveType" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.leave_types.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addLeaveType">

                <div class="modal-header">
                    <h5 class="modal-title">Add Leave Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Name</label>
                            <input class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Code</label>
                            <input class="form-control" name="code" value="{{ old('code') }}">
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Max per year</label>
                            <input class="form-control" type="number" name="max_per_year"
                                value="{{ old('max_per_year') }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Carry forward limit</label>
                            <input class="form-control" type="number" name="carry_forward_limit"
                                value="{{ old('carry_forward_limit') }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Options</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_paid" value="1"
                                    {{ old('is_paid', 1) ? 'checked' : '' }}>
                                <label class="form-check-label">Paid</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="requires_approval" value="1"
                                    {{ old('requires_approval', 1) ? 'checked' : '' }}>
                                <label class="form-check-label">Requires approval</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="carry_forward" value="1"
                                    {{ old('carry_forward') ? 'checked' : '' }}>
                                <label class="form-check-label">Carry forward</label>
                            </div>
                        </div>
                    </div>

                    @if ($errors->any() && old('__modal') === 'addLeaveType')
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
