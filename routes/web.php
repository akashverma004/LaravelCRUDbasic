<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeavePolicyController;
use App\Http\Controllers\OrgChartController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

// Protected routes - require authentication
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Organization Chart
    Route::prefix('org-chart')->group(function () {
        Route::get('/', [OrgChartController::class, 'index'])->name('org-chart.index');
        Route::get('/hierarchy', [OrgChartController::class, 'getHierarchy'])->name('org-chart.hierarchy');
        Route::get('/{id}', [OrgChartController::class, 'show'])->name('org-chart.show');
    });

    // Departments
    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('/', [DepartmentController::class, 'store'])->name('departments.store');
        Route::get('/{id}', [DepartmentController::class, 'show'])->name('departments.show');
        Route::get('/{id}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::patch('/{id}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    });

    // Employees
    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::post('/assign-manager', [EmployeeController::class, 'assignManager'])->name('employees.assign-manager');
        Route::get('/{id}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::patch('/{id}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('/search', [EmployeeController::class, 'search'])->name('employees.search');
    });

    // Leave Requests
    Route::prefix('leaves')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index'])->name('leaves.index');
        Route::get('/pending', [LeaveRequestController::class, 'pending'])->name('leaves.pending');
        Route::get('/create', [LeaveRequestController::class, 'create'])->name('leaves.create');
        Route::post('/', [LeaveRequestController::class, 'store'])->name('leaves.store');
        Route::get('/{id}', [LeaveRequestController::class, 'show'])->name('leaves.show');
        Route::patch('/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
        Route::patch('/{id}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');
    });

    // Policies
    Route::prefix('policies')->middleware('role:admin,hr_manager')->group(function () {
        Route::get('/leave', [LeavePolicyController::class, 'edit'])->name('policies.leave.edit');
        Route::patch('/leave', [LeavePolicyController::class, 'update'])->name('policies.leave.update');
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
});
