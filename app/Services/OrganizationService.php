<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

class OrganizationService
{
    public function getOrganizationHierarchy()
    {
        return Employee::with('subordinates', 'department')
            ->whereNull('manager_id')
            ->orderBy('full_name')
            ->get();
    }

    public function getEmployeeHierarchy(Employee $employee)
    {
        return $employee->load('subordinates.department', 'subordinates.subordinates');
    }

    public function getDepartmentHierarchy(int $departmentId)
    {
        return Employee::where('department_id', $departmentId)
            ->with('subordinates.department', 'manager')
            ->whereNull('manager_id')
            ->orderBy('full_name')
            ->get();
    }

    public function getTeamHeads(): Collection
    {
        return Employee::query()
            ->with('subordinates', 'department')
            ->whereHas('subordinates')
            ->orderBy('job_title')
            ->orderBy('full_name')
            ->get();
    }

    public function getTeamMembers(int $managerId)
    {
        return Employee::query()
            ->where('manager_id', $managerId)
            ->with('subordinates', 'department')
            ->orderBy('full_name')
            ->get();
    }

    public function buildHierarchyTree(string $format = 'tree')
    {
        $roots = $this->getOrganizationHierarchy();

        if ($format === 'tree') {
            return $this->buildTreeStructure($roots);
        }

        return $roots;
    }

    private function buildTreeStructure(Collection $employees, int $level = 0): array
    {
        return $employees->map(function (Employee $employee) use ($level) {
            return [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'title' => $employee->job_title,
                'department' => $employee->department->name ?? 'N/A',
                'email' => $employee->email,
                'phone' => $employee->phone,
                'status' => $employee->status,
                'level' => $level,
                'children' => $employee->subordinates->isNotEmpty()
                    ? $this->buildTreeStructure($employee->subordinates, $level + 1)
                    : [],
            ];
        })->toArray();
    }

    public function getOrgChartStats()
    {
        return [
            'totalEmployees' => Employee::count(),
            'managers' => Employee::whereHas('subordinates')->count(),
            'teamHeads' => Employee::whereNull('manager_id')->whereHas('subordinates')->count(),
            'avgTeamSize' => round(
                Employee::whereHas('subordinates')
                    ->withCount('subordinates')
                    ->get()
                    ->avg('subordinates_count') ?? 0,
                2
            ),
        ];
    }
}
