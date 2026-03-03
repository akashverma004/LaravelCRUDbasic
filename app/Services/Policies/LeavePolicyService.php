<?php

namespace App\Services\Policies;

use App\Models\LeavePolicy;
use App\Repositories\EloquentPolicyRepository;
use App\Support\PolicyType;

class LeavePolicyService extends BasePolicyService
{
    public function __construct(PolicyRuleEvaluator $ruleEvaluator)
    {
        parent::__construct(new EloquentPolicyRepository(LeavePolicy::class), $ruleEvaluator);
    }

    protected function getPolicyType(): string
    {
        return PolicyType::LEAVE;
    }

    public function getLeaveLimit(string $leaveType, ?int $tenantId = null, ?string $effectiveOn = null): int
    {
        $policy = $this->getActivePolicy($tenantId, $effectiveOn);
        if (! $policy) {
            return match ($leaveType) {
                'annual' => 12,
                'sick' => 8,
                'casual' => 6,
                'unpaid' => 0,
                default => 0,
            };
        }

        return (int) match ($leaveType) {
            'annual' => $policy->annual_limit,
            'sick' => $policy->sick_limit,
            'casual' => $policy->casual_limit,
            'unpaid' => $policy->unpaid_limit,
            default => 0,
        };
    }
}
