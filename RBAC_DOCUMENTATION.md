# HRMS Role-Based Access Control (RBAC) Documentation

## Overview

A comprehensive role-based access control system has been implemented for the HRMS application. This system provides granular permission management, role assignment, and authorization checks throughout the application.

## Key Features

### 1. **Role-Based Access Control**
- Predefined roles: Admin, HR Manager, Manager, HR Officer, Employee
- Custom role creation and management
- Role assignment to users
- Role hierarchies and inheritance

### 2. **Permission System**
- Granular permissions grouped by modules
- 27 total permissions covering all HRMS modules
- Direct permission-to-role mapping
- Module-based permission organization

### 3. **Authorization Checks**
- Middleware-based role and permission checking
- Gate-based authorization in controllers and views
- Helper functions for Blade templates
- Service layer authorization methods

## Database Schema

### Tables Created

#### `roles`
- `id` (Primary Key)
- `name` (unique, slug format)
- `display_name`
- `description` (nullable)
- `timestamps`

#### `permissions`
- `id` (Primary Key)
- `name` (unique)
- `display_name`
- `description` (nullable)
- `module` (e.g., employees, leaves, departments)
- `timestamps`

#### `role_permission` (Many-to-Many Pivot)
- `id` (Primary Key)
- `role_id` (Foreign Key → roles.id)
- `permission_id` (Foreign Key → permissions.id)
- `timestamps`
- Unique constraint on (role_id, permission_id)

#### `user_role` (Many-to-Many Pivot)
- `id` (Primary Key)
- `user_id` (Foreign Key → users.id)
- `role_id` (Foreign Key → roles.id)
- `timestamps`
- Unique constraint on (user_id, role_id)

## Predefined Roles

### 1. **Admin** (Full Access)
- All permissions granted
- Can create, read, update, delete all resources
- Can manage roles and permissions
- Full system access

### 2. **HR Manager**
- Employee management (view, create, edit, delete, export)
- Department management (full CRUD)
- Leave request management (view, approve, reject)
- Attendance tracking (view, create, edit)
- Dashboard and reporting access
- **Cannot**: Manage roles/permissions

### 3. **Manager** (Team Lead)
- View all employees
- View and manage team leave requests (approve/reject)
- View attendance records
- Dashboard and report viewing
- **Limited to**: Own team members

### 4. **HR Officer** (HR Support)
- Employee management (view, create, edit)
- Department viewing
- Leave request management (view, create, edit, delete)
- Attendance management (view, create, edit)
- Dashboard and report viewing
- **Limited to**: Support functions, no deletion

### 5. **Employee** (Basic User)
- View own employee information
- Create and view personal leave requests
- View dashboard
- **Limited to**: Own information only

## Permission List by Module

### Employees Module
- `employee.view` - View Employees
- `employee.create` - Create Employee
- `employee.edit` - Edit Employee
- `employee.delete` - Delete Employee
- `employee.export` - Export Employees

### Departments Module
- `department.view` - View Departments
- `department.create` - Create Department
- `department.edit` - Edit Department
- `department.delete` - Delete Department

### Leaves Module
- `leave.view` - View Leave Requests
- `leave.create` - Create Leave Request
- `leave.edit` - Edit Leave Request
- `leave.delete` - Delete Leave Request
- `leave.approve` - Approve Leave Request
- `leave.reject` - Reject Leave Request

### Attendance Module
- `attendance.view` - View Attendance
- `attendance.create` - Create Attendance
- `attendance.edit` - Edit Attendance
- `attendance.delete` - Delete Attendance

### System Module
- `dashboard.view` - View Dashboard
- `report.view` - View Reports
- `report.export` - Export Reports
- `role.manage` - Manage Roles
- `permission.manage` - Manage Permissions

## Implementation Files

### Models
- `app/Models/Role.php` - Role model with permission relationships
- `app/Models/Permission.php` - Permission model with role relationships
- `app/Models/User.php` - Enhanced with role/permission methods

### Services
- `app/Services/AuthorizationService.php` - Centralized authorization logic

### Middleware
- `app/Http/Middleware/CheckRole.php` - Role verification middleware
- `app/Http/Middleware/CheckPermission.php` - Permission verification middleware

### Controllers
- `app/Http/Controllers/RoleController.php` - Role management (CRUD)
- `app/Http/Controllers/UserRoleController.php` - User role assignment

### Helpers
- `app/Helpers/RoleHelper.php` - Helper functions for views

### Migrations
- `2026_03_01_000001_create_roles_table.php`
- `2026_03_01_000002_create_permissions_table.php`
- `2026_03_01_000003_create_role_permission_table.php`
- `2026_03_01_000004_create_user_role_table.php`

### Seeders
- `database/seeders/RolePermissionSeeder.php` - Seed roles and permissions

### Routes
- Role management routes under `/roles`
- User role assignment routes under `/users`

## Usage Examples

### In Controllers

```php
// Check role
if ($user->hasRole('admin')) {
    // Admin only code
}

// Check permission
if ($user->hasPermission('employee.create')) {
    // Allow employee creation
}

// Using gates
Gate::authorize('is-admin');
Gate::authorize('create-employee');

// Using middleware on routes
Route::post('/employees', [EmployeeController::class, 'store'])
    ->middleware('role:admin,hr_manager')
    ->middleware('permission:employee.create');
```

