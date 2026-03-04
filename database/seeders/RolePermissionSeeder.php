<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $tenantIds = DB::table('tenants')->pluck('id')->all();
        if (empty($tenantIds)) {
            $tenantIds = [1];
        }

        $permissions = [
            ['name' => 'employee.view', 'display_name' => 'View Employees', 'module' => 'employees'],
            ['name' => 'employee.create', 'display_name' => 'Create Employee', 'module' => 'employees'],
            ['name' => 'employee.edit', 'display_name' => 'Edit Employee', 'module' => 'employees'],
            ['name' => 'employee.delete', 'display_name' => 'Delete Employee', 'module' => 'employees'],
            ['name' => 'employee.export', 'display_name' => 'Export Employees', 'module' => 'employees'],
            ['name' => 'department.view', 'display_name' => 'View Departments', 'module' => 'departments'],
            ['name' => 'department.create', 'display_name' => 'Create Department', 'module' => 'departments'],
            ['name' => 'department.edit', 'display_name' => 'Edit Department', 'module' => 'departments'],
            ['name' => 'department.delete', 'display_name' => 'Delete Department', 'module' => 'departments'],
            ['name' => 'leave.view', 'display_name' => 'View Leave Requests', 'module' => 'leaves'],
            ['name' => 'leave.create', 'display_name' => 'Create Leave Request', 'module' => 'leaves'],
            ['name' => 'leave.edit', 'display_name' => 'Edit Leave Request', 'module' => 'leaves'],
            ['name' => 'leave.delete', 'display_name' => 'Delete Leave Request', 'module' => 'leaves'],
            ['name' => 'leave.approve', 'display_name' => 'Approve Leave Request', 'module' => 'leaves'],
            ['name' => 'leave.reject', 'display_name' => 'Reject Leave Request', 'module' => 'leaves'],
            ['name' => 'attendance.view', 'display_name' => 'View Attendance', 'module' => 'attendance'],
            ['name' => 'attendance.create', 'display_name' => 'Create Attendance', 'module' => 'attendance'],
            ['name' => 'attendance.edit', 'display_name' => 'Edit Attendance', 'module' => 'attendance'],
            ['name' => 'attendance.delete', 'display_name' => 'Delete Attendance', 'module' => 'attendance'],
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'module' => 'dashboard'],
            ['name' => 'report.view', 'display_name' => 'View Reports', 'module' => 'reports'],
            ['name' => 'report.export', 'display_name' => 'Export Reports', 'module' => 'reports'],
            ['name' => 'role.manage', 'display_name' => 'Manage Roles', 'module' => 'settings'],
            ['name' => 'permission.manage', 'display_name' => 'Manage Permissions', 'module' => 'settings'],
        ];

        foreach ($tenantIds as $tenantId) {
            foreach ($permissions as $permission) {
                Permission::query()->firstOrCreate(
                    ['tenant_id' => $tenantId, 'name' => $permission['name']],
                    ['display_name' => $permission['display_name'], 'module' => $permission['module']]
                );
            }

            $admin = Role::query()->firstOrCreate(
                ['tenant_id' => $tenantId, 'name' => 'admin'],
                ['display_name' => 'Administrator', 'description' => 'Full system access']
            );
            $hrManager = Role::query()->firstOrCreate(
                ['tenant_id' => $tenantId, 'name' => 'hr_manager'],
                ['display_name' => 'HR Manager', 'description' => 'Manage employees, leaves, and departments']
            );
            $manager = Role::query()->firstOrCreate(
                ['tenant_id' => $tenantId, 'name' => 'manager'],
                ['display_name' => 'Manager', 'description' => 'Manage team members and approve leaves']
            );
            $hrOfficer = Role::query()->firstOrCreate(
                ['tenant_id' => $tenantId, 'name' => 'hr_officer'],
                ['display_name' => 'HR Officer', 'description' => 'Support HR processes']
            );
            $employee = Role::query()->firstOrCreate(
                ['tenant_id' => $tenantId, 'name' => 'employee'],
                ['display_name' => 'Employee', 'description' => 'Basic employee access']
            );

            $allPermissionIds = Permission::query()->where('tenant_id', $tenantId)->pluck('id')->toArray();
            $admin->syncPermissions($allPermissionIds);

            $hrManager->syncPermissions($this->permissionIdsByNames($tenantId, [
                'employee.view', 'employee.create', 'employee.edit', 'employee.delete', 'employee.export',
                'department.view', 'department.create', 'department.edit', 'department.delete',
                'leave.view', 'leave.approve', 'leave.reject',
                'attendance.view', 'attendance.create', 'attendance.edit',
                'dashboard.view', 'report.view', 'report.export',
            ]));

            $manager->syncPermissions($this->permissionIdsByNames($tenantId, [
                'employee.view',
                'leave.view', 'leave.approve', 'leave.reject',
                'attendance.view',
                'dashboard.view', 'report.view',
            ]));

            $hrOfficer->syncPermissions($this->permissionIdsByNames($tenantId, [
                'employee.view', 'employee.create', 'employee.edit',
                'department.view',
                'leave.view', 'leave.create', 'leave.edit', 'leave.delete',
                'attendance.view', 'attendance.create', 'attendance.edit',
                'dashboard.view', 'report.view',
            ]));

            $employee->syncPermissions($this->permissionIdsByNames($tenantId, [
                'employee.view',
                'leave.create', 'leave.view',
                'dashboard.view',
            ]));
        }

        $this->command?->info('Roles and permissions seeded for all tenants.');
    }

    private function permissionIdsByNames(int $tenantId, array $names): array
    {
        return Permission::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('name', $names)
            ->pluck('id')
            ->toArray();
    }
}
