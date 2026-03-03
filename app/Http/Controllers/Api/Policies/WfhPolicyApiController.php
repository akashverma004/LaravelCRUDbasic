<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Requests\Policies\StoreWfhPolicyRequest;
use App\Http\Requests\Policies\UpdateWfhPolicyRequest;
use App\Services\Policies\WfhPolicyService;
use Illuminate\Http\JsonResponse;

class WfhPolicyApiController extends BasePolicyApiController
{
    public function __construct(WfhPolicyService $service)
    {
        parent::__construct($service);
    }

    public function store(StoreWfhPolicyRequest $request): JsonResponse
    {
        return parent::store($request);
    }

    public function update(UpdateWfhPolicyRequest $request, int $id): JsonResponse
    {
        return parent::update($request, $id);
    }
}
