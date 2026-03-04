<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasPolicyRules;
use App\Support\PolicyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeavePolicy extends Model
{
    use HasFactory, HasPolicyRules, BelongsToTenant, SoftDeletes;

    public const POLICY_TYPE = PolicyType::LEAVE;
    public const LEAVE_TYPES = ['annual', 'sick', 'casual', 'unpaid'];
    public const ACCRUAL_FREQUENCIES = ['monthly', 'quarterly', 'yearly'];

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'is_active',
        'effective_from',
        'effective_to',
        'annual_limit',
        'sick_limit',
        'casual_limit',
        'unpaid_limit',
        'carry_forward_limit',
        'accrual_frequency',
        'rules',
        'exceptions',
        'metadata',
        'created_by',
        'updated_by',
    ];
}
