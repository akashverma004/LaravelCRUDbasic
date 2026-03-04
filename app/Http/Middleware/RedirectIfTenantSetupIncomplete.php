<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfTenantSetupIncomplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user || $user->is_platform_admin) {
            return $next($request);
        }

        $tenant = $user->tenant;
        if (! $tenant) {
            return $next($request);
        }

        $allowedRoutes = [
            'logout',
            'profile.*',
            'onboarding.*',
            'tenant-invitations.accept',
            'tenant-invitations.store-acceptance',
        ];

        foreach ($allowedRoutes as $pattern) {
            if ($request->routeIs($pattern)) {
                return $next($request);
            }
        }

        if (! $tenant->setup_completed) {
            return redirect()->route('onboarding.show');
        }

        return $next($request);
    }
}
