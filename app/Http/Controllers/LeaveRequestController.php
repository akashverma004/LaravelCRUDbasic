<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Services\LeaveService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function __construct(private LeaveService $leaveService) {}

    public function index(): View
    {
        $monthInput = request('month', now()->format('Y-m'));
        try {
            $monthStart = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } catch (\Throwable) {
            $monthStart = now()->startOfMonth();
        }
        $monthEnd = $monthStart->copy()->endOfMonth();

        $filters = request()->only(['q', 'department_id', 'sort']);
        $filters['sort'] = $filters['sort'] ?? 'name_asc';

        $employeesQuery = Employee::query()
            ->with('department')
            ->with([
                'leaveRequests' => fn ($query) => $query
                    ->where('status', 'approved')
                    ->whereDate('end_date', '>=', $monthStart->toDateString())
                    ->whereDate('start_date', '<=', $monthEnd->toDateString())
                    ->orderBy('start_date'),
            ]);

        if (! empty($filters['q'])) {
            $searchTerm = $filters['q'];
            $employeesQuery->where(function ($query) use ($searchTerm) {
                $query
                    ->where('full_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('department', fn ($departmentQuery) => $departmentQuery->where('name', 'like', '%' . $searchTerm . '%'));
            });
        }

        if (! empty($filters['department_id'])) {
            $employeesQuery->where('department_id', (int) $filters['department_id']);
        }

        match ($filters['sort']) {
            'name_desc' => $employeesQuery->orderByDesc('full_name'),
            'department' => $employeesQuery->join('departments', 'employees.department_id', '=', 'departments.id')
                ->select('employees.*')
                ->orderBy('departments.name')
                ->orderBy('employees.full_name'),
            default => $employeesQuery->orderBy('full_name'),
        };

        $employees = $employeesQuery->get();

        $eventMap = [];
        foreach ($employees as $employee) {
            $eventMap[$employee->id] = $employee->leaveRequests->map(function ($leave) use ($monthStart, $monthEnd) {
                $clampedStart = $leave->start_date->lt($monthStart) ? $monthStart->copy() : $leave->start_date->copy();
                $clampedEnd = $leave->end_date->gt($monthEnd) ? $monthEnd->copy() : $leave->end_date->copy();

                $startCol = $monthStart->diffInDays($clampedStart) + 1;
                $endCol = $monthStart->diffInDays($clampedEnd) + 2;

                return [
                    'start_col' => $startCol,
                    'end_col' => $endCol,
                    'type' => $leave->leave_type,
                    'label' => $this->buildLeaveLabel($leave->leave_type, $leave->leave_session),
                ];
            })->values();
        }

        $calendarDays = collect(range(1, $monthEnd->day))->map(function (int $day) use ($monthStart) {
            $date = $monthStart->copy()->day($day);

            return [
                'day' => $day,
                'dow' => strtoupper($date->format('D')),
                'is_weekend' => $date->isWeekend(),
            ];
        });

        $departments = Department::orderBy('name')->get();

        return view('hrms.leaves.index', [
            'employees' => $employees,
            'eventMap' => $eventMap,
            'calendarDays' => $calendarDays,
            'monthStart' => $monthStart,
            'prevMonth' => $monthStart->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $monthStart->copy()->addMonth()->format('Y-m'),
            'filters' => $filters,
            'departments' => $departments,
        ]);
    }

    public function pending(): View
    {
        $tab = request('tab', 'pending');
        $leaves = $tab === 'all'
            ? $this->leaveService->getAllLeaveRequests()
            : $this->leaveService->getPendingLeaveRequests();

        return view('hrms.leaves.pending', compact('leaves', 'tab'));
    }

    public function create(): View
    {
        $employees = Employee::orderBy('full_name')->get();
        return view('hrms.leaves.create', compact('employees'));
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $this->leaveService->createLeaveRequest($request->validated());
        return redirect()->route('leaves.index')->with('status', 'Leave request submitted successfully.');
    }

    public function show(int $id): View
    {
        $leave = LeaveRequest::with('employee')->findOrFail($id);
        return view('hrms.leaves.show', compact('leave'));
    }

    public function approve(int $id): RedirectResponse
    {
        $leave = LeaveRequest::findOrFail($id);
        $this->leaveService->approveLeaveRequest($leave);
        return redirect()->back()->with('status', 'Leave request approved.');
    }

    public function reject(int $id): RedirectResponse
    {
        $leave = LeaveRequest::findOrFail($id);
        $this->leaveService->rejectLeaveRequest($leave);
        return redirect()->back()->with('status', 'Leave request rejected.');
    }

    private function buildLeaveLabel(string $leaveType, ?string $leaveSession): string
    {
        $type = ucfirst($leaveType);
        $session = match ($leaveSession) {
            'morning' => 'Morning',
            'evening' => 'Evening',
            default => null,
        };

        return $session ? $type . ' (' . $session . ')' : $type;
    }
}
