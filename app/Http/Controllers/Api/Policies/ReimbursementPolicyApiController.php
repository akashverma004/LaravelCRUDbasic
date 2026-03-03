<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Requests\Policies\StoreReimbursementPolicyRequest;
use App\Http\Requests\Policies\UpdateReimbursementPolicyRequest;
use App\Services\Policies\ReimbursementPolicyService;
use Illuminate\Http\JsonResponse;

class ReimbursementPolicyApiController extends BasePolicyApiController
{
    public function __construct(ReimbursementPolicyService $service)
    {
        parent::__construct($service);
    }

    public function store(StoreReimbursementPolicyRequest $request): JsonResponse
    {
        return parent::store($request);
    }

    public function update(UpdateReimbursementPolicyRequest $request, int $id): JsonResponse
    {
        return parent::update($request, $id);
    }
}
