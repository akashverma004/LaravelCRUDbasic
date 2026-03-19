<?php

namespace App\Http\Requests;

use App\Support\GeoLookup;
use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('id');
        $tenantId = TenantContext::id() ?? (int) auth()->user()?->tenant_id ?: 1;
        $isUpdate = $this->isMethod('PATCH') || $this->isMethod('PUT');
        $required = $isUpdate ? 'sometimes' : 'required';

        return [
            'department_id' => [$required, 'integer', Rule::exists('departments', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'manager_id' => ['sometimes', 'nullable', 'integer', Rule::exists('employees', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'role_id' => ['sometimes', 'nullable', 'integer', Rule::exists('roles', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'full_name' => [$required, 'string', 'max:255'],
            'email' => [$required, 'email', 'max:255', Rule::unique('employees', 'email')->ignore($employeeId)->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'phone' => [$required, 'string', 'max:30'],
            'job_title' => [$required, 'string', 'max:255'],
            'employment_type' => [$required, 'in:full-time,part-time,contract,intern'],
            'salary' => [$required, 'numeric', 'min:0', 'max:9999999999999'],
            'joined_on' => [$required, 'date', 'before_or_equal:today'],
            'status' => [$required, 'in:active,on-leave,resigned'],
            'country' => [$required, Rule::in(array_keys(config('geo.countries', [])))],
            'state' => [$required, Rule::in(array_keys(config('geo.states_in', [])))],
            'city' => [$required, 'string', 'max:100'],
            'address' => [$required, 'string', 'max:500'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'hobbies' => ['nullable', 'string'],
            'likes' => ['nullable', 'string'],
            'food_preference' => ['nullable', 'in:veg,non-veg'],
            'health_issues' => ['nullable', 'string'],
            'password' => [$employeeId ? 'nullable' : 'required', 'string', 'min:8'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:50'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            'pan_number' => ['nullable', 'string', 'max:50'],
            'aadhaar_number' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
            'bank_ifsc' => ['nullable', 'string', 'max:50'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        if ($this->has('country')) {
            $normalized['country'] = GeoLookup::normalizeCountryCode($this->input('country'));
        }

        if ($this->has('state')) {
            $normalized['state'] = GeoLookup::normalizeIndianStateCode($this->input('state'));
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Department selection is required.',
            'email.unique' => 'This email address is already in use.',
            'employment_type.in' => 'Invalid employment type selected.',
            'joined_on.before_or_equal' => 'Join date cannot be in the future.',
        ];
    }
}
