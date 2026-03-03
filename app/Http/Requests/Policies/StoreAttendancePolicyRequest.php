<?php

namespace App\Http\Requests\Policies;

class StoreAttendancePolicyRequest extends BasePolicyRequest
{
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'standard_hours_per_day' => ['nullable', 'numeric', 'min:0'],
            'grace_minutes' => ['nullable', 'integer', 'min:0'],
            'max_late_marks_per_month' => ['nullable', 'integer', 'min:0'],
            'work_days' => ['nullable', 'array'],
        ]);
    }
}
