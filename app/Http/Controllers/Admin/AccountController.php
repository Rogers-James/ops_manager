<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPUnit\Event\Code\Throwable;

class AccountController extends Controller
{
    public function usersPage()
    {
        $users = User::with('roles:id,name,slug')->paginate(15);
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('admin.pages.accounts.users', compact('users', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:80',
            'email'     => 'required|email|max:120|unique:users,email',
            'password'  => 'required|string|min:8',
            'role_ids'  => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => $data['password'],
                'is_active' => (bool)($data['is_active'] ?? true),
            ]);

            $user->roles()->sync($data['role_ids']);

            DB::commit();
            return back()->with('success', 'User created successfully!');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('User create failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while creating user.');
        }
    }

    public function rolesPage()
    {
        $roles = Role::withCount('users')
            ->withCount('permissions') // remove if you haven't built permissions yet
            ->paginate(15);

        return view('admin.pages.accounts.roles', compact('roles'));
    }

    public function rolesStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|regex:/^[a-z0-9_]+$/|unique:roles,slug',
        ], [
            'slug.regex' => 'Slug must be lowercase with underscores only (example: hr_manager).',
        ]);

        try {
            DB::beginTransaction();

            Role::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);

            DB::commit();
            return back()->with('success', 'Role created successfully!');
        } catch (QueryException $e) {
            DB::rollBack();

            // Duplicate key / unique violation (MySQL)
            if (($e->errorInfo[1] ?? null) == 1062) {
                return back()->withInput()->with('error', 'Role slug already exists. Please choose a different slug.');
            }

            return back()->withInput()->with('error', 'Database error occurred while saving role.');
        } catch (Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function updateRoles(Request $request, User $user)
    {
        $data = $request->validate([
            'role_ids'   => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
        ]);

        try {
            $user->roles()->sync($data['role_ids']);
            return back()->with('success', 'Roles updated successfully!');
        } catch (Throwable $e) {
            Log::error('User role update failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while updating roles.');
        }
    }

    public function destroyUser(User $user)
    {
        try {
            DB::beginTransaction();

            // detach pivot roles first
            $user->roles()->detach();

            $user->delete();

            DB::commit();
            return back()->with('success', 'User deleted successfully!');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('User delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while deleting user.');
        }
    }

    public function rolesDelete($id)
    {
        try {
            $role = Role::withCount('users')->findOrFail($id);

            if ($role->users_count > 0) {
                return back()->with('error', 'This role is assigned to users, so it cannot be deleted.');
            }

            $role->delete();
            return back()->with('success', 'Role deleted successfully!');
        } catch (QueryException $e) {
            // FK constraint (MySQL)
            if (($e->errorInfo[1] ?? null) == 1451) {
                return back()->with('error', 'Cannot delete because this role is linked to other records.');
            }
            return back()->with('error', 'Database error while deleting.');
        } catch (Throwable $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function permissionsPage()
    {
        $permissions = Permission::query()
            ->withCount('roles')
            ->orderBy('module')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.pages.accounts.permissions', compact('permissions'));
    }

    public function permissionsStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'module' => 'nullable|string|max:60',
            'slug' => 'nullable|string|max:120',
        ]);

        try {
            DB::beginTransaction();

            $baseSlug = $data['slug']
                ? Str::slug($data['slug'], '.')
                : Str::slug($data['name'], '.');

            $slug = $baseSlug;
            $n = 1;
            while (Permission::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '.' . $n;
                $n++;
            }

            Permission::create([
                'name' => $data['name'],
                'module' => $data['module'],
                'slug' => $slug,
            ]);

            DB::commit();
            return back()->with('success', 'Permission created successfully!');
        } catch (QueryException $e) {
            DB::rollBack();
            if (($e->errorInfo[1] ?? null) == 1062) {
                return back()->withInput()->with('error', 'Permission slug already exists.');
            }
            return back()->withInput()->with('error', 'Database error while saving permission.');
        } catch (Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function roleAssignmentPage()
    {
        $users = User::with('roles:id,name,slug')->orderBy('name')->get();
        $roles = Role::with('permissions:id,name,slug,module')->orderBy('name')->get();

        // group permissions by module for checkboxes
        $permissionsByModule = Permission::orderBy('module')->orderBy('name')
            ->get()
            ->groupBy(function ($p) {
                return $p->module ?: 'General';
            });

        return view('admin.pages.accounts.role-assignment', compact('users', 'roles', 'permissionsByModule'));
    }

    public function assignRolesToUser(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($data['user_id']);
            $roleIds = $data['role_ids'] ?? [];

            // sync roles (removes old, adds new)
            $user->roles()->sync($roleIds);

            DB::commit();
            return back()->with('success', 'User roles updated successfully!');
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Database error while updating user roles.');
        } catch (Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong while updating user roles.');
        }
    }

    public function assignPermissionsToRole(Request $request)
    {
        $data = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::findOrFail($data['role_id']);
            $permissionIds = $data['permission_ids'] ?? [];

            // sync permissions
            $role->permissions()->sync($permissionIds);

            DB::commit();
            return back()->with('success', 'Role permissions updated successfully!');
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Database error while updating role permissions.');
        } catch (Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong while updating role permissions.');
        }
    }
}
