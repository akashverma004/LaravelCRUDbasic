<?php

namespace App\Http\Requests\Policies;

use Illuminate\Foundation\Http\FormRequest;

abstract class BasePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function baseRules(): array
    {
        return [
            'tenant_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'rules' => ['nullable', 'array'],
            'exceptions' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
