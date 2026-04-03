<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('My Attendance - PeopleFlow HRMS')]
class AttendanceDashboard extends Component
{
    use WithPagination;

    public bool $loading = false;
    public ?AttendanceRecord $todayRecord = null;
    
    // Status helpers
    public bool $isClockedIn = false;
    public bool $isOnBreak = false;
    public bool $isCompleted = false;

    public function mount()
    {
        $this->loadTodayRecord();
    }

    public function loadTodayRecord()
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->where('tenant_id', $user->tenant_id)->first();
        
        if ($employee) {
            $this->todayRecord = AttendanceRecord::where('employee_id', $employee->id)
                ->where('attendance_date', now()->toDateString())
                ->first();
                
            if ($this->todayRecord) {
                $this->isClockedIn = $this->todayRecord->status === 'clocked_in';
                $this->isOnBreak = str_contains($this->todayRecord->status, 'on_');
                $this->isCompleted = $this->todayRecord->status === 'completed';
            } else {
                $this->isClockedIn = false;
                $this->isOnBreak = false;
                $this->isCompleted = false;
            }
        }
    }

    public function punchIn(float $latitude = null, float $longitude = null)
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->where('tenant_id', $user->tenant_id)->first();
        
        if (!$employee) return;

        $now = now()->toDateTimeString();
        $today = now()->toDateString();
        $ip = request()->ip();

        $this->todayRecord = AttendanceRecord::firstOrCreate(
            ['employee_id' => $employee->id, 'attendance_date' => $today],
            [
                'tenant_id' => $user->tenant_id,
                'clock_in_at' => now()->toTimeString(),
                'work_mode' => 'onsite',
                'status' => 'clocked_in',
                'ip_address' => $ip,
                'location_metadata' => ($latitude && $longitude) ? ['lat' => $latitude, 'lng' => $longitude] : null,
                'intervals' => [
                    ['type' => 'work', 'start' => $now, 'end' => null]
                ]
            ]
        );

        $this->todayRecord->refresh()->updateCalculatedSeconds();
        $this->loadTodayRecord();
        $this->dispatch('notify', message: 'Clocked in successfully.', type: 'success');
        $this->dispatch('attendance-updated');
    }

    public function pause(string $type = 'break')
    {
        if (!$this->todayRecord || $this->todayRecord->status !== 'clocked_in') return;

        $intervals = $this->todayRecord->intervals ?? [];
        $now = now()->toDateTimeString();

        // End current work interval
        foreach ($intervals as &$interval) {
            if ($interval['type'] === 'work' && ($interval['end'] ?? null) === null) {
                $interval['end'] = $now;
                break;
            }
        }

        // Add pause interval
        $intervals[] = ['type' => $type, 'start' => $now, 'end' => null];

        $this->todayRecord->update([
            'status' => 'on_' . $type,
            'intervals' => $intervals
        ]);

        $this->todayRecord->refresh()->updateCalculatedSeconds();
        $this->loadTodayRecord();
        $this->dispatch('notify', message: ucfirst($type) . ' started.', type: 'info');
        $this->dispatch('attendance-updated');
    }

    public function resume()
    {
        if (!$this->todayRecord || !str_contains($this->todayRecord->status, 'on_')) return;

        $intervals = $this->todayRecord->intervals ?? [];
        $now = now()->toDateTimeString();

        // End break/lunch interval
        foreach ($intervals as &$interval) {
            if (str_contains($this->todayRecord->status, $interval['type']) && ($interval['end'] ?? null) === null) {
                $interval['end'] = $now;
                break;
            }
        }

        // Resume work
        $intervals[] = ['type' => 'work', 'start' => $now, 'end' => null];

        $this->todayRecord->update([
            'status' => 'clocked_in',
            'intervals' => $intervals
        ]);

        $this->todayRecord->refresh()->updateCalculatedSeconds();
        $this->loadTodayRecord();
        $this->dispatch('notify', message: 'Work resumed.', type: 'success');
        $this->dispatch('attendance-updated');
    }

    public function punchOut()
    {
        if (!$this->todayRecord || $this->todayRecord->status === 'completed') return;

        $intervals = $this->todayRecord->intervals ?? [];
        $now = now()->toDateTimeString();

        // End whatever is running
        foreach ($intervals as &$interval) {
            if (($interval['end'] ?? null) === null) {
                $interval['end'] = $now;
                break;
            }
        }

        $this->todayRecord->update([
            'clock_out_at' => now()->toTimeString(),
            'status' => 'completed',
            'intervals' => $intervals
        ]);

        $this->todayRecord->refresh()->updateCalculatedSeconds();
        $this->loadTodayRecord();
        $this->dispatch('notify', message: 'Shift completed. Have a great day!', type: 'success');
        $this->dispatch('attendance-updated');
    }

    public function render()
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->where('tenant_id', $user->tenant_id)->first();
        
        $history = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $weeklyStats = [
            'total_hours' => 0,
            'days_present' => 0,
            'avg_punch_in' => '--:--',
        ];
        $roster = collect();

        if ($employee) {
            $history = AttendanceRecord::where('employee_id', $employee->id)
                ->orderBy('attendance_date', 'desc')
                ->paginate(10);
                
            // Weekly stats (last 7 days)
            $lastSevenDays = AttendanceRecord::where('employee_id', $employee->id)
                ->where('attendance_date', '>=', now()->subDays(6)->toDateString())
                ->get();
                
            $weeklyStats['total_hours'] = round($lastSevenDays->sum('total_work_seconds') / 3600, 1);
            $weeklyStats['days_present'] = $lastSevenDays->count();
            
            if ($weeklyStats['days_present'] > 0) {
                $avgSeconds = $lastSevenDays->avg(fn($r) => Carbon::parse($r->clock_in_at)->secondsSinceMidnight());
                $weeklyStats['avg_punch_in'] = Carbon::today()->addSeconds($avgSeconds)->format('H:i');
            }

            // Next 7 days roster
            $roster = ShiftSchedule::with('shift')
                ->where('employee_id', $employee->id)
                ->where('date', '>=', now()->toDateString())
                ->where('date', '<=', now()->addDays(6)->toDateString())
                ->orderBy('date')
                ->get();
        }

        return view('livewire.attendance.attendance-dashboard', [
            'history' => $history,
            'weeklyStats' => $weeklyStats,
            'roster' => $roster,
            'employee' => $employee,
        ]);
    }
}
