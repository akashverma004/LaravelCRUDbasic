<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\TenantContext;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'display_name',
        'description',
    ];

    public function permissions(): BelongsToMany
    {
        $relation = $this->belongsToMany(Permission::class, 'role_permission')
            ->withPivot('tenant_id')
            ->withTimestamps();
        $tenantId = $this->resolveTenantId();
        if ($tenantId !== null) {
            $relation->wherePivot('tenant_id', $tenantId)->withPivotValue('tenant_id', $tenantId);
        }

        return $relation;
    }

    public function users(): BelongsToMany
    {
        $relation = $this->belongsToMany(User::class, 'user_role')
            ->withPivot('tenant_id')
            ->withTimestamps();
        $tenantId = $this->resolveTenantId();
        if ($tenantId !== null) {
            $relation->wherePivot('tenant_id', $tenantId)->withPivotValue('tenant_id', $tenantId);
        }

        return $relation;
    }

    public function givePermission(Permission $permission): void
    {
        $tenantId = $this->resolveTenantId() ?? $permission->tenant_id;
        if ($tenantId !== null && $permission->tenant_id !== null && (int) $permission->tenant_id !== (int) $tenantId) {
            throw new InvalidArgumentException('Cannot assign a permission from another tenant.');
        }

        $this->permissions()->syncWithoutDetaching([
            $permission->id => ['tenant_id' => $tenantId],
        ]);
    }

    public function revokePermission(Permission $permission): void
    {
        $this->permissions()->detach($permission->id);
    }

    public function syncPermissions(array $permissionIds): void
    {
        $tenantId = $this->resolveTenantId();
        $allowedPermissionIds = Permission::query()
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereIn('id', $permissionIds)
            ->pluck('id')
            ->all();

        $payload = [];
        foreach ($allowedPermissionIds as $permissionId) {
            $payload[$permissionId] = ['tenant_id' => $tenantId];
        }

        $this->permissions()->sync($payload);
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()
            ->where('name', $permissionName)
            ->exists();
    }

    private function resolveTenantId(): ?int
    {
        $tenantId = TenantContext::id();
        if ($tenantId !== null) {
            return $tenantId;
        }

        if (isset($this->tenant_id) && $this->tenant_id !== null) {
            return (int) $this->tenant_id;
        }

        return 1;
    }
}
