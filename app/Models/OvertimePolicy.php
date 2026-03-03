<?php

namespace App\Models;

use App\Models\Concerns\HasPolicyRules;
use App\Support\PolicyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimePolicy extends Model
{
    use HasFactory, HasPolicyRules, SoftDeletes;

    public const POLICY_TYPE = PolicyType::OVERTIME;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'is_active',
        'effective_from',
        'effective_to',
        'minimum_minutes',
        'weekday_multiplier',
        'weekend_multiplier',
        'holiday_multiplier',
        'max_hours_per_month',
        'rules',
        'exceptions',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'minimum_minutes' => 'integer',
        'weekday_multiplier' => 'decimal:2',
        'weekend_multiplier' => 'decimal:2',
        'holiday_multiplier' => 'decimal:2',
        'max_hours_per_month' => 'decimal:2',
    ];
}
