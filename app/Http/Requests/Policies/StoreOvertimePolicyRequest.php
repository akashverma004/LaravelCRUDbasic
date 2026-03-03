<?php

namespace App\Http\Requests\Policies;

class StoreOvertimePolicyRequest extends BasePolicyRequest
{
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'minimum_minutes' => ['nullable', 'integer', 'min:0'],
            'weekday_multiplier' => ['nullable', 'numeric', 'min:0'],
            'weekend_multiplier' => ['nullable', 'numeric', 'min:0'],
            'holiday_multiplier' => ['nullable', 'numeric', 'min:0'],
            'max_hours_per_month' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}
