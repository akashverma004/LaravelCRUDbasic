<?php

namespace App\Livewire\Leaves;

use App\Models\Department;
use App\Models\Employee;
use App\Models\HolidayPolicy;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('hrms.layouts.app')]
#[Title('Leaves Overview - PeopleFlow HRMS')]
class LeaveCalendar extends Component
{
    public string $month = '';
    public string $q = '';
    public ?int $department_id = null;

    protected $queryString = [
        'month' => ['except' => ''],
        'q' => ['except' => ''],
        'department_id' => ['except' => null],
    ];

    public function mount()
    {
        $this->month = $this->month ?: now()->format('Y-m');
    }

    public function prevMonth()
    {
        $this->month = Carbon::createFromFormat('Y-m', $this->month)->subMonth()->format('Y-m');
    }

    public function nextMonth()
    {
        $this->month = Carbon::createFromFormat('Y-m', $this->month)->addMonth()->format('Y-m');
    }

    public function resetFilters()
    {
        $this->reset(['q', 'department_id']);
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

    public function render()
    {
        try {
            $monthStart = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        } catch (\Throwable) {
            $monthStart = now()->startOfMonth();
            $this->month = $monthStart->format('Y-m');
        }
        $monthEnd = $monthStart->copy()->endOfMonth();

        $employeesQuery = Employee::query()
            ->with(['department', 'leaveRequests' => function ($query) use ($monthStart, $monthEnd) {
                $query->where('status', 'approved')
                    ->whereDate('end_date', '>=', $monthStart->toDateString())
                    ->whereDate('start_date', '<=', $monthEnd->toDateString())
                    ->orderBy('start_date');
            }]);

        if (!empty($this->q)) {
            $searchTerm = $this->q;
            $employeesQuery->where(function ($query) use ($searchTerm) {
                $query->where('full_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($this->department_id) {
            $employeesQuery->where('department_id', $this->department_id);
        }

        $employees = $employeesQuery->orderBy('full_name')->get();

        $holidayPolicies = HolidayPolicy::query()
            ->with(['holidayDates' => function ($query) use ($monthStart, $monthEnd) {
                $query->whereDate('holiday_date', '>=', $monthStart->toDateString())
                    ->whereDate('holiday_date', '<=', $monthEnd->toDateString())
                    ->orderBy('holiday_date');
            }])
            ->where('is_active', true)
            ->where(function ($query) use ($monthEnd) {
                $query->whereNull('effective_from')->orWhereDate('effective_from', '<=', $monthEnd->toDateString());
            })
            ->where(function ($query) use ($monthStart) {
                $query->whereNull('effective_to')->orWhereDate('effective_to', '>=', $monthStart->toDateString());
            })
            ->get()
            ->keyBy(fn (HolidayPolicy $p) => strtoupper((string)$p->country_code) . '|' . strtoupper((string)$p->state_code));

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
            });

            $policyKey = strtoupper((string)$employee->country) . '|' . strtoupper((string)$employee->state);
            $holidayEvents = collect();
            $matchedPolicy = $holidayPolicies->get($policyKey);
            if ($matchedPolicy) {
                $holidayEvents = $matchedPolicy->holidayDates->map(function ($hd) use ($monthStart) {
                    $startCol = $monthStart->diffInDays($hd->holiday_date) + 1;
                    return [
                        'start_col' => $startCol,
                        'end_col' => $startCol + 1,
                        'type' => 'holiday',
                        'label' => $hd->name . ($hd->is_optional ? ' (Optional)' : ''),
                        'priority' => 1,
                    ];
                });
            }

            $eventMap[$employee->id] = $holidayEvents->concat($leaveEvents)->sortBy('priority')->values();
        }

        $calendarDays = collect(range(1, $monthEnd->day))->map(function ($day) use ($monthStart) {
            $date = $monthStart->copy()->day($day);
            return [
                'day' => $day,
                'dow' => strtoupper($date->format('D')),
                'is_weekend' => $date->isWeekend(),
            ];
        });

        return view('livewire.leaves.leave-calendar', [
            'employees' => $employees,
            'eventMap' => $eventMap,
            'calendarDays' => $calendarDays,
            'monthStart' => $monthStart,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}
