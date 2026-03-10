<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Support\TenantContext;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        return view('hrms.analytics.index');
    }

    public function data(): JsonResponse
    {
        $tenantId = TenantContext::id();

        // 1. Headcount Growth (Last 6 Months)
        $headcountTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Employee::where('tenant_id', $tenantId)
                ->where('created_at', '<=', $month->endOfMonth())
                ->count();
            $headcountTrend[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        // 2. Absence Trends (Total leave days per month, last 6 months)
        $absenceTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $leaveDays = LeaveRequest::where('tenant_id', $tenantId)
                ->where('status', 'approved')
                ->where(function ($query) use ($month) {
                    $query->whereBetween('start_date', [$month->startOfMonth(), $month->endOfMonth()])
                          ->orWhereBetween('end_date', [$month->startOfMonth(), $month->endOfMonth()]);
                })
                ->get()
                ->sum(function ($leave) {
                    // Simple sum for brevity
                    return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
                });
            
            $absenceTrend[] = [
                'month' => $month->format('M'),
                'days' => $leaveDays
            ];
        }

        // 3. Attendance Heatmap Data (Last 30 days)
        // Calculating average attendance % per day
        $totalEmployees = Employee::where('tenant_id', $tenantId)->count() ?: 1;
        $attendanceTrend = [];
        $period = CarbonPeriod::create(now()->subDays(14), now());
        foreach ($period as $date) {
            $count = Attendance::where('tenant_id', $tenantId)
                ->whereDate('punch_in', $date)
                ->distinct('employee_id')
                ->count();
            
            $attendanceTrend[] = [
                'day' => $date->format('D d'),
                'percentage' => round(($count / $totalEmployees) * 100)
            ];
        }

        // 4. Department Distribution
        $deptDist = Employee::where('tenant_id', $tenantId)
            ->with('department')
            ->get()
            ->groupBy('department_id')
            ->map(fn($group) => [
                'name' => $group->first()->department?->name ?? 'Unassigned',
                'count' => $group->count()
            ])
            ->values();

        return response()->json([
            'headcountTrend' => $headcountTrend,
            'absenceTrend' => $absenceTrend,
            'attendanceTrend' => $attendanceTrend,
            'departmentDistribution' => $deptDist,
            'stats' => [
                'totalEmployees' => Employee::where('tenant_id', $tenantId)->count(),
                'activeLeaves' => LeaveRequest::where('tenant_id', $tenantId)->where('status', 'approved')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->count(),
                'presentToday' => Attendance::where('tenant_id', $tenantId)->whereDate('punch_in', now())->distinct('employee_id')->count()
            ]
        ]);
    }
}
