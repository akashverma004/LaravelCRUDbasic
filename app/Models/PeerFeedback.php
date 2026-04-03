<?php

namespace App\Models;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeerFeedback extends Model
{
    protected $guarded = [];

    protected $casts = [
        'completed_at' => 'datetime',
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

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requester_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }
}