### In Blade Views

```blade
<!-- Check if user has role -->
@if (userHasRole('admin'))
    <div>Admin panel</div>
@endif

<!-- Check if user has permission -->
@can('create-employee')
    <button>Create Employee</button>
@endcan

<!-- Helper functions -->
@if (isAdmin())
    <div>Admin section</div>
@endif

@if (isHRManager())
    <div>HR Manager section</div>
@endif

@if (userHasPermission('employee.view'))
    <div>Employees list</div>
@endif
```

### Using Authorization Service

```php
class SomeController extends Controller
{
    public function __construct(private AuthorizationService $authService) {}

    public function index()
    {
        // Assign role
        $this->authService->assignRole($user, 'manager');

        // Check permission
        $hasPermission = $this->authService->hasPermission($user, 'employee.view');

        // Get user roles
        $roles = $this->authService->getRolesByUser($user);

        // Sync roles
        $this->authService->syncRoles($user, ['manager', 'employee']);
    }
}
```

## User Model Methods

### Role Methods
```php
// Check single role
$user->hasRole('admin');

// Check multiple roles (OR logic)
$user->hasAnyRole(['admin', 'hr_manager']);

// Check all roles (AND logic)
$user->hasAllRoles(['admin', 'manager']);

// Assign role
$user->assignRole('manager');
$user->assignRole($roleObject);

// Remove role
$user->removeRole('manager');

// Sync roles
$user->syncRoles(['admin', 'hr_manager']);
```

### Permission Methods
```php
// Check permission
$user->hasPermission('employee.create');
```

## API Endpoints

### Role Management
- `GET /roles` - List all roles
- `GET /roles/create` - Create role form
- `POST /roles` - Store new role
- `GET /roles/{id}/edit` - Edit role form
- `PATCH /roles/{id}` - Update role
- `DELETE /roles/{id}` - Delete role
- `GET /roles/{id}/users` - View users with role

### User Role Assignment
- `GET /users` - List all users with roles
- `GET /users/{id}/roles` - Edit user roles form
- `PATCH /users/{id}/roles` - Update user roles
- `POST /users/{id}/assign-role` - Assign single role
- `POST /users/{id}/remove-role` - Remove single role

## Authorization Gates (in AuthServiceProvider)

### Predefined Gates
```php
Gate::define('is-admin', fn($user) => $user->hasRole('admin'));
Gate::define('is-hr-manager', fn($user) => $user->hasRole('hr_manager'));
Gate::define('is-manager', fn($user) => $user->hasRole('manager'));
Gate::define('is-hr-officer', fn($user) => $user->hasRole('hr_officer'));
Gate::define('is-employee', fn($user) => $user->hasRole('employee'));

// Permission gates
Gate::define('view-employees', fn($user) => $user->hasPermission('employee.view'));
Gate::define('create-employee', fn($user) => $user->hasPermission('employee.create'));
// ... more gates
```

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate:fresh --seed
```

This will:
- Create all role and permission tables
- Seed default roles (Admin, HR Manager, Manager, HR Officer, Employee)
- Create all 27 permissions
- Assign permissions to roles

### 2. Create a Test User with Admin Role
```php
// In tinker
$user = User::first(); // Or create new user
$user->assignRole('admin');
```

### 3. Access Role Management
- Admin users can access `/roles` and `/users` for role management

## Security Considerations

1. **Default Deny**: Without a role/permission, users get access denied
2. **Middleware Validation**: All protected routes require proper roles/permissions
3. **Cascading Deletes**: Removing roles removes associated permissions
4. **Unique Constraints**: Prevents duplicate role-user and role-permission assignments
5. **Gate Authorization**: Factory method ensures consistent checks

## Best Practices

1. **Use Middleware for Route Protection**
   ```php
   Route::delete('/employees/{id}')
       ->middleware(['role:admin,hr_manager'])
       ->middleware(['permission:employee.delete']);
   ```

2. **Use Gates in Controllers**
   ```php
   $this->authorize('delete-employee');
   ```

3. **Use Helper Functions in Views**
   ```blade
   @if (userHasPermission('employee.create'))
       {{-- show create button --}}
   @endif
   ```

4. **Regularly Audit Permissions**
   - Review user role assignments
   - Monitor permission usage
   - Remove obsolete permissions

5. **Log Authorization Events**
   - Track role changes
   - Monitor failed authorization attempts
   - Maintain audit trail

## Future Enhancements

1. **Time-Based Roles**: Temporary role assignments
2. **Conditional Permissions**: Department-specific access
3. **Approval Workflows**: Multi-level authorization
4. **Audit Logging**: Complete action tracking
5. **Role/Permission UI**: Advanced management interface
6. **API Token Scopes**: TokenPermissions for API access
7. **Dynamic Permissions**: Runtime permission creation

## Troubleshooting

### Issue: "Unauthorized" errors
- Check user has required role: `$user->roles()->get()`
- Verify role has permission: `$role->permissions()->get()`
- Check middleware placement on routes

### Issue: Permissions not applying
- Clear config cache: `php artisan config:cache`
- Clear route cache: `php artisan route:cache`
- Verify RolePermissionSeeder ran successfully

### Issue: Helper functions not found
- Run: `composer dump-autoload`
- Check RoleHelper.php is in app/Helpers/

