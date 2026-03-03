<?php

namespace App\Http\Requests\Policies;

class UpdateCodeOfConductPolicyRequest extends StoreCodeOfConductPolicyRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        foreach ($rules as $field => $fieldRules) {
            if (is_array($fieldRules) && ! in_array('sometimes', $fieldRules, true)) {
                array_unshift($fieldRules, 'sometimes');
                $rules[$field] = $fieldRules;
            }
        }

        return $rules;
    }
}
