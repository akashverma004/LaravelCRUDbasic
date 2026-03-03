<?php

namespace App\Services\Policies;

use Illuminate\Support\Arr;

class PolicyRuleEvaluator
{
    public function evaluate(array $ruleSet, array $context): array
    {
        if ($ruleSet === []) {
            return [
                'passed' => true,
                'mode' => 'all',
                'matched' => [],
                'failed' => [],
            ];
        }

        $mode = strtolower((string) ($ruleSet['mode'] ?? 'all')) === 'any' ? 'any' : 'all';
        $conditions = $ruleSet['conditions'] ?? [];

        $matched = [];
        $failed = [];

        foreach ($conditions as $condition) {
            $passed = $this->evaluateCondition($condition, $context);
            if ($passed) {
                $matched[] = $condition;
            } else {
                $failed[] = $condition;
            }
        }

        $final = $mode === 'all'
            ? count($failed) === 0
            : count($matched) > 0;

        return [
            'passed' => $final,
            'mode' => $mode,
            'matched' => $matched,
            'failed' => $failed,
        ];
    }

    private function evaluateCondition(array $condition, array $context): bool
    {
        if (isset($condition['group']) && is_array($condition['group'])) {
            return $this->evaluate($condition['group'], $context)['passed'];
        }

        $field = (string) ($condition['field'] ?? '');
        $operator = strtolower((string) ($condition['operator'] ?? 'eq'));
        $expected = $condition['value'] ?? null;
        $actual = Arr::get($context, $field);

        return match ($operator) {
            'eq' => $actual == $expected,
            'neq' => $actual != $expected,
            'gt' => is_numeric($actual) && is_numeric($expected) && $actual > $expected,
            'gte' => is_numeric($actual) && is_numeric($expected) && $actual >= $expected,
            'lt' => is_numeric($actual) && is_numeric($expected) && $actual < $expected,
            'lte' => is_numeric($actual) && is_numeric($expected) && $actual <= $expected,
            'in' => is_array($expected) && in_array($actual, $expected, true),
            'not_in' => is_array($expected) && ! in_array($actual, $expected, true),
            'contains' => is_array($actual)
                ? in_array($expected, $actual, true)
                : (is_string($actual) && is_string($expected) && str_contains($actual, $expected)),
            'exists' => Arr::has($context, $field),
            default => false,
        };
    }
}
