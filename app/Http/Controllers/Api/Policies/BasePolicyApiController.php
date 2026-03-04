<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Controllers\Controller;
use App\Http\Requests\Policies\EvaluateActivePolicyRequest;
use App\Http\Requests\Policies\EvaluatePolicyRequest;
use App\Http\Resources\Policies\PolicyEvaluationResource;
use App\Http\Resources\Policies\PolicyResource;
use App\Services\Policies\BasePolicyService;
use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BasePolicyApiController extends Controller
{
    public function __construct(protected BasePolicyService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['q', 'is_active']);
        $filters['tenant_id'] = TenantContext::id();
        $perPage = (int) $request->integer('per_page', 15);
        $policies = $this->service->list($filters, max(1, min($perPage, 100)));
        $data = PolicyResource::collection($policies->getCollection())->resolve();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $policies->currentPage(),
                'last_page' => $policies->lastPage(),
                'per_page' => $policies->perPage(),
                'total' => $policies->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $policy = $this->service->get($id);
        return response()->json(['data' => PolicyResource::make($policy)->resolve()]);
    }

    protected function storeValidated(FormRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['tenant_id'] = TenantContext::id();
        $policy = $this->service->create($payload, auth()->id());

        return response()->json([
            'message' => 'Policy created successfully.',
            'data' => PolicyResource::make($policy)->resolve(),
        ], 201);
    }

    protected function updateValidated(FormRequest $request, int $id): JsonResponse
    {
        $payload = $request->validated();
        $payload['tenant_id'] = TenantContext::id();
        $policy = $this->service->update($id, $payload, auth()->id());

        return response()->json([
            'message' => 'Policy updated successfully.',
            'data' => PolicyResource::make($policy)->resolve(),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Policy deleted successfully.',
        ]);
    }

    public function evaluate(EvaluatePolicyRequest $request, int $id): JsonResponse
    {
        $result = $this->service->evaluatePolicy($id, $request->validated('context'));

        return response()->json(['data' => PolicyEvaluationResource::make($result)->resolve()]);
    }

    public function evaluateActive(EvaluateActivePolicyRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $tenantId = TenantContext::id();

        $result = $this->service->evaluateActivePolicy(
            $payload['context'],
            $tenantId,
            $payload['effective_on'] ?? null
        );

        return response()->json(['data' => PolicyEvaluationResource::make($result)->resolve()]);
    }

}
