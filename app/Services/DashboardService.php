<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\AttendanceRecord;
use App\Models\WorkflowRequest;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function __construct(private OrganizationService $organizationService) {}

    public function getDashboardStats(): array
    {
        $tenantId = auth()->user()->tenant_id ?? 'global';
        return Cache::remember("dashboard_stats_v2_{$tenantId}_" . now()->toDateString(), 300, function () {
            return [
                'employeeCount' => Employee::count(),
                'departmentCount' => Department::count(),
                'leavePending' => LeaveRequest::where('status', 'pending')->count(),
                'attendanceToday' => AttendanceRecord::whereDate('attendance_date', now()->toDateString())->count(),
                'workflowPending' => WorkflowRequest::where('status', 'pending')->count(),
            ];
        });
    }

    public function getDepartmentBreakdown()
    {
        $tenantId = auth()->user()->tenant_id ?? 'global';
        return Cache::remember("dashboard_dept_breakdown_v2_{$tenantId}", 300, function () {
            return Department::query()
                ->withCount('employees')
                ->orderBy('name')
                ->get();
        });
    }

    public function getRecentEmployees(int $limit = 8)
    {
        return Employee::query()
            ->with('department')
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getLatestLeaveRequests(int $limit = 6)
    {
        return LeaveRequest::query()
            ->with('employee')
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getLeaveStatistics()
    {
        return [
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];
    }

    public function getEmploymentTypeBreakdown()
    {
        $tenantId = auth()->user()->tenant_id ?? 'global';
        return Cache::remember("dashboard_employment_type_v2_{$tenantId}", 300, function () {
            return Employee::query()
                ->selectRaw('employment_type, COUNT(*) as count')
                ->groupBy('employment_type')
                ->get();
        });
    }

    public function getEmployeesByStatus()
    {
        $tenantId = auth()->user()->tenant_id ?? 'global';
        return Cache::remember("dashboard_emp_status_v2_{$tenantId}", 300, function () {
            return Employee::query()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();
        });
    }

    public function getTopDepartmentsByCount(int $limit = 5)
    {
        $tenantId = auth()->user()->tenant_id ?? 'global';
        return Cache::remember("dashboard_top_depts_v2_{$tenantId}_{$limit}", 300, function () use ($limit) {
            return Department::query()
                ->withCount('employees')
                ->orderByDesc('employees_count')
                ->take($limit)
                ->get();
        });
    }

    public function getNewJoinees(int $limit = 5)
    {
        return Employee::query()
            ->with('department')
            ->orderByDesc('joined_on')
            ->take($limit)
            ->get();
    }

    public function getTeamHeads()
    {
        return $this->organizationService->getTeamHeads();
    }

    public function getOrganizationHierarchy()
    {
        return $this->organizationService->getOrganizationHierarchy();
    }

    public function getOrgChartStats()
    {
        return $this->organizationService->getOrgChartStats();
    }

    public function calculateAverageSalaryByDepartment()
    {
        return Department::query()
            ->select('departments.id', 'departments.name')
            ->selectRaw('AVG(employees.salary) as avg_salary')
            ->leftJoin('employees', 'employees.department_id', '=', 'departments.id')
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('departments.name')
            ->get()
            ->map(fn($d) => [
                'name' => $d->name,
                'avgSalary' => round($d->avg_salary ?? 0, 2),
            ]);
    }

    public function getAllEmployees()
    {
        return Employee::query()
            ->with('department', 'manager')
            ->orderBy('full_name')
            ->get();
    }

    public function getLeaveRequestsByType()
    {
        $tenantId = auth()->user()->tenant_id ?? 'global';
        return Cache::remember("dashboard_leave_types_v2_{$tenantId}", 300, function () {
            return LeaveRequest::query()
                ->selectRaw('leave_type, COUNT(*) as count')
                ->groupBy('leave_type')
                ->get();
        });
    }

    public function getLeaveTrendData(int $days = 30)
    {
        $tenantId = auth()->user()->tenant_id ?? 'global';
        return Cache::remember("dashboard_leave_trend_v2_{$tenantId}_{$days}", 300, function () use ($days) {
            $startDate = now()->subDays($days);
            $totalEmployees = Employee::count();
            $capacity = max(1, $totalEmployees);

            $leaves = LeaveRequest::query()
                ->selectRaw('DATE(start_date) as date, COUNT(*) as count')
                ->where('start_date', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $labels = [];
            $bookings = [];
            $capacities = [];

            for ($i = 0; $i < $days; $i++) {
                $date = $startDate->copy()->addDays($i)->toDateString();
                $labels[] = date('M d', strtotime($date));
                $found = $leaves->firstWhere('date', $date);
                $bookings[] = $found ? $found->count : 0;
                $capacities[] = $capacity;
            }

            return [
                'labels' => $labels,
                'bookings' => $bookings,
                'capacity' => $capacities,
            ];
        });
    }

    public function getActiveSessions(int $limit = 4)
    {
        return \App\Models\User::query()
            ->with(['tenant'])
            ->latest('updated_at')
            ->take($limit)
            ->get()
            ->map(function($user) {
                return [
                    'name' => $user->name,
                    'last_activity' => $user->updated_at->diffForHumans(),
                    'avatar' => $user->profile_photo_url, // Using dynamic URL
                    'initial' => substr($user->name, 0, 1),
                    'role' => $user->is_platform_admin ? 'Platform Admin' : 'User'
                ];
            });
    }
}
