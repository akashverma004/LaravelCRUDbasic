<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(private AuthorizationService $authorizationService) {}

    public function index(): View
    {
        $this->authorize('manage-roles');
        $roles = $this->authorizationService->getAllRoles();
        return view('hrms.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorize('manage-roles');
        $permissions = $this->authorizationService->getAllPermissions();
        $permissionsByModule = $permissions->groupBy('module');
        return view('hrms.roles.create', compact('permissionsByModule'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-roles');

        $validated = $request->validate([
            'name' => 'required|unique:roles,name|alpha_dash',
            'display_name' => 'required|string',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $permissionIds = $request->get('permissions', []);
        $this->authorizationService->createRole([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'permissions' => $permissionIds,
        ]);

        return redirect()->route('roles.index')->with('status', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        $this->authorize('manage-roles');
        $permissions = $this->authorizationService->getAllPermissions();
        $permissionsByModule = $permissions->groupBy('module');
        $rolePermissions = $role->permissions()->pluck('id')->toArray();
        return view('hrms.roles.edit', compact('role', 'permissionsByModule', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('manage-roles');

        $validated = $request->validate([
            'name' => 'required|alpha_dash|unique:roles,name,' . $role->id,
            'display_name' => 'required|string',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $permissionIds = $request->get('permissions', []);
        $this->authorizationService->updateRole($role, [
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'permissions' => $permissionIds,
        ]);

        return redirect()->route('roles.index')->with('status', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $this->authorize('manage-roles');

        if ($role->users()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete role with assigned users.');
        }

        $this->authorizationService->deleteRole($role);
        return redirect()->route('roles.index')->with('status', 'Role deleted successfully.');
    }

    public function users(Role $role): View
    {
        $this->authorize('manage-roles');
        $users = $role->users()->get();
        return view('hrms.roles.users', compact('role', 'users'));
    }
}
