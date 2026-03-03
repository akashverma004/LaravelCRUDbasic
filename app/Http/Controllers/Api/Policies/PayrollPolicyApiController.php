<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Requests\Policies\StorePayrollPolicyRequest;
use App\Http\Requests\Policies\UpdatePayrollPolicyRequest;
use App\Services\Policies\PayrollPolicyService;
use Illuminate\Http\JsonResponse;

class PayrollPolicyApiController extends BasePolicyApiController
{
    public function __construct(PayrollPolicyService $service)
    {
        parent::__construct($service);
    }

    public function store(StorePayrollPolicyRequest $request): JsonResponse
    {
        return parent::store($request);
    }

    public function update(UpdatePayrollPolicyRequest $request, int $id): JsonResponse
    {
        return parent::update($request, $id);
    }
}
