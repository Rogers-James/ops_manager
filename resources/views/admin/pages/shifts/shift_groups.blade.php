@extends('admin.layouts.layout')

@section('title', 'Admin - Shift Groups')

@section('main-content')

    <div class="container-xxl flex-grow-1 contain er-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Shifts & Calendar /</span> Shift Groups</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addShiftGroup">
                <i class="bx bx-plus me-1"></i> Add Shift Group
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Group</th>
                            <th>Default Shift</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($shiftGroups as $i => $g)
                            <tr>
                                <td>{{ $shiftGroups->firstItem() + $i }}</td>
                                <td><strong>{{ $g->name }}</strong></td>
                                <td>{{ $g->defaultShiftType?->name ?? '-' }}</td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editShiftGroup-{{ $g->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.shift_groups.destroy', $g) }}"
                                        onsubmit="return confirm('Delete this group?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editShiftGroup-{{ $g->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.shift_groups.update', $g) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="__modal" value="editShiftGroup-{{ $g->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Shift Group</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label">Name</label>
                                                <input class="form-control" name="name"
                                                    value="{{ old('name', $g->name) }}" required>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Default Shift Type</label>
                                                <select class="form-select" name="default_shift_type_id" required>
                                                    @foreach ($shiftTypes as $st)
                                                        <option value="{{ $st->id }}"
                                                            {{ (string) old('default_shift_type_id', $g->default_shift_type_id) === (string) $st->id ? 'selected' : '' }}>
                                                            {{ $st->name }} ({{ $st->start_time }} -
                                                            {{ $st->end_time }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            @if ($errors->any() && old('__modal') === "editShiftGroup-$g->id")
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
                                <td colspan="4" class="text-center py-4">No shift groups found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $shiftGroups->links() }}</div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addShiftGroup" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.shift_groups.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addShiftGroup">

                <div class="modal-header">
                    <h5 class="modal-title">Add Shift Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Default Shift Type</label>
                        <select class="form-select" name="default_shift_type_id" required>
                            <option value="">-- Select --</option>
                            @foreach ($shiftTypes as $st)
                                <option value="{{ $st->id }}"
                                    {{ (string) old('default_shift_type_id') === (string) $st->id ? 'selected' : '' }}>
                                    {{ $st->name }} ({{ $st->start_time }} - {{ $st->end_time }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($errors->any() && old('__modal') === 'addShiftGroup')
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
