<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\HolidayPolicy;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveApproved;
use App\Notifications\LeaveRejected;
use App\Notifications\LeaveSubmitted;
use App\Services\LeaveService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
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

    public function pendingData(): JsonResponse
    {
        $tab = request('tab', 'pending');
        $leaves = $tab === 'all'
            ? $this->leaveService->getAllLeaveRequests()
            : $this->leaveService->getPendingLeaveRequests();

        return response()->json([
            'leaves' => $leaves->getCollection()->map(fn (LeaveRequest $leave) => $this->transformLeave($leave))->values(),
            'meta' => [
                'current_page' => $leaves->currentPage(),
                'last_page' => $leaves->lastPage(),
                'per_page' => $leaves->perPage(),
                'total' => $leaves->total(),
            ],
        ]);
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

    public function data(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        
        $employee = Employee::where('email', $user->email)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee record not found'], 404);
        }

        // 1. Leave history for the current employee
        $leaves = LeaveRequest::where('employee_id', $employee->id)
            ->latest()
            ->get()
            ->map(fn($l) => $this->transformLeave($l));

        // 2. Balances
        $policy = $employee->leavePolicy ?: new \App\Models\EmployeeLeavePolicy([
            'annual_limit' => 20, 
            'sick_limit' => 10, 
            'casual_limit' => 10, 
            'unpaid_limit' => 30
        ]);

        $used = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->selectRaw('leave_type, SUM(DATEDIFF(end_date, start_date) + 1) as total')
            ->groupBy('leave_type')
            ->pluck('total', 'leave_type');

        $balances = [
            'annual' => [
                'limit' => $policy->annual_limit,
                'used' => (float)($used['annual'] ?? 0),
                'remaining' => max(0, $policy->annual_limit - ($used['annual'] ?? 0))
            ],
            'sick' => [
                'limit' => $policy->sick_limit,
                'used' => (float)($used['sick'] ?? 0),
                'remaining' => max(0, $policy->sick_limit - ($used['sick'] ?? 0))
            ],
            'casual' => [
                'limit' => $policy->casual_limit,
                'used' => (float)($used['casual'] ?? 0),
                'remaining' => max(0, $policy->casual_limit - ($used['casual'] ?? 0))
            ],
            'unpaid' => [
                'limit' => $policy->unpaid_limit,
                'used' => (float)($used['unpaid'] ?? 0),
                'remaining' => max(0, $policy->unpaid_limit - ($used['unpaid'] ?? 0))
            ],
        ];

        // 3. Who's Away (Today & Upcoming 7 days)
        $today = now()->toDateString();
        $nextWeek = now()->addDays(7)->toDateString();

        $awayToday = LeaveRequest::where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->with('employee:id,full_name,profile_photo,job_title')
            ->get()
            ->map(fn($l) => [
                'id' => $l->employee->id,
                'name' => $l->employee->full_name,
                'photo' => $l->employee->profile_photo,
                'title' => $l->employee->job_title,
                'type' => $l->leave_type,
            ]);

        $upcomingAway = LeaveRequest::where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->whereDate('start_date', '>', $today)
            ->whereDate('start_date', '<=', $nextWeek)
            ->with('employee:id,full_name,profile_photo,job_title')
            ->orderBy('start_date')
            ->get()
            ->map(fn($l) => [
                'id' => $l->employee->id,
                'name' => $l->employee->full_name,
                'photo' => $l->employee->profile_photo,
                'title' => $l->employee->job_title,
                'type' => $l->leave_type,
                'from' => $l->start_date->format('d M'),
            ]);

        // 4. Admin only employees list if needed
        $employees = [];
        if ($user->hasAnyRole(['admin', 'hr_manager'])) {
            $employees = Employee::where('tenant_id', $tenantId)->orderBy('full_name')->get(['id', 'full_name']);
        }

        return response()->json([
            'leaves' => $leaves,
            'balances' => $balances,
            'whoIsAway' => [
                'today' => $awayToday,
                'upcoming' => $upcomingAway
            ],
            'stats' => [
                'pending' => $leaves->where('status', 'pending')->count(),
                'approved' => $leaves->where('status', 'approved')->count(),
            ],
            'employees' => $employees,
            'isAdmin' => $user->hasAnyRole(['admin', 'hr_manager'])
        ]);
    }

    public function store(StoreLeaveRequest $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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

        $leave = $this->leaveService->createLeaveRequest($data);

        // Notify admin/HR users about the new leave request
        $employee = Employee::find($data['employee_id']);
        $adminUsers = User::where('tenant_id', auth()->user()->tenant_id)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'hr_manager']))
            ->where('id', '!=', auth()->id())
            ->get();

        if ($employee && $adminUsers->isNotEmpty()) {
            Notification::send($adminUsers, new LeaveSubmitted($leave, $employee->full_name));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'leave' => $leave->load('employee'),
                'message' => 'Leave request submitted successfully.',
                'redirect' => $user->hasAnyRole(['admin', 'hr_manager']) ? route('leaves.index') : route('leaves.my'),
            ]);
        }

        return redirect()->route('leaves.index')->with('status', 'Leave request submitted successfully.');
    }

    public function show(int $id): View
    {
        $leave = LeaveRequest::with('employee')->findOrFail($id);
        return view('hrms.leaves.show', compact('leave'));
    }

    public function approve(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $leave = LeaveRequest::with('employee')->findOrFail($id);
        $this->leaveService->approveLeaveRequest($leave);

        // Notify the employee
        $employeeUser = User::where('email', $leave->employee->email)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();
        $employeeUser?->notify(new LeaveApproved($leave));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Leave request approved.',
                'leave' => $this->transformLeave($leave->fresh()->load('employee')),
            ]);
        }

        return redirect()->back()->with('status', 'Leave request approved.');
    }

    public function reject(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $leave = LeaveRequest::with('employee')->findOrFail($id);
        $this->leaveService->rejectLeaveRequest($leave);

        // Notify the employee
        $employeeUser = User::where('email', $leave->employee->email)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();
        $employeeUser?->notify(new LeaveRejected($leave));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Leave request rejected.',
                'leave' => $this->transformLeave($leave->fresh()->load('employee')),
            ]);
        }

        return redirect()->back()->with('status', 'Leave request rejected.');
    }

    public function my(): View
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (!$employee) {
            return view('hrms.leaves.my', [
                'leaves' => collect(),
                'employee' => null,
                'error' => 'No employee record found for your account. Please contact HR.'
            ]);
        }

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

    public function update(StoreLeaveRequest $request, int $id): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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
                if ($request->wantsJson()) return response()->json(['error' => 'Only pending requests can be updated.'], 403);
                return redirect()->route('leaves.my')->with('error', 'Only pending requests can be updated.');
            }

            // Force employee_id and status for regular employees
            $data['employee_id'] = $employee->id;
            $data['status'] = 'pending';
        }

        $this->leaveService->updateLeaveRequest($leave, $data);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'leave' => $leave->fresh()->load('employee'),
                'message' => 'Leave request updated successfully.',
                'redirect' => $user->hasAnyRole(['admin', 'hr_manager']) ? route('leaves.index') : route('leaves.my'),
            ]);
        }

        return redirect()->route($user->hasAnyRole(['admin', 'hr_manager']) ? 'leaves.index' : 'leaves.my')
            ->with('status', 'Leave request updated successfully.');
    }

    public function destroy(Request $request, int $id): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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
                if ($request->wantsJson()) return response()->json(['error' => 'Only pending requests can be deleted.'], 403);
                return redirect()->back()->with('error', 'Only pending requests can be deleted.');
            }
        }

        $this->leaveService->deleteLeaveRequest($leave);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Leave request deleted successfully.',
                'redirect' => $user->hasAnyRole(['admin', 'hr_manager']) ? route('leaves.index') : route('leaves.my'),
            ]);
        }
        
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

    private function transformLeave(LeaveRequest $leave): array
    {
        return [
            'id' => $leave->id,
            'employee_id' => $leave->employee_id,
            'employee_name' => $leave->employee?->full_name,
            'leave_type' => $leave->leave_type,
            'leave_session' => $leave->leave_session,
            'status' => $leave->status,
            'reason' => $leave->reason,
            'start_date' => $leave->start_date?->format('Y-m-d'),
            'end_date' => $leave->end_date?->format('Y-m-d'),
            'start_label' => $leave->start_date?->format('d M Y'),
            'end_label' => $leave->end_date?->format('d M Y'),
            'days' => $leave->start_date && $leave->end_date ? $leave->start_date->diffInDays($leave->end_date) + 1 : 0,
            'created_at' => $leave->created_at?->format('Y-m-d H:i:s'),
            'show_url' => route('leaves.show', $leave->id),
        ];
    }
}
