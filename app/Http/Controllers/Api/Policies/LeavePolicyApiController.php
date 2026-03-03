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
        return parent::store($request);
    }

    public function update(UpdateLeavePolicyRequest $request, int $id): JsonResponse
    {
        return parent::update($request, $id);
    }
}
