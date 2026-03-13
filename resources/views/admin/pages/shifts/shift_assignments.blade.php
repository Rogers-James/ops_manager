@extends('admin.layouts.layout')

@section('title', 'Admin - Shift Assignments')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Shifts & Calendar /</span> Shift Assignments</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addShiftAssignment">
                <i class="bx bx-plus me-1"></i> Assign Shift Group
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Shift Group</th>
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
                                    <strong>{{ $a->employee?->employee_code }}</strong><br>
                                    <small class="text-muted">{{ $a->employee?->first_name }}
                                        {{ $a->employee?->last_name }}</small>
                                </td>
                                <td>{{ $a->group?->name ?? '-' }}</td>
                                <td>{{ $a->effective_from }}</td>
                                <td>{{ $a->effective_to ?? '-' }}</td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editAssignment-{{ $a->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.shift_assignments.destroy', $a) }}"
                                        onsubmit="return confirm('Delete this assignment?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editAssignment-{{ $a->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.shift_assignments.update', $a) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="__modal" value="editAssignment-{{ $a->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Assignment</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label">Shift Group</label>
                                                <select class="form-select" name="shift_group_id" required>
                                                    @foreach ($shiftGroups as $g)
                                                        <option value="{{ $g->id }}"
                                                            {{ (string) old('shift_group_id', $a->shift_group_id) === (string) $g->id ? 'selected' : '' }}>
                                                            {{ $g->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="row g-2">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Effective From</label>
                                                    <input class="form-control" type="date" name="effective_from"
                                                        value="{{ old('effective_from', $a->effective_from) }}" required>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Effective To</label>
                                                    <input class="form-control" type="date" name="effective_to"
                                                        value="{{ old('effective_to', $a->effective_to) }}">
                                                </div>
                                            </div>

                                            @if ($errors->any() && old('__modal') === "editAssignment-$a->id")
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
                                <td colspan="6" class="text-center py-4">No assignments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $assignments->links() }}</div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addShiftAssignment" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.shift_assignments.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addShiftAssignment">

                <div class="modal-header">
                    <h5 class="modal-title">Assign Shift Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Employee</label>
                        <select class="form-select" name="employee_id" required>
                            <option value="">-- Select --</option>
                            @foreach ($employees as $e)
                                <option value="{{ $e->id }}"
                                    {{ (string) old('employee_id') === (string) $e->id ? 'selected' : '' }}>
                                    {{ $e->employee_code }} - {{ $e->first_name }} {{ $e->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Shift Group</label>
                        <select class="form-select" name="shift_group_id" required>
                            <option value="">-- Select --</option>
                            @foreach ($shiftGroups as $g)
                                <option value="{{ $g->id }}"
                                    {{ (string) old('shift_group_id') === (string) $g->id ? 'selected' : '' }}>
                                    {{ $g->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Effective From</label>
                            <input class="form-control" type="date" name="effective_from"
                                value="{{ old('effective_from', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Effective To</label>
                            <input class="form-control" type="date" name="effective_to"
                                value="{{ old('effective_to') }}">
                        </div>
                    </div>

                    @if ($errors->any() && old('__modal') === 'addShiftAssignment')
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
