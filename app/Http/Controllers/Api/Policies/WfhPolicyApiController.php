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
        return $this->storeValidated($request);
    }

    public function update(UpdateWfhPolicyRequest $request, int $id): JsonResponse
    {
        return $this->updateValidated($request, $id);
    }
}
