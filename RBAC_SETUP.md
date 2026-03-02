# HRMS Role-Based Application - Setup Guide

## What Has Been Implemented

### 1. **Database Models** ✓
- `Role` model with role ↔ permission relationships
- `Permission` model with permission ↔ role relationships
- Enhanced `User` model with role/permission methods

### 2. **Database Migrations** ✓
- `2026_03_01_000001_create_roles_table.php`
- `2026_03_01_000002_create_permissions_table.php`
- `2026_03_01_000003_create_role_permission_table.php`
- `2026_03_01_000004_create_user_role_table.php`

### 3. **Authorization System** ✓
- `AuthorizationService` - Centralized authorization logic
- `CheckRole` middleware - Role verification on routes
- `CheckPermission` middleware - Permission verification on routes
- Gates in `AuthServiceProvider` - Declarative authorization

### 4. **User Management** ✓
- User role assignment methods
- Role and permission checking methods
- Helper functions in `RoleHelper.php`

### 5. **Controllers for Management** ✓
- `RoleController` - Create, read, update, delete roles
- `UserRoleController` - Assign/manage user roles

### 6. **Routes** ✓
- `/roles` - Role management (protected: admin, hr_manager)
- `/users` - User role management (protected: admin, hr_manager)

### 7. **Seeder** ✓
- `RolePermissionSeeder` - Seeds 5 roles and 27 permissions

## 5 Core Roles Defined

### 1. **Admin**
- Full system access
- All permissions granted
- Can manage roles and permissions

### 2. **HR Manager**
- Employee management (CRUD operations)
- Leave request management and approvals
- Department management
- Attendance tracking
- View reports and dashboard

### 3. **Manager** (Team Lead)
- View and approve team leave requests
- View team member information
- View attendance records
- Limited to team members only

### 4. **HR Officer**
- Support HR operations
- Employee and attendancerecord management
- Leave request handling
- Cannot delete records
- Support role only

### 5. **Employee**
- View own information
- Create personal leave requests
- View dashboard
- Limited to own data

## 27 Permissions Across 5 Modules

### Employees (5 permissions)
- employee.view
- employee.create
- employee.edit
- employee.delete
- employee.export

### Departments (4 permissions)
- department.view
- department.create
- department.edit
- department.delete

### Leaves (6 permissions)
- leave.view
- leave.create
- leave.edit
- leave.delete
- leave.approve
- leave.reject

### Attendance (4 permissions)
- attendance.view
- attendance.create
- attendance.edit
- attendance.delete

### System (5 permissions)
- dashboard.view
- report.view
- report.export
- role.manage
- permission.manage

## Quick Setup Steps

### Step 1: Run Migrations
```bash
php artisan migrate:fresh --seed
```

### Step 2: Seed Roles and Permissions
```bash
php artisan db:seed --class=RolePermissionSeeder
```

### Step 3: Assign Admin Role to Test User
```bash
php artisan tinker
> $user = User::first();
> $user->assignRole('admin');
> exit
```

### Step 4: Clear Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Usage in Application

### In Controllers
```php
// Check role
if (auth()->user()->hasRole('admin')) {
    // Admin code
}

// Check permission
if (auth()->user()->hasPermission('employee.create')) {
    // Create employee code
}

// Using authorization service
$authService->syncRoles($user, ['hr_manager', 'manager']);
```

### In Blade Views
```blade
<!-- Helper functions -->
@if (isAdmin())
    <div class="admin-panel">Admin Panel</div>
@endif

@if (userHasPermission('employee.create'))
    <button>Create Employee</button>
@endif

<!-- Laravel gates -->
@can('create-employee')
    <a href="{{ route('employees.create') }}">Create</a>
@endcan
```

### Route Protection
```php
// Using middleware
Route::post('/employees', [EmployeeController::class, 'store'])
    ->middleware('role:admin,hr_manager')
    ->middleware('permission:employee.create');

// Routes already protected
Route::prefix('roles')->middleware(['auth', 'role:admin,hr_manager'])->group(function () {
    // Role management routes
});
```

## File Structure

