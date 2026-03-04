<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;

class SetTenantContext
{
    public function handle(Request $request, Closure $next)
    {
        $tenantId = null;

        if ($request->user() && is_numeric($request->user()->tenant_id)) {
            $tenantId = (int) $request->user()->tenant_id;
        } elseif ($request->hasHeader('X-Tenant-Id') && is_numeric($request->header('X-Tenant-Id'))) {
            $tenantId = (int) $request->header('X-Tenant-Id');
        }

        TenantContext::set($tenantId);
        try {
            return $next($request);
        } finally {
            TenantContext::set(null);
        }
    }
}
