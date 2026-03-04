<?php

namespace App\Http\Resources\Policies;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PolicyEvaluationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'policy_id' => $this['policy_id'] ?? null,
            'policy_type' => $this['policy_type'] ?? null,
            'passed' => (bool) ($this['passed'] ?? false),
            'mode' => $this['mode'] ?? 'all',
            'matched' => $this['matched'] ?? [],
            'failed' => $this['failed'] ?? [],
        ];
    }
}
