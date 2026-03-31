<?php

namespace App\Livewire\Attendance;

use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('hrms.layouts.app')]
#[Title('Shift Roster - PeopleFlow HRMS')]
class ShiftRoster extends Component
{
    #[Url]
    public string $weekStart = '';
    
    // Modal states
    public bool $showShiftModal = false;
    public bool $showAssignModal = false;

    // Shift Template Form
    public string $shiftName = '';
    public string $startTime = '';
    public string $endTime = '';
    public string $shiftColor = '#3b82f6';

    // Assignment Form
    public ?int $selectedEmployeeId = null;
    public ?int $selectedShiftId = null;
    public string $assignmentDate = '';

    public function mount()
    {
        $this->weekStart = $this->weekStart ?: now()->startOfWeek()->toDateString();
    }

    public function nextWeek()
    {
        $this->weekStart = Carbon::parse($this->weekStart)->addWeek()->toDateString();
    }

    public function prevWeek()
    {
        $this->weekStart = Carbon::parse($this->weekStart)->subWeek()->toDateString();
    }

    public function createShift()
    {
        $this->validate([
            'shiftName' => 'required|string|max:255',
            'startTime' => 'required',
            'endTime' => 'required',
        ]);

        Shift::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $this->shiftName,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'color' => $this->shiftColor,
        ]);

        $this->reset(['shiftName', 'startTime', 'endTime', 'shiftColor', 'showShiftModal']);
        $this->dispatch('notify', message: 'Shift template created.', type: 'success');
    }

    public function openAssignModal(int $employeeId, string $date)
    {
        $this->selectedEmployeeId = $employeeId;
        $this->assignmentDate = $date;
        $this->showAssignModal = true;
    }

    public function assignShift()
    {
        $this->validate([
            'selectedEmployeeId' => 'required|exists:employees,id',
            'selectedShiftId' => 'required|exists:shifts,id',
            'assignmentDate' => 'required|date',
        ]);

        // Remove existing for this date/employee
        ShiftSchedule::where('employee_id', $this->selectedEmployeeId)
            ->where('date', $this->assignmentDate)
            ->delete();

        ShiftSchedule::create([
            'tenant_id' => auth()->user()->tenant_id,
            'employee_id' => $this->selectedEmployeeId,
            'shift_id' => $this->selectedShiftId,
            'date' => $this->assignmentDate,
        ]);

        $this->showAssignModal = false;
        $this->dispatch('notify', message: 'Shift assigned successfully.', type: 'success');
    }

    public function deleteAssignment(int $id)
    {
        ShiftSchedule::where('id', $id)->where('tenant_id', auth()->user()->tenant_id)->delete();
        $this->dispatch('notify', message: 'Assignment removed.', type: 'info');
    }

    public function render()
    {
        $tenantId = auth()->user()->tenant_id;
        $user = auth()->user();
        $isAdmin = $user->hasAnyRole(['admin', 'hr_manager']);

        $start = Carbon::parse($this->weekStart);
        $end = $start->copy()->endOfWeek();
        
        $days = [];
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $days[] = $date->copy();
        }

        $shifts = Shift::where('tenant_id', $tenantId)->get();
        
        $query = ShiftSchedule::with(['employee', 'shift'])
            ->where('tenant_id', $tenantId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()]);

        if (!$isAdmin) {
            $employee = Employee::where('email', $user->email)->where('tenant_id', $tenantId)->first();
            $query->where('employee_id', $employee?->id ?? 0);
        }

        $schedules = $query->get();
        
        $employees = [];
        if ($isAdmin) {
            $employees = Employee::where('tenant_id', $tenantId)
                ->where('status', 'active')
                ->get(['id', 'full_name', 'job_title']);
        } else {
            $employees = Employee::where('email', $user->email)
                ->where('tenant_id', $tenantId)
                ->get(['id', 'full_name', 'job_title']);
        }

        return view('livewire.attendance.shift-roster', [
            'days' => $days,
            'employees' => $employees,
            'shifts' => $shifts,
            'schedules' => $schedules,
            'isAdmin' => $isAdmin,
        ]);
    }
}
