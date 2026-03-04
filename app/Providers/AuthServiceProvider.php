<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register role gates
        Gate::define('is-admin', fn($user) => $user->hasRole('admin'));
        Gate::define('is-hr-manager', fn($user) => $user->hasRole('hr_manager'));
        Gate::define('is-manager', fn($user) => $user->hasRole('manager'));
        Gate::define('is-hr-officer', fn($user) => $user->hasRole('hr_officer'));
        Gate::define('is-employee', fn($user) => $user->hasRole('employee'));

        // Register permission gates
        Gate::define('view-employees', fn($user) => $user->hasPermission('employee.view'));
        Gate::define('create-employee', fn($user) => $user->hasPermission('employee.create'));
        Gate::define('edit-employee', fn($user) => $user->hasPermission('employee.edit'));
        Gate::define('delete-employee', fn($user) => $user->hasPermission('employee.delete'));

        Gate::define('view-departments', fn($user) => $user->hasPermission('department.view'));
        Gate::define('create-department', fn($user) => $user->hasPermission('department.create'));
        Gate::define('edit-department', fn($user) => $user->hasPermission('department.edit'));
        Gate::define('delete-department', fn($user) => $user->hasPermission('department.delete'));

        Gate::define('view-leaves', fn($user) => $user->hasPermission('leave.view'));
        Gate::define('create-leave', fn($user) => $user->hasPermission('leave.create'));
        Gate::define('approve-leave', fn($user) => $user->hasPermission('leave.approve'));
        Gate::define('reject-leave', fn($user) => $user->hasPermission('leave.reject'));

        Gate::define('view-attendance', fn($user) => $user->hasPermission('attendance.view'));
        Gate::define('create-attendance', fn($user) => $user->hasPermission('attendance.create'));

        Gate::define('view-dashboard', fn($user) => $user->hasPermission('dashboard.view'));
        Gate::define('view-reports', fn($user) => $user->hasPermission('report.view'));

        Gate::define('manage-roles', fn($user) => $user->hasPermission('role.manage'));
        Gate::define('manage-tenants', fn($user) => (bool) $user->is_platform_admin);
    }
}
