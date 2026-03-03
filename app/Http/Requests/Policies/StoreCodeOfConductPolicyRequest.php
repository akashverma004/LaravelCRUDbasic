<?php

namespace App\Http\Requests\Policies;

class StoreCodeOfConductPolicyRequest extends BasePolicyRequest
{
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'document_version' => ['nullable', 'string', 'max:30'],
            'acknowledgement_required' => ['sometimes', 'boolean'],
            'policy_text' => ['nullable', 'string'],
            'breach_actions' => ['nullable', 'array'],
        ]);
    }
}
