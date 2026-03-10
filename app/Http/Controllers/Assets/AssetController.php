<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Employee;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(): View
    {
        return view('hrms.assets.index');
    }

    public function data(Request $request): JsonResponse
    {
        $tenantId = TenantContext::id();
        $user = auth()->user();

        $query = Asset::where('tenant_id', $tenantId);

        // Regular employees only see assets assigned to them
        if (!$user->hasAnyRole(['admin', 'hr_manager'])) {
            $employee = Employee::where('email', $user->email)->where('tenant_id', $tenantId)->first();
            $query->where('employee_id', $employee?->id ?? 0);
        }

        $assets = $query->with('employee:id,full_name')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'assets' => $assets,
            'categories' => Asset::categories(),
            'isAdmin' => $user->hasAnyRole(['admin', 'hr_manager'])
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->hasAnyRole(['admin', 'hr_manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(Asset::categories())),
            'serial_number' => 'nullable|string|max:255',
            'employee_id' => 'nullable|exists:employees,id',
            'status' => 'required|string|in:available,assigned,damaged,lost,retired',
            'notes' => 'nullable|string',
        ]);

        $asset = Asset::create(array_merge($validated, [
            'tenant_id' => TenantContext::id(),
            'assigned_at' => $validated['employee_id'] ? now() : null
        ]));

        return response()->json(['success' => true, 'asset' => $asset]);
    }

    public function update(Request $request, Asset $asset): JsonResponse
    {
        if (!auth()->user()->hasAnyRole(['admin', 'hr_manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:available,assigned,damaged,lost,retired',
            'employee_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        $asset->update(array_merge($validated, [
            'assigned_at' => ($validated['employee_id'] && !$asset->employee_id) ? now() : $asset->assigned_at
        ]));

        return response()->json(['success' => true]);
    }
}
