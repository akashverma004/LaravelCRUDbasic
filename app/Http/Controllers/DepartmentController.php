<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Models\Employee;
use App\Services\DepartmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(private DepartmentService $departmentService) {}

    public function index(): View
    {
        $departments = $this->departmentService->getAllDepartments();
        return view('hrms.departments.index', compact('departments'));
    }

    public function create(): View
    {
        $employees = Employee::orderBy('full_name')->get();

        return view('hrms.departments.create', compact('employees'));
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        if (! empty($validated['lead_employee_id'])) {
            $validated['lead_name'] = Employee::findOrFail($validated['lead_employee_id'])->full_name;
        }
        unset($validated['lead_employee_id']);

        $this->departmentService->createDepartment($validated);

        return redirect()->route('departments.index')->with('status', 'Department created successfully.');
    }

    public function show(int $id): View
    {
        $department = $this->departmentService->getDepartmentWithEmployees($id);
        return view('hrms.departments.show', compact('department'));
    }

    public function edit(int $id): View
    {
        $department = $this->departmentService->getDepartmentById($id);
        $employees = Employee::orderBy('full_name')->get();

        return view('hrms.departments.edit', compact('department', 'employees'));
    }

    public function update(StoreDepartmentRequest $request, int $id): RedirectResponse
    {
        $department = $this->departmentService->getDepartmentById($id);
        $validated = $request->validated();
        if (! empty($validated['lead_employee_id'])) {
            $validated['lead_name'] = Employee::findOrFail($validated['lead_employee_id'])->full_name;
        }
        unset($validated['lead_employee_id']);

        $this->departmentService->updateDepartment($department, $validated);

        return redirect()->route('departments.show', $id)->with('status', 'Department updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $department = $this->departmentService->getDepartmentById($id);
        $this->departmentService->deleteDepartment($department);
        return redirect()->route('departments.index')->with('status', 'Department deleted successfully.');
    }
}
