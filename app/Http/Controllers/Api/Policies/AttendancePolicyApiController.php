<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Requests\Policies\StoreAttendancePolicyRequest;
use App\Http\Requests\Policies\UpdateAttendancePolicyRequest;
use App\Services\Policies\AttendancePolicyService;
use Illuminate\Http\JsonResponse;

class AttendancePolicyApiController extends BasePolicyApiController
{
    public function __construct(AttendancePolicyService $service)
    {
        parent::__construct($service);
    }

    public function store(StoreAttendancePolicyRequest $request): JsonResponse
    {
        return $this->storeValidated($request);
    }

    public function update(UpdateAttendancePolicyRequest $request, int $id): JsonResponse
    {
        return $this->updateValidated($request, $id);
    }
}
