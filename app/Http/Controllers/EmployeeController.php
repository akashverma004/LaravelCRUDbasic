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
    ) {
        $this->middleware('auth');
        // Only Admin/HR can create/edit/delete
        $this->middleware('role:admin,hr_manager')->except(['index', 'show', 'search']);
    }

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
        $user = auth()->user();
        $employee = $this->employeeService->getEmployeeById($id);
        
        $currentUserEmployee = Employee::where('email', $user->email)->where('tenant_id', $user->tenant_id)->first();

        $isAdmin = $user->hasAnyRole(['admin', 'hr_manager']);
        $isManager = $currentUserEmployee && $employee->manager_id === $currentUserEmployee->id;
        $isSelf = $currentUserEmployee && $employee->id === $currentUserEmployee->id;

        if (!$isAdmin && !$isManager && !$isSelf) {
            abort(403, 'You do not have permission to view this profile.');
        }

        // Hide sensitive data if not Admin/HR or Self
        if (!$isAdmin && !$isSelf) {
            $employee->salary = null;
            $employee->pan_number = '********';
            $employee->aadhaar_number = '********';
            $employee->bank_account_number = '********';
        }

        $departments = collect();
        $roles = collect();
        $managers = collect();
        $countries = [];
        $states = [];

        // For inline editing on the show page
        if ($isAdmin || $isSelf) {
            $departments = $this->departmentService->getAllDepartments();
            $roles = Role::all();
            $managers = Employee::with('department')->where('id', '!=', $employee->id)->orderBy('full_name')->get();
            $countries = config('geo.countries', []);
            $states = config('geo.states_in', []);
        }

        return view('hrms.employees.show', compact(
            'employee', 'isAdmin', 'isManager', 'isSelf',
            'departments', 'roles', 'managers', 'countries', 'states'
        ));
    }

    public function edit(int $id): View
    {
        return $this->show($id);
    }

    public function update(StoreEmployeeRequest $request, int $id)
    {
        $employee = $this->employeeService->getEmployeeById($id);
        $this->employeeService->updateEmployee($employee, $request->validated());
        
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Employee updated successfully.']);
        }
        
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
