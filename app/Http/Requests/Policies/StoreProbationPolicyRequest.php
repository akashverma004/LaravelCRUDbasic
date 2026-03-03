<?php

namespace App\Http\Requests\Policies;

class StoreProbationPolicyRequest extends BasePolicyRequest
{
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'probation_days' => ['nullable', 'integer', 'min:0'],
            'extension_allowed' => ['sometimes', 'boolean'],
            'max_extension_days' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
