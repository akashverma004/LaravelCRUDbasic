<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasPolicyRules;
use App\Support\PolicyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendancePolicy extends Model
{
    use HasFactory, HasPolicyRules, BelongsToTenant, SoftDeletes;

    public const POLICY_TYPE = PolicyType::ATTENDANCE;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'is_active',
        'effective_from',
        'effective_to',
        'standard_hours_per_day',
        'grace_minutes',
        'max_late_marks_per_month',
        'work_days',
        'rules',
        'exceptions',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'standard_hours_per_day' => 'decimal:2',
        'grace_minutes' => 'integer',
        'max_late_marks_per_month' => 'integer',
        'work_days' => 'array',
    ];
}
