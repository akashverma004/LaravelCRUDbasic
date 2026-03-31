<?php

namespace App\Livewire\Employees;

use Livewire\Component;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use App\Services\DepartmentService;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeForm extends Component
{
    public $full_name;
    public $email;
    public $password;
    public $phone;
    public $job_title;
    public $department_id;
    public $manager_id;
    public $role_id;
    public $employment_type;
    public $salary;
    public $joined_on;
    public $status = 'active';

    public $country = 'IN';
    public $state;
    public $city;
    public $zip_code;
    public $address;

    public $hobbies;
    public $food_preference;
    public $health_issues;

    public function rules()
    {
        return [
            'full_name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->where('tenant_id', Auth::user()->tenant_id)],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'string', 'max:30'],
            'job_title' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'employment_type' => ['required', 'in:full-time,part-time,contract,intern'],
            'salary' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'joined_on' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', 'in:active,on-leave'],
            'country' => ['required', 'string', 'max:3'],
            'state' => ['required', 'string', 'max:3'],
            'city' => ['required', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'hobbies' => ['nullable', 'string', 'max:1000'],
            'food_preference' => ['nullable', 'in:veg,non-veg'],
            'health_issues' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function getDepartmentsProperty(DepartmentService $departmentService)
    {
        return $departmentService->getAllDepartments();
    }

    public function getManagersProperty()
    {
        return Employee::with('department')->where('tenant_id', Auth::user()->tenant_id)->orderBy('full_name')->get();
    }

    public function getRolesProperty()
    {
        return Role::all();
    }

    public function getCountriesProperty()
    {
        return config('geo.countries', []);
    }

    public function getStatesProperty()
    {
        return config('geo.states_in', []);
    }

    public function mount()
    {
        // Default password generation
        $this->password = Str::random(10);
        $this->joined_on = now()->toDateString();
        
        // Auto-select Role if employee exists
        $employeeRole = Role::where('name', 'employee')->first();
        if ($employeeRole) {
            $this->role_id = $employeeRole->id;
        }
    }

    public function save(EmployeeService $employeeService)
    {
        $this->authorize('create', Employee::class);
        $validated = $this->validate();

        $employeeService->createEmployee($validated);

        session()->flash('success', 'Employee created successfully.');
        return $this->redirectRoute('employees.index', navigate: true);
    }
    
    // Polyfill for generic Authorization bypassing since Livewire uses standard Laravel authorize
    public function authorize($ability, $arguments = []) {
        abort_unless(Auth::user()->hasAnyRole(['admin', 'hr_manager']), 403);
    }

    public function render()
    {
        return view('livewire.employees.employee-form')->layout('hrms.layouts.app');
    }
}
