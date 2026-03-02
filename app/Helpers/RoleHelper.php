<?php

// Helper functions for role and permission checking in Blade views

if (!function_exists('userHasRole')) {
    function userHasRole(string|array $roles): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->hasRole($roles);
    }
}

if (!function_exists('userHasPermission')) {
    function userHasPermission(string $permission): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->hasPermission($permission);
    }
}

if (!function_exists('userHasAnyRole')) {
    function userHasAnyRole(array $roles): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->hasAnyRole($roles);
    }
}

if (!function_exists('userHasAllRoles')) {
    function userHasAllRoles(array $roles): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->hasAllRoles($roles);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return userHasRole('admin');
    }
}

if (!function_exists('isHRManager')) {
    function isHRManager(): bool
    {
        return userHasRole('hr_manager');
    }
}

if (!function_exists('isManager')) {
    function isManager(): bool
    {
        return userHasRole('manager');
    }
}

if (!function_exists('isEmployee')) {
    function isEmployee(): bool
    {
        return userHasRole('employee');
    }
}
