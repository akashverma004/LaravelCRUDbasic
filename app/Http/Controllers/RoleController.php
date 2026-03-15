<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Services\AuthorizationService;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        $permissionsByModule = $this->authorizationService->getAllPermissions()->groupBy('module');
        return view('hrms.roles.index', compact('roles', 'permissionsByModule'));
    }

    public function create(): RedirectResponse
    {
        $this->authorize('manage-roles');
        return redirect()->route('roles.index');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
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
        $role = $this->authorizationService->createRole([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'permissions' => $permissionIds,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'role' => $this->transformRole($role->load('permissions')),
            ]);
        }

        return redirect()->route('roles.index')->with('status', 'Role created successfully.');
    }

    public function edit(int $role): RedirectResponse
    {
        $this->authorize('manage-roles');
        return redirect()->route('roles.index');
    }

    public function update(Request $request, int $role): RedirectResponse|JsonResponse
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
        $role = $this->authorizationService->updateRole($role, [
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'permissions' => $permissionIds,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully.',
                'role' => $this->transformRole($role->load('permissions')),
            ]);
        }

        return redirect()->route('roles.index')->with('status', 'Role updated successfully.');
    }

    public function destroy(Request $request, int $role): RedirectResponse|JsonResponse
    {
        $this->authorize('manage-roles');
        $role = Role::query()->findOrFail($role);

        if ($role->users()->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Cannot delete role with assigned users.'], 422);
            }

            return redirect()->back()->with('error', 'Cannot delete role with assigned users.');
        }

        $this->authorizationService->deleteRole($role);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.',
            ]);
        }

        return redirect()->route('roles.index')->with('status', 'Role deleted successfully.');
    }

    public function users(int $role): View
    {
        $this->authorize('manage-roles');
        $role = Role::query()->findOrFail($role);
        $users = $role->users()->get();
        return view('hrms.roles.users', compact('role', 'users'));
    }

    private function transformRole(Role $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name,
            'description' => $role->description,
            'permissions' => $role->permissions->map(fn (Permission $permission) => [
                'id' => $permission->id,
                'display_name' => $permission->display_name,
                'module' => $permission->module,
            ])->values(),
            'users_count' => $role->users()->count(),
            'users_url' => route('roles.users', $role),
            'update_url' => route('roles.update', $role),
            'delete_url' => route('roles.destroy', $role),
        ];
    }
}
