<?php

namespace App\Services\Policies;

use App\Models\HolidayPolicy;
use App\Repositories\EloquentPolicyRepository;
use App\Support\PolicyType;

class HolidayPolicyService extends BasePolicyService
{
    public function __construct(PolicyRuleEvaluator $ruleEvaluator)
    {
        parent::__construct(new EloquentPolicyRepository(HolidayPolicy::class), $ruleEvaluator);
    }

    protected function getPolicyType(): string
    {
        return PolicyType::HOLIDAY;
    }
}
