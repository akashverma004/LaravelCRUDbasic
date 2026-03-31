<?php

namespace App\Livewire\Employees;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Models\Employee;
use App\Models\Role;
use App\Services\DepartmentService;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\Auth;

class EmployeeDirectory extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $department_id = '';

    #[Url(history: true)]
    public $role_id = '';

    // Modals
    public $showCreateModal = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDepartmentId()
    {
        $this->resetPage();
    }

    public function updatingRoleId()
    {
        $this->resetPage();
    }

    #[Computed]
    public function employees()
    {
        $query = Employee::withTrashed()
            ->with(['department', 'role', 'manager']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->department_id)) {
            $query->where('department_id', $this->department_id);
        }

        if (!empty($this->role_id)) {
            $query->where('role_id', $this->role_id);
        }

        return $query->orderByRaw('CASE WHEN deleted_at IS NULL THEN 0 ELSE 1 END')
                     ->orderBy('full_name')
                     ->paginate(15);
    }

    #[Computed]
    public function departments()
    {
        return app(DepartmentService::class)->getAllDepartments();
    }

    #[Computed]
    public function roles()
    {
        return Role::orderBy('name')->get();
    }

    public function deleteEmployee(Employee $employee)
    {
        abort_unless(Auth::user()->hasAnyRole(['admin', 'hr_manager']), 403);
        $employee->delete();
        session()->flash('success', 'Employee archived successfully.');
    }

    public function restoreEmployee($id)
    {
        abort_unless(Auth::user()->hasAnyRole(['admin', 'hr_manager']), 403);
        $employee = Employee::withTrashed()->findOrFail($id);
        if ($employee->tenant_id === Auth::user()->tenant_id) {
            $employee->restore();
            session()->flash('success', 'Employee restored successfully.');
        }
    }

    public function render()
    {
        return view('livewire.employees.employee-directory')->layout('hrms.layouts.app');
    }
}
