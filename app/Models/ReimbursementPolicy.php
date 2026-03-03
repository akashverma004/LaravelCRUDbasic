<?php

namespace App\Models;

use App\Models\Concerns\HasPolicyRules;
use App\Support\PolicyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReimbursementPolicy extends Model
{
    use HasFactory, HasPolicyRules, SoftDeletes;

    public const POLICY_TYPE = PolicyType::REIMBURSEMENT;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'is_active',
        'effective_from',
        'effective_to',
        'monthly_claim_limit',
        'single_claim_limit',
        'receipt_required',
        'allowed_categories',
        'approval_matrix',
        'rules',
        'exceptions',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'monthly_claim_limit' => 'decimal:2',
        'single_claim_limit' => 'decimal:2',
        'receipt_required' => 'boolean',
        'allowed_categories' => 'array',
        'approval_matrix' => 'array',
    ];
}
