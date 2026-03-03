<?php

namespace App\Models\Concerns;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasPolicyRules
{
    protected function initializeHasPolicyRules(): void
    {
        $this->casts = array_merge([
            'is_active' => 'boolean',
            'effective_from' => 'date',
            'effective_to' => 'date',
            'rules' => 'array',
            'exceptions' => 'array',
            'metadata' => 'array',
        ], $this->casts ?? []);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeEffectiveOn(Builder $query, Carbon|string|null $date = null): Builder
    {
        $effectiveDate = $date instanceof Carbon ? $date->toDateString() : ($date ?: now()->toDateString());

        return $query
            ->where(function (Builder $q) use ($effectiveDate) {
                $q->whereNull('effective_from')
                    ->orWhereDate('effective_from', '<=', $effectiveDate);
            })
            ->where(function (Builder $q) use ($effectiveDate) {
                $q->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $effectiveDate);
            });
    }

    public function getIsCurrentlyEffectiveAttribute(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $today = now()->toDateString();
        $fromOk = ! $this->effective_from || $this->effective_from->toDateString() <= $today;
        $toOk = ! $this->effective_to || $this->effective_to->toDateString() >= $today;

        return $fromOk && $toOk;
    }

    public function getRulesSummaryAttribute(): string
    {
        $rules = $this->rules ?? [];
        $count = is_array($rules) ? count($rules) : 0;

        return $count . ' rule group' . ($count === 1 ? '' : 's');
    }

    public function setCodeAttribute(?string $value): void
    {
        $this->attributes['code'] = $value
            ? strtoupper(str_replace(' ', '_', trim($value)))
            : null;
    }
}
