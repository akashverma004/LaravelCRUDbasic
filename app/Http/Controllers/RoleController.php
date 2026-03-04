<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Services\AuthorizationService;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $tenantId = TenantContext::id();

        $validated = $request->validate([
            'name' => [
                'required',
                'alpha_dash',
                Rule::unique('roles', 'name')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
            'display_name' => 'required|string',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => [
                'integer',
                Rule::exists('permissions', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
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

    public function edit(int $role): View
    {
        $this->authorize('manage-roles');
        $role = Role::query()->findOrFail($role);
        $permissions = $this->authorizationService->getAllPermissions();
        $permissionsByModule = $permissions->groupBy('module');
        $rolePermissions = $role->permissions()->pluck('id')->toArray();
        return view('hrms.roles.edit', compact('role', 'permissionsByModule', 'rolePermissions'));
    }

    public function update(Request $request, int $role)
    {
        $this->authorize('manage-roles');
        $role = Role::query()->findOrFail($role);
        $tenantId = TenantContext::id() ?? $role->tenant_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'alpha_dash',
                Rule::unique('roles', 'name')
                    ->ignore($role->id)
                    ->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
            'display_name' => 'required|string',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => [
                'integer',
                Rule::exists('permissions', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
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

    public function destroy(int $role)
    {
        $this->authorize('manage-roles');
        $role = Role::query()->findOrFail($role);

        if ($role->users()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete role with assigned users.');
        }

        $this->authorizationService->deleteRole($role);
        return redirect()->route('roles.index')->with('status', 'Role deleted successfully.');
    }

    public function users(int $role): View
    {
        $this->authorize('manage-roles');
        $role = Role::query()->findOrFail($role);
        $users = $role->users()->get();
        return view('hrms.roles.users', compact('role', 'users'));
    }
}
