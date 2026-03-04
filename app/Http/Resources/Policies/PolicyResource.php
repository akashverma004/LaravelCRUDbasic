<?php

namespace App\Http\Resources\Policies;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PolicyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $modelClass = get_class($this->resource);
        $policyType = defined($modelClass . '::POLICY_TYPE')
            ? constant($modelClass . '::POLICY_TYPE')
            : Str::snake(class_basename($modelClass));

        $baseAttributes = [
            'id',
            'tenant_id',
            'name',
            'code',
            'description',
            'is_active',
            'effective_from',
            'effective_to',
            'rules',
            'exceptions',
            'metadata',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $raw = $this->resource->getAttributes();
        $moduleSpecific = Arr::except($raw, $baseAttributes);

        return [
            'id' => $this->id,
            'policy_type' => $policyType,
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
            'is_currently_effective' => (bool) ($this->is_currently_effective ?? false),
            'effective_from' => optional($this->effective_from)->toDateString(),
            'effective_to' => optional($this->effective_to)->toDateString(),
            'rules_summary' => $this->rules_summary ?? '0 rule groups',
            'rules' => $this->rules ?? [],
            'exceptions' => $this->exceptions ?? [],
            'metadata' => $this->metadata ?? [],
            'module_specific' => $moduleSpecific,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];
    }
}
