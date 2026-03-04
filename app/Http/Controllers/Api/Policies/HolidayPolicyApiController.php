<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Requests\Policies\StoreHolidayPolicyRequest;
use App\Http\Requests\Policies\UpdateHolidayPolicyRequest;
use App\Services\Policies\HolidayPolicyService;
use Illuminate\Http\JsonResponse;

class HolidayPolicyApiController extends BasePolicyApiController
{
    public function __construct(HolidayPolicyService $service)
    {
        parent::__construct($service);
    }

    public function store(StoreHolidayPolicyRequest $request): JsonResponse
    {
        return $this->storeValidated($request);
    }

    public function update(UpdateHolidayPolicyRequest $request, int $id): JsonResponse
    {
        return $this->updateValidated($request, $id);
    }
}
