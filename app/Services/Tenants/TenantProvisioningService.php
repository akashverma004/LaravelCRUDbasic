<?php

namespace App\Services\Tenants;

use App\Models\AttendancePolicy;
use App\Models\CodeOfConductPolicy;
use App\Models\HolidayPolicy;
use App\Models\LeavePolicy;
use App\Models\NoticePeriodPolicy;
use App\Models\OvertimePolicy;
use App\Models\PayrollPolicy;
use App\Models\Permission;
use App\Models\ProbationPolicy;
use App\Models\ReimbursementPolicy;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WfhPolicy;
use App\Support\TenantContext;

class TenantProvisioningService
{
    public function provision(Tenant $tenant, User $admin): void
    {
        $previousTenantId = TenantContext::id();
        TenantContext::set((int) $tenant->id);

        try {
            $this->seedRolesAndPermissions();
            $this->assignAdminRole($admin);
            $this->seedDefaultPolicies((int) $admin->id);
        } finally {
            TenantContext::set($previousTenantId);
        }
    }

    private function seedRolesAndPermissions(): void
    {
        $permissions = [
            ['name' => 'employee.view',      'display_name' => 'View Employees',       'module' => 'employees'],
            ['name' => 'employee.create',    'display_name' => 'Create Employee',       'module' => 'employees'],
            ['name' => 'employee.edit',      'display_name' => 'Edit Employee',         'module' => 'employees'],
            ['name' => 'employee.delete',    'display_name' => 'Delete Employee',       'module' => 'employees'],
            ['name' => 'employee.export',    'display_name' => 'Export Employees',      'module' => 'employees'],
            ['name' => 'department.view',    'display_name' => 'View Departments',      'module' => 'departments'],
            ['name' => 'department.create',  'display_name' => 'Create Department',     'module' => 'departments'],
            ['name' => 'department.edit',    'display_name' => 'Edit Department',       'module' => 'departments'],
            ['name' => 'department.delete',  'display_name' => 'Delete Department',     'module' => 'departments'],
            ['name' => 'leave.view',         'display_name' => 'View Leave Requests',   'module' => 'leaves'],
            ['name' => 'leave.create',       'display_name' => 'Create Leave Request',  'module' => 'leaves'],
            ['name' => 'leave.edit',         'display_name' => 'Edit Leave Request',    'module' => 'leaves'],
            ['name' => 'leave.delete',       'display_name' => 'Delete Leave Request',  'module' => 'leaves'],
            ['name' => 'leave.approve',      'display_name' => 'Approve Leave Request', 'module' => 'leaves'],
            ['name' => 'leave.reject',       'display_name' => 'Reject Leave Request',  'module' => 'leaves'],
            ['name' => 'attendance.view',    'display_name' => 'View Attendance',       'module' => 'attendance'],
            ['name' => 'attendance.create',  'display_name' => 'Create Attendance',     'module' => 'attendance'],
            ['name' => 'attendance.edit',    'display_name' => 'Edit Attendance',       'module' => 'attendance'],
            ['name' => 'attendance.delete',  'display_name' => 'Delete Attendance',     'module' => 'attendance'],
            ['name' => 'dashboard.view',     'display_name' => 'View Dashboard',        'module' => 'dashboard'],
            ['name' => 'report.view',        'display_name' => 'View Reports',          'module' => 'reports'],
            ['name' => 'report.export',      'display_name' => 'Export Reports',        'module' => 'reports'],
            ['name' => 'role.manage',        'display_name' => 'Manage Roles',          'module' => 'settings'],
            ['name' => 'permission.manage',  'display_name' => 'Manage Permissions',    'module' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(
                ['name' => $permission['name']],
                ['display_name' => $permission['display_name'], 'module' => $permission['module']]
            );
        }

        $roles = [
            'admin'      => 'Administrator',
            'hr_manager' => 'HR Manager',
            'manager'    => 'Manager',
            'hr_officer' => 'HR Officer',
            'employee'   => 'Employee',
        ];

        foreach ($roles as $name => $displayName) {
            Role::query()->firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName, 'description' => $displayName . ' role']
            );
        }

