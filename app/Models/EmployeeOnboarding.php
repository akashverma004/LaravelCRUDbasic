<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeOnboarding extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'employee_id', 'template_id', 'status', 'started_at', 'completed_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(EmployeeOnboardingTask::class, 'onboarding_id');
    }

    public function getProgressAttribute(): int
    {
        $total = $this->tasks()->count();
        if ($total == 0) return 0;
        $completed = $this->tasks()->where('is_completed', true)->count();
        return (int) round(($completed / $total) * 100);
    }
}
