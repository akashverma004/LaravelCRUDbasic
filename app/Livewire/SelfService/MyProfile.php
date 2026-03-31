<?php

namespace App\Livewire\SelfService;

use App\Models\Employee;
use App\Models\EmployeeEducation;
use App\Models\EmployeeExperience;
use App\Models\EmployeeSkill;
use App\Services\DepartmentService;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('hrms.layouts.app')]
#[Title('My Profile - PeopleFlow HRMS')]
class MyProfile extends Component
{
    use WithFileUploads;

    public ?Employee $employee = null;
    
    #[Url(history: true)]
    public $activeTab = 'personal';

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

    // Modals state
    public bool $showEduModal = false;
    public bool $showExpModal = false;
    public bool $showSkillModal = false;

    // Photo upload
    public $photo;
    public $cover_photo;

    // Password Update (separate from profile edit)
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Modals Data
    public array $eduForm = ['degree' => '', 'institution' => '', 'field_of_study' => '', 'year_from' => '', 'year_to' => ''];
    public array $expForm = ['company' => '', 'designation' => '', 'from_date' => '', 'to_date' => '', 'description' => ''];
    public array $skillForm = ['name' => '', 'proficiency' => 'beginner'];

    public function mount()
    {
        $this->loadEmployee();
        $this->fillForm();
    }

    public function loadEmployee()
    {
        $user = Auth::user();
        $this->employee = Employee::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->with(['department', 'role', 'manager', 'educations', 'experiences', 'skills'])
            ->first();

        if (!$this->employee && $user->hasAnyRole(['admin', 'hr_manager'])) {
            $this->createAdminEmployee();
        }

        if (!$this->employee) {
            abort(404, 'Employee profile not found.');
        }
    }

    private function createAdminEmployee()
    {
        $user = Auth::user();
        $dept = \App\Models\Department::where('tenant_id', $user->tenant_id)->first();
        if ($dept) {
            $this->employee = Employee::create([
                'tenant_id' => $user->tenant_id,
                'department_id' => $dept->id,
                'full_name' => $user->name,
                'email' => $user->email,
                'phone' => '0000000000',
                'job_title' => 'Administrator',
                'employment_type' => 'full-time',
                'salary' => 0,
                'status' => 'active',
                'joined_on' => now(),
            ]);
        }
    }

    private function fillForm()
    {
        if (!$this->employee) return;
        
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

    public function updatedPhoto()
    {
        $this->validate(['photo' => 'image|max:2048']);
        
        if ($this->employee->profile_photo) {
            Storage::disk('public')->delete($this->employee->profile_photo);
        }

        $path = $this->photo->store('profile-photos/' . ($this->employee->tenant_id ?? 'global'), 'public');
        $this->employee->update(['profile_photo' => $path]);
        $this->employee->refresh();
        $this->reset('photo');
        session()->flash('success', 'Profile photo updated.');
    }

    public function updatedCoverPhoto()
    {
        $this->validate(['cover_photo' => 'image|max:2048']); // Aligned with PHP 2MB limit
        
        if ($this->employee->cover_photo) {
            Storage::disk('public')->delete($this->employee->cover_photo);
        }

        $path = $this->cover_photo->store('cover-photos/' . ($this->employee->tenant_id ?? 'global'), 'public');
        $this->employee->update(['cover_photo' => $path]);
        
        // Redundant re-fetch to ensure absolute state consistency
        $this->loadEmployee(); 
        
        $this->reset('cover_photo');
        session()->flash('success', 'Cover photo updated.');
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
        // For SelfService, only certain sections are typically allowed or we use stricter validation
        $fieldsToUpdate = $this->sectionFields[$this->editingSection] ?? [];
        if (empty($fieldsToUpdate)) return;

        $payload = [];
        foreach ($fieldsToUpdate as $field) {
            // Work related fields cannot be updated by user (unless admin)
            if (in_array($field, ['full_name', 'job_title', 'department_id', 'manager_id', 'status', 'joined_on']) && !$this->isAdmin) {
                continue;
            }
            $payload[$field] = $this->form[$field] ?? null;
        }

        $employeeService->updateEmployee($this->employee, $payload);
        
        $this->employee->refresh();
        $this->fillForm();
        
        $this->editingSection = null;
        session()->flash('success', 'Profile section updated successfully.');
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

    public function addEducation()
    {
        $this->validate([
            'eduForm.degree' => 'required|string|max:100',
            'eduForm.institution' => 'required|string|max:150',
            'eduForm.year_from' => 'nullable|integer',
            'eduForm.year_to' => 'nullable|integer',
        ]);

        $this->employee->educations()->create([
            ...$this->eduForm,
            'tenant_id' => $this->employee->tenant_id,
        ]);

        $this->eduForm = ['degree' => '', 'institution' => '', 'field_of_study' => '', 'year_from' => '', 'year_to' => ''];
        $this->showEduModal = false;
        $this->employee->refresh();
        session()->flash('success', 'Education record added.');
    }

    public function removeEducation(int $id)
    {
        $this->employee->educations()->where('id', $id)->delete();
        $this->employee->refresh();
        session()->flash('success', 'Education record removed.');
    }

    public function addExperience()
    {
        $this->validate([
            'expForm.company' => 'required|string|max:150',
            'expForm.designation' => 'required|string|max:100',
            'expForm.from_date' => 'nullable|date',
            'expForm.to_date' => 'nullable|date',
        ]);

        $this->employee->experiences()->create([
            ...$this->expForm,
            'tenant_id' => $this->employee->tenant_id,
        ]);

        $this->expForm = ['company' => '', 'designation' => '', 'from_date' => '', 'to_date' => '', 'description' => ''];
        $this->showExpModal = false;
        $this->employee->refresh();
        session()->flash('success', 'Experience record added.');
    }

    public function removeExperience(int $id)
    {
        $this->employee->experiences()->where('id', $id)->delete();
        $this->employee->refresh();
        session()->flash('success', 'Experience record removed.');
    }

    public function addSkill()
    {
        $this->validate([
            'skillForm.name' => 'required|string|max:80',
            'skillForm.proficiency' => 'required|string',
        ]);

        $this->employee->skills()->create([
            ...$this->skillForm,
            'tenant_id' => $this->employee->tenant_id,
        ]);

        $this->skillForm = ['name' => '', 'proficiency' => 'beginner'];
        $this->showSkillModal = false;
        $this->employee->refresh();
        session()->flash('success', 'Skill added.');
    }

    public function removeSkill(int $id)
    {
        $this->employee->skills()->where('id', $id)->delete();
        $this->employee->refresh();
        session()->flash('success', 'Skill removed.');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('success', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.self-service.my-profile');
    }
}
