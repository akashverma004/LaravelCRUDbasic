<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Attendance Management - PeopleFlow HRMS')]
class AttendanceManagement extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $date = '';

    #[Url(except: '')]
    public string $q = '';

    // Modal state
    public bool $showEditModal = false;
    public ?int $editingRecordId = null;
    public string $employeeName = '';
    public string $clock_in = '';
    public string $clock_out = '';

    public function mount()
    {
        $this->date = $this->date ?: now()->toDateString();
        abort_unless(auth()->user()->hasAnyRole(['admin', 'hr_manager']), 403);
    }

    public function updatingQ()
    {
        $this->resetPage();
    }

    public function updatingDate()
    {
        $this->resetPage();
    }

    public function editRecord(int $id)
    {
        $record = AttendanceRecord::with('employee')->findOrFail($id);
        
        $this->editingRecordId = $record->id;
        $this->employeeName = $record->employee->full_name;
        $this->clock_in = $record->clock_in_at ? Carbon::parse($record->clock_in_at)->format('H:i') : '';
        $this->clock_out = $record->clock_out_at ? Carbon::parse($record->clock_out_at)->format('H:i') : '';
        
        $this->showEditModal = true;
    }

    public function saveEdit()
    {
        $this->validate([
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
        ]);

        $record = AttendanceRecord::findOrFail($this->editingRecordId);
        
        $data = [
            'clock_in_at' => $this->clock_in ?: null,
            'clock_out_at' => $this->clock_out ?: null,
        ];

        // If both times provided, simplify intervals to a single block
        if ($this->clock_in && $this->clock_out) {
            $datePrefix = $record->attendance_date->format('Y-m-d') . ' ';
            $start = Carbon::parse($datePrefix . $this->clock_in);
            $end = Carbon::parse($datePrefix . $this->clock_out);

            $data['intervals'] = [
                ['type' => 'work', 'start' => $start->toDateTimeString(), 'end' => $end->toDateTimeString()]
            ];
            $data['status'] = 'completed';
        }

        $record->update($data);
        $record->refresh()->updateCalculatedSeconds();

        $this->showEditModal = false;
        $this->dispatch('notify', message: 'Attendance record updated successfully.', type: 'success');
    }

    public function render()
    {
        $tenantId = auth()->user()->tenant_id;

        $query = AttendanceRecord::where('tenant_id', $tenantId)
            ->with('employee:id,full_name,job_title,status')
            ->orderBy('attendance_date', 'desc')
            ->orderBy('clock_in_at', 'desc');

        if ($this->date) {
            $query->where('attendance_date', $this->date);
        }

        if ($this->q) {
            $searchTerm = '%' . $this->q . '%';
            $query->whereHas('employee', function ($empQ) use ($searchTerm) {
                $empQ->where('full_name', 'like', $searchTerm)
                     ->orWhere('job_title', 'like', $searchTerm);
            });
        }

        $records = $query->paginate(15);

        // Stats for the selected date or overall?
        // Legacy stats were for the specific date if selected, else past 7 days.
        // Let's do daily stats if date is selected, else total for current view.
        $statsQuery = AttendanceRecord::where('tenant_id', $tenantId);
        if ($this->date) {
            $statsQuery->where('attendance_date', $this->date);
        }

        $stats = [
            'total_hours' => round($statsQuery->sum('total_work_seconds') / 3600, 1),
            'present_today' => AttendanceRecord::where('tenant_id', $tenantId)->where('attendance_date', now()->toDateString())->count(),
            'active_now' => AttendanceRecord::where('tenant_id', $tenantId)->where('attendance_date', now()->toDateString())->where('status', 'clocked_in')->count(),
        ];

        return view('livewire.attendance.attendance-management', [
            'records' => $records,
            'stats' => $stats,
        ]);
    }
}
