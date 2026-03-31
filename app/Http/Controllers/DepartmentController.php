<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Models\Employee;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(private DepartmentService $departmentService) {}

    public function index(): View
    {
        $departments = Department::query()
            ->withCount('employees')
            ->orderBy('name')
            ->get();
        $employees = Employee::orderBy('full_name')->get();

        return view('hrms.departments.index', compact('departments', 'employees'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
            'lead_employee_id' => 'nullable|exists:employees,id',
        ]);

        if (! empty($validated['lead_employee_id'])) {
            $validated['lead_name'] = Employee::findOrFail($validated['lead_employee_id'])->full_name;
        }
        unset($validated['lead_employee_id']);

        $department = $this->departmentService->createDepartment($validated);

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully.',
            'department' => $department,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $department = $this->departmentService->getDepartmentById($id);
        $this->departmentService->deleteDepartment($department);

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully.',
        ]);
    }
}
