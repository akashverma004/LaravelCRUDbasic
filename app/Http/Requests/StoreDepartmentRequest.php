<?php

namespace App\Http\Requests;

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

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30', Rule::unique('departments', 'code')->ignore($departmentId)],
            'lead_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'lead_name' => ['required_without:lead_employee_id', 'nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Department name is required.',
            'code.required' => 'Department code is required.',
            'code.unique' => 'This department code already exists.',
            'lead_name.required_without' => 'Department lead is required.',
        ];
    }
}
