<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Requests\Policies\StoreProbationPolicyRequest;
use App\Http\Requests\Policies\UpdateProbationPolicyRequest;
use App\Services\Policies\ProbationPolicyService;
use Illuminate\Http\JsonResponse;

class ProbationPolicyApiController extends BasePolicyApiController
{
    public function __construct(ProbationPolicyService $service)
    {
        parent::__construct($service);
    }

    public function store(StoreProbationPolicyRequest $request): JsonResponse
    {
        return $this->storeValidated($request);
    }

    public function update(UpdateProbationPolicyRequest $request, int $id): JsonResponse
    {
        return $this->updateValidated($request, $id);
    }
}
