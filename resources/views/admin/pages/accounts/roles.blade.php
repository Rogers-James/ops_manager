@extends('admin.layouts.layout')

@section('title', 'Admin - Roles')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Accounts /</span> Roles
        </h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Roles</h5>
                <a href="javascript:void(0);" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRole">
                    <i class="bx bx-plus me-1"></i> Add Role
                </a>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Role</th>
                            <th>Slug</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($roles as $i => $role)
                            <tr>
                                <td>{{ $roles->firstItem() + $i }}</td>

                                <td><strong>{{ $role->name }}</strong></td>

                                <td>
                                    <span class="badge bg-label-secondary" style="text-transform: lowercase;">{{ $role->slug }}</span>
                                </td>

                                <td>
                                    <span class="badge bg-label-info">{{ $role->users_count }}</span>
                                </td>

                                <td>
                                    <span class="badge bg-label-primary">{{ $role->permissions_count ?? 0 }}</span>
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

                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <i class="bx bx-lock-alt me-1"></i> Assign Permissions
                                            </a>

                                            @if ($role->users_count == 0)
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
                                <td colspan="6" class="text-center py-4">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $roles->links() }}
            </div>
        </div>
    </div>


    <!-- Add Role Modal -->
    <div class="modal fade" id="addRole" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.roles.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" id="role_name" class="form-control"
                            placeholder="e.g. HR Manager" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="role_slug" class="form-control"
                            placeholder="auto-generated" readonly>
                        <small class="text-muted">Slug is auto-generated from name.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>

        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const nameInput = document.getElementById('role_name');
            const slugInput = document.getElementById('role_slug');

            function slugify(text) {
                return text
                    .toString()
                    .trim()
                    .toLowerCase()
                    .replace(/&/g, 'and')
                    .replace(/[^a-z0-9]+/g, '_') // non-alphanumeric -> _
                    .replace(/^_+|_+$/g, '') // trim _
                    .replace(/_+/g, '_'); // collapse __
            }

            nameInput.addEventListener('input', function() {
                slugInput.value = slugify(nameInput.value);
            });
        });
    </script>

@endsection
