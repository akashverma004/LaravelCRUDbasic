<?php

namespace App\Services\Policies;

use App\Models\ReimbursementPolicy;
use App\Repositories\EloquentPolicyRepository;
use App\Support\PolicyType;

class ReimbursementPolicyService extends BasePolicyService
{
    public function __construct(PolicyRuleEvaluator $ruleEvaluator)
    {
        parent::__construct(new EloquentPolicyRepository(ReimbursementPolicy::class), $ruleEvaluator);
    }

    protected function getPolicyType(): string
    {
        return PolicyType::REIMBURSEMENT;
    }
}
