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

    public function data(): JsonResponse
    {
        $departments = Department::query()
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return response()->json([
            'departments' => $departments->map(fn (Department $department) => $this->transformDepartment($department))->values(),
        ]);
    }

    public function create(): View
    {
        $employees = Employee::orderBy('full_name')->get();

        return view('hrms.departments.create', compact('employees'));
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        // Resolve lead_name from the chosen employee when one is selected.
        if (! empty($validated['lead_employee_id'])) {
            $validated['lead_name'] = Employee::findOrFail($validated['lead_employee_id'])->full_name;
        }
        // lead_employee_id is not a real DB column — remove it before persisting.
        unset($validated['lead_employee_id']);

        $department = $this->departmentService->createDepartment($validated)->loadCount('employees');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Department created successfully.',
                'department' => $this->transformDepartment($department),
            ]);
        }

        return redirect()->route('departments.index')->with('status', 'Department created successfully.');
    }

    public function show(int $id): View
    {
        $department = $this->departmentService->getDepartmentWithEmployees($id);
        $employees = Employee::orderBy('full_name')->get();

        return view('hrms.departments.show', compact('department', 'employees'));
    }

    public function edit(int $id): View
    {
        // View replaced by inline editing in `show` block. Still keeping logic if needed.
        return $this->show($id);
    }

    public function update(StoreDepartmentRequest $request, int $id)
    {
        $department = $this->departmentService->getDepartmentById($id);
        $validated  = $request->validated();

        // Resolve lead_name from the chosen employee when one is selected.
        if (! empty($validated['lead_employee_id'])) {
            $validated['lead_name'] = Employee::findOrFail($validated['lead_employee_id'])->full_name;
        }
        unset($validated['lead_employee_id']);

        $this->departmentService->updateDepartment($department, $validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Department updated successfully.']);
        }

        return redirect()->route('departments.show', $id)->with('status', 'Department updated successfully.');
    }


    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $department = $this->departmentService->getDepartmentById($id);
        $this->departmentService->deleteDepartment($department);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Department deleted successfully.',
            ]);
        }

        return redirect()->route('departments.index')->with('status', 'Department deleted successfully.');
    }

    private function transformDepartment(Department $department): array
    {
        return [
            'id' => $department->id,
            'name' => $department->name,
            'code' => $department->code,
            'lead_name' => $department->lead_name,
            'employees_count' => $department->employees_count ?? 0,
            'show_url' => route('departments.show', $department->id),
            'delete_url' => route('departments.destroy', $department->id),
        ];
    }
}
