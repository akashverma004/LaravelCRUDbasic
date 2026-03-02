<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'manager_id' => ['nullable', 'integer', 'exists:employees,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'phone' => ['required', 'string', 'max:30'],
            'job_title' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', 'in:full-time,part-time,contract,intern'],
            'salary' => ['required', 'numeric', 'min:0'],
            'joined_on' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', 'in:active,on-leave,resigned'],
        ];
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
