<?php

namespace App\Models;

use App\Models\Concerns\HasPolicyRules;
use App\Support\PolicyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProbationPolicy extends Model
{
    use HasFactory, HasPolicyRules, SoftDeletes;

    public const POLICY_TYPE = PolicyType::PROBATION;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'is_active',
        'effective_from',
        'effective_to',
        'probation_days',
        'extension_allowed',
        'max_extension_days',
        'rules',
        'exceptions',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'probation_days' => 'integer',
        'extension_allowed' => 'boolean',
        'max_extension_days' => 'integer',
    ];
}
