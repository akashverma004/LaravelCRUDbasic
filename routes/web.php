<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayCalendarController;
use App\Http\Controllers\HolidayPolicyController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeavePolicyController;
use App\Http\Controllers\PolicyManagementController;
use App\Http\Controllers\OrgChartController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TenantOnboardingController;
use App\Http\Controllers\TenantUserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\TenantManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForcePasswordChangeController;

// Password force change routes
Route::middleware(['auth'])->group(function () {
    Route::get('/password/force-change', [ForcePasswordChangeController::class, 'show'])->name('password.force-change.show');
    Route::post('/password/force-change', [ForcePasswordChangeController::class, 'store'])->name('password.force-change.store');
});

// Protected routes - require authentication and password change
Route::middleware(['auth', 'must.change.password', 'tenant', 'tenant.active', 'tenant.setup'])->group(function () {
    Route::get('/onboarding/setup', [TenantOnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding/setup', [TenantOnboardingController::class, 'store'])->name('onboarding.store');
    Route::get('/onboarding/departments', [TenantOnboardingController::class, 'showDepartments'])->name('onboarding.departments.show');
    Route::post('/onboarding/departments', [TenantOnboardingController::class, 'storeDepartments'])->name('onboarding.departments.store');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Organization Chart
    Route::prefix('org-chart')->group(function () {
        Route::get('/', [OrgChartController::class, 'index'])->name('org-chart.index');
        Route::get('/hierarchy', [OrgChartController::class, 'getHierarchy'])->name('org-chart.hierarchy');
        Route::get('/{id}', [OrgChartController::class, 'show'])->name('org-chart.show');
    });

    // Departments
    Route::prefix('departments')->group(function () {
        // Public (All Employees)
        Route::get('/', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/{id}', [DepartmentController::class, 'show'])->name('departments.show');

        // Protected (Admin/HR only)
        Route::middleware('role:admin,hr_manager')->group(function () {
            Route::get('/create', [DepartmentController::class, 'create'])->name('departments.create');
            Route::post('/', [DepartmentController::class, 'store'])->name('departments.store');
            Route::get('/{id}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
            Route::patch('/{id}', [DepartmentController::class, 'update'])->name('departments.update');
            Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
        });
    });

    // Employees
    Route::prefix('employees')->group(function () {
        // Public (All Employees)
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/search', [EmployeeController::class, 'search'])->name('employees.search');
        Route::get('/{id}', [EmployeeController::class, 'show'])->name('employees.show');

        // Protected (Admin/HR only)
        Route::middleware('role:admin,hr_manager')->group(function () {
            Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
            Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
            Route::post('/assign-manager', [EmployeeController::class, 'assignManager'])->name('employees.assign-manager');
            Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
            Route::patch('/{id}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        });
    });

    // Leave Requests
    Route::prefix('leaves')->group(function () {
        // Public (All Employees)
        Route::get('/', [LeaveRequestController::class, 'index'])->name('leaves.index');
        Route::get('/create', [LeaveRequestController::class, 'create'])->name('leaves.create');
        Route::post('/', [LeaveRequestController::class, 'store'])->name('leaves.store');
        Route::get('/events', [LeaveRequestController::class, 'events'])->name('leaves.events');
        Route::get('/{id}', [LeaveRequestController::class, 'show'])->name('leaves.show');

        // Protected (Admin/HR only)
        Route::middleware('role:admin,hr_manager')->group(function () {
            Route::get('/pending', [LeaveRequestController::class, 'pending'])->name('leaves.pending');
            Route::patch('/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
            Route::patch('/{id}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');
        });
    });

    // Policies
    Route::prefix('policies')->middleware('role:admin,hr_manager')->group(function () {
        Route::get('/', [PolicyManagementController::class, 'index'])->name('policies.index');
        Route::get('/holiday-policies', [HolidayPolicyController::class, 'index'])->name('policies.holiday-policies.index');
        Route::post('/holiday-policies', [HolidayPolicyController::class, 'store'])->name('policies.holiday-policies.store');
        Route::patch('/holiday-policies/{holidayPolicy}', [HolidayPolicyController::class, 'update'])->name('policies.holiday-policies.update');
        Route::delete('/holiday-policies/{holidayPolicy}', [HolidayPolicyController::class, 'destroy'])->name('policies.holiday-policies.destroy');
        Route::get('/holiday-calendar', [HolidayCalendarController::class, 'index'])->name('policies.holiday-calendar.index');
        Route::post('/holiday-calendar/policies/{holidayPolicy}/dates', [HolidayCalendarController::class, 'storeDate'])->name('policies.holiday-calendar.dates.store');
        Route::patch('/holiday-calendar/policies/{holidayPolicy}/dates/{holidayDate}', [HolidayCalendarController::class, 'updateDate'])->name('policies.holiday-calendar.dates.update');
        Route::delete('/holiday-calendar/policies/{holidayPolicy}/dates/{holidayDate}', [HolidayCalendarController::class, 'destroyDate'])->name('policies.holiday-calendar.dates.destroy');
        Route::get('/leave', [LeavePolicyController::class, 'edit'])->name('policies.leave.edit');
        Route::patch('/leave', [LeavePolicyController::class, 'update'])->name('policies.leave.update');
        Route::get('/{type}', [PolicyManagementController::class, 'edit'])->name('policies.edit');
        Route::patch('/{type}', [PolicyManagementController::class, 'update'])->name('policies.update');
    });

    // Roles & Permissions Management
    Route::prefix('roles')->middleware('role:admin,hr_manager')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::patch('/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/{role}/users', [RoleController::class, 'users'])->name('roles.users');
    });

    // User Roles Management
    Route::prefix('users')->middleware('role:admin,hr_manager')->group(function () {
        Route::get('/', [UserRoleController::class, 'index'])->name('users.index');
        Route::get('/{user}/roles', [UserRoleController::class, 'edit'])->name('users.edit-roles');
        Route::patch('/{user}/roles', [UserRoleController::class, 'update'])->name('users.update-roles');
        Route::post('/{user}/assign-role', [UserRoleController::class, 'assignRole'])->name('users.assign-role');
        Route::post('/{user}/remove-role', [UserRoleController::class, 'removeRole'])->name('users.remove-role');
    });

    // Tenant users and invitations
    Route::prefix('tenant-users')->middleware('role:admin,hr_manager')->group(function () {
        Route::get('/', [TenantUserController::class, 'index'])->name('tenant-users.index');
        Route::post('/create', [TenantUserController::class, 'store'])->name('tenant-users.store');
        Route::post('/invite', [TenantUserController::class, 'invite'])->name('tenant-users.invite');
    });
});

Route::middleware(['auth', 'can:manage-tenants'])->prefix('platform/tenants')->group(function () {
    Route::get('/', [TenantManagementController::class, 'index'])->name('tenants.index');
    Route::get('/create', [TenantManagementController::class, 'create'])->name('tenants.create');
    Route::post('/', [TenantManagementController::class, 'store'])->name('tenants.store');
    Route::get('/{tenant}/edit', [TenantManagementController::class, 'edit'])->name('tenants.edit');
    Route::patch('/{tenant}', [TenantManagementController::class, 'update'])->name('tenants.update');
    Route::delete('/{tenant}', [TenantManagementController::class, 'destroy'])->name('tenants.destroy');
});
