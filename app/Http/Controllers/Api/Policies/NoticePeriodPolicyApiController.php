<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Requests\Policies\StoreNoticePeriodPolicyRequest;
use App\Http\Requests\Policies\UpdateNoticePeriodPolicyRequest;
use App\Services\Policies\NoticePeriodPolicyService;
use Illuminate\Http\JsonResponse;

class NoticePeriodPolicyApiController extends BasePolicyApiController
{
    public function __construct(NoticePeriodPolicyService $service)
    {
        parent::__construct($service);
    }

    public function store(StoreNoticePeriodPolicyRequest $request): JsonResponse
    {
        return $this->storeValidated($request);
    }

    public function update(UpdateNoticePeriodPolicyRequest $request, int $id): JsonResponse
    {
        return $this->updateValidated($request, $id);
    }
}
