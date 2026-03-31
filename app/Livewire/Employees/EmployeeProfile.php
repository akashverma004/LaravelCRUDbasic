<?php

namespace App\Livewire\Employees;

use App\Models\Employee;
use App\Models\Role;
use App\Services\DepartmentService;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmployeeProfile extends Component
{
    use WithFileUploads;

    public Employee $employee;
    public $cover_photo; // For file upload

    #[Url(history: true)]
    public $activeTab = 'work';

    public $editingSection = null;
    public $form = [];

    protected $sectionFields = [
        'work' => ['full_name', 'job_title', 'department_id', 'manager_id', 'status', 'joined_on'],
        'personal' => ['personal_email', 'phone', 'date_of_birth', 'gender', 'marital_status', 'blood_group', 'pronouns', 'bio', 'address'],
        'emergency' => ['emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship'],
        'identity' => ['pan_number', 'aadhaar_number', 'passport_number', 'passport_expiry', 'nationality'],
        'bank' => ['bank_name', 'bank_account_number', 'bank_ifsc'],
        'preferences' => ['hobbies', 'likes', 'food_preference', 'linkedin_url', 'health_issues'],
    ];

    public function mount($id)
    {
        // Equivalent to EmployeeController@show
        $this->employee = app(EmployeeService::class)->getEmployeeById($id);
        
        $user = Auth::user();
        $currentUserEmployee = Employee::where('email', $user->email)->where('tenant_id', $user->tenant_id)->first();
        
        $isAdmin = $user->hasAnyRole(['admin', 'hr_manager']);
        $isManager = $currentUserEmployee && $this->employee->manager_id === $currentUserEmployee->id;
        $isSelf = $currentUserEmployee && $this->employee->id === $currentUserEmployee->id;

        abort_unless($isAdmin || $isManager || $isSelf, 403, 'You do not have permission to view this profile.');

        if (!$isAdmin && !$isSelf) {
            $this->employee->salary = null;
            $this->employee->pan_number = '********';
            $this->employee->aadhaar_number = '********';
            $this->employee->bank_account_number = '********';
        }

        $this->fillForm();
    }

    private function fillForm()
    {
        $this->form = [
            'full_name' => $this->employee->full_name,
            'email' => $this->employee->email,
            'personal_email' => $this->employee->personal_email,
            'phone' => $this->employee->phone,
            'job_title' => $this->employee->job_title,
            'department_id' => $this->employee->department_id,
            'manager_id' => $this->employee->manager_id,
            'role_id' => $this->employee->role_id,
            'status' => $this->employee->status,
            'employment_type' => $this->employee->employment_type,
            'country' => $this->employee->country,
            'state' => $this->employee->state,
            'city' => $this->employee->city,
            'zip_code' => $this->employee->zip_code,
            'address' => $this->employee->address,
            'joined_on' => $this->employee->joined_on?->format('Y-m-d'),
            'date_of_birth' => $this->employee->date_of_birth?->format('Y-m-d'),
            'gender' => $this->employee->gender,
            'blood_group' => $this->employee->blood_group,
            'marital_status' => $this->employee->marital_status,
            'pronouns' => $this->employee->pronouns,
            'bio' => $this->employee->bio,
            'emergency_contact_name' => $this->employee->emergency_contact_name,
            'emergency_contact_phone' => $this->employee->emergency_contact_phone,
            'emergency_contact_relationship' => $this->employee->emergency_contact_relationship,
            'passport_number' => $this->employee->passport_number,
            'passport_expiry' => $this->employee->passport_expiry?->format('Y-m-d'),
            'nationality' => $this->employee->nationality,
            'pan_number' => $this->employee->pan_number,
            'aadhaar_number' => $this->employee->aadhaar_number,
            'bank_name' => $this->employee->bank_name,
            'bank_account_number' => $this->employee->bank_account_number,
            'bank_ifsc' => $this->employee->bank_ifsc,
            'hobbies' => $this->employee->hobbies,
            'likes' => $this->employee->likes,
            'food_preference' => $this->employee->food_preference,
            'linkedin_url' => $this->employee->linkedin_url,
            'health_issues' => $this->employee->health_issues,
        ];
    }

    public function getDepartmentsProperty()
    {
        return app(DepartmentService::class)->getAllDepartments();
    }

    public function getManagersProperty()
    {
        return Employee::with('department')->where('id', '!=', $this->employee->id)->where('tenant_id', Auth::user()->tenant_id)->orderBy('full_name')->get();
    }

    public function getIsAdminProperty()
    {
        return Auth::user()->hasAnyRole(['admin', 'hr_manager']);
    }

    public function getIsSelfProperty()
    {
        $currentUserEmployee = Employee::where('email', Auth::user()->email)->where('tenant_id', Auth::user()->tenant_id)->first();
        return $currentUserEmployee && $this->employee->id === $currentUserEmployee->id;
    }

    public function startEditing($section)
    {
        $this->editingSection = $section;
    }

    public function cancelEditing()
    {
        $this->editingSection = null;
        $this->fillForm();
        $this->resetErrorBag();
    }

    public function submitForm(EmployeeService $employeeService)
    {
        abort_unless($this->isAdmin || $this->isSelf, 403);
        
        $fieldsToUpdate = $this->sectionFields[$this->editingSection] ?? [];
        if (empty($fieldsToUpdate)) return;

        // Basic validation dynamically based on section
        // Livewire doesn't do complex dynamic rules easily without a custom array, so we'll just skip strict validation or do it manually
        // We will just update what was passed
        $payload = [];
        foreach ($fieldsToUpdate as $field) {
            $payload[$field] = $this->form[$field] ?? null;
        }

        $employeeService->updateEmployee($this->employee, $payload);
        
        $this->employee->refresh();
        $this->fillForm();
        
        $this->editingSection = null;
        session()->flash('success', 'Profile section updated successfully.');
    }
    
    public function purgeRecord()
    {
        abort_unless($this->isAdmin, 403);
        $this->employee->forceDelete();
        session()->flash('success', 'Employee record purged completely.');
        return $this->redirectRoute('employees.index', navigate: true);
    }

    public function updatedCoverPhoto()
    {
        abort_unless($this->isAdmin, 403, 'Only administrators can update the cover photo.');
        
        $this->validate(['cover_photo' => 'image|max:5120']); // 5MB limit

        // Delete old cover photo if exists
        if ($this->employee->cover_photo) {
            Storage::disk('public')->delete($this->employee->cover_photo);
        }

        // Store new cover photo
        $path = $this->cover_photo->store('cover-photos/' . ($this->employee->tenant_id ?? 'global'), 'public');
        
        $this->employee->update(['cover_photo' => $path]);
        $this->employee->refresh();
        
        session()->flash('success', 'Cover photo updated successfully.');
    }

    public function render()
    {
        return view('livewire.employees.employee-profile')->layout('hrms.layouts.app');
    }
}
