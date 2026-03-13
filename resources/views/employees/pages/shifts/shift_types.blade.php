@extends('admin.layouts.layout')

@section('title', 'Admin - Shift Types')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Shifts & Calendar /</span> Shift Types</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addShiftType">
                <i class="bx bx-plus me-1"></i> Add Shift Type
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Grace (min)</th>
                            <th>Break (min)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($shiftTypes as $i => $st)
                            <tr>
                                <td>{{ $shiftTypes->firstItem() + $i }}</td>
                                <td><strong>{{ $st->name }}</strong></td>
                                <td>{{ $st->start_time }}</td>
                                <td>{{ $st->end_time }}</td>
                                <td>{{ $st->grace_in_minutes ?? 0 }}</td>
                                <td>{{ $st->break_minutes ?? 0 }}</td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editShiftType-{{ $st->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.shift_types.destroy', $st) }}"
                                        onsubmit="return confirm('Delete this shift type?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editShiftType-{{ $st->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.shift_types.update', $st) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="__modal" value="editShiftType-{{ $st->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Shift Type</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label">Name</label>
                                                <input class="form-control" name="name"
                                                    value="{{ old('name', $st->name) }}" required>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Start Time</label>
                                                    <input class="form-control" type="time" name="start_time"
                                                        value="{{ old('start_time', $st->start_time) }}" required>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">End Time</label>
                                                    <input class="form-control" type="time" name="end_time"
                                                        value="{{ old('end_time', $st->end_time) }}" required>
                                                </div>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Grace In (minutes)</label>
                                                    <input class="form-control" type="number" name="grace_in_minutes"
                                                        value="{{ old('grace_in_minutes', $st->grace_in_minutes ?? 0) }}">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Break (minutes)</label>
                                                    <input class="form-control" type="number" name="break_minutes"
                                                        value="{{ old('break_minutes', $st->break_minutes ?? 0) }}">
                                                </div>
                                            </div>

                                            @if ($errors->any() && old('__modal') === "editShiftType-$st->id")
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
                                <td colspan="7" class="text-center py-4">No shift types found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $shiftTypes->links() }}</div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addShiftType" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.shift_types.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addShiftType">

                <div class="modal-header">
                    <h5 class="modal-title">Add Shift Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Start Time</label>
                            <input class="form-control" type="time" name="start_time"
                                value="{{ old('start_time') }}" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">End Time</label>
                            <input class="form-control" type="time" name="end_time" value="{{ old('end_time') }}"
                                required>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Grace In (minutes)</label>
                            <input class="form-control" type="number" name="grace_in_minutes"
                                value="{{ old('grace_in_minutes', 0) }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Break (minutes)</label>
                            <input class="form-control" type="number" name="break_minutes"
                                value="{{ old('break_minutes', 0) }}">
                        </div>
                    </div>

                    @if ($errors->any() && old('__modal') === 'addShiftType')
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
