<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Component
{
    public function render(DashboardService $dashboardService)
    {
        $user = auth()->user();

        // Employee Dashboard
        if (! $user->hasAnyRole(['admin', 'hr_manager'])) {
            $employee = \App\Models\Employee::where('email', $user->email)
                ->where('tenant_id', $user->tenant_id)
                ->first();

            if ($employee) {
                $myLeaves = Cache::remember("emp_dash_leaves_v2_{$employee->id}", 300, function () use ($employee) {
                    return \App\Models\LeaveRequest::where('employee_id', $employee->id)
                        ->latest()
                        ->take(5)
                        ->get();
                });

                $upcomingHolidays = Cache::remember("emp_dash_holidays_v2_" . ($user->tenant_id ?? 'global'), 3600, function () {
                    return \App\Models\HolidayPolicyDate::where('holiday_date', '>=', now()->toDateString())
                        ->orderBy('holiday_date', 'asc')
                        ->take(5)
                        ->get();
                });

                $colleagues = Cache::remember("emp_dash_colleagues_v2_{$employee->department_id}", 300, function () use ($employee) {
                    return \App\Models\Employee::where('department_id', $employee->department_id)
                        ->where('id', '!=', $employee->id)
                        ->take(5)
                        ->get();
                });
                    
                $todayAttendance = Cache::remember("emp_dash_attendance_v2_{$employee->id}_" . now()->toDateString(), 60, function () use ($employee) {
                    return \App\Models\AttendanceRecord::where('employee_id', $employee->id)
                        ->where('attendance_date', now()->toDateString())
                        ->first();
                });

                $noticePolicy = \App\Models\NoticePeriodPolicy::active()->effectiveOn()->first();
                $wfhPolicy = \App\Models\WfhPolicy::active()->effectiveOn()->first();
                $attendancePolicy = \App\Models\AttendancePolicy::active()->effectiveOn()->first();

                $rawLeaveChart = Cache::remember("emp_dash_leave_chart_v2_{$employee->id}", 300, function () use ($employee) {
                    return \App\Models\LeaveRequest::where('employee_id', $employee->id)
                        ->where('status', 'approved')
                        ->selectRaw('leave_type, count(*) as count')
                        ->groupBy('leave_type')
                        ->get();
                });
                $leaveChartData = [
                    'labels' => $rawLeaveChart->isEmpty() ? collect(['None']) : $rawLeaveChart->pluck('leave_type')->map(fn($t) => ucfirst($t)),
                    'values' => $rawLeaveChart->isEmpty() ? collect([1]) : $rawLeaveChart->pluck('count'),
                ];

                $leaveTrendChartData = Cache::remember("emp_dash_leave_trend_v2_{$employee->id}", 300, function () use ($employee) {
                    return [
                        'labels' => collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('M')),
                        'bookings' => collect(range(5, 0))->map(function($i) use ($employee) {
                            return \App\Models\LeaveRequest::where('employee_id', $employee->id)
                                ->where('status', 'approved')
                                ->whereYear('start_date', now()->subMonths($i)->year)
                                ->whereMonth('start_date', now()->subMonths($i)->month)
                                ->count();
                        })
                    ];
                });

                // Notice the return path here simply wraps inside the default Livewire layout
                return view('livewire.dashboard.employee-dashboard', [
                    'employee' => $employee,
                    'myLeaves' => $myLeaves,
                    'upcomingHolidays' => $upcomingHolidays,
                    'colleagues' => $colleagues,
                    'todayAttendance' => $todayAttendance,
                    'noticePolicy' => $noticePolicy,
                    'wfhPolicy' => $wfhPolicy,
                    'attendancePolicy' => $attendancePolicy,
                    'leaveChartData' => $leaveChartData,
                    'leaveTrendChartData' => $leaveTrendChartData,
                ])->layout('hrms.layouts.app');
            }
        }

        // Admin Dashboard
        $rawStats = $dashboardService->getDashboardStats();
        $departmentBreakdown = $dashboardService->getDepartmentBreakdown();
        $employees = $dashboardService->getRecentEmployees();
        $leaveRequests = $dashboardService->getLatestLeaveRequests();
        $leaveStats = $dashboardService->getLeaveStatistics();
        $employmentBreakdown = $dashboardService->getEmploymentTypeBreakdown();
        $employeesByStatus = $dashboardService->getEmployeesByStatus();
        $topDepartments = $dashboardService->getTopDepartmentsByCount();
        $newJoinees = $dashboardService->getNewJoinees();
        $teamHeads = $dashboardService->getTeamHeads();
        $allEmployees = $dashboardService->getAllEmployees();
        $avgSalary = $dashboardService->calculateAverageSalaryByDepartment();
        $leaveTypeData = $dashboardService->getLeaveRequestsByType();
        $leaveTrendData = $dashboardService->getLeaveTrendData();
        $activeSessions = $dashboardService->getActiveSessions();

        return view('livewire.dashboard.dashboard', [
            ...$rawStats,
            'activeSessions' => $activeSessions,
            'departmentBreakdown' => $departmentBreakdown,
            'departmentChartData' => [
                'labels' => $departmentBreakdown->pluck('name'),
                'values' => $departmentBreakdown->pluck('employees_count'),
            ],
            'employees' => $employees,
            'leaveRequests' => $leaveRequests,
            'leaveStats' => $leaveStats,
            'leaveTypeChartData' => [
                'labels' => $leaveTypeData->pluck('leave_type')->map(fn($t) => ucfirst($t)),
                'values' => $leaveTypeData->pluck('count'),
            ],
            'leaveTrendChartData' => $leaveTrendData,
            'employmentBreakdown' => $employmentBreakdown,
            'employeesByStatus' => $employeesByStatus,
            'topDepartments' => $topDepartments,
            'newJoinees' => $newJoinees,
            'teamHeads' => $teamHeads,
            'allEmployees' => $allEmployees,
            'avgSalary' => $avgSalary,
        ])->layout('hrms.layouts.app');
    }
}
