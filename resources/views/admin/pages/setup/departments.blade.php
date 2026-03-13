@extends('admin.layouts.layout')

@section('title', 'Admin - Departments')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Company Setup /</span> Departments
        </h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Departments</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDepartment">
                    <i class="bx bx-plus me-1"></i> Add Department
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Department</th>
                            <th>Parent</th>
                            <th>Children</th>
                            <th>Used</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($departments as $i => $dep)
                            <tr>
                                <td>{{ $departments->firstItem() + $i }}</td>
                                <td><strong>{{ $dep->name }}</strong></td>
                                <td>{{ $dep->parent?->name ?? '-' }}</td>
                                <td><span class="badge bg-label-info">{{ $dep->children_count }}</span></td>
                                <td><span class="badge bg-label-primary">{{ $dep->employments_count }}</span></td>

                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#editDepartment{{ $dep->id }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </button>

                                            <form method="POST"
                                                action="{{ route('admin.departments.destroy', $dep->id) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Delete this department?')"
                                                    {{ $dep->children_count > 0 || $dep->employments_count > 0 ? 'disabled' : '' }}>
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Edit Modal --}}
                                    <div class="modal fade" id="editDepartment{{ $dep->id }}" data-bs-backdrop="static"
                                        tabindex="-1">
                                        <div class="modal-dialog">
                                            <form class="modal-content" method="POST"
                                                action="{{ route('admin.departments.update', $dep->id) }}">
                                                @csrf @method('PUT')

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Department</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="mb-2">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control" required
                                                            value="{{ $dep->name }}">
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label">Parent Department (optional)</label>
                                                        <select name="parent_id" class="form-select">
                                                            <option value="">-- None --</option>
                                                            @foreach ($parents as $p)
                                                                <option value="{{ $p->id }}"
                                                                    {{ $dep->parent_id == $p->id ? 'selected' : '' }}
                                                                    {{ $p->id == $dep->id ? 'disabled' : '' }}>
                                                                    {{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    {{-- /Edit Modal --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No departments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $departments->links() }}
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addDepartment" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.departments.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Parent Department (optional)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- None --</option>
                            @foreach ($parents as $p)
                                <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                new bootstrap.Modal(document.getElementById('addDepartment')).show();
            });
        </script>
    @endif

@endsection
