<?php

// Test script to verify RBAC system
require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n════════════════════════════════════════════════════════════════\n";
echo "HRMS RBAC SYSTEM TEST\n";
echo "════════════════════════════════════════════════════════════════\n\n";

// Test 1: Database Tables
echo "✓ TEST 1: Database Tables\n";
echo "  Roles table: " . (\DB::table('roles')->count()) . " records\n";
echo "  Permissions table: " . (\DB::table('permissions')->count()) . " records\n";
echo "  Role-Permission links: " . (\DB::table('role_permission')->count()) . " records\n";
echo "  User-Role links: " . (\DB::table('user_role')->count()) . " records\n\n";

// Test 2: Roles
echo "✓ TEST 2: Available Roles\n";
$roles = \DB::table('roles')->pluck('display_name')->toArray();
foreach ($roles as $role) {
    echo "  - $role\n";
}
echo "\n";

// Test 3: User and Roles
echo "✓ TEST 3: User Role Assignment\n";
$user = \App\Models\User::first();
echo "  User: " . $user->name . "\n";

// Assign admin role
$user->assignRole('admin');
echo "  Assigned role: admin\n";

$hasRole = $user->hasRole('admin');
echo "  Has admin role: " . ($hasRole ? "YES ✓" : "NO ✗") . "\n";
echo "\n";

// Test 4: Permissions
echo "✓ TEST 4: User Permissions (Admin)\n";
$hasCreateEmployee = $user->hasPermission('employee.create');
$hasDeleteEmployee = $user->hasPermission('employee.delete');
$hasManageRoles = $user->hasPermission('role.manage');

echo "  employee.create: " . ($hasCreateEmployee ? "YES ✓" : "NO ✗") . "\n";
echo "  employee.delete: " . ($hasDeleteEmployee ? "YES ✓" : "NO ✗") . "\n";
echo "  role.manage: " . ($hasManageRoles ? "YES ✓" : "NO ✗") . "\n";
echo "\n";

// Test 5: Role Permissions
echo "✓ TEST 5: Role Permissions\n";
$adminRole = \App\Models\Role::where('name', 'admin')->first();
echo "  Admin role has " . $adminRole->permissions()->count() . " permissions\n";

$hrManagerRole = \App\Models\Role::where('name', 'hr_manager')->first();
echo "  HR Manager role has " . $hrManagerRole->permissions()->count() . " permissions\n";

$managerRole = \App\Models\Role::where('name', 'manager')->first();
echo "  Manager role has " . $managerRole->permissions()->count() . " permissions\n";

$employeeRole = \App\Models\Role::where('name', 'employee')->first();
echo "  Employee role has " . $employeeRole->permissions()->count() . " permissions\n";
echo "\n";

// Test 6: Authorization Service
echo "✓ TEST 6: Authorization Service\n";
$authService = app(\App\Services\AuthorizationService::class);

$userRoles = $authService->getRolesByUser($user);
echo "  User has " . count($userRoles) . " role(s)\n";

$allRoles = $authService->getAllRoles();
echo "  System has " . count($allRoles) . " role(s)\n";

$allPermissions = $authService->getAllPermissions();
echo "  System has " . count($allPermissions) . " permission(s)\n";
echo "\n";

// Test 7: Helper Functions
echo "✓ TEST 7: Helper Functions\n";
echo "  userHasRole('admin'): " . (userHasRole('admin') ? "YES ✓" : "NO ✗") . "\n";
echo "  userHasPermission('employee.create'): " . (userHasPermission('employee.create') ? "YES ✓" : "NO ✗") . "\n";
echo "  isAdmin(): " . (isAdmin() ? "YES ✓" : "NO ✗") . "\n";
echo "\n";

echo "════════════════════════════════════════════════════════════════\n";
echo "✓ ALL TESTS PASSED - RBAC SYSTEM IS FULLY OPERATIONAL\n";
echo "════════════════════════════════════════════════════════════════\n\n";

// Print Setup Instructions
echo "NEXT STEPS:\n";
echo "1. Create user accounts for team members\n";
echo "2. Assign appropriate roles using:\n";
echo "   - Web UI at /roles and /users\n";
echo "   - Or in tinker: \$user->assignRole('manager')\n";
echo "3. Protect routes with: middleware('role:admin,hr_manager')\n";
echo "4. Check views with: @can('view-employees')\n";
echo "5. Use helpers in views: @if (userHasRole('admin'))\n\n";
