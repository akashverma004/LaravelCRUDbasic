<?php

namespace App\Livewire\Organization;

use Livewire\Component;
use App\Models\Department;
use App\Models\Employee;
use App\Services\DepartmentService;
use Illuminate\Support\Facades\Auth;

class DepartmentProfile extends Component
{
    public Department $department;
    public $isEditing = false;
    
    public $form = [
        'name' => '',
        'code' => '',
        'lead_employee_id' => '',
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.code' => 'required|string|max:50',
        'form.lead_employee_id' => 'nullable|exists:employees,id',
    ];

    public function mount($id, DepartmentService $departmentService)
    {
        $this->department = $departmentService->getDepartmentWithEmployees($id);
    }

    public function getEmployeesListProperty()
    {
        return Employee::orderBy('full_name')->get(['id', 'full_name']);
    }

    public function getCanManageProperty()
    {
        return Auth::user()->hasAnyRole(['admin', 'hr_manager']);
    }

    public function openEditModal()
    {
        abort_unless($this->canManage, 403);
        
        $leadEmployee = Employee::where('full_name', $this->department->lead_name)->first();

        $this->form = [
            'name' => $this->department->name,
            'code' => $this->department->code,
            'lead_employee_id' => $leadEmployee ? $leadEmployee->id : '',
        ];

        $this->resetValidation();
        $this->isEditing = true;
    }

    public function closeEditModal()
    {
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function submitEdit(DepartmentService $departmentService)
    {
        abort_unless($this->canManage, 403);
        
        $this->rules['form.code'] = 'required|string|max:50|unique:departments,code,' . $this->department->id;
        $this->validate();

        $validated = $this->form;

        if (!empty($validated['lead_employee_id'])) {
            $validated['lead_name'] = Employee::findOrFail($validated['lead_employee_id'])->full_name;
        } else {
            $validated['lead_name'] = null;
        }

        unset($validated['lead_employee_id']);

        $departmentService->updateDepartment($this->department, $validated);

        // Refresh the local tracking variable since the service updates it.
        $this->department->refresh();

        $this->closeEditModal();
        session()->flash('success', 'Department details updated successfully.');
    }

    public function deleteDepartment(DepartmentService $departmentService)
    {
        abort_unless($this->canManage, 403);
        
        if ($this->department->employees()->count() > 0) {
            session()->flash('error', 'Cannot archive a department that contains active employees.');
            return;
        }

        $departmentService->deleteDepartment($this->department);
        session()->flash('success', 'Department archived successfully.');
        return $this->redirectRoute('departments.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.organization.department-profile')->layout('hrms.layouts.app');
    }
}
