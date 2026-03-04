<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'display_name',
        'description',
        'module',
    ];

    public function roles(): BelongsToMany
    {
        $relation = $this->belongsToMany(Role::class, 'role_permission');
        $tenantId = TenantContext::id();
        if ($tenantId !== null) {
            $relation->wherePivot('tenant_id', $tenantId);
        }

        return $relation;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permission');
    }
}
