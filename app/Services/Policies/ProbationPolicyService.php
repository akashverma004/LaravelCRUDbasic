<?php

namespace App\Services\Policies;

use App\Models\ProbationPolicy;
use App\Repositories\EloquentPolicyRepository;
use App\Support\PolicyType;

class ProbationPolicyService extends BasePolicyService
{
    public function __construct(PolicyRuleEvaluator $ruleEvaluator)
    {
        parent::__construct(new EloquentPolicyRepository(ProbationPolicy::class), $ruleEvaluator);
    }

    protected function getPolicyType(): string
    {
        return PolicyType::PROBATION;
    }
}
