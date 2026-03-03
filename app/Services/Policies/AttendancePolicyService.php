<?php

namespace App\Services\Policies;

use App\Models\AttendancePolicy;
use App\Repositories\EloquentPolicyRepository;
use App\Support\PolicyType;

class AttendancePolicyService extends BasePolicyService
{
    public function __construct(PolicyRuleEvaluator $ruleEvaluator)
    {
        parent::__construct(new EloquentPolicyRepository(AttendancePolicy::class), $ruleEvaluator);
    }

    protected function getPolicyType(): string
    {
        return PolicyType::ATTENDANCE;
    }
}
