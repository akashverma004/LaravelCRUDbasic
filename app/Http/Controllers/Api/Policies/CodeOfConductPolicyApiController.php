<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Requests\Policies\StoreCodeOfConductPolicyRequest;
use App\Http\Requests\Policies\UpdateCodeOfConductPolicyRequest;
use App\Services\Policies\CodeOfConductPolicyService;
use Illuminate\Http\JsonResponse;

class CodeOfConductPolicyApiController extends BasePolicyApiController
{
    public function __construct(CodeOfConductPolicyService $service)
    {
        parent::__construct($service);
    }

    public function store(StoreCodeOfConductPolicyRequest $request): JsonResponse
    {
        return parent::store($request);
    }

    public function update(UpdateCodeOfConductPolicyRequest $request, int $id): JsonResponse
    {
        return parent::update($request, $id);
    }
}
