<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\DepartmentService;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        private EmployeeService $employeeService,
        private DepartmentService $departmentService
    ) {}

    public function index(): View
    {
        $filters = request()->only(['q', 'role_id', 'department_id']);
        $employees = $this->employeeService->getAllEmployees($filters);
        $roles = Role::orderBy('name')->get();
        $departments = $this->departmentService->getAllDepartments();

        return view('hrms.employees.index', compact('employees', 'roles', 'departments', 'filters'));
    }

    public function create(): View
    {
        $departments = $this->departmentService->getAllDepartments();
        $roles = Role::all();
        $managers = Employee::with('department')->orderBy('full_name')->get();
        $countries = config('geo.countries', []);
        $states = config('geo.states_in', []);

        return view('hrms.employees.create', compact('departments', 'roles', 'managers', 'countries', 'states'));
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $this->employeeService->createEmployee($request->validated());
        return redirect()->route('employees.index')->with('status', 'Employee created successfully.');
    }

    public function show(int $id): View
    {
        $employee = $this->employeeService->getEmployeeById($id);
        return view('hrms.employees.show', compact('employee'));
    }

    public function edit(int $id): View
    {
        $employee = $this->employeeService->getEmployeeById($id);
        $departments = $this->departmentService->getAllDepartments();
        $roles = Role::all();
        $managers = Employee::with('department')
            ->where('id', '!=', $employee->id)
            ->orderBy('full_name')
            ->get();
        $countries = config('geo.countries', []);
        $states = config('geo.states_in', []);

        return view('hrms.employees.edit', compact('employee', 'departments', 'roles', 'managers', 'countries', 'states'));
    }

    public function update(StoreEmployeeRequest $request, int $id): RedirectResponse
    {
        $employee = $this->employeeService->getEmployeeById($id);
        $this->employeeService->updateEmployee($employee, $request->validated());
        return redirect()->route('employees.show', $id)->with('status', 'Employee updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $employee = $this->employeeService->getEmployeeById($id);
        $this->employeeService->deleteEmployee($employee);
        return redirect()->route('employees.index')->with('status', 'Employee deleted successfully.');
    }

    public function search(): View
    {
        $query = request('q', '');
        $employees = $query ? $this->employeeService->searchEmployees($query) : collect();
        return view('hrms.employees.search', compact('employees', 'query'));
    }

    public function assignManager(): RedirectResponse
    {
        $validated = request()->validate([
            'employee_id' => 'required|exists:employees,id',
            'manager_id' => 'nullable|exists:employees,id|different:employee_id',
            'effective_date' => 'required|date',
        ]);

        $employee = $this->employeeService->getEmployeeById($validated['employee_id']);
        $this->employeeService->updateEmployee($employee, [
            'manager_id' => $validated['manager_id'],
        ]);

        return redirect()->back()->with('status', 'Manager assigned successfully.');
    }
}
