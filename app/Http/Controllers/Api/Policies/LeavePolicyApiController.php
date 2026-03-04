<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Requests\Policies\StoreLeavePolicyRequest;
use App\Http\Requests\Policies\UpdateLeavePolicyRequest;
use App\Services\Policies\LeavePolicyService;
use Illuminate\Http\JsonResponse;

class LeavePolicyApiController extends BasePolicyApiController
{
    public function __construct(LeavePolicyService $service)
    {
        parent::__construct($service);
    }

    public function store(StoreLeavePolicyRequest $request): JsonResponse
    {
        return $this->storeValidated($request);
    }

    public function update(UpdateLeavePolicyRequest $request, int $id): JsonResponse
    {
        return $this->updateValidated($request, $id);
    }
}
