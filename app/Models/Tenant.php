<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'email',
        'phone',
        'address',
        'country',
        'timezone',
        'currency',
        'is_active',
        'setup_completed',
        'setup_completed_at',
        'owner_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'setup_completed' => 'boolean',
        'setup_completed_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(TenantInvitation::class);
    }
}
