@extends('admin.layouts.layout')

@section('title', 'Admin - Users')

@section('main-content')

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Accounts /</span> Users</h4>

        <!-- Striped Rows -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Users</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUser">
                    <i class="bx bx-plus me-1"></i> Add User
                </button>
            </div>
            <hr>
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead class="">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($users as $i => $user)
                            <tr>
                                <td>{{ $users->firstItem() + $i }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <strong>{{ $user->name }}</strong>
                                    </div>
                                </td>

                                <td>{{ $user->email }}</td>

                                <td>
                                    @if ($user->roles->count())
                                        @foreach ($user->roles as $role)
                                            <span class="badge bg-label-info me-1">{{ $role->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-label-secondary">No Role</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($user->is_active)
                                        <span class="badge bg-label-success">Active</span>
                                    @else
                                        <span class="badge bg-label-danger">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            {{-- Assign roles --}}
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#assignRolesModal" data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}"
                                                data-user-roles='@json($user->roles->pluck('id'))'>
                                                <i class="bx bx-user-check me-1"></i> Assign Roles
                                            </button>

                                            {{-- Delete --}}
                                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteUserModal" data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
        <!--/ Striped Rows -->

    </div>
    <!-- / Content -->

    {{-- Add User Modal --}}
    <div class="modal fade" id="addUser" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add User (Admin / HR / Manager)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                    </div>

                    <div class="row g-2">
                        <div class="col-md-12 mb-2">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Assign Roles</label>
                        <select name="role_ids[]" class="form-select" multiple required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ collect(old('role_ids'))->contains($role->id) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl / Cmd to select multiple roles.</small>
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            {{ old('is_active', 1) ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>

                    {{-- show validation errors inside modal --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mt-3 mb-0">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Assign Roles Modal --}}
    <div class="modal fade" id="assignRolesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" id="assignRolesForm">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Assign Roles</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-2">User: <strong id="assignRolesUserName">-</strong></p>

                    <div class="mb-2">
                        <label class="form-label">Roles</label>
                        <select name="role_ids[]" id="assignRolesSelect" class="form-select" multiple required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Roles</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete User Modal --}}
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" id="deleteUserForm">
                @csrf
                @method('DELETE')

                <div class="modal-header">
                    <h5 class="modal-title text-danger">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteUserName">-</strong>?</p>
                    <p class="text-muted mb-0">This action cannot be undone.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Assign Roles modal open
            const assignRolesModal = document.getElementById('assignRolesModal');
            assignRolesModal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;

                const userId = btn.getAttribute('data-user-id');
                const userName = btn.getAttribute('data-user-name');
                const roleIds = JSON.parse(btn.getAttribute('data-user-roles') || '[]');

                document.getElementById('assignRolesUserName').innerText = userName;

                // set form action
                const form = document.getElementById('assignRolesForm');
                form.action = "{{ url('/admin/users') }}/" + userId + "/roles";

                // preselect roles
                const select = document.getElementById('assignRolesSelect');
                [...select.options].forEach(opt => {
                    opt.selected = roleIds.includes(parseInt(opt.value));
                });
            });

            // Delete modal open
            const deleteUserModal = document.getElementById('deleteUserModal');
            deleteUserModal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;

                const userId = btn.getAttribute('data-user-id');
                const userName = btn.getAttribute('data-user-name');

                document.getElementById('deleteUserName').innerText = userName;

                const form = document.getElementById('deleteUserForm');
                form.action = "{{ url('/admin/users') }}/" + userId;
            });

        });
    </script>

@endsection
