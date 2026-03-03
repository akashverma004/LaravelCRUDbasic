<?php

namespace App\Services\Policies;

use App\Models\NoticePeriodPolicy;
use App\Repositories\EloquentPolicyRepository;
use App\Support\PolicyType;

class NoticePeriodPolicyService extends BasePolicyService
{
    public function __construct(PolicyRuleEvaluator $ruleEvaluator)
    {
        parent::__construct(new EloquentPolicyRepository(NoticePeriodPolicy::class), $ruleEvaluator);
    }

    protected function getPolicyType(): string
    {
        return PolicyType::NOTICE_PERIOD;
    }
}
