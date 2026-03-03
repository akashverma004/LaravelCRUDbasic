<?php

namespace App\Http\Requests\Policies;

class StoreLeavePolicyRequest extends BasePolicyRequest
{
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'annual_limit' => ['required', 'integer', 'min:0'],
            'sick_limit' => ['required', 'integer', 'min:0'],
            'casual_limit' => ['required', 'integer', 'min:0'],
            'unpaid_limit' => ['required', 'integer', 'min:0'],
            'carry_forward_limit' => ['nullable', 'integer', 'min:0'],
            'accrual_frequency' => ['nullable', 'in:monthly,quarterly,yearly'],
        ]);
    }
}
