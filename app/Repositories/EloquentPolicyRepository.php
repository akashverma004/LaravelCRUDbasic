<?php

namespace App\Repositories;

use App\Repositories\Contracts\PolicyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class EloquentPolicyRepository implements PolicyRepositoryInterface
{
    public function __construct(private readonly string $modelClass)
    {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->buildFilteredQuery($filters)
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function all(array $filters = []): Collection
    {
        return $this->buildFilteredQuery($filters)
            ->orderByDesc('id')
            ->get();
    }

    public function find(int $id): ?Model
    {
        return $this->newQuery()->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->newQuery()->findOrFail($id);
    }

    public function findActive(?int $tenantId = null, ?string $effectiveOn = null): ?Model
    {
        $query = $this->newQuery()
            ->where('is_active', true)
            ->when($tenantId, fn (Builder $q) => $q->where('tenant_id', $tenantId))
            ->when(
                $effectiveOn,
                fn (Builder $q) => $q->where(function (Builder $inner) use ($effectiveOn) {
                    $inner
                        ->whereNull('effective_from')
                        ->orWhereDate('effective_from', '<=', $effectiveOn);
                })->where(function (Builder $inner) use ($effectiveOn) {
                    $inner
                        ->whereNull('effective_to')
                        ->orWhereDate('effective_to', '>=', $effectiveOn);
                })
            )
            ->orderByDesc('id');

        return $query->first();
    }

    public function create(array $data): Model
    {
        /** @var Model $model */
        $model = new $this->modelClass();
        $model->fill($data);
        $model->save();

        return $model;
    }

    public function update(Model $model, array $data): Model
    {
        $model->fill($data);
        $model->save();

        return $model;
    }

    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }

    private function buildFilteredQuery(array $filters): Builder
    {
        return $this->newQuery()
            ->when(
                isset($filters['tenant_id']) && $filters['tenant_id'] !== '',
                fn (Builder $q) => $q->where('tenant_id', (int) $filters['tenant_id'])
            )
            ->when(
                isset($filters['is_active']) && $filters['is_active'] !== '',
                fn (Builder $q) => $q->where('is_active', (bool) $filters['is_active'])
            )
            ->when(
                ! empty($filters['q']),
                fn (Builder $q) => $q->where(function (Builder $inner) use ($filters) {
                    $inner
                        ->where('name', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('code', 'like', '%' . $filters['q'] . '%');
                })
            );
    }

    private function newQuery(): Builder
    {
        /** @var Model $instance */
        $instance = new $this->modelClass();

        return $instance->newQuery();
    }
}
