<?php

namespace App\Http\Controllers;

use App\Services\OrganizationService;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class OrgChartController extends Controller
{
    public function __construct(private OrganizationService $organizationService) {}

    public function index(): View
    {
        $ceo = $this->organizationService->getOrganizationHierarchy()->first();
        $stats = $this->organizationService->getOrgChartStats();

        return view('hrms.org-chart.index', compact('ceo', 'stats'));
    }

    public function getHierarchy(): JsonResponse
    {
        $hierarchy = $this->organizationService->buildHierarchyTree('json');
        return response()->json($hierarchy);
    }

    public function show(int $id): JsonResponse
    {
        $employee = \App\Models\Employee::with('subordinates.department', 'manager', 'department')->findOrFail($id);

        return response()->json([
            'id' => $employee->id,
            'name' => $employee->full_name,
            'title' => $employee->job_title,
            'department' => $employee->department?->name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'status' => $employee->status,
            'employment_type' => $employee->employment_type,
            'joined_on' => $employee->joined_on?->format('d M Y'),
            'manager' => $employee->manager?->full_name,
            'direct_reports' => $employee->subordinates->count(),
            'subordinates' => $employee->subordinates->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->full_name,
                'title' => $e->job_title,
                'department' => $e->department?->name,
            ])->toArray(),
        ]);
    }
}
