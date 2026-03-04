<?php

namespace Tests\Unit\Policies;

use App\Services\Policies\PolicyRuleEvaluator;
use Tests\TestCase;

class PolicyRuleEvaluatorTest extends TestCase
{
    public function test_it_passes_when_rule_set_is_empty(): void
    {
        $evaluator = new PolicyRuleEvaluator();

        $result = $evaluator->evaluate([], []);

        $this->assertTrue($result['passed']);
        $this->assertSame('all', $result['mode']);
        $this->assertSame([], $result['matched']);
        $this->assertSame([], $result['failed']);
    }

    public function test_it_evaluates_all_mode_conditions(): void
    {
        $evaluator = new PolicyRuleEvaluator();

        $rules = [
            'mode' => 'all',
            'conditions' => [
                ['field' => 'employee.department', 'operator' => 'eq', 'value' => 'engineering'],
                ['field' => 'request.duration_days', 'operator' => 'lte', 'value' => 5],
            ],
        ];

        $context = [
            'employee' => ['department' => 'engineering'],
            'request' => ['duration_days' => 3],
        ];

        $result = $evaluator->evaluate($rules, $context);

        $this->assertTrue($result['passed']);
        $this->assertCount(2, $result['matched']);
        $this->assertCount(0, $result['failed']);
    }

    public function test_it_evaluates_any_mode_conditions(): void
    {
        $evaluator = new PolicyRuleEvaluator();

        $rules = [
            'mode' => 'any',
            'conditions' => [
                ['field' => 'employee.role', 'operator' => 'eq', 'value' => 'intern'],
                ['field' => 'employee.location', 'operator' => 'eq', 'value' => 'IN'],
            ],
        ];

        $context = [
            'employee' => ['role' => 'employee', 'location' => 'IN'],
        ];

        $result = $evaluator->evaluate($rules, $context);

        $this->assertTrue($result['passed']);
        $this->assertCount(1, $result['matched']);
        $this->assertCount(1, $result['failed']);
    }

    public function test_it_supports_nested_group_conditions(): void
    {
        $evaluator = new PolicyRuleEvaluator();

        $rules = [
            'mode' => 'all',
            'conditions' => [
                [
                    'group' => [
                        'mode' => 'all',
                        'conditions' => [
                            ['field' => 'employee.type', 'operator' => 'eq', 'value' => 'full-time'],
                            ['field' => 'employee.joined_months', 'operator' => 'gte', 'value' => 3],
                        ],
                    ],
                ],
            ],
        ];

        $context = [
            'employee' => ['type' => 'full-time', 'joined_months' => 8],
        ];

        $result = $evaluator->evaluate($rules, $context);

        $this->assertTrue($result['passed']);
        $this->assertCount(1, $result['matched']);
    }
}
