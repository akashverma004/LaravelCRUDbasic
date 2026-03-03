<?php

namespace App\Services\Policies;

use App\Models\WfhPolicy;
use App\Repositories\EloquentPolicyRepository;
use App\Support\PolicyType;

class WfhPolicyService extends BasePolicyService
{
    public function __construct(PolicyRuleEvaluator $ruleEvaluator)
    {
        parent::__construct(new EloquentPolicyRepository(WfhPolicy::class), $ruleEvaluator);
    }

    protected function getPolicyType(): string
    {
        return PolicyType::WFH;
    }
}
