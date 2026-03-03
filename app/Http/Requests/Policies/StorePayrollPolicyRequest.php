<?php

namespace App\Http\Requests\Policies;

class StorePayrollPolicyRequest extends BasePolicyRequest
{
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'pay_cycle' => ['nullable', 'in:weekly,biweekly,monthly'],
            'pay_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'cutoff_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'prorate_on_join' => ['sometimes', 'boolean'],
            'prorate_on_exit' => ['sometimes', 'boolean'],
        ]);
    }
}
