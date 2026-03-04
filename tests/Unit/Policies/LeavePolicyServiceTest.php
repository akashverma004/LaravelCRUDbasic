<?php

namespace Tests\Unit\Policies;

use App\Models\LeavePolicy;
use App\Services\Policies\LeavePolicyService;
use App\Services\Policies\PolicyRuleEvaluator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeavePolicyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_leave_limit_falls_back_to_defaults_when_no_active_policy_exists(): void
    {
        $service = new LeavePolicyService(new PolicyRuleEvaluator());

        $this->assertSame(12, $service->getLeaveLimit('annual'));
        $this->assertSame(8, $service->getLeaveLimit('sick'));
        $this->assertSame(6, $service->getLeaveLimit('casual'));
        $this->assertSame(0, $service->getLeaveLimit('unpaid'));
    }

    public function test_get_leave_limit_reads_from_active_policy(): void
    {
        LeavePolicy::query()->create([
            'name' => 'Test Leave Policy',
            'code' => 'LEAVE_TEST',
            'annual_limit' => 20,
            'sick_limit' => 12,
            'casual_limit' => 7,
            'unpaid_limit' => 0,
            'carry_forward_limit' => 5,
            'accrual_frequency' => 'monthly',
            'is_active' => true,
            'effective_from' => now()->subDay()->toDateString(),
            'rules' => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'employee.type', 'operator' => 'eq', 'value' => 'full-time'],
                ],
            ],
        ]);

        $service = new LeavePolicyService(new PolicyRuleEvaluator());

        $this->assertSame(20, $service->getLeaveLimit('annual'));
        $this->assertSame(12, $service->getLeaveLimit('sick'));
    }

    public function test_evaluate_active_policy_returns_no_policy_reason_when_missing(): void
    {
        $service = new LeavePolicyService(new PolicyRuleEvaluator());

        $result = $service->evaluateActivePolicy(['employee' => ['type' => 'full-time']]);

        $this->assertNull($result['policy_id']);
        $this->assertFalse($result['passed']);
        $this->assertSame('No active policy found', $result['failed'][0]['reason']);
    }

    public function test_evaluate_policy_applies_rule_engine(): void
    {
        $policy = LeavePolicy::query()->create([
            'name' => 'Rule Leave Policy',
            'code' => 'LEAVE_RULE',
            'annual_limit' => 20,
            'sick_limit' => 10,
            'casual_limit' => 5,
            'unpaid_limit' => 0,
            'carry_forward_limit' => 5,
            'accrual_frequency' => 'monthly',
            'is_active' => true,
            'rules' => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'employee.department', 'operator' => 'eq', 'value' => 'engineering'],
                ],
            ],
        ]);

        $service = new LeavePolicyService(new PolicyRuleEvaluator());

        $passed = $service->evaluatePolicy($policy->id, ['employee' => ['department' => 'engineering']]);
        $failed = $service->evaluatePolicy($policy->id, ['employee' => ['department' => 'finance']]);

        $this->assertTrue($passed['passed']);
        $this->assertFalse($failed['passed']);
    }
}
