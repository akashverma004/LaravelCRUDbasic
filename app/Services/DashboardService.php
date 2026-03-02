<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\AttendanceRecord;

class DashboardService
{
    public function __construct(private OrganizationService $organizationService) {}

    public function getDashboardStats(): array
    {
        return [
            'employeeCount' => Employee::count(),
            'departmentCount' => Department::count(),
            'leavePending' => LeaveRequest::where('status', 'pending')->count(),
            'attendanceToday' => AttendanceRecord::whereDate('attendance_date', now()->toDateString())->count(),
        ];
    }

    public function getDepartmentBreakdown()
    {
        return Department::query()
            ->withCount('employees')
            ->orderBy('name')
            ->get();
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
        return Employee::query()
            ->selectRaw('employment_type, COUNT(*) as count')
            ->groupBy('employment_type')
            ->get();
    }

    public function getEmployeesByStatus()
    {
        return Employee::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
    }

    public function getTopDepartmentsByCount(int $limit = 5)
    {
        return Department::query()
            ->withCount('employees')
            ->orderByDesc('employees_count')
            ->take($limit)
            ->get();
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
}
