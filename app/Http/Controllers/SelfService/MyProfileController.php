<?php

namespace App\Http\Controllers\SelfService;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeEducation;
use App\Models\EmployeeExperience;
use App\Models\EmployeeSkill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class MyProfileController extends Controller
{
    private function getEmployee(): Employee
    {
        $user = auth()->user();

        $employee = Employee::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->first();

        // If HR/Admin user doesn't have an employee record yet, dynamically create one
        if (!$employee && $user->hasAnyRole(['admin', 'hr_manager'])) {
            $dept = \App\Models\Department::where('tenant_id', $user->tenant_id)->first();
            
            if ($dept) {
                $employee = Employee::create([
                    'tenant_id' => $user->tenant_id,
                    'department_id' => $dept->id,
                    'full_name' => $user->name,
                    'email' => $user->email,
                    'phone' => '0000000000',
                    'job_title' => 'Administrator',
                    'employment_type' => 'full-time',
                    'salary' => 0,
                    'status' => 'active',
                    'country' => 'IN',
                    'state' => 'MH',
                    'city' => 'Unknown',
                    'address' => 'Unknown',
                    'joined_on' => now(),
                ]);
            }
        }

        if (!$employee) {
            abort(404, 'Employee profile not found.');
        }

        return $employee;
    }

    public function show(): View
    {
        $employee = $this->getEmployee();
        $employee->load(['department', 'role', 'manager', 'educations', 'experiences', 'skills']);

        return view('hrms.self-service.profile', compact('employee'));
    }

    public function data(): JsonResponse
    {
        $employee = $this->getEmployee();
        $employee->load(['department', 'role', 'manager', 'educations', 'experiences', 'skills']);

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'job_title' => $employee->job_title,
                'employment_type' => $employee->employment_type,
                'joined_on' => $employee->joined_on?->format('d M Y'),
                'status' => $employee->status,
                'department' => $employee->department?->name,
                'role' => $employee->role?->name,
                'manager' => $employee->manager?->full_name,
                'country' => $employee->country,
                'state' => $employee->state,
                'city' => $employee->city,
                'address' => $employee->address,
                'hobbies' => $employee->hobbies,
                'likes' => $employee->likes,
                'food_preference' => $employee->food_preference,
                'health_issues' => $employee->health_issues,
                'profile_photo' => $employee->profile_photo
                    ? Storage::url($employee->profile_photo)
                    : null,
                // New personal fields
                'date_of_birth' => $employee->date_of_birth?->format('Y-m-d'),
                'gender' => $employee->gender,
                'marital_status' => $employee->marital_status,
                'blood_group' => $employee->blood_group,
                'nationality' => $employee->nationality,
                'personal_email' => $employee->personal_email,
                // Emergency contact
                'emergency_contact_name' => $employee->emergency_contact_name,
                'emergency_contact_phone' => $employee->emergency_contact_phone,
                'emergency_contact_relationship' => $employee->emergency_contact_relationship,
                // Identity
                'pan_number' => $employee->pan_number,
                'aadhaar_number' => $employee->aadhaar_number,
                'passport_number' => $employee->passport_number,
                'passport_expiry' => $employee->passport_expiry?->format('Y-m-d'),
                // Bank
                'bank_name' => $employee->bank_name,
                'bank_account_number' => $employee->bank_account_number,
                'bank_ifsc' => $employee->bank_ifsc,
                // Social
                'linkedin_url' => $employee->linkedin_url,
                'pronouns' => $employee->pronouns,
                'bio' => $employee->bio,
                // Relations
                'educations' => $employee->educations->map(fn($e) => [
                    'id' => $e->id,
                    'degree' => $e->degree,
                    'institution' => $e->institution,
                    'field_of_study' => $e->field_of_study,
                    'year_from' => $e->year_from,
                    'year_to' => $e->year_to,
                ]),
                'experiences' => $employee->experiences->map(fn($e) => [
                    'id' => $e->id,
                    'company' => $e->company,
                    'designation' => $e->designation,
                    'from_date' => $e->from_date?->format('Y-m-d'),
                    'to_date' => $e->to_date?->format('Y-m-d'),
                    'description' => $e->description,
                ]),
                'skills' => $employee->skills->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'proficiency' => $s->proficiency,
                ]),
            ],
            'user' => [
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
        ]);
    }

    public function updateInfo(Request $request): JsonResponse
    {
        $employee = $this->getEmployee();

        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'hobbies' => 'nullable|string|max:255',
            'likes' => 'nullable|string|max:255',
            'food_preference' => 'nullable|string|max:50',
            'health_issues' => 'nullable|string|max:255',
            // New personal fields
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,non-binary,other,prefer_not_to_say',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed',
            'blood_group' => 'nullable|string|max:10',
            'nationality' => 'nullable|string|max:60',
            'personal_email' => 'nullable|email|max:255',
            // Emergency contact
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            // Identity
            'pan_number' => 'nullable|string|max:20',
            'aadhaar_number' => 'nullable|string|max:20',
            'passport_number' => 'nullable|string|max:30',
            'passport_expiry' => 'nullable|date',
            // Bank
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:30',
            'bank_ifsc' => 'nullable|string|max:20',
            // Social
            'linkedin_url' => 'nullable|url|max:255',
            'pronouns' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:500',
        ]);

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
        ]);
    }

    public function uploadPhoto(Request $request): JsonResponse
    {
        $employee = $this->getEmployee();

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($employee->profile_photo) {
            Storage::disk('public')->delete($employee->profile_photo);
        }

        $path = $request->file('photo')->store(
            'profile-photos/' . $employee->tenant_id,
            'public'
        );

        $employee->update(['profile_photo' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Photo uploaded successfully.',
            'photo_url' => Storage::url($path),
        ]);
    }

    public function removePhoto(): JsonResponse
    {
        $employee = $this->getEmployee();

        if ($employee->profile_photo) {
            Storage::disk('public')->delete($employee->profile_photo);
            $employee->update(['profile_photo' => null]);
        }

        return response()->json(['success' => true, 'message' => 'Photo removed.']);
    }

    // ── Education CRUD ──────────────────────────────────────────────────
    public function storeEducation(Request $request): JsonResponse
    {
        $employee = $this->getEmployee();

        $validated = $request->validate([
            'degree' => 'required|string|max:100',
            'institution' => 'required|string|max:150',
            'field_of_study' => 'nullable|string|max:100',
            'year_from' => 'nullable|integer|min:1950|max:2099',
            'year_to' => 'nullable|integer|min:1950|max:2099',
        ]);

        $edu = $employee->educations()->create([
            ...$validated,
            'tenant_id' => $employee->tenant_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Education added.',
            'education' => [
                'id' => $edu->id,
                'degree' => $edu->degree,
                'institution' => $edu->institution,
                'field_of_study' => $edu->field_of_study,
                'year_from' => $edu->year_from,
                'year_to' => $edu->year_to,
            ],
        ]);
    }

    public function destroyEducation(int $id): JsonResponse
    {
        $employee = $this->getEmployee();
        $employee->educations()->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Education removed.']);
    }

    // ── Experience CRUD ─────────────────────────────────────────────────
    public function storeExperience(Request $request): JsonResponse
    {
        $employee = $this->getEmployee();

        $validated = $request->validate([
            'company' => 'required|string|max:150',
            'designation' => 'required|string|max:100',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'description' => 'nullable|string|max:500',
        ]);

        $exp = $employee->experiences()->create([
            ...$validated,
            'tenant_id' => $employee->tenant_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Experience added.',
            'experience' => [
                'id' => $exp->id,
                'company' => $exp->company,
                'designation' => $exp->designation,
                'from_date' => $exp->from_date?->format('Y-m-d'),
                'to_date' => $exp->to_date?->format('Y-m-d'),
                'description' => $exp->description,
            ],
        ]);
    }

    public function destroyExperience(int $id): JsonResponse
    {
        $employee = $this->getEmployee();
        $employee->experiences()->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Experience removed.']);
    }

    // ── Skills CRUD ─────────────────────────────────────────────────────
    public function storeSkill(Request $request): JsonResponse
    {
        $employee = $this->getEmployee();

        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'proficiency' => 'nullable|string|in:beginner,intermediate,expert',
        ]);

        $skill = $employee->skills()->create([
            ...$validated,
            'tenant_id' => $employee->tenant_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Skill added.',
            'skill' => [
                'id' => $skill->id,
                'name' => $skill->name,
                'proficiency' => $skill->proficiency,
            ],
        ]);
    }

    public function destroySkill(int $id): JsonResponse
    {
        $employee = $this->getEmployee();
        $employee->skills()->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Skill removed.']);
    }

    // ── Account Management ──────────────────────────────────────────────
    public function updateAccount(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return response()->json(['success' => true, 'message' => 'Account updated successfully.']);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true, 'message' => 'Account deleted.', 'redirect' => '/']);
    }
}
