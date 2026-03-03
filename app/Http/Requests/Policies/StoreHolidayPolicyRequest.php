<?php

namespace App\Http\Requests\Policies;

class StoreHolidayPolicyRequest extends BasePolicyRequest
{
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'country_code' => ['nullable', 'string', 'size:3'],
            'state_code' => ['nullable', 'string', 'max:20'],
            'weekend_days' => ['nullable', 'array'],
        ]);
    }
}
