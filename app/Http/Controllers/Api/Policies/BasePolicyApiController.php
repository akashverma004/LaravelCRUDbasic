<?php

namespace App\Http\Controllers\Api\Policies;

use App\Http\Controllers\Controller;
use App\Http\Requests\Policies\EvaluateActivePolicyRequest;
use App\Http\Requests\Policies\EvaluatePolicyRequest;
use App\Services\Policies\BasePolicyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BasePolicyApiController extends Controller
{
    public function __construct(protected BasePolicyService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['q', 'tenant_id', 'is_active']);
        $perPage = (int) $request->integer('per_page', 15);
        $policies = $this->service->list($filters, max(1, min($perPage, 100)));

        return response()->json([
            'data' => $policies->items(),
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
        return response()->json(['data' => $policy]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validated();
        $policy = $this->service->create($payload, auth()->id());

        return response()->json([
            'message' => 'Policy created successfully.',
            'data' => $policy,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $payload = $request->validated();
        $policy = $this->service->update($id, $payload, auth()->id());

        return response()->json([
            'message' => 'Policy updated successfully.',
            'data' => $policy,
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

        return response()->json(['data' => $result]);
    }

    public function evaluateActive(EvaluateActivePolicyRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $result = $this->service->evaluateActivePolicy(
            $payload['context'],
            $payload['tenant_id'] ?? null,
            $payload['effective_on'] ?? null
        );

        return response()->json(['data' => $result]);
    }

}
