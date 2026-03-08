<?php

namespace App\Http\Middleware;

use App\Models\Department;
use App\Support\TenantContext;
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

        // Step 1: Company info not completed yet
        if (! $tenant->setup_completed) {
            return redirect()->route('onboarding.show');
        }

        // Step 2: Company info done but no departments created yet
        TenantContext::set((int) $tenant->id);
        $hasDepartments = Department::query()->exists();
        TenantContext::set(null);

        if (! $hasDepartments) {
            return redirect()->route('onboarding.departments.show');
        }

        return $next($request);
    }
}
