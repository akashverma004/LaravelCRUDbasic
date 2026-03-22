<?php

namespace App\Http\Controllers\Hrms;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Support\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceManagementController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->hasAnyRole(['admin', 'hr_manager']), 403, 'Unauthorized.');
        return view('hrms.attendance.index');
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->hasAnyRole(['admin', 'hr_manager']), 403, 'Unauthorized.');

        $tenantId = TenantContext::id();

        $query = AttendanceRecord::where('tenant_id', $tenantId)
            ->with('employee:id,full_name,job_title,status')
            ->orderBy('attendance_date', 'desc')
            ->orderBy('clock_in_at', 'desc');

        // Date filtering
        if ($request->filled('date')) {
            $query->where('attendance_date', $request->date);
        } else {
            // Default to past 7 days
            $query->where('attendance_date', '>=', now()->subDays(6)->toDateString());
        }

        // Search filtering
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('employee', function ($empQ) use ($q) {
                $empQ->where('full_name', 'like', "%{$q}%")
                     ->orWhere('job_title', 'like', "%{$q}%");
            });
        }

        $records = $query->get()->map(function ($record) {
            $workedHours = $record->total_work_seconds / 3600;
            return [
                'id' => $record->id,
                'employee' => $record->employee,
                'date' => $record->attendance_date->format('Y-m-d'),
                'clock_in_at' => $record->clock_in_at ? Carbon::parse($record->clock_in_at)->format('H:i') : null,
                'clock_out_at' => $record->clock_out_at ? Carbon::parse($record->clock_out_at)->format('H:i') : null,
                'work_mode' => $record->work_mode,
                'status' => $record->status,
                'total_seconds' => $record->total_work_seconds,
                'total_hours_formatted' => floor($workedHours) . 'h ' . round(($workedHours - floor($workedHours)) * 60) . 'm',
                'intervals' => $record->intervals ?? [],
            ];
        });

        $stats = [
            'total_hours' => round($records->sum('total_seconds') / 3600, 1),
            'present_today' => AttendanceRecord::where('tenant_id', $tenantId)->where('attendance_date', now()->toDateString())->count(),
            'active_now' => AttendanceRecord::where('tenant_id', $tenantId)->where('attendance_date', now()->toDateString())->where('status', 'clocked_in')->count(),
        ];

        return response()->json([
            'records' => $records,
            'stats' => $stats,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->hasAnyRole(['admin', 'hr_manager']), 403, 'Unauthorized.');

        $validated = $request->validate([
            'record_id' => 'required|exists:attendance_records,id',
            'clock_in_at' => 'nullable|date_format:H:i',
            'clock_out_at' => 'nullable|date_format:H:i',
        ]);

        $record = AttendanceRecord::where('tenant_id', TenantContext::id())
            ->findOrFail($validated['record_id']);

        // Update basic fields
        $data = [];
        if (isset($validated['clock_in_at'])) $data['clock_in_at'] = $validated['clock_in_at'];
        if (isset($validated['clock_out_at'])) $data['clock_out_at'] = $validated['clock_out_at'];
        
        $record->update($data);

        // If intervals exist, adjust them so total_work_seconds recalculates correctly.
        // For simplicity: if HR overrides clock_in/out, we replace intervals with a single block.
        if (isset($validated['clock_in_at']) && isset($validated['clock_out_at'])) {
            $datePrefix = $record->attendance_date->format('Y-m-d') . ' ';
            $start = Carbon::parse($datePrefix . $validated['clock_in_at']);
            $end = Carbon::parse($datePrefix . $validated['clock_out_at']);

            $intervals = [
                ['type' => 'work', 'start' => $start->toDateTimeString(), 'end' => $end->toDateTimeString()]
            ];

            $record->update([
                'intervals' => $intervals,
                'status' => 'completed'
            ]);

            $record->refresh()->updateCalculatedSeconds();
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance record updated.'
        ]);
    }
}
