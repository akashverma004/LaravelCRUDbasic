<?php

namespace App\Http\Requests\Policies;

class StoreWfhPolicyRequest extends BasePolicyRequest
{
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'monthly_limit_days' => ['nullable', 'integer', 'min:0'],
            'approval_required' => ['sometimes', 'boolean'],
            'max_consecutive_days' => ['nullable', 'integer', 'min:0'],
            'allowed_departments' => ['nullable', 'array'],
            'allowed_roles' => ['nullable', 'array'],
        ]);
    }
}
