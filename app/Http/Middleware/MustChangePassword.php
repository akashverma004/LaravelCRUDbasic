<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->require_password_change) {
            if (! $request->routeIs('password.force-change.*') && ! $request->routeIs('logout')) {
                return redirect()->route('password.force-change.show')
                    ->with('warning', 'For security reasons, you must change your password before continuing.');
            }
        }

        return $next($request);
    }
}
