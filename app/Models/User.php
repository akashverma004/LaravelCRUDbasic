<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Employee;
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
        'google_id',
        'microsoft_id',
        'avatar',
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

    private ?\Illuminate\Support\Collection $cachedRoles = null;

    public function getRolesAttribute()
    {
        if ($this->cachedRoles === null) {
            $this->cachedRoles = $this->roles()->get();
        }
        return $this->cachedRoles;
    }

    public function hasRole(string|array $roleName): bool
    {
        if ($this->is_platform_admin) return true;

        if (is_array($roleName)) {
            return $this->roles_list->intersect($roleName)->isNotEmpty();
        }

        return $this->roles_list->contains($roleName);
    }

    public function hasAnyRole(array $roleNames): bool
    {
        if ($this->is_platform_admin) return true;
        return $this->roles_list->intersect($roleNames)->isNotEmpty();
    }

    public function getRolesListAttribute(): \Illuminate\Support\Collection
    {
        return $this->roles->pluck('name');
    }

    public function hasAllRoles(array $roleNames): bool
    {
        if ($this->is_platform_admin) return true;
        return $this->roles_list->intersect($roleNames)->count() === count($roleNames);
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

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Employee::class, 'email', 'email');
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if ($this->avatar) return \Illuminate\Support\Facades\Storage::url($this->avatar);
        
        // Use a direct query without scopes to ensure we find the employee record
        // even if the user is currently in a different tenant context (e.g. platform admin)
        $employee = Employee::withoutGlobalScopes()
            ->where('email', $this->email)
            ->first();

        if ($employee && $employee->profile_photo) {
            return \Illuminate\Support\Facades\Storage::url($employee->profile_photo);
        }

        return null;
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
