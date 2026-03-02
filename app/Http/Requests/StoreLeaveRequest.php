<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'leave_type' => ['required', 'in:annual,sick,casual,unpaid'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:500'],
            'status' => ['required', 'in:pending,approved,rejected'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Employee selection is required.',
            'start_date.after_or_equal' => 'Leave start date must be today or later.',
            'end_date.after_or_equal' => 'Leave end date must be after or equal to start date.',
            'reason.required' => 'Leave reason is required.',
        ];
    }
}
