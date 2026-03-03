<?php

namespace App\Http\Requests\Policies;

use Illuminate\Foundation\Http\FormRequest;

class EvaluateActivePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'context' => ['required', 'array'],
            'tenant_id' => ['nullable', 'integer'],
            'effective_on' => ['nullable', 'date'],
        ];
    }
}
