# HRMS Roles & Permissions Quick Reference

## 5 Core Roles

```
┌─────────────────────────────────────────────────────────────────┐
│                     ADMIN (Full Access)                         │
├─────────────────────────────────────────────────────────────────┤
│ • All system resources                                          │
│ • Can manage roles and permissions                              │
│ • Full CRUD on all modules                                      │
│ • Can access all reports and dashboards                         │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│              HR MANAGER (Human Resources Lead)                  │
├─────────────────────────────────────────────────────────────────┤
│ Employees:     ✓ View ✓ Create ✓ Edit ✓ Delete ✓ Export        │
│ Departments:   ✓ View ✓ Create ✓ Edit ✓ Delete                 │
│ Leave:         ✓ View ✓ Approve ✓ Reject (Cannot Submit)       │
│ Attendance:    ✓ View ✓ Create ✓ Edit (-) Delete               │
│ Dashboard:     ✓ View                                           │
│ Reports:       ✓ View ✓ Export                                  │
│ Roles:         ✗ Cannot Manage                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│               MANAGER (Team Lead / Supervisor)                  │
├─────────────────────────────────────────────────────────────────┤
│ Employees:     ✓ View (Own Team)                                │
│ Leave:         ✓ View Team Leaves, ✓ Approve, ✓ Reject         │
│ Attendance:    ✓ View Team Attendance                           │
│ Dashboard:     ✓ View                                           │
│ Reports:       ✓ View (Team Data)                               │
│ Scope:         LIMITED TO DIRECT REPORTS ONLY                   │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                HR OFFICER (HR Support Staff)                    │
├─────────────────────────────────────────────────────────────────┤
│ Employees:     ✓ View ✓ Create ✓ Edit (-) Delete               │
│ Departments:   ✓ View (-) Create (-) Edit (-) Delete            │
│ Leave:         ✓ View ✓ Create ✓ Edit ✓ Delete                 │
│ Attendance:    ✓ View ✓ Create ✓ Edit (-) Delete               │
│ Dashboard:     ✓ View                                           │
│ Reports:       ✓ View                                           │
│ Role:          Role: SUPPORT OPERATIONS ONLY                    │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                EMPLOYEE (Individual Contributor)                │
├─────────────────────────────────────────────────────────────────┤
│ Employees:     ✓ View (Own Profile)                             │
│ Leave:         ✓ Create ✓ View (Own Requests)                   │
│ Dashboard:     ✓ View                                           │
│ Scope:         PERSONAL DATA ONLY                               │
└─────────────────────────────────────────────────────────────────┘
```

## Permission Matrix

| Permission | Admin | HR Manager | Manager | HR Officer | Employee |
|----------|:-----:|:----------:|:-------:|:----------:|:--------:|
| **Employees Module** |
| employee.view | ✓ | ✓ | ✓ | ✓ | ✗ |
| employee.create | ✓ | ✓ | ✗ | ✓ | ✗ |
| employee.edit | ✓ | ✓ | ✗ | ✓ | ✗ |
| employee.delete | ✓ | ✓ | ✗ | ✗ | ✗ |
| employee.export | ✓ | ✓ | ✗ | ✗ | ✗ |
| **Departments Module** |
| department.view | ✓ | ✓ | ✗ | ✓ | ✗ |
| department.create | ✓ | ✓ | ✗ | ✗ | ✗ |
| department.edit | ✓ | ✓ | ✗ | ✗ | ✗ |
| department.delete | ✓ | ✓ | ✗ | ✗ | ✗ |
| **Leaves Module** |
| leave.view | ✓ | ✓ | ✓ | ✓ | ✓ |
| leave.create | ✓ | ✓ | ✗ | ✓ | ✓ |
| leave.edit | ✓ | ✓ | ✗ | ✓ | ✗ |
| leave.delete | ✓ | ✓ | ✗ | ✓ | ✗ |
| leave.approve | ✓ | ✓ | ✓ | ✗ | ✗ |
| leave.reject | ✓ | ✓ | ✓ | ✗ | ✗ |
| **Attendance Module** |
| attendance.view | ✓ | ✓ | ✓ | ✓ | ✗ |
| attendance.create | ✓ | ✓ | ✗ | ✓ | ✗ |
| attendance.edit | ✓ | ✓ | ✗ | ✓ | ✗ |
| attendance.delete | ✓ | ✓ | ✗ | ✗ | ✗ |
| **Dashboard & Reports** |
| dashboard.view | ✓ | ✓ | ✓ | ✓ | ✓ |
| report.view | ✓ | ✓ | ✓ | ✓ | ✗ |
| report.export | ✓ | ✓ | ✗ | ✗ | ✗ |
| **Settings** |
| role.manage | ✓ | ✗ | ✗ | ✗ | ✗ |
| permission.manage | ✓ | ✗ | ✗ | ✗ | ✗ |

