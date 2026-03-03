<?php

namespace App\Http\Requests\Policies;

class StoreNoticePeriodPolicyRequest extends BasePolicyRequest
{
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'notice_days' => ['nullable', 'integer', 'min:0'],
            'buyout_allowed' => ['sometimes', 'boolean'],
            'waiver_allowed' => ['sometimes', 'boolean'],
        ]);
    }
}