        $allPermissionIds = Permission::query()->pluck('id')->all();
        Role::query()->where('name', 'admin')->first()?->syncPermissions($allPermissionIds);
    }

    private function assignAdminRole(User $admin): void
    {
        $adminRole = Role::query()->where('name', 'admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }
    }

    private function seedDefaultPolicies(int $adminId): void
    {
        $tenantId = TenantContext::id();

        // Search key always includes tenant_id explicitly — safe even if global scope is not active.
        $key = fn (string $code) => ['tenant_id' => $tenantId, 'code' => $code];

        LeavePolicy::query()->firstOrCreate($key('LEAVE_DEFAULT'), [
            'name'                => 'Default Leave Policy',
            'is_active'           => true,
            'annual_limit'        => 15,
            'sick_limit'          => 10,
            'casual_limit'        => 8,
            'unpaid_limit'        => 0,
            'carry_forward_limit' => 5,
            'accrual_frequency'   => 'monthly',
            'rules'               => [],
            'exceptions'          => [],
            'metadata'            => [],
            'created_by'          => $adminId,
            'updated_by'          => $adminId,
        ]);

        AttendancePolicy::query()->firstOrCreate($key('ATTENDANCE_DEFAULT'), [
            'name'                     => 'Default Attendance Policy',
            'is_active'                => true,
            'standard_hours_per_day'   => 8,
            'grace_minutes'            => 10,
            'max_late_marks_per_month' => 3,
            'work_days'                => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            'rules'                    => [],
            'exceptions'               => [],
            'metadata'                 => [],
            'created_by'               => $adminId,
            'updated_by'               => $adminId,
        ]);

        HolidayPolicy::query()->firstOrCreate($key('HOLIDAY_DEFAULT'), [
            'name'         => 'Default Holiday Policy',
            'is_active'    => true,
            'country_code' => 'IN',
            'state_code'   => 'KA',
            'weekend_days' => ['saturday', 'sunday'],
            'rules'        => [],
            'exceptions'   => [],
            'metadata'     => [],
            'created_by'   => $adminId,
            'updated_by'   => $adminId,
        ]);

        PayrollPolicy::query()->firstOrCreate($key('PAYROLL_DEFAULT'), [
            'name'            => 'Default Payroll Policy',
            'is_active'       => true,
            'pay_cycle'       => 'monthly',
            'pay_day'         => 30,
            'cutoff_day'      => 25,
            'prorate_on_join' => true,
            'prorate_on_exit' => true,
            'rules'           => [],
            'exceptions'      => [],
            'metadata'        => [],
            'created_by'      => $adminId,
            'updated_by'      => $adminId,
        ]);

        ProbationPolicy::query()->firstOrCreate($key('PROBATION_DEFAULT'), [
            'name'               => 'Default Probation Policy',
            'is_active'          => true,
            'probation_days'     => 90,
            'extension_allowed'  => true,
            'max_extension_days' => 60,
            'rules'              => [],
            'exceptions'         => [],
            'metadata'           => [],
            'created_by'         => $adminId,
            'updated_by'         => $adminId,
        ]);

        NoticePeriodPolicy::query()->firstOrCreate($key('NOTICE_PERIOD_DEFAULT'), [
            'name'           => 'Default Notice Policy',
            'is_active'      => true,
            'notice_days'    => 30,
            'buyout_allowed' => true,
            'waiver_allowed' => false,
            'rules'          => [],
            'exceptions'     => [],
            'metadata'       => [],
            'created_by'     => $adminId,
            'updated_by'     => $adminId,
        ]);

        OvertimePolicy::query()->firstOrCreate($key('OVERTIME_DEFAULT'), [
            'name'                => 'Default Overtime Policy',
            'is_active'           => true,
            'minimum_minutes'     => 30,
            'weekday_multiplier'  => 1.5,
            'weekend_multiplier'  => 2.0,
            'holiday_multiplier'  => 2.5,
            'max_hours_per_month' => 40,
            'rules'               => [],
            'exceptions'          => [],
            'metadata'            => [],
            'created_by'          => $adminId,
            'updated_by'          => $adminId,
        ]);

        WfhPolicy::query()->firstOrCreate($key('WFH_DEFAULT'), [
            'name'                 => 'Default WFH Policy',
            'is_active'            => true,
            'monthly_limit_days'   => 8,
            'approval_required'    => true,
            'max_consecutive_days' => 3,
            'allowed_departments'  => [],
            'allowed_roles'        => [],
            'rules'                => [],
            'exceptions'           => [],
            'metadata'             => [],
            'created_by'           => $adminId,
            'updated_by'           => $adminId,
        ]);

        ReimbursementPolicy::query()->firstOrCreate($key('REIMBURSEMENT_DEFAULT'), [
            'name'                => 'Default Reimbursement Policy',
            'is_active'           => true,
            'monthly_claim_limit' => 25000,
            'single_claim_limit'  => 10000,
            'receipt_required'    => true,
            'allowed_categories'  => [],
            'approval_matrix'     => [],
            'rules'               => [],
            'exceptions'          => [],
            'metadata'            => [],
            'created_by'          => $adminId,
            'updated_by'          => $adminId,
        ]);

        CodeOfConductPolicy::query()->firstOrCreate($key('CODE_OF_CONDUCT_DEFAULT'), [
            'name'                    => 'Default Code of Conduct Policy',
            'is_active'               => true,
            'document_version'        => '1.0',
            'acknowledgement_required'=> true,
            'policy_text'             => 'Follow company conduct standards.',
            'breach_actions'          => ['warning', 'suspension'],
            'rules'                   => [],
            'exceptions'              => [],
            'metadata'                => [],
            'created_by'              => $adminId,
            'updated_by'              => $adminId,
        ]);
    }
}
