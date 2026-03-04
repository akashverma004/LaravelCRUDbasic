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

        return [
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'manager_id' => ['nullable', 'integer', Rule::exists('employees', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'role_id' => ['nullable', 'integer', Rule::exists('roles', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($employeeId)->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'phone' => ['required', 'string', 'max:30'],
            'job_title' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', 'in:full-time,part-time,contract,intern'],
            'salary' => ['required', 'numeric', 'min:0'],
            'joined_on' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', 'in:active,on-leave,resigned'],
            'country' => ['required', Rule::in(array_keys(config('geo.countries', [])))],
            'state' => ['required', Rule::in(array_keys(config('geo.states_in', [])))],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:500'],
            'hobbies' => ['nullable', 'string'],
            'likes' => ['nullable', 'string'],
            'food_preference' => ['nullable', 'in:veg,non-veg'],
            'health_issues' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'country' => GeoLookup::normalizeCountryCode($this->input('country')),
            'state' => GeoLookup::normalizeIndianStateCode($this->input('state')),
        ]);
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
