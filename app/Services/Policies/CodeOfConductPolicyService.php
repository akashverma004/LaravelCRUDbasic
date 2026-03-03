<?php

namespace App\Services\Policies;

use App\Models\CodeOfConductPolicy;
use App\Repositories\EloquentPolicyRepository;
use App\Support\PolicyType;

class CodeOfConductPolicyService extends BasePolicyService
{
    public function __construct(PolicyRuleEvaluator $ruleEvaluator)
    {
        parent::__construct(new EloquentPolicyRepository(CodeOfConductPolicy::class), $ruleEvaluator);
    }

    protected function getPolicyType(): string
    {
        return PolicyType::CODE_OF_CONDUCT;
    }
}
