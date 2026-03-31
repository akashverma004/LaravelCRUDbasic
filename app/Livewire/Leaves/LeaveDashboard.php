<?php

namespace App\Livewire\Leaves;

use App\Models\Employee;
use App\Models\LeavePolicy;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveSubmitted;
use App\Services\LeaveService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Time Off - PeopleFlow HRMS')]
class LeaveDashboard extends Component
{
    use WithPagination;

    // View state
    public bool $showLeaveModal = false;
    public bool $isEditing = false;
    public ?int $editingLeaveId = null;

    // Permissions
    public bool $isAdmin = false;

    // Form data
    public ?int $employee_id = null;
    public string $leave_type = 'annual';
    public string $leave_session = 'full_day';
    public ?string $start_date = null;
    public ?string $end_date = null;
    public ?string $reason = null;

    public function mount()
    {
        $user = auth()->user();
        $this->isAdmin = $user->hasAnyRole(['admin', 'hr_manager']);
        
        if (!$this->isAdmin) {
            $employee = Employee::where('email', $user->email)
                ->where('tenant_id', $user->tenant_id)
                ->first();
                
            if ($employee) {
                $this->employee_id = $employee->id;
            }
        }
    }

    private function getEmployee()
    {
        if ($this->isAdmin && $this->employee_id) {
            return Employee::find($this->employee_id);
        }
        
        $user = auth()->user();
        return Employee::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->first();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->isEditing = false;
        $this->editingLeaveId = null;
        $this->leave_type = 'annual';
        $this->leave_session = 'full_day';
        $this->start_date = null;
        $this->end_date = null;
        $this->reason = null;
        
        if (!$this->isAdmin) {
            $employee = $this->getEmployee();
            $this->employee_id = $employee?->id;
        } else {
            $this->employee_id = null;
        }
        
        $this->showLeaveModal = true;
    }

    public function editLeave(int $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $user = auth()->user();

        // Security check
        if (!$this->isAdmin) {
            $employee = $this->getEmployee();
            if ($leave->employee_id !== $employee?->id) {
                abort(403, 'Unauthorized action.');
            }
            if ($leave->status !== 'pending') {
                $this->dispatch('notify', message: 'Only pending requests can be edited.', type: 'error');
                return;
            }
        }

        $this->resetValidation();
        $this->isEditing = true;
        $this->editingLeaveId = $leave->id;
        
        $this->employee_id = $leave->employee_id;
        $this->leave_type = $leave->leave_type;
        $this->leave_session = $leave->leave_session;
        $this->start_date = $leave->start_date?->format('Y-m-d');
        $this->end_date = $leave->end_date?->format('Y-m-d');
        $this->reason = $leave->reason;
        
        $this->showLeaveModal = true;
    }

    public function closeLeaveModal()
    {
        $this->showLeaveModal = false;
        $this->resetValidation();
    }

