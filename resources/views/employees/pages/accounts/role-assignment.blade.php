@extends('admin.layouts.layout')

@section('title', 'Admin - Role Assigned')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Accounts /</span> Role Assignment
        </h4>

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-pills" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-user-roles" type="button"
                            role="tab">
                            Assign Roles to Users
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-role-perms" type="button"
                            role="tab">
                            Assign Permissions to Roles
                        </button>
                    </li>
                </ul>
                <hr>
            </div>

            <div class="tab-content p-3">

                {{-- TAB 1: User -> Roles --}}
                <div class="tab-pane fade show active" id="tab-user-roles" role="tabpanel">
                    <form method="POST" action="{{ route('admin.user_roles.assign') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Select User</label>
                                <select name="user_id" id="user_select" class="form-select" required>
                                    <option value="">-- Select user --</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}" data-roles='@json($u->roles->pluck('id'))'>
                                            {{ $u->name }} ({{ $u->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">User roles will be replaced by selected roles.</small>
                            </div>

                            <div class="col-md-7">
                                <label class="form-label">Assign Roles</label>
                                <select name="role_ids[]" id="user_roles_select" class="form-select" multiple
                                    size="8">
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->slug }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Save User Roles
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- TAB 2: Role -> Permissions --}}
                <div class="tab-pane fade" id="tab-role-perms" role="tabpanel">
                    <form method="POST" action="{{ route('admin.role_permissions.assign') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Select Role</label>
                                <select name="role_id" id="role_select" class="form-select" required>
                                    <option value="">-- Select role --</option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}" data-perms='@json($r->permissions->pluck('id'))'>
                                            {{ $r->name }} ({{ $r->slug }})
                                        </option>
                                    @endforeach
                                </select>

                                <div class="mt-3">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_check_all">
                                        Check All
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_uncheck_all">
                                        Uncheck All
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Permissions</label>

                                <div class="row">
                                    @foreach ($permissionsByModule as $module => $perms)
                                        <div class="col-12 mb-3">
                                            <div class="border rounded p-2">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <strong>{{ $module }}</strong>
                                                </div>

                                                <div class="row">
                                                    @foreach ($perms as $p)
                                                        <div class="col-md-6">
                                                            <label class="form-check">
                                                                <input class="form-check-input perm-checkbox"
                                                                    type="checkbox" name="permission_ids[]"
                                                                    value="{{ $p->id }}">
                                                                <span class="form-check-label">
                                                                    {{ $p->name }}
                                                                    <small class="text-muted">({{ $p->slug }})</small>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Save Role Permissions
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // ---- User -> Roles auto-fill
            const userSelect = document.getElementById('user_select');
            const userRolesSelect = document.getElementById('user_roles_select');

            userSelect?.addEventListener('change', function() {
                const selected = userSelect.options[userSelect.selectedIndex];
                const roleIds = JSON.parse(selected.getAttribute('data-roles') || '[]');

                // clear all
                Array.from(userRolesSelect.options).forEach(opt => opt.selected = false);

                // select assigned
                Array.from(userRolesSelect.options).forEach(opt => {
                    if (roleIds.includes(parseInt(opt.value))) opt.selected = true;
                });
            });

            // ---- Role -> Permissions auto-check
            const roleSelect = document.getElementById('role_select');
            const permCheckboxes = document.querySelectorAll('.perm-checkbox');

            roleSelect?.addEventListener('change', function() {
                const selected = roleSelect.options[roleSelect.selectedIndex];
                const permIds = JSON.parse(selected.getAttribute('data-perms') || '[]');

                permCheckboxes.forEach(cb => {
                    cb.checked = permIds.includes(parseInt(cb.value));
                });
            });

            // check all / uncheck all
            document.getElementById('btn_check_all')?.addEventListener('click', function() {
                permCheckboxes.forEach(cb => cb.checked = true);
            });

            document.getElementById('btn_uncheck_all')?.addEventListener('click', function() {
                permCheckboxes.forEach(cb => cb.checked = false);
            });
        });
    </script>


@endsection
