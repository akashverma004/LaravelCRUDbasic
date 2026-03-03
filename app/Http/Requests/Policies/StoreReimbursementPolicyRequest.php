<?php

namespace App\Http\Requests\Policies;

class StoreReimbursementPolicyRequest extends BasePolicyRequest
{
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'monthly_claim_limit' => ['nullable', 'numeric', 'min:0'],
            'single_claim_limit' => ['nullable', 'numeric', 'min:0'],
            'receipt_required' => ['sometimes', 'boolean'],
            'allowed_categories' => ['nullable', 'array'],
            'approval_matrix' => ['nullable', 'array'],
        ]);
    }
}