## Role Selection Guide

### Choose **ADMIN** for:
- System administrators
- Full system access needed
- Troubleshooting and maintenance

### Choose **HR MANAGER** for:
- Human Resources department lead
- Employee lifecycle management
- Leave and attendance oversight
- Department management

### Choose **MANAGER** for:
- Team leads and supervisors
- Approving team member leaves
- Monitoring team attendance
- Team performance overview

### Choose **HR OFFICER** for:
- HR support staff
- Leave request processing
- Employee record maintenance
- Attendance tracking assistance

### Choose **EMPLOYEE** for:
- Regular company employees
- Viewing own information
- Submitting personal leave requests
- Personal dashboard access

## How to Assign Roles

### Method 1: Using Tinker (Command Line)
```bash
php artisan tinker
> $user = User::find(1);
> $user->assignRole('hr_manager');
> $user->hasRole('hr_manager');  // true
```

### Method 2: Using Web Interface (Admin Only)
1. Navigate to `/users`
2. Click on user
3. Select roles
4. Save

### Method 3: Using Code
```php
$user->assignRole('manager');
$user->syncRoles(['manager', 'employee']);
$user->removeRole('manager');
```

## How to Check Permissions

### In Controllers
```php
if (auth()->user()->hasPermission('employee.create')) {
    // Allow action
}
```

### In Blade Templates
```blade
@if (userHasPermission('employee.export'))
    <button>Export</button>
@endif
```

### In Routes
```php
Route::post('/employees', [EmployeeController::class, 'store'])
    ->middleware('permission:employee.create');
```

## Role Change Policy

- **Admin** can assign/remove any role
- **HR Manager** cannot assign roles (contact Admin)
- **Manager** cannot change roles (contact Admin)
- **HR Officer** cannot change roles (contact Admin)
- **Employee** cannot change roles (contact Admin)

## API Endpoints for Role Management

| Endpoint | Method | Roles Allowed | Purpose |
|----------|--------|---------------|---------|
| `/roles` | GET | admin, hr_manager | List all roles |
| `/roles/create` | GET | admin, hr_manager | Create role form |
| `/roles` | POST | admin, hr_manager | Store new role |
| `/roles/{id}/edit` | GET | admin, hr_manager | Edit role form |
| `/roles/{id}` | PATCH | admin, hr_manager | Update role |
| `/roles/{id}` | DELETE | admin, hr_manager | Delete role |
| `/users` | GET | admin, hr_manager | List users with roles |
| `/users/{id}/roles` | GET | admin, hr_manager | Edit user roles |
| `/users/{id}/roles` | PATCH | admin, hr_manager | Update user roles |

## Best Practices

1. **Principle of Least Privilege**
   - Give only necessary permissions
   - Start with least access
   - Increase as needed

2. **Regular Audits**
   - Review user roles quarterly
   - Remove unused roles
   - Monitor permission usage

3. **Role Documentation**
   - Document each role's purpose
   - Keep this reference updated
   - Train team on roles

4. **Change Management**
   - Log all role changes
   - Get approval before changes
   - Notify users of role changes

5. **Department Alignment**
   - Map business roles to system roles
   - Keep alignment updated
   - Review annually

## Example Workflows

### Employee Onboarding
1. Create user account
2. Assign "employee" role
3. If team member → assign "manager" role to their manager
4. If HR staff → assign "hr_officer" role
5. If department head → assign "manager" role

### HR Manager Assignment
1. Create user account
2. Assign "hr_manager" role
3. Review all permissions (should have most except role.manage)
4. Test access to all required features

### Department Manager Promotion
1. Remove "employee" role
2. Assign "manager" role
3. Verify team visibility
4. Test leave approval workflow