```
app/
├── Models/
│   ├── Role.php                    (Role model)
│   ├── Permission.php              (Permission model)
│   └── User.php                    (Enhanced with role methods)
├── Services/
│   └── AuthorizationService.php    (Authorization logic)
├── Http/
│   ├── Controllers/
│   │   ├── RoleController.php      (Role management)
│   │   └── UserRoleController.php  (User role assignment)
│   ├── Middleware/
│   │   ├── CheckRole.php           (Role middleware)
│   │   └── CheckPermission.php     (Permission middleware)
│   └── Kernel.php                  (Updated with role middleware)
├── Helpers/
│   └── RoleHelper.php              (Helper functions)
├── Providers/
│   └── AuthServiceProvider.php     (Gates configuration)
└── Helpers/
    └── RoleHelper.php              (View helper functions)

database/
├── migrations/
│   ├── 2026_03_01_000001_create_roles_table.php
│   ├── 2026_03_01_000002_create_permissions_table.php
│   ├── 2026_03_01_000003_create_role_permission_table.php
│   └── 2026_03_01_000004_create_user_role_table.php
└── seeders/
    └── RolePermissionSeeder.php    (Seed roles & permissions)

routes/
└── web.php                         (Routes updated with role management)
```

## Key Features

### ✓ Role-Based Access Control (RBAC)
- Predefined roles with specific permissions
- Flexible permission assignment
- User can have multiple roles

### ✓ Granular Permissions
- Module-based organization
- 27 distinct permissions
- Easy to add new permissions

### ✓ Multiple Authorization Methods
- Middleware for routes
- Gates for controllers
- Helper functions for views
- Direct model methods

### ✓ Role Management UI
- Create new roles
- Assign permissions to roles
- View users with specific roles
- Delete unused roles

### ✓ User Role Management
- Assign roles to users
- Remove roles from users
- View user permissions

### ✓ Complete Documentation
- RBAC_DOCUMENTATION.md
- API endpoints documented
- Usage examples provided

## Security Features

1. **Default Deny** - Users get access denied without proper role/permission
2. **Middleware Validation** - All protected routes verified
3. **Cascading Deletes** - Removing roles cleans up permissions
4. **Unique Constraints** - Prevents duplicate assignments
5. **Gate Authorization** - Consistent authorization checks

## Next Steps

1. Run migrations to create tables
2. Seed default roles and permissions
3. Assign roles to existing users
4. Customize permissions as needed
5. Implement row-level authorization for specific resources
6. Add audit logging for security tracking
7. Create role-based UI components

## Important Files Modified

- `app/Models/User.php` - Added role/permission methods
- `app/Http/Kernel.php` - Added role and permission middleware aliases
- `composer.json` - Added RoleHelper to autoload files
- `routes/web.php` - Added role and user management routes
- `app/Providers/AuthServiceProvider.php` - Configured gates

## Important Files Created

- 4 Migration files for database schema
- 2 Model files (Role, Permission) 
- 1 Service file (AuthorizationService)
- 2 Middleware files (CheckRole, CheckPermission)
- 2 Controller files (RoleController, UserRoleController)
- 1 Seeder file (RolePermissionSeeder)
- 1 Helper file (RoleHelper)
- 2 Documentation files

## Testing the System

### Test Admin Access
```bash
php artisan tinker
> $admin = User::find(1);  // Or create new user
> $admin->assignRole('admin');
> $admin->hasRole('admin');  // Should return true
> $admin->hasPermission('employee.create');  // Should return true
```

### Test Employee Access
```bash
php artisan tinker
> $emp = User::find(2);
> $emp->assignRole('employee');
> $emp->hasRole('employee');  // Should return true
> $emp->hasPermission('employee.create');  // Should return false
> $emp->hasPermission('leave.create');  // Should return true
```

## Troubleshooting

**Q: Getting "Class not found" errors**
A: Run `composer dump-autoload` to regenerate autoloader

**Q: Middleware not working**
A: Clear route cache: `php artisan route:cache` (or `:clear`)

**Q: Helper functions not available in views**
A: Run `composer dump-autoload` and clear view cache

**Q: Permissions not applying**
A: Verify RolePermissionSeeder ran: Check roles and permissions tables

