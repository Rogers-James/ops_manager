@extends('admin.layouts.layout')

@section('title', 'Admin - Permissions')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Accounts /</span> Permissions
        </h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Permissions</h5>
                <a href="javascript:void(0);" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#addPermission">
                    <i class="bx bx-plus me-1"></i> Add Permission
                </a>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Permission</th>
                            <th>Slug</th>
                            <th>Module</th>
                            <th>Roles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($permissions as $i => $perm)
                            <tr>
                                <td>{{ $permissions->firstItem() + $i }}</td>

                                <td><strong>{{ $perm->name }}</strong></td>

                                <td><span class="badge bg-label-secondary" style="text-transform: lowercase;">{{ $perm->slug }}</span></td>

                                <td>
                                    @if ($perm->module)
                                        <span class="badge bg-label-info">{{ $perm->module }}</span>
                                    @else
                                        <span class="badge bg-label-warning">General</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-label-primary">{{ $perm->roles_count }}</span>
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>

                                            @if ($perm->roles_count == 0)
                                                <a class="dropdown-item text-danger" href="javascript:void(0);">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </a>
                                            @else
                                                <span class="dropdown-item text-muted">
                                                    <i class="bx bx-info-circle me-1"></i> In use (can’t delete)
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No permissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>


    <!-- Add Role Modal -->
    <div class="modal fade" id="addPermission" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.permissions.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Permission Name</label>
                        <input type="text" name="name" id="perm_name" class="form-control"
                            placeholder="e.g. Create Employee" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Module</label>
                        <select name="module" class="form-select">
                            <option value="">General</option>
                            <option value="Employees">Employees</option>
                            <option value="Attendance">Attendance</option>
                            <option value="Leaves">Leaves</option>
                            <option value="Payroll">Payroll</option>
                            <option value="Reports">Reports</option>
                            <option value="Settings">Settings</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="perm_slug" class="form-control" readonly
                            placeholder="auto-generated">
                        <small class="text-muted">Auto-generated from name.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const nameInput = document.getElementById('perm_name');
            const slugInput = document.getElementById('perm_slug');

            function slugify(text) {
                return text.toString().trim().toLowerCase()
                    .replace(/&/g, 'and')
                    .replace(/[^a-z0-9]+/g, '.') // use dot style like employees.create
                    .replace(/^\.+|\.+$/g, '')
                    .replace(/\.+/g, '.');
            }

            nameInput.addEventListener('input', function() {
                slugInput.value = slugify(nameInput.value);
            });
        });
    </script>

@endsection
