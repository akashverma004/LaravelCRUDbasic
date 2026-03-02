<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;

class DepartmentService
{
    public function createDepartment(array $data): Department
    {
        return Department::create($data);
    }

    public function updateDepartment(Department $department, array $data): Department
    {
        $department->update($data);
        return $department;
    }

    public function deleteDepartment(Department $department): bool
    {
        return $department->delete();
    }

    public function getAllDepartments(): Collection
    {
        return Department::orderBy('name')->get();
    }

    public function getDepartmentById(int $id): ?Department
    {
        return Department::findOrFail($id);
    }

    public function getDepartmentWithEmployees(int $id)
    {
        return Department::with('employees')->findOrFail($id);
    }
}
