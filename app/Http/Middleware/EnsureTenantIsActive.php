<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTenantIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        if ($user->is_platform_admin) {
            return $next($request);
        }

        $tenant = $user->tenant;
        if (! $tenant || ! $tenant->is_active) {
            auth()->guard('web')->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your company workspace is inactive. Please contact support.',
            ]);
        }

        return $next($request);
    }
}
