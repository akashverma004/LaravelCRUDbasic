<?php

namespace App\Services\Policies;

use App\Repositories\Contracts\PolicyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicyService
{
    public function __construct(
        protected readonly PolicyRepositoryInterface $repository,
        protected readonly PolicyRuleEvaluator $ruleEvaluator
    ) {
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function all(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function get(int $id): Model
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, ?int $actorId = null): Model
    {
        $payload = $this->prepareWritePayload($data, $actorId, true);
        return $this->repository->create($payload);
    }

    public function update(int $id, array $data, ?int $actorId = null): Model
    {
        $model = $this->get($id);
        $payload = $this->prepareWritePayload($data, $actorId, false);
        return $this->repository->update($model, $payload);
    }

    public function delete(int $id): bool
    {
        $model = $this->get($id);
        return $this->repository->delete($model);
    }

    public function getActivePolicy(?int $tenantId = null, ?string $effectiveOn = null): ?Model
    {
        return $this->repository->findActive($tenantId, $effectiveOn);
    }

    public function evaluatePolicy(int $policyId, array $context): array
    {
        $policy = $this->get($policyId);
        $rules = is_array($policy->rules) ? $policy->rules : [];

        return [
            'policy_id' => $policy->id,
            'policy_type' => $this->getPolicyType(),
            ...$this->ruleEvaluator->evaluate($rules, $context),
        ];
    }

    public function evaluateActivePolicy(array $context, ?int $tenantId = null, ?string $effectiveOn = null): array
    {
        $active = $this->getActivePolicy($tenantId, $effectiveOn);
        if (! $active) {
            return [
                'policy_id' => null,
                'policy_type' => $this->getPolicyType(),
                'passed' => false,
                'mode' => 'all',
                'matched' => [],
                'failed' => [
                    ['reason' => 'No active policy found'],
                ],
            ];
        }

        $rules = is_array($active->rules) ? $active->rules : [];

        return [
            'policy_id' => $active->id,
            'policy_type' => $this->getPolicyType(),
            ...$this->ruleEvaluator->evaluate($rules, $context),
        ];
    }

    abstract protected function getPolicyType(): string;

    protected function prepareWritePayload(array $data, ?int $actorId, bool $isCreate): array
    {
        if ($actorId) {
            if ($isCreate) {
                $data['created_by'] = $actorId;
            }
            $data['updated_by'] = $actorId;
        }

        return $data;
    }
}
