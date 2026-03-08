<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\HolidayPolicy;
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

        $holidayPolicies = HolidayPolicy::query()
            ->with([
                'holidayDates' => fn ($query) => $query
                    ->whereDate('holiday_date', '>=', $monthStart->toDateString())
                    ->whereDate('holiday_date', '<=', $monthEnd->toDateString())
                    ->orderBy('holiday_date'),
            ])
            ->where('is_active', true)
            ->where(function ($query) use ($monthEnd) {
                $query->whereNull('effective_from')
                    ->orWhereDate('effective_from', '<=', $monthEnd->toDateString());
            })
            ->where(function ($query) use ($monthStart) {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $monthStart->toDateString());
            })
            ->get()
            ->keyBy(fn (HolidayPolicy $policy) => strtoupper((string) $policy->country_code) . '|' . strtoupper((string) $policy->state_code));

        $eventMap = [];
        foreach ($employees as $employee) {
            $leaveEvents = $employee->leaveRequests->map(function ($leave) use ($monthStart, $monthEnd) {
                $clampedStart = $leave->start_date->lt($monthStart) ? $monthStart->copy() : $leave->start_date->copy();
                $clampedEnd = $leave->end_date->gt($monthEnd) ? $monthEnd->copy() : $leave->end_date->copy();

                $startCol = $monthStart->diffInDays($clampedStart) + 1;
                $endCol = $monthStart->diffInDays($clampedEnd) + 2;

                return [
                    'start_col' => $startCol,
                    'end_col' => $endCol,
                    'type' => $leave->leave_type,
                    'label' => $this->buildLeaveLabel($leave->leave_type, $leave->leave_session),
                    'priority' => 2,
                ];
            })->values();

            $policyKey = strtoupper((string) $employee->country) . '|' . strtoupper((string) $employee->state);
            $holidayEvents = collect();

            $matchedPolicy = $holidayPolicies->get($policyKey);
            if ($matchedPolicy) {
                $holidayEvents = $matchedPolicy->holidayDates->map(function ($holidayDate) use ($monthStart) {
                    $dayOffset = $monthStart->diffInDays($holidayDate->holiday_date->copy());
                    $startCol = $dayOffset + 1;

                    return [
                        'start_col' => $startCol,
                        'end_col' => $startCol + 1,
                        'type' => 'holiday',
                        'label' => $holidayDate->name . ($holidayDate->is_optional ? ' (Optional)' : ''),
                        'priority' => 1,
                    ];
                })->values();
            }

            $eventMap[$employee->id] = $holidayEvents
                ->concat($leaveEvents)
                ->sortBy('priority')
                ->values();
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
        $user = auth()->user();
        $isAdminOrHR = $user->hasAnyRole(['admin', 'hr_manager']);
        
        if ($isAdminOrHR) {
            $employees = Employee::orderBy('full_name')->get();
        } else {
            // For regular employees, they can only see themselves.
            $employees = Employee::where('email', $user->email)
                ->where('tenant_id', $user->tenant_id)
                ->get();
        }

        return view('hrms.leaves.create', compact('employees', 'isAdminOrHR'));
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $isAdminOrHR = $user->hasAnyRole(['admin', 'hr_manager']);
        $data = $request->validated();

        if (! $isAdminOrHR) {
            // Security: Force employee_id to the authenticated user's employee record
            $employee = Employee::where('email', $user->email)
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();
            
            $data['employee_id'] = $employee->id;
            // Security: Force status to pending regardless of what was sent
            $data['status'] = 'pending';
        }

        $this->leaveService->createLeaveRequest($data);
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

    public function my(): View
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        $leaves = LeaveRequest::where('employee_id', $employee->id)
            ->latest()
            ->paginate(15);

        return view('hrms.leaves.my', compact('leaves', 'employee'));
    }

    public function edit(int $id): View
    {
        $user = auth()->user();
        $leave = LeaveRequest::with('employee')->findOrFail($id);

        // Security check
        if (! $user->hasAnyRole(['admin', 'hr_manager'])) {
            $employee = Employee::where('email', $user->email)
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            if ($leave->employee_id !== $employee->id) {
                abort(403, 'Unauthorized action.');
            }

            if ($leave->status !== 'pending') {
                return redirect()->route('leaves.my')->with('error', 'Only pending requests can be edited.');
            }
        }

        $employees = Employee::orderBy('full_name')->get();
        $isAdminOrHR = $user->hasAnyRole(['admin', 'hr_manager']);

        return view('hrms.leaves.edit', compact('leave', 'employees', 'isAdminOrHR'));
    }

    public function update(StoreLeaveRequest $request, int $id): RedirectResponse
    {
        $user = auth()->user();
        $leave = LeaveRequest::findOrFail($id);
        $data = $request->validated();

        // Security check
        if (! $user->hasAnyRole(['admin', 'hr_manager'])) {
            $employee = Employee::where('email', $user->email)
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            if ($leave->employee_id !== $employee->id) {
                abort(403, 'Unauthorized action.');
            }

            if ($leave->status !== 'pending') {
                return redirect()->route('leaves.my')->with('error', 'Only pending requests can be updated.');
            }

            // Force employee_id and status for regular employees
            $data['employee_id'] = $employee->id;
            $data['status'] = 'pending';
        }

        $this->leaveService->updateLeaveRequest($leave, $data);
        return redirect()->route($user->hasAnyRole(['admin', 'hr_manager']) ? 'leaves.index' : 'leaves.my')
            ->with('status', 'Leave request updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = auth()->user();
        $leave = LeaveRequest::findOrFail($id);

        // Security check
        if (! $user->hasAnyRole(['admin', 'hr_manager'])) {
            $employee = Employee::where('email', $user->email)
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            if ($leave->employee_id !== $employee->id) {
                abort(403, 'Unauthorized action.');
            }

            if ($leave->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requests can be deleted.');
            }
        }

        $this->leaveService->deleteLeaveRequest($leave);
        return redirect()->back()->with('status', 'Leave request deleted successfully.');
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

    public function events(): View
    {
        return view('hrms.events.index');
    }
}
