<?php

namespace App\Services\Policies;

use App\Models\OvertimePolicy;
use App\Repositories\EloquentPolicyRepository;
use App\Support\PolicyType;

class OvertimePolicyService extends BasePolicyService
{
    public function __construct(PolicyRuleEvaluator $ruleEvaluator)
    {
        parent::__construct(new EloquentPolicyRepository(OvertimePolicy::class), $ruleEvaluator);
    }

    protected function getPolicyType(): string
    {
        return PolicyType::OVERTIME;
    }
}
