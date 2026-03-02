<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

class EmployeeService
{
    public function createEmployee(array $data): Employee
    {
        return Employee::create($data);
    }

    public function updateEmployee(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee;
    }

    public function deleteEmployee(Employee $employee): bool
    {
        return $employee->delete();
    }

    public function getAllEmployees()
    {
        return Employee::with('department')->orderBy('full_name')->paginate(15);
    }

    public function getEmployeeById(int $id): ?Employee
    {
        return Employee::with('department', 'leaveRequests', 'attendanceRecords')->findOrFail($id);
    }

    public function getEmployeesByDepartment(int $departmentId): Collection
    {
        return Employee::where('department_id', $departmentId)
            ->orderBy('full_name')
            ->get();
    }

    public function searchEmployees(string $query)
    {
        return Employee::with('department')
            ->where('full_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orderBy('full_name')
            ->paginate(15);
    }
}
