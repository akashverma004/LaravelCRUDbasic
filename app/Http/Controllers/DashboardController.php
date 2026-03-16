<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index(): View
    {
        $user = auth()->user();

        // If user is a regular employee (not admin or hr_manager)
        if (! $user->hasAnyRole(['admin', 'hr_manager'])) {
            $employee = \App\Models\Employee::where('email', $user->email)
                ->where('tenant_id', $user->tenant_id)
                ->first();

            if ($employee) {
                $myLeaves = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                    ->latest()
                    ->take(5)
                    ->get();

                $upcomingHolidays = \App\Models\HolidayPolicyDate::where('holiday_date', '>=', now()->toDateString())
                    ->orderBy('holiday_date', 'asc')
                    ->take(5)
                    ->get();

                $colleagues = \App\Models\Employee::where('department_id', $employee->department_id)
                    ->where('id', '!=', $employee->id)
                    ->take(5)
                    ->get();
                    
                $todayAttendance = \App\Models\AttendanceRecord::where('employee_id', $employee->id)
                    ->where('attendance_date', now()->toDateString())
                    ->first();

                // Fetch Active Policies (Zoho/BambooHR style)
                $noticePolicy = \App\Models\NoticePeriodPolicy::active()->effectiveOn()->first();
                $wfhPolicy = \App\Models\WfhPolicy::active()->effectiveOn()->first();
                $attendancePolicy = \App\Models\AttendancePolicy::active()->effectiveOn()->first();

                return view('hrms.employee-dashboard', [
                    'employee' => $employee,
                    'myLeaves' => $myLeaves,
                    'upcomingHolidays' => $upcomingHolidays,
                    'colleagues' => $colleagues,
                    'todayAttendance' => $todayAttendance,
                    'noticePolicy' => $noticePolicy,
                    'wfhPolicy' => $wfhPolicy,
                    'attendancePolicy' => $attendancePolicy,
                ]);
            }
        }

        // Standard HR/Admin Dashboard
        $stats = $this->dashboardService->getDashboardStats();
        $departmentBreakdown = $this->dashboardService->getDepartmentBreakdown();
        $employees = $this->dashboardService->getRecentEmployees();
        $leaveRequests = $this->dashboardService->getLatestLeaveRequests();
        $leaveStats = $this->dashboardService->getLeaveStatistics();
        $employmentBreakdown = $this->dashboardService->getEmploymentTypeBreakdown();
        $employeesByStatus = $this->dashboardService->getEmployeesByStatus();
        $topDepartments = $this->dashboardService->getTopDepartmentsByCount();
        $newJoinees = $this->dashboardService->getNewJoinees();
        $teamHeads = $this->dashboardService->getTeamHeads();
        $allEmployees = $this->dashboardService->getAllEmployees();
        $avgSalary = $this->dashboardService->calculateAverageSalaryByDepartment();
        $leaveTypeData = $this->dashboardService->getLeaveRequestsByType();
        $leaveTrendData = $this->dashboardService->getLeaveTrendData();
        $activeSessions = $this->dashboardService->getActiveSessions();

        return view('hrms.dashboard', [
            ...$stats,
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
        ]);
    }

    public function myPolicies(): View
    {
        $user = auth()->user();
        $employee = \App\Models\Employee::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        $definitions = \App\Support\PolicyDefinitions::all();
        $policies = [];

        foreach ($definitions as $slug => $definition) {
            $modelClass = $definition['model'];
            
            // Fetch the active and currently effective policy for this tenant
            // The BelongsToTenant global scope handles tenant filtering.
            $policy = $modelClass::active()->effectiveOn()->first();

            if ($policy) {
                $policies[] = [
                    'slug' => $slug,
                    'title' => $definition['title'],
                    'description' => $policy->description ?? $definition['description'],
                    'record' => $policy,
                    'fields' => $definition['fields'],
                ];
            }
        }

        return view('hrms.policies.viewer', compact('employee', 'policies'));
    }
}
