<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(): View
    {
        return view('hrms.audit.index');
    }

    public function data(): JsonResponse
    {
        $tenantId = TenantContext::id();

        $logs = ActivityLog::where('tenant_id', $tenantId)
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(50);

        return response()->json([
            'logs' => $logs
        ]);
    }
}
