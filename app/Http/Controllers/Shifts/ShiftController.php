<?php

namespace App\Http\Controllers\Shifts;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index(): View
    {
        return view('hrms.shifts.index');
    }

    public function data(Request $request): JsonResponse
    {
        $tenantId = TenantContext::id();
        $user = auth()->user();
        
        // Default to current week
        $start = $request->filled('start') ? Carbon::parse($request->start) : now()->startOfWeek();
        $end = $request->filled('end') ? Carbon::parse($request->end) : now()->endOfWeek();

        $shifts = Shift::where('tenant_id', $tenantId)->get();
        
        $query = ShiftSchedule::where('tenant_id', $tenantId)
            ->whereBetween('date', [$start, $end])
            ->with(['employee:id,full_name', 'shift']);

        // Non-admins only see their own roster
        if (!$user->hasAnyRole(['admin', 'hr_manager'])) {
            $employee = Employee::where('email', $user->email)->where('tenant_id', $tenantId)->first();
            $query->where('employee_id', $employee?->id ?? 0);
        }

        $schedules = $query->get();

        $employees = [];
        if ($user->hasAnyRole(['admin', 'hr_manager'])) {
            $employees = Employee::where('tenant_id', $tenantId)->get(['id', 'full_name']);
        }

        return response()->json([
            'shifts' => $shifts,
            'schedules' => $schedules,
            'employees' => $employees,
            'isAdmin' => $user->hasAnyRole(['admin', 'hr_manager']),
            'period' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'label' => $start->format('d M') . ' - ' . $end->format('d M Y')
            ]
        ]);
    }

    public function storeShift(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'color' => 'nullable|string',
        ]);

        $shift = Shift::create(array_merge($validated, [
            'tenant_id' => TenantContext::id()
        ]));

        return response()->json(['success' => true, 'shift' => $shift]);
    }

    public function assign(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $schedule = ShiftSchedule::create(array_merge($validated, [
            'tenant_id' => TenantContext::id()
        ]));

        return response()->json(['success' => true, 'schedule' => $schedule->load(['employee:id,full_name', 'shift'])]);
    }

    public function destroy(ShiftSchedule $schedule): JsonResponse
    {
        if (!auth()->user()->hasAnyRole(['admin', 'hr_manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $schedule->delete();
        return response()->json(['success' => true]);
    }
}