    public function saveLeaveRequest(LeaveService $leaveService)
    {
        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|in:annual,sick,casual,unpaid',
            'leave_session' => 'required|in:full_day,morning,evening',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:1000',
        ]);

        $data = [
            'employee_id' => $this->employee_id,
            'leave_type' => $this->leave_type,
            'leave_session' => $this->leave_session,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'reason' => $this->reason,
            'status' => 'pending', // Always pending initially, or let admin override later
        ];

        // Security override for non-admins
        if (!$this->isAdmin) {
            $employee = $this->getEmployee();
            $data['employee_id'] = $employee->id;
            $data['status'] = 'pending';
        }

        if ($this->isEditing && $this->editingLeaveId) {
            $leave = LeaveRequest::findOrFail($this->editingLeaveId);
            $leaveService->updateLeaveRequest($leave, $data);
            $this->dispatch('notify', message: 'Leave request updated successfully.', type: 'success');
        } else {
            $leave = $leaveService->createLeaveRequest($data);
            
            // Notify admin/HR
            $employee = Employee::find($data['employee_id']);
            $adminUsers = User::where('tenant_id', auth()->user()->tenant_id)
                ->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'hr_manager']))
                ->where('id', '!=', auth()->id())
                ->get();

            if ($employee && $adminUsers->isNotEmpty()) {
                Notification::send($adminUsers, new LeaveSubmitted($leave, $employee->full_name));
            }
            
            $this->dispatch('notify', message: 'Leave request submitted successfully.', type: 'success');
        }

        $this->closeLeaveModal();
    }

    public function deleteLeave(int $id, LeaveService $leaveService)
    {
        $leave = LeaveRequest::findOrFail($id);
        
        // Security check
        if (!$this->isAdmin) {
            $employee = $this->getEmployee();
            if ($leave->employee_id !== $employee?->id) {
                abort(403, 'Unauthorized action.');
            }
            if ($leave->status !== 'pending') {
                $this->dispatch('notify', message: 'Only pending requests can be deleted.', type: 'error');
                return;
            }
        }

        $leaveService->deleteLeaveRequest($leave);
        $this->dispatch('notify', message: 'Leave request deleted.', type: 'success');
    }

    public function render()
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $employee = $this->getEmployee();

        $leaves = collect();
        $balances = [];
        $whoIsAwayToday = collect();
        $whoIsAwayUpcoming = collect();
        $stats = ['pending' => 0, 'approved' => 0];
        $allEmployees = collect();

        if ($employee) {
            // My Leave History
            $leaves = LeaveRequest::where('employee_id', $employee->id)->latest()->get();

            // Resolve Policy
            $policy = $employee->leavePolicy ?? LeavePolicy::where('tenant_id', $tenantId)->where('is_active', true)->first() ?? (object)[
                'annual_limit' => 0, 'sick_limit' => 0, 'casual_limit' => 0, 'unpaid_limit' => 0
            ];

            // Used Balances
            $used = LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->selectRaw('leave_type, SUM(DATEDIFF(end_date, start_date) + 1) as total')
                ->groupBy('leave_type')
                ->pluck('total', 'leave_type');

            $balances = [
                'annual' => ['limit' => $policy->annual_limit, 'remaining' => max(0, $policy->annual_limit - ($used['annual'] ?? 0)), 'used' => $used['annual'] ?? 0],
                'sick' => ['limit' => $policy->sick_limit, 'remaining' => max(0, $policy->sick_limit - ($used['sick'] ?? 0)), 'used' => $used['sick'] ?? 0],
                'casual' => ['limit' => $policy->casual_limit, 'remaining' => max(0, $policy->casual_limit - ($used['casual'] ?? 0)), 'used' => $used['casual'] ?? 0],
                'unpaid' => ['limit' => $policy->unpaid_limit, 'remaining' => max(0, $policy->unpaid_limit - ($used['unpaid'] ?? 0)), 'used' => $used['unpaid'] ?? 0],
            ];

            $stats['pending'] = $leaves->where('status', 'pending')->count();
            $stats['approved'] = $leaves->where('status', 'approved')->count();
        }

        // Who's Away
        $today = now()->toDateString();
        $nextWeek = now()->addDays(7)->toDateString();

        $whoIsAwayToday = LeaveRequest::with('employee:id,full_name,profile_photo,job_title')
            ->where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get()->filter(fn($l) => $l->employee);

        $whoIsAwayUpcoming = LeaveRequest::with('employee:id,full_name,profile_photo,job_title')
            ->where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->whereDate('start_date', '>', $today)
            ->whereDate('start_date', '<=', $nextWeek)
            ->orderBy('start_date')
            ->get()->filter(fn($l) => $l->employee);

        if ($this->isAdmin) {
            $allEmployees = Employee::where('tenant_id', $tenantId)->orderBy('full_name')->get(['id', 'full_name']);
        }

        return view('livewire.leaves.leave-dashboard', [
            'employee' => $employee,
            'leaves' => $leaves,
            'balances' => $balances,
            'whoIsAwayToday' => $whoIsAwayToday,
            'whoIsAwayUpcoming' => $whoIsAwayUpcoming,
            'stats' => $stats,
            'allEmployees' => $allEmployees,
        ]);
    }
}
