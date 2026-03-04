<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Requests\Policies\StoreOvertimePolicyRequest;
use App\Http\Requests\Policies\UpdateOvertimePolicyRequest;
use App\Services\Policies\OvertimePolicyService;
use Illuminate\Http\JsonResponse;

class OvertimePolicyApiController extends BasePolicyApiController
{
    public function __construct(OvertimePolicyService $service)
    {
        parent::__construct($service);
    }

    public function store(StoreOvertimePolicyRequest $request): JsonResponse
    {
        return $this->storeValidated($request);
    }

    public function update(UpdateOvertimePolicyRequest $request, int $id): JsonResponse
    {
        return $this->updateValidated($request, $id);
    }
}
