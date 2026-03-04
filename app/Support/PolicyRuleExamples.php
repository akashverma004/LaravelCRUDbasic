<?php

namespace App\Support;

final class PolicyRuleExamples
{
    public static function baseTemplate(): array
    {
        return [
            'mode' => 'all',
            'conditions' => [
                [
                    'field' => 'employee.department',
                    'operator' => 'in',
                    'value' => ['engineering', 'product'],
                ],
                [
                    'field' => 'request.duration_days',
                    'operator' => 'lte',
                    'value' => 5,
                ],
            ],
            'actions' => [
                'require_approval' => true,
            ],
        ];
    }

    public static function byPolicyType(): array
    {
        return [
            PolicyType::LEAVE => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'employee.type', 'operator' => 'eq', 'value' => 'full-time'],
                    ['field' => 'request.leave_type', 'operator' => 'in', 'value' => ['annual', 'sick']],
                    ['field' => 'employee.joined_months', 'operator' => 'gte', 'value' => 3],
                ],
                'actions' => [
                    'max_days_per_request' => 10,
                    'require_manager_approval' => true,
                    'allow_carry_forward' => true,
                ],
            ],
            PolicyType::ATTENDANCE => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'attendance.clock_in', 'operator' => 'exists', 'value' => true],
                    ['field' => 'attendance.late_minutes', 'operator' => 'lte', 'value' => 15],
                ],
                'actions' => [
                    'mark_present' => true,
                    'apply_grace' => true,
                ],
            ],
            PolicyType::HOLIDAY => [
                'mode' => 'any',
                'conditions' => [
                    ['field' => 'employee.location_country', 'operator' => 'eq', 'value' => 'IN'],
                    ['field' => 'employee.location_state', 'operator' => 'eq', 'value' => 'KA'],
                ],
                'actions' => [
                    'holiday_calendar' => 'india-karnataka',
                    'optional_holidays_allowed' => 2,
                ],
            ],
            PolicyType::PAYROLL => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'payroll.pay_cycle', 'operator' => 'eq', 'value' => 'monthly'],
                ],
                'actions' => [
                    'pay_day' => 30,
                    'cutoff_day' => 25,
                    'prorate_on_join' => true,
                    'prorate_on_exit' => true,
                ],
            ],
            PolicyType::PROBATION => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'employee.role', 'operator' => 'not_in', 'value' => ['intern']],
                ],
                'actions' => [
                    'probation_days' => 90,
                    'extension_allowed' => true,
                    'max_extension_days' => 60,
                ],
            ],
            PolicyType::NOTICE_PERIOD => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'employee.confirmed', 'operator' => 'eq', 'value' => true],
                ],
                'actions' => [
                    'notice_days' => 30,
                    'buyout_allowed' => true,
                    'waiver_allowed' => false,
                ],
            ],
            PolicyType::OVERTIME => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'attendance.overtime_minutes', 'operator' => 'gte', 'value' => 30],
                ],
                'actions' => [
                    'weekday_multiplier' => 1.5,
                    'weekend_multiplier' => 2.0,
                    'holiday_multiplier' => 2.5,
                ],
            ],
            PolicyType::WFH => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'employee.department', 'operator' => 'in', 'value' => ['engineering', 'design']],
                    ['field' => 'request.days', 'operator' => 'lte', 'value' => 3],
                ],
                'actions' => [
                    'approval_required' => true,
                    'monthly_limit_days' => 8,
                ],
            ],
            PolicyType::REIMBURSEMENT => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'claim.category', 'operator' => 'in', 'value' => ['travel', 'meal', 'internet']],
                    ['field' => 'claim.amount', 'operator' => 'lte', 'value' => 10000],
                ],
                'actions' => [
                    'receipt_required' => true,
                    'approval_matrix' => ['manager', 'finance'],
                ],
            ],
            PolicyType::CODE_OF_CONDUCT => [
                'mode' => 'all',
                'conditions' => [
                    ['field' => 'employee.status', 'operator' => 'eq', 'value' => 'active'],
                ],
                'actions' => [
                    'acknowledgement_required' => true,
                    'breach_actions' => ['warning', 'suspension', 'termination'],
                ],
            ],
        ];
    }

    private function __construct()
    {
    }
}
