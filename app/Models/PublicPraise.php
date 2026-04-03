<?php

namespace App\Models;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicPraise extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (TenantContext::id() !== null) {
                $builder->where($builder->getQuery()->from . '.tenant_id', TenantContext::id());
            }
        });

        static::creating(function ($model) {
            if (empty($model->tenant_id) && TenantContext::id() !== null) {
                $model->tenant_id = TenantContext::id();
            }
        });
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'receiver_id');
    }
}
