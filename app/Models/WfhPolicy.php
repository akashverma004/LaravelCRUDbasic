<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasPolicyRules;
use App\Support\PolicyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WfhPolicy extends Model
{
    use HasFactory, HasPolicyRules, BelongsToTenant, SoftDeletes;

    public const POLICY_TYPE = PolicyType::WFH;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'is_active',
        'effective_from',
        'effective_to',
        'monthly_limit_days',
        'approval_required',
        'max_consecutive_days',
        'allowed_departments',
        'allowed_roles',
        'rules',
        'exceptions',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'monthly_limit_days' => 'integer',
        'approval_required' => 'boolean',
        'max_consecutive_days' => 'integer',
        'allowed_departments' => 'array',
        'allowed_roles' => 'array',
    ];
}
