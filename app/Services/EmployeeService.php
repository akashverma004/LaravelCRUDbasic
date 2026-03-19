<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

class EmployeeService
{
    public function createEmployee(array $data): Employee
    {
        $password = $data['password'] ?? null;
        unset($data['password']);

        $employee = Employee::create($data);

        if (!empty($password)) {
            $user = \App\Models\User::updateOrCreate(
                [
                    'email' => $data['email'],
                    'tenant_id' => \App\Support\TenantContext::id() ?? 1
                ],
                [
                    'name' => $data['full_name'],
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'require_password_change' => true,
                ]
            );

            if (!empty($data['role_id'])) {
                $role = \App\Models\Role::find($data['role_id']);
                if ($role) {
                    $user->assignRole($role);
                }
            }
        }

        return $employee;
    }

    public function updateEmployee(Employee $employee, array $data): Employee
    {
        $password = $data['password'] ?? null;
        $roleId = $data['role_id'] ?? null;
        unset($data['password']);

        $employee->update($data);

        if (!empty($password)) {
            $user = \App\Models\User::updateOrCreate(
                [
                    'email' => $employee->email,
                    'tenant_id' => $employee->tenant_id
                ],
                [
                    'name' => $employee->full_name,
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'require_password_change' => true,
                ]
            );

            if (!empty($roleId)) {
                $role = \App\Models\Role::find($roleId);
                if ($role) {
                    $user->syncRoles([$role->id]);
                }
            }
        } elseif (!empty($roleId)) {
            // Unchanged password, but role might have changed
            $user = \App\Models\User::where('email', $employee->email)
                ->where('tenant_id', $employee->tenant_id)
                ->first();
            
            if ($user) {
                $role = \App\Models\Role::find($roleId);
                if ($role) {
                    $user->syncRoles([$role->id]);
                }
            }
        }

        return $employee;
    }

    public function deleteEmployee(Employee $employee): bool
    {
        return $employee->delete();
    }

    public function restoreEmployee(int $id): Employee
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();

        return $employee->fresh([
            'department',
            'role',
            'manager',
            'leaveRequests',
            'attendanceRecords',
            'educations',
            'experiences',
            'skills',
        ]);
    }

    public function getAllEmployees(array $filters = [])
    {
        return Employee::withTrashed()
            ->with(['department', 'role'])
            ->when(
                ! empty($filters['q']),
                fn ($query) => $query->where(function ($innerQuery) use ($filters) {
                    $innerQuery
                        ->where('full_name', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('email', 'like', '%' . $filters['q'] . '%');
                })
            )
            ->when(
                ! empty($filters['department_id']),
                fn ($query) => $query->where('department_id', (int) $filters['department_id'])
            )
            ->when(
                ! empty($filters['role_id']),
                fn ($query) => $query->where('role_id', (int) $filters['role_id'])
            )
            ->orderByRaw('CASE WHEN deleted_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('full_name')
            ->paginate(15)
            ->withQueryString();
    }

    public function getEmployeeById(int $id): ?Employee
    {
        return Employee::withTrashed()->with([
            'department', 'role', 'manager',
            'leaveRequests', 'attendanceRecords',
            'educations', 'experiences', 'skills',
        ])->findOrFail($id);
    }

    public function getEmployeesByDepartment(int $departmentId): Collection
    {
        return Employee::where('department_id', $departmentId)
            ->orderBy('full_name')
            ->get();
    }

    public function searchEmployees(string $query)
    {
        return Employee::withTrashed()->with('department')
            ->where(function ($innerQuery) use ($query) {
                $innerQuery
                    ->where('full_name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('full_name')
            ->paginate(15);
    }
}
