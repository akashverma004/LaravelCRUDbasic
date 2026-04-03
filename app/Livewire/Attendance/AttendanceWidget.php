<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use Livewire\Component;

class AttendanceWidget extends Component
{
    public ?AttendanceRecord $todayRecord = null;
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
            }
        }
    }

    public function punchIn(float $latitude = null, float $longitude = null)
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->where('tenant_id', $user->tenant_id)->first();
        if (!$employee) return;

        $now = now()->toDateTimeString();
        $ip = request()->ip();

        $this->todayRecord = AttendanceRecord::firstOrCreate(
            ['employee_id' => $employee->id, 'attendance_date' => now()->toDateString()],
            [
                'tenant_id' => $user->tenant_id,
                'clock_in_at' => now()->toTimeString(),
                'status' => 'clocked_in',
                'ip_address' => $ip,
                'location_metadata' => ($latitude && $longitude) ? ['lat' => $latitude, 'lng' => $longitude] : null,
                'intervals' => [['type' => 'work', 'start' => $now, 'end' => null]]
            ]
        );
        $this->loadTodayRecord();
        $this->dispatch('notify', message: 'Clocked in.', type: 'success');
    }

    public function punchOut()
    {
        if (!$this->todayRecord || $this->todayRecord->status === 'completed') return;
        
        $intervals = $this->todayRecord->intervals ?? [];
        $now = now()->toDateTimeString();
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
        $this->dispatch('notify', message: 'Clocked out.', type: 'info');
    }

    public function render()
    {
        return view('livewire.attendance.attendance-widget');
    }
}
