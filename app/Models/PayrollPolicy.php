<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasPolicyRules;
use App\Support\PolicyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollPolicy extends Model
{
    use HasFactory, HasPolicyRules, BelongsToTenant, SoftDeletes;

    public const POLICY_TYPE = PolicyType::PAYROLL;
    public const PAY_CYCLES = ['weekly', 'biweekly', 'monthly'];

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'is_active',
        'effective_from',
        'effective_to',
        'pay_cycle',
        'pay_day',
        'cutoff_day',
        'prorate_on_join',
        'prorate_on_exit',
        'rules',
        'exceptions',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'pay_day' => 'integer',
        'cutoff_day' => 'integer',
        'prorate_on_join' => 'boolean',
        'prorate_on_exit' => 'boolean',
    ];
}
