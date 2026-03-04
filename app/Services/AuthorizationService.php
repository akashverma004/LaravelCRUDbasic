<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Support\TenantContext;

class AuthorizationService
{
    public function hasRole(User $user, string|array $roleName): bool
    {
        return $user->hasRole($roleName);
    }

    public function hasAnyRole(User $user, array $roleNames): bool
    {
        return $user->hasAnyRole($roleNames);
    }

    public function hasAllRoles(User $user, array $roleNames): bool
    {
        return $user->hasAllRoles($roleNames);
    }

    public function hasPermission(User $user, string $permissionName): bool
    {
        return $user->hasPermission($permissionName);
    }

    public function assignRole(User $user, string $roleName): void
    {
        $user->assignRole($roleName);
    }

    public function removeRole(User $user, string $roleName): void
    {
        $user->removeRole($roleName);
    }

    public function syncRoles(User $user, array $roleNames): void
    {
        $tenantId = TenantContext::id() ?? $user->tenant_id;
        $roleIds = Role::whereIn('name', $roleNames)
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->pluck('id')
            ->toArray();
        $user->syncRoles($roleIds);
    }

    public function getRolesByUser(User $user)
    {
        return $user->roles()->get();
    }

    public function getPermissionsByUser(User $user)
    {
        return $user->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }

    public function getAllRoles()
    {
        return Role::with('permissions')->get();
    }

    public function getAllPermissions()
    {
        return Permission::orderBy('module')->orderBy('display_name')->get();
    }

    public function getPermissionsByModule(string $module)
    {
        return Permission::where('module', $module)->get();
    }

    public function createRole(array $data)
    {
        $role = Role::create([
            'name' => $data['name'],
            'display_name' => $data['display_name'],
            'description' => $data['description'] ?? null,
        ]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    public function updateRole(Role $role, array $data)
    {
        $role->update([
            'name' => $data['name'] ?? $role->name,
            'display_name' => $data['display_name'] ?? $role->display_name,
            'description' => $data['description'] ?? $role->description,
        ]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    public function deleteRole(Role $role)
    {
        return $role->delete();
    }
}
