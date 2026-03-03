<?php

namespace App\Services\Policies;

use App\Models\PayrollPolicy;
use App\Repositories\EloquentPolicyRepository;
use App\Support\PolicyType;

class PayrollPolicyService extends BasePolicyService
{
    public function __construct(PolicyRuleEvaluator $ruleEvaluator)
    {
        parent::__construct(new EloquentPolicyRepository(PayrollPolicy::class), $ruleEvaluator);
    }

    protected function getPolicyType(): string
    {
        return PolicyType::PAYROLL;
    }
}
