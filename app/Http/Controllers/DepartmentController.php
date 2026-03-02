<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
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
        return view('hrms.departments.create');
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $this->departmentService->createDepartment($request->validated());
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
        return view('hrms.departments.edit', compact('department'));
    }

    public function update(StoreDepartmentRequest $request, int $id): RedirectResponse
    {
        $department = $this->departmentService->getDepartmentById($id);
        $this->departmentService->updateDepartment($department, $request->validated());
        return redirect()->route('departments.show', $id)->with('status', 'Department updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $department = $this->departmentService->getDepartmentById($id);
        $this->departmentService->deleteDepartment($department);
        return redirect()->route('departments.index')->with('status', 'Department deleted successfully.');
    }
}
