<?php

namespace App\Http\Requests;

use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departmentId = $this->route('id');
        $tenantId = TenantContext::id() ?? (int) auth()->user()?->tenant_id ?: 1;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30', Rule::unique('departments', 'code')->ignore($departmentId)->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            // Lead is fully optional — useful when no employees exist yet in a fresh company.
            'lead_employee_id' => ['nullable', 'integer', Rule::exists('employees', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'lead_name'        => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Department name is required.',
            'code.required' => 'Department code is required.',
            'code.unique'   => 'This department code already exists.',
        ];
    }
}

