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
        return parent::store($request);
    }

    public function update(UpdateNoticePeriodPolicyRequest $request, int $id): JsonResponse
    {
        return parent::update($request, $id);
    }
}
