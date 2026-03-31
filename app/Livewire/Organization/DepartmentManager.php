<?php

namespace App\Livewire\Organization;

use Livewire\Component;
use App\Models\Department;
use App\Models\Employee;
use App\Services\DepartmentService;
use Illuminate\Support\Facades\Auth;

class DepartmentManager extends Component
{
    public $viewMode = 'list';
    public $showCreateModal = false;
    
    // Form fields
    public $form = [
        'name' => '',
        'code' => '',
        'lead_employee_id' => '',
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.code' => 'required|string|max:50|unique:departments,code',
        'form.lead_employee_id' => 'nullable|exists:employees,id',
    ];

    public function getDepartmentsProperty()
    {
        return Department::query()
            ->withCount('employees')
            ->orderBy('name')
            ->get();
    }

    public function getEmployeesListProperty()
    {
        return Employee::orderBy('full_name')->get(['id', 'full_name']);
    }

    public function getCanManageProperty()
    {
        return Auth::user()->hasAnyRole(['admin', 'hr_manager']);
    }

    public function openCreateModal()
    {
        abort_unless($this->canManage, 403);
        $this->reset('form');
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset('form');
        $this->resetValidation();
    }

    public function submitCreate(DepartmentService $departmentService)
    {
        abort_unless($this->canManage, 403);
        $this->validate();

        $validated = $this->form;

        if (!empty($validated['lead_employee_id'])) {
            $validated['lead_name'] = Employee::findOrFail($validated['lead_employee_id'])->full_name;
        }

        unset($validated['lead_employee_id']);

        $departmentService->createDepartment($validated);

        $this->closeCreateModal();
        session()->flash('success', 'Department created successfully.');
    }

    public function deleteDepartment($id, DepartmentService $departmentService)
    {
        abort_unless($this->canManage, 403);
        $department = $departmentService->getDepartmentById($id);
        
        if ($department->employees()->count() > 0) {
            session()->flash('error', 'Cannot delete a department that contains employees.');
            return;
        }

        $departmentService->deleteDepartment($department);
        session()->flash('success', 'Department deleted successfully.');
    }

    public function render()
    {
        return view('livewire.organization.department-manager')->layout('hrms.layouts.app');
    }
}
