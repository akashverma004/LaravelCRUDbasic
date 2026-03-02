<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index(): View
    {
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

        return view('hrms.dashboard', [
            ...$stats,
            'departmentBreakdown' => $departmentBreakdown,
            'employees' => $employees,
            'leaveRequests' => $leaveRequests,
            'leaveStats' => $leaveStats,
            'employmentBreakdown' => $employmentBreakdown,
            'employeesByStatus' => $employeesByStatus,
            'topDepartments' => $topDepartments,
            'newJoinees' => $newJoinees,
            'teamHeads' => $teamHeads,
            'allEmployees' => $allEmployees,
            'avgSalary' => $avgSalary,
        ]);
    }
}
