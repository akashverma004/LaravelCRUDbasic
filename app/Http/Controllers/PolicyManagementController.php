<?php

namespace App\Http\Controllers;

use App\Models\AttendancePolicy;
use App\Models\CodeOfConductPolicy;
use App\Models\HolidayPolicy;
use App\Models\LeavePolicy;
use App\Models\NoticePeriodPolicy;
use App\Models\OvertimePolicy;
use App\Models\PayrollPolicy;
use App\Models\ProbationPolicy;
use App\Models\ReimbursementPolicy;
use App\Models\WfhPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PolicyManagementController extends Controller
{
    public function index(): View
    {
        $types = collect($this->definitions())
            ->map(fn (array $config, string $type) => [
                'type' => $type,
                'title' => $config['title'],
                'description' => $config['description'],
                'route' => route('policies.edit', $type),
            ])
            ->values();

        return view('hrms.policies.index', compact('types'));
    }

    public function edit(string $type): View
    {
        $definition = $this->resolveDefinition($type);
        $modelClass = $definition['model'];

        $policy = $modelClass::query()->first();
        if (! $policy) {
            $policy = $modelClass::query()->create([
                'name' => $definition['title'] . ' (Default)',
                'code' => strtoupper($type) . '_DEFAULT',
                'description' => $definition['description'],
                'is_active' => true,
            ]);
        }

        return view('hrms.policies.edit', [
            'type' => $type,
            'definition' => $definition,
            'policy' => $policy,
        ]);
    }

    public function update(Request $request, string $type): RedirectResponse
    {
        $definition = $this->resolveDefinition($type);
        $modelClass = $definition['model'];
        $rules = $this->buildValidationRules($definition['fields']);
        $validated = $request->validate($rules);

        $payload = $this->normalizePayload($validated, $definition['fields']);

        $policy = $modelClass::query()->first();
        if (! $policy) {
            $payload['name'] = $payload['name'] ?? ($definition['title'] . ' (Default)');
            $payload['code'] = $payload['code'] ?? strtoupper($type) . '_DEFAULT';
            $policy = $modelClass::query()->create($payload);
        } else {
            $policy->update($payload);
        }

        return redirect()
            ->route('policies.edit', $type)
            ->with('status', $definition['title'] . ' updated successfully.');
    }

    private function resolveDefinition(string $type): array
    {
        $definitions = $this->definitions();
        abort_unless(isset($definitions[$type]), 404);

        return $definitions[$type];
    }

    private function buildValidationRules(array $fields): array
    {
        $rules = [];
        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'];
            $required = $field['required'] ?? false;

            $base = $required ? ['required'] : ['nullable'];
            $rules[$name] = match ($type) {
                'text' => array_merge($base, ['string', 'max:255']),
                'textarea' => array_merge($base, ['string']),
                'date' => array_merge($base, ['date']),
                'number' => array_merge($base, ['numeric', 'min:0']),
                'integer' => array_merge($base, ['integer', 'min:0']),
                'boolean' => ['sometimes', 'boolean'],
                'select' => array_merge($base, ['string']),
                'json' => array_merge($base, ['json']),
                default => $base,
            };
        }

        return $rules;
    }

    private function normalizePayload(array $validated, array $fields): array
    {
        foreach ($fields as $field) {
            $name = $field['name'];
            if (! array_key_exists($name, $validated)) {
                if (($field['type'] ?? '') === 'boolean') {
                    $validated[$name] = false;
                }
                continue;
            }

            if (($field['type'] ?? '') === 'json' && is_string($validated[$name])) {
                $validated[$name] = json_decode($validated[$name], true);
            }
        }

        return $validated;
    }

    private function definitions(): array
    {
        return [
            'leave' => [
                'title' => 'Leave Policy',
                'description' => 'Configure leave balances and accrual behavior.',
                'model' => LeavePolicy::class,
                'fields' => [
                    ['name' => 'name', 'label' => 'Policy Name', 'type' => 'text', 'required' => true],
                    ['name' => 'code', 'label' => 'Policy Code', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'effective_from', 'label' => 'Effective From', 'type' => 'date'],
                    ['name' => 'effective_to', 'label' => 'Effective To', 'type' => 'date'],
                    ['name' => 'annual_limit', 'label' => 'Annual Limit', 'type' => 'integer', 'required' => true],
                    ['name' => 'sick_limit', 'label' => 'Sick Limit', 'type' => 'integer', 'required' => true],
                    ['name' => 'casual_limit', 'label' => 'Casual Limit', 'type' => 'integer', 'required' => true],
                    ['name' => 'unpaid_limit', 'label' => 'Unpaid Limit', 'type' => 'integer', 'required' => true],
                    ['name' => 'carry_forward_limit', 'label' => 'Carry Forward Limit', 'type' => 'integer'],
                    ['name' => 'accrual_frequency', 'label' => 'Accrual Frequency', 'type' => 'select', 'options' => ['monthly', 'quarterly', 'yearly']],
                    ['name' => 'rules', 'label' => 'Rules JSON', 'type' => 'json'],
                    ['name' => 'exceptions', 'label' => 'Exceptions JSON', 'type' => 'json'],
                    ['name' => 'metadata', 'label' => 'Metadata JSON', 'type' => 'json'],
                ],
            ],
            'attendance' => [
                'title' => 'Attendance Policy',
                'description' => 'Configure attendance, grace, and late rules.',
                'model' => AttendancePolicy::class,
                'fields' => [
                    ['name' => 'name', 'label' => 'Policy Name', 'type' => 'text', 'required' => true],
                    ['name' => 'code', 'label' => 'Policy Code', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'standard_hours_per_day', 'label' => 'Standard Hours Per Day', 'type' => 'number'],
                    ['name' => 'grace_minutes', 'label' => 'Grace Minutes', 'type' => 'integer'],
                    ['name' => 'max_late_marks_per_month', 'label' => 'Max Late Marks Per Month', 'type' => 'integer'],
                    ['name' => 'work_days', 'label' => 'Work Days JSON', 'type' => 'json'],
                    ['name' => 'rules', 'label' => 'Rules JSON', 'type' => 'json'],
                ],
            ],
            'holiday' => [
                'title' => 'Holiday Policy',
                'description' => 'Configure holiday calendar behavior and weekends.',
                'model' => HolidayPolicy::class,
                'fields' => [
                    ['name' => 'name', 'label' => 'Policy Name', 'type' => 'text', 'required' => true],
                    ['name' => 'code', 'label' => 'Policy Code', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'country_code', 'label' => 'Country Code', 'type' => 'text'],
                    ['name' => 'state_code', 'label' => 'State Code', 'type' => 'text'],
                    ['name' => 'weekend_days', 'label' => 'Weekend Days JSON', 'type' => 'json'],
                    ['name' => 'rules', 'label' => 'Rules JSON', 'type' => 'json'],
                ],
            ],
            'payroll' => [
                'title' => 'Payroll Policy',
                'description' => 'Configure payroll cycle, cutoffs, and prorating.',
                'model' => PayrollPolicy::class,
                'fields' => [
                    ['name' => 'name', 'label' => 'Policy Name', 'type' => 'text', 'required' => true],
                    ['name' => 'code', 'label' => 'Policy Code', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'pay_cycle', 'label' => 'Pay Cycle', 'type' => 'select', 'options' => ['weekly', 'biweekly', 'monthly']],
                    ['name' => 'pay_day', 'label' => 'Pay Day', 'type' => 'integer'],
                    ['name' => 'cutoff_day', 'label' => 'Cutoff Day', 'type' => 'integer'],
                    ['name' => 'prorate_on_join', 'label' => 'Prorate On Join', 'type' => 'boolean'],
                    ['name' => 'prorate_on_exit', 'label' => 'Prorate On Exit', 'type' => 'boolean'],
                    ['name' => 'rules', 'label' => 'Rules JSON', 'type' => 'json'],
                ],
            ],
            'probation' => [
                'title' => 'Probation Policy',
                'description' => 'Configure probation and extension settings.',
                'model' => ProbationPolicy::class,
                'fields' => [
                    ['name' => 'name', 'label' => 'Policy Name', 'type' => 'text', 'required' => true],
                    ['name' => 'code', 'label' => 'Policy Code', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'probation_days', 'label' => 'Probation Days', 'type' => 'integer'],
                    ['name' => 'extension_allowed', 'label' => 'Extension Allowed', 'type' => 'boolean'],
                    ['name' => 'max_extension_days', 'label' => 'Max Extension Days', 'type' => 'integer'],
                    ['name' => 'rules', 'label' => 'Rules JSON', 'type' => 'json'],
                ],
            ],
            'notice-period' => [
                'title' => 'Notice Period Policy',
                'description' => 'Configure notice days, buyout, and waiver.',
                'model' => NoticePeriodPolicy::class,
                'fields' => [
                    ['name' => 'name', 'label' => 'Policy Name', 'type' => 'text', 'required' => true],
                    ['name' => 'code', 'label' => 'Policy Code', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'notice_days', 'label' => 'Notice Days', 'type' => 'integer'],
                    ['name' => 'buyout_allowed', 'label' => 'Buyout Allowed', 'type' => 'boolean'],
                    ['name' => 'waiver_allowed', 'label' => 'Waiver Allowed', 'type' => 'boolean'],
                    ['name' => 'rules', 'label' => 'Rules JSON', 'type' => 'json'],
                ],
            ],
            'overtime' => [
                'title' => 'Overtime Policy',
                'description' => 'Configure overtime multipliers and limits.',
                'model' => OvertimePolicy::class,
                'fields' => [
                    ['name' => 'name', 'label' => 'Policy Name', 'type' => 'text', 'required' => true],
                    ['name' => 'code', 'label' => 'Policy Code', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'minimum_minutes', 'label' => 'Minimum Minutes', 'type' => 'integer'],
                    ['name' => 'weekday_multiplier', 'label' => 'Weekday Multiplier', 'type' => 'number'],
                    ['name' => 'weekend_multiplier', 'label' => 'Weekend Multiplier', 'type' => 'number'],
                    ['name' => 'holiday_multiplier', 'label' => 'Holiday Multiplier', 'type' => 'number'],
                    ['name' => 'max_hours_per_month', 'label' => 'Max Hours Per Month', 'type' => 'number'],
                    ['name' => 'rules', 'label' => 'Rules JSON', 'type' => 'json'],
                ],
            ],
            'wfh' => [
                'title' => 'WFH Policy',
                'description' => 'Configure remote work eligibility and limits.',
                'model' => WfhPolicy::class,
                'fields' => [
                    ['name' => 'name', 'label' => 'Policy Name', 'type' => 'text', 'required' => true],
                    ['name' => 'code', 'label' => 'Policy Code', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'monthly_limit_days', 'label' => 'Monthly Limit Days', 'type' => 'integer'],
                    ['name' => 'approval_required', 'label' => 'Approval Required', 'type' => 'boolean'],
                    ['name' => 'max_consecutive_days', 'label' => 'Max Consecutive Days', 'type' => 'integer'],
                    ['name' => 'allowed_departments', 'label' => 'Allowed Departments JSON', 'type' => 'json'],
                    ['name' => 'allowed_roles', 'label' => 'Allowed Roles JSON', 'type' => 'json'],
                    ['name' => 'rules', 'label' => 'Rules JSON', 'type' => 'json'],
                ],
            ],
            'reimbursement' => [
                'title' => 'Reimbursement Policy',
                'description' => 'Configure claim limits and approvals.',
                'model' => ReimbursementPolicy::class,
                'fields' => [
                    ['name' => 'name', 'label' => 'Policy Name', 'type' => 'text', 'required' => true],
                    ['name' => 'code', 'label' => 'Policy Code', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'monthly_claim_limit', 'label' => 'Monthly Claim Limit', 'type' => 'number'],
                    ['name' => 'single_claim_limit', 'label' => 'Single Claim Limit', 'type' => 'number'],
                    ['name' => 'receipt_required', 'label' => 'Receipt Required', 'type' => 'boolean'],
                    ['name' => 'allowed_categories', 'label' => 'Allowed Categories JSON', 'type' => 'json'],
                    ['name' => 'approval_matrix', 'label' => 'Approval Matrix JSON', 'type' => 'json'],
                    ['name' => 'rules', 'label' => 'Rules JSON', 'type' => 'json'],
                ],
            ],
            'code-of-conduct' => [
                'title' => 'Code of Conduct Policy',
                'description' => 'Configure conduct versioning and acknowledgement.',
                'model' => CodeOfConductPolicy::class,
                'fields' => [
                    ['name' => 'name', 'label' => 'Policy Name', 'type' => 'text', 'required' => true],
                    ['name' => 'code', 'label' => 'Policy Code', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'document_version', 'label' => 'Document Version', 'type' => 'text'],
                    ['name' => 'acknowledgement_required', 'label' => 'Acknowledgement Required', 'type' => 'boolean'],
                    ['name' => 'policy_text', 'label' => 'Policy Text', 'type' => 'textarea'],
                    ['name' => 'breach_actions', 'label' => 'Breach Actions JSON', 'type' => 'json'],
                    ['name' => 'rules', 'label' => 'Rules JSON', 'type' => 'json'],
                ],
            ],
        ];
    }
}
