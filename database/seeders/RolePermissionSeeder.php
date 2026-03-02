<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // Employee Permissions
            ['name' => 'employee.view', 'display_name' => 'View Employees', 'module' => 'employees'],
            ['name' => 'employee.create', 'display_name' => 'Create Employee', 'module' => 'employees'],
            ['name' => 'employee.edit', 'display_name' => 'Edit Employee', 'module' => 'employees'],
            ['name' => 'employee.delete', 'display_name' => 'Delete Employee', 'module' => 'employees'],
            ['name' => 'employee.export', 'display_name' => 'Export Employees', 'module' => 'employees'],

            // Department Permissions
            ['name' => 'department.view', 'display_name' => 'View Departments', 'module' => 'departments'],
            ['name' => 'department.create', 'display_name' => 'Create Department', 'module' => 'departments'],
            ['name' => 'department.edit', 'display_name' => 'Edit Department', 'module' => 'departments'],
            ['name' => 'department.delete', 'display_name' => 'Delete Department', 'module' => 'departments'],

            // Leave Request Permissions
            ['name' => 'leave.view', 'display_name' => 'View Leave Requests', 'module' => 'leaves'],
            ['name' => 'leave.create', 'display_name' => 'Create Leave Request', 'module' => 'leaves'],
            ['name' => 'leave.edit', 'display_name' => 'Edit Leave Request', 'module' => 'leaves'],
            ['name' => 'leave.delete', 'display_name' => 'Delete Leave Request', 'module' => 'leaves'],
            ['name' => 'leave.approve', 'display_name' => 'Approve Leave Request', 'module' => 'leaves'],
            ['name' => 'leave.reject', 'display_name' => 'Reject Leave Request', 'module' => 'leaves'],

            // Attendance Permissions
            ['name' => 'attendance.view', 'display_name' => 'View Attendance', 'module' => 'attendance'],
            ['name' => 'attendance.create', 'display_name' => 'Create Attendance', 'module' => 'attendance'],
            ['name' => 'attendance.edit', 'display_name' => 'Edit Attendance', 'module' => 'attendance'],
            ['name' => 'attendance.delete', 'display_name' => 'Delete Attendance', 'module' => 'attendance'],

            // Dashboard & Reports
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'module' => 'dashboard'],
            ['name' => 'report.view', 'display_name' => 'View Reports', 'module' => 'reports'],
            ['name' => 'report.export', 'display_name' => 'Export Reports', 'module' => 'reports'],

            // Role & Permission Management
            ['name' => 'role.manage', 'display_name' => 'Manage Roles', 'module' => 'settings'],
            ['name' => 'permission.manage', 'display_name' => 'Manage Permissions', 'module' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['display_name' => $permission['display_name'], 'module' => $permission['module']]
            );
        }

        // Create Roles
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator', 'description' => 'Full system access']
        );

        $hrManager = Role::firstOrCreate(
            ['name' => 'hr_manager'],
            ['display_name' => 'HR Manager', 'description' => 'Manage employees, leaves, and departments']
        );

        $manager = Role::firstOrCreate(
            ['name' => 'manager'],
            ['display_name' => 'Manager', 'description' => 'Manage team members and approve leaves']
        );

        $hrOfficer = Role::firstOrCreate(
            ['name' => 'hr_officer'],
            ['display_name' => 'HR Officer', 'description' => 'Support HR processes']
        );

        $employee = Role::firstOrCreate(
            ['name' => 'employee'],
            ['display_name' => 'Employee', 'description' => 'Basic employee access']
        );

        // Assign Permissions to Admin Role (all permissions)
        $adminPermissions = Permission::all()->pluck('id')->toArray();
        $admin->syncPermissions($adminPermissions);

        // Assign Permissions to HR Manager
        $hrManagerPermissions = Permission::whereIn('name', [
            'employee.view', 'employee.create', 'employee.edit', 'employee.delete', 'employee.export',
            'department.view', 'department.create', 'department.edit', 'department.delete',
            'leave.view', 'leave.approve', 'leave.reject',
            'attendance.view', 'attendance.create', 'attendance.edit',
            'dashboard.view', 'report.view', 'report.export',
        ])->pluck('id')->toArray();
        $hrManager->syncPermissions($hrManagerPermissions);

        // Assign Permissions to Manager
        $managerPermissions = Permission::whereIn('name', [
            'employee.view',
            'leave.view', 'leave.approve', 'leave.reject',
            'attendance.view',
            'dashboard.view', 'report.view',
        ])->pluck('id')->toArray();
        $manager->syncPermissions($managerPermissions);

        // Assign Permissions to HR Officer
        $hrOfficerPermissions = Permission::whereIn('name', [
            'employee.view', 'employee.create', 'employee.edit',
            'department.view',
            'leave.view', 'leave.create', 'leave.edit', 'leave.delete',
            'attendance.view', 'attendance.create', 'attendance.edit',
            'dashboard.view', 'report.view',
        ])->pluck('id')->toArray();
        $hrOfficer->syncPermissions($hrOfficerPermissions);

        // Assign Permissions to Employee
        $employeePermissions = Permission::whereIn('name', [
            'employee.view',
            'leave.create', 'leave.view',
            'dashboard.view',
        ])->pluck('id')->toArray();
        $employee->syncPermissions($employeePermissions);

        $this->command->info('✓ Roles and permissions created successfully!');
        $this->command->info('Roles created: Admin, HR Manager, Manager, HR Officer, Employee');
    }
}
