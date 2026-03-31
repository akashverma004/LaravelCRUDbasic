<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\TenantOnboardingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForcePasswordChangeController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DepartmentController;

// Public Marketing Routes (Livewire 3)
Route::get('/features', \App\Livewire\Public\Features::class)->name('public.features');
Route::get('/solutions', \App\Livewire\Public\Solutions::class)->name('public.solutions');
Route::get('/pricing', \App\Livewire\Public\Pricing::class)->name('public.pricing');
Route::get('/docs', \App\Livewire\Public\Docs::class)->name('public.docs');

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

    // Dashboard (Livewire 3 SPA)
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard.home');
    Route::get('/', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('/dashboard/leave-trend-data', [DashboardController::class, 'leaveTrendData'])->name('dashboard.leave-trend-data');

    // Attendance Punch In / Out
    Route::prefix('attendance')->group(function () {
        Route::get('/my', \App\Livewire\Attendance\AttendanceDashboard::class)->name('attendance.my');
        Route::post('/punch-in', [\App\Http\Controllers\AttendanceController::class, 'punchIn'])->name('attendance.punch-in');
        Route::post('/pause', [\App\Http\Controllers\AttendanceController::class, 'pause'])->name('attendance.pause');
        Route::post('/resume', [\App\Http\Controllers\AttendanceController::class, 'resume'])->name('attendance.resume');
        Route::post('/punch-out', [\App\Http\Controllers\AttendanceController::class, 'punchOut'])->name('attendance.punch-out');
    });

    // Organization Chart
    Route::prefix('org-chart')->group(function () {
        Route::get('/', \App\Livewire\OrgChart::class)->name('org-chart.index');
    });

    // Departments
    Route::prefix('departments')->group(function () {
        // Public (All Employees)
        Route::get('/', \App\Livewire\Organization\DepartmentManager::class)->name('departments.index');
        Route::get('/{id}', \App\Livewire\Organization\DepartmentProfile::class)->name('departments.show')->where('id', '[0-9]+');
        Route::post('/', [DepartmentController::class, 'store'])->name('departments.store');
        Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy')->where('id', '[0-9]+');
    });

    // Employees
    Route::prefix('employees')->group(function () {
        // Public (All Employees) - Livewire 3
        Route::get('/', \App\Livewire\Employees\EmployeeDirectory::class)->name('employees.index');
        Route::get('/search', [EmployeeController::class, 'search'])->name('employees.search');
        Route::get('/{id}', \App\Livewire\Employees\EmployeeProfile::class)->name('employees.show')->where('id', '[0-9]+');

        // Protected (Admin/HR only)
        Route::middleware('role:admin,hr_manager')->group(function () {
            // Livewire 3
            Route::get('/create', \App\Livewire\Employees\EmployeeForm::class)->name('employees.create');
            
            Route::post('/assign-manager', [EmployeeController::class, 'assignManager'])->name('employees.assign-manager');
        });
    });

    // Leave Requests
    Route::prefix('leaves')->group(function () {
        // Protected (Admin/HR only)
        Route::middleware('role:admin,hr_manager')->group(function () {
            Route::get('/pending', \App\Livewire\Leaves\LeaveApprovals::class)->name('leaves.pending');
        });

        // Public (All Employees)
        Route::get('/', \App\Livewire\Leaves\LeaveCalendar::class)->name('leaves.index');
        Route::get('/my', \App\Livewire\Leaves\LeaveDashboard::class)->name('leaves.my');
        Route::get('/events', [LeaveRequestController::class, 'events'])->name('leaves.events');
        
        // Resource-like routes (scoped by ownership in controller)
        Route::get('/{id}', [LeaveRequestController::class, 'show'])->name('leaves.show')->where('id', '[0-9]+');
        Route::delete('/{id}', [LeaveRequestController::class, 'destroy'])->name('leaves.destroy')->where('id', '[0-9]+');

        // Actions requiring specific role
        Route::middleware('role:admin,hr_manager')->group(function () {
            Route::patch('/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
            Route::patch('/{id}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');
        });
    });

    // Policies
    Route::prefix('policies')->group(function () {
        // Public (All Employees) - Policy Viewer (Livewire 3)
        Route::get('/my-policies', \App\Livewire\Policies\MyPolicies::class)->name('policies.viewer');

        // Protected (Admin/HR only) - Policy Management (Livewire 3)
        Route::middleware(['role:admin,hr_manager'])->group(function () {
            Route::get('/', \App\Livewire\Policies\PolicyManager::class)->name('policies.index');
            
            // Holiday Policies & Calendar (Livewire 3)
            Route::get('/holiday-governance', \App\Livewire\Policies\HolidayHub::class)->name('policies.holiday-policies.index');
            Route::get('/holiday-calendar', \App\Livewire\Policies\HolidayHub::class)->name('policies.holiday-calendar.index');
        });
    });

    // Privilege Architecture (Livewire 3)
    Route::get('/roles-users', \App\Livewire\Settings\AclManager::class)->name('roles.index');
    Route::get('/users-mapping', \App\Livewire\Settings\AclManager::class)->name('users.index');

    // Tenant users and invitations
    // User Lattice (Livewire 3)
    Route::prefix('tenant-users')->middleware('role:admin,hr_manager')->group(function () {
        Route::get('/', \App\Livewire\Lattice\UserStation::class)->name('tenant-users.index');
    });
});

Route::middleware(['auth', 'can:manage-tenants'])->prefix('platform/tenants')->group(function () {
    Route::get('/', \App\Livewire\Platform\Workspaces::class)->name('tenants.index');
});
