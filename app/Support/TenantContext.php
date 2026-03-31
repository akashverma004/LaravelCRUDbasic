<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

final class TenantContext
{
    public static function id(): ?int
    {
        if (app()->bound('tenant.id')) {
            $tenantId = app('tenant.id');

            return is_numeric($tenantId) ? (int) $tenantId : null;
        }

        // Avoid lazy-loading auth user here: this method is used by tenant global scopes.
        // Calling Auth::user() during User model resolution can recurse and exhaust memory.
        $guard = Auth::guard();
        if (method_exists($guard, 'hasUser') && $guard->hasUser()) {
            $user = $guard->user();
            if ($user && isset($user->tenant_id) && is_numeric($user->tenant_id)) {
                return (int) $user->tenant_id;
            }
        }

        return null;
    }

    public static function set(?int $tenantId): void
    {
        if ($tenantId === null) {
            if (app()->bound('tenant.id')) {
                app()->forgetInstance('tenant.id');
            }

            return;
        }

        app()->instance('tenant.id', $tenantId);
    }

    /**
     * Alias for set() for more descriptive usage.
     */
    public static function setId(?int $tenantId): void
    {
        self::set($tenantId);
    }

    private function __construct()
    {
    }
}
