<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\BelongsToTenant;
use App\Support\TenantContext;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, BelongsToTenant, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'is_platform_admin',
        'name',
        'email',
        'password',
        'require_password_change',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_platform_admin' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function roles(): BelongsToMany
    {
        $relation = $this->belongsToMany(Role::class, 'user_role')
            ->withPivot('tenant_id')
            ->withTimestamps();
        $tenantId = $this->resolveTenantId();
        if ($tenantId !== null) {
            $relation->wherePivot('tenant_id', $tenantId)->withPivotValue('tenant_id', $tenantId);
        }

        return $relation;
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
            ->through('roles');
    }

    public function hasRole(string|array $roleName): bool
    {
        if (is_array($roleName)) {
            return $this->roles()->whereIn('name', $roleName)->exists();
        }

        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    public function hasAllRoles(array $roleNames): bool
    {
        return count($roleNames) === $this->roles()->whereIn('name', $roleNames)->count();
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn($q) => $q->where('name', $permissionName))
            ->exists();
    }

    public function assignRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $tenantId = $this->resolveTenantId() ?? $role->tenant_id;
        if ($tenantId !== null && $role->tenant_id !== null && (int) $role->tenant_id !== (int) $tenantId) {
            throw new InvalidArgumentException('Cannot assign a role from another tenant.');
        }

        $this->roles()->syncWithoutDetaching([
            $role->id => ['tenant_id' => $tenantId],
        ]);
    }

    public function removeRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->detach($role->id);
    }

    public function syncRoles(array $roleIds): void
    {
        $tenantId = $this->resolveTenantId();
        $allowedRoleIds = Role::query()
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereIn('id', $roleIds)
            ->pluck('id')
            ->all();

        $payload = [];
        foreach ($allowedRoleIds as $roleId) {
            $payload[$roleId] = ['tenant_id' => $tenantId];
        }

        $this->roles()->sync($payload);
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
