<?php

namespace Database\Seeders;

use App\Models\AttendancePolicy;
use App\Models\CodeOfConductPolicy;
use App\Models\HolidayPolicy;
use App\Models\HolidayPolicyDate;
use App\Models\LeavePolicy;
use App\Models\NoticePeriodPolicy;
use App\Models\OvertimePolicy;
use App\Models\PayrollPolicy;
use App\Models\ProbationPolicy;
use App\Models\ReimbursementPolicy;
use App\Models\User;
use App\Models\WfhPolicy;
use App\Support\PolicyRuleExamples;
use App\Support\PolicyType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PolicySeeder extends Seeder
{
    public function run(): void
    {
        $tenantIds = DB::table('tenants')->pluck('id')->all();
        if (empty($tenantIds)) {
            $tenantIds = [1];
        }

        $rulesByType = PolicyRuleExamples::byPolicyType();
        $effectiveFrom = now()->startOfYear()->toDateString();

        foreach ($tenantIds as $tenantId) {
            $adminId = User::query()
                ->where('tenant_id', $tenantId)
                ->where('email', 'admin@hrmsai.test')
                ->value('id');

            $leavePolicy = LeavePolicy::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'LEAVE_DEFAULT'],
                [
                    'name' => 'Default Leave Policy',
                    'description' => 'Standard leave allocation and accrual rules for all employees.',
                    'is_active' => true,
                    'effective_from' => $effectiveFrom,
                    'annual_limit' => 15,
                    'sick_limit' => 10,
                    'casual_limit' => 8,
                    'unpaid_limit' => 0,
                    'carry_forward_limit' => 5,
                    'accrual_frequency' => 'monthly',
                    'rules' => $rulesByType[PolicyType::LEAVE] ?? PolicyRuleExamples::baseTemplate(),
                    'exceptions' => [],
                    'metadata' => ['version' => '1.0', 'scope' => 'global'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            AttendancePolicy::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'ATTENDANCE_DEFAULT'],
                [
                    'name' => 'Default Attendance Policy',
                    'description' => 'Standard attendance and grace period controls.',
                    'is_active' => true,
                    'effective_from' => $effectiveFrom,
                    'standard_hours_per_day' => 8,
                    'grace_minutes' => 10,
                    'max_late_marks_per_month' => 3,
                    'work_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                    'rules' => $rulesByType[PolicyType::ATTENDANCE] ?? PolicyRuleExamples::baseTemplate(),
                    'exceptions' => [],
                    'metadata' => ['version' => '1.0', 'scope' => 'global'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            $holidayPolicy = HolidayPolicy::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'HOLIDAY_DEFAULT'],
                [
                    'name' => 'Default Holiday Policy',
                    'description' => 'Common public holiday calendar and weekend setup.',
                    'is_active' => true,
                    'effective_from' => $effectiveFrom,
                    'country_code' => 'IN',
                    'state_code' => 'KA',
                    'weekend_days' => ['saturday', 'sunday'],
                    'rules' => $rulesByType[PolicyType::HOLIDAY] ?? PolicyRuleExamples::baseTemplate(),
                    'exceptions' => [],
                    'metadata' => ['version' => '1.0', 'scope' => 'global'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            $this->seedHolidayDates($tenantId, $holidayPolicy->id);

            PayrollPolicy::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'PAYROLL_DEFAULT'],
                [
                    'name' => 'Default Payroll Policy',
                    'description' => 'Monthly payroll cycle, cutoffs, and proration behavior.',
                    'is_active' => true,
                    'effective_from' => $effectiveFrom,
                    'pay_cycle' => 'monthly',
                    'pay_day' => 30,
                    'cutoff_day' => 25,
                    'prorate_on_join' => true,
                    'prorate_on_exit' => true,
                    'rules' => $rulesByType[PolicyType::PAYROLL] ?? PolicyRuleExamples::baseTemplate(),
                    'exceptions' => [],
                    'metadata' => ['version' => '1.0', 'scope' => 'global'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            ProbationPolicy::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'PROBATION_DEFAULT'],
                [
                    'name' => 'Default Probation Policy',
                    'description' => 'Initial confirmation and extension criteria.',
                    'is_active' => true,
                    'effective_from' => $effectiveFrom,
                    'probation_days' => 90,
                    'extension_allowed' => true,
                    'max_extension_days' => 60,
                    'rules' => $rulesByType[PolicyType::PROBATION] ?? PolicyRuleExamples::baseTemplate(),
                    'exceptions' => [],
                    'metadata' => ['version' => '1.0', 'scope' => 'global'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            NoticePeriodPolicy::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'NOTICE_DEFAULT'],
                [
                    'name' => 'Default Notice Period Policy',
                    'description' => 'Notice period and separation terms.',
                    'is_active' => true,
                    'effective_from' => $effectiveFrom,
                    'notice_days' => 30,
                    'buyout_allowed' => true,
                    'waiver_allowed' => false,
                    'rules' => $rulesByType[PolicyType::NOTICE_PERIOD] ?? PolicyRuleExamples::baseTemplate(),
                    'exceptions' => [],
                    'metadata' => ['version' => '1.0', 'scope' => 'global'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            OvertimePolicy::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'OVERTIME_DEFAULT'],
                [
                    'name' => 'Default Overtime Policy',
                    'description' => 'Overtime eligibility, multipliers, and limits.',
                    'is_active' => true,
                    'effective_from' => $effectiveFrom,
                    'minimum_minutes' => 30,
                    'weekday_multiplier' => 1.50,
                    'weekend_multiplier' => 2.00,
                    'holiday_multiplier' => 2.50,
                    'max_hours_per_month' => 40,
                    'rules' => $rulesByType[PolicyType::OVERTIME] ?? PolicyRuleExamples::baseTemplate(),
                    'exceptions' => [],
                    'metadata' => ['version' => '1.0', 'scope' => 'global'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            WfhPolicy::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'WFH_DEFAULT'],
                [
                    'name' => 'Default WFH Policy',
                    'description' => 'Work from home eligibility and approval controls.',
                    'is_active' => true,
                    'effective_from' => $effectiveFrom,
                    'monthly_limit_days' => 8,
                    'approval_required' => true,
                    'max_consecutive_days' => 3,
                    'allowed_departments' => ['engineering', 'design', 'product'],
                    'allowed_roles' => ['employee', 'manager'],
                    'rules' => $rulesByType[PolicyType::WFH] ?? PolicyRuleExamples::baseTemplate(),
                    'exceptions' => [],
                    'metadata' => ['version' => '1.0', 'scope' => 'global'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            ReimbursementPolicy::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'REIMBURSE_DEFAULT'],
                [
                    'name' => 'Default Reimbursement Policy',
                    'description' => 'Expense claim categories, limits, and approvals.',
                    'is_active' => true,
                    'effective_from' => $effectiveFrom,
                    'monthly_claim_limit' => 25000,
                    'single_claim_limit' => 10000,
                    'receipt_required' => true,
                    'allowed_categories' => ['travel', 'meal', 'internet', 'office_supplies'],
                    'approval_matrix' => ['manager', 'finance'],
                    'rules' => $rulesByType[PolicyType::REIMBURSEMENT] ?? PolicyRuleExamples::baseTemplate(),
                    'exceptions' => [],
                    'metadata' => ['version' => '1.0', 'scope' => 'global'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            CodeOfConductPolicy::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'COC_DEFAULT'],
                [
                    'name' => 'Default Code of Conduct Policy',
                    'description' => 'Employee behavior standards and disciplinary actions.',
                    'is_active' => true,
                    'effective_from' => $effectiveFrom,
                    'document_version' => '1.0',
                    'acknowledgement_required' => true,
                    'policy_text' => 'All employees must follow ethical standards, respect colleagues, and protect confidential company information.',
                    'breach_actions' => ['warning', 'suspension', 'termination'],
                    'rules' => $rulesByType[PolicyType::CODE_OF_CONDUCT] ?? PolicyRuleExamples::baseTemplate(),
                    'exceptions' => [],
                    'metadata' => ['version' => '1.0', 'scope' => 'global'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );
        }

        $this->command?->info('Default policy records seeded for all tenants.');
    }

    private function seedHolidayDates(int $tenantId, int $holidayPolicyId): void
    {
        $year = (int) now()->format('Y');

        $holidays = [
            ['name' => 'Republic Day', 'holiday_date' => sprintf('%d-01-26', $year), 'is_optional' => false],
            ['name' => 'Independence Day', 'holiday_date' => sprintf('%d-08-15', $year), 'is_optional' => false],
            ['name' => 'Gandhi Jayanti', 'holiday_date' => sprintf('%d-10-02', $year), 'is_optional' => false],
            ['name' => 'Diwali', 'holiday_date' => sprintf('%d-11-01', $year), 'is_optional' => true],
            ['name' => 'Christmas', 'holiday_date' => sprintf('%d-12-25', $year), 'is_optional' => true],
        ];

        foreach ($holidays as $holiday) {
            HolidayPolicyDate::query()->updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'holiday_policy_id' => $holidayPolicyId,
                    'name' => $holiday['name'],
                    'holiday_date' => $holiday['holiday_date'],
                ],
                [
                    'is_optional' => $holiday['is_optional'],
                    'rules' => ['source' => 'default_seeder'],
                ]
            );
        }
    }
}
