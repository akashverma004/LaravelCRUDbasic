<?php

namespace App\Livewire\Leaves;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveApproved;
use App\Notifications\LeaveRejected;
use App\Services\LeaveService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Review Leave Requests - PeopleFlow HRMS')]
class LeaveApprovals extends Component
{
    use WithPagination;

    public string $tab = 'pending'; // 'all' or 'pending'

    protected $queryString = ['tab'];

    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function approve(int $id, LeaveService $leaveService)
    {
        $leave = LeaveRequest::with('employee')->findOrFail($id);
        $leaveService->approveLeaveRequest($leave);

        // Notify the employee
        $employeeUser = User::where('email', $leave->employee->email)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();
        $employeeUser?->notify(new LeaveApproved($leave));

        $this->dispatch('notify', message: 'Leave request approved.', type: 'success');
    }

    public function reject(int $id, LeaveService $leaveService)
    {
        $leave = LeaveRequest::with('employee')->findOrFail($id);
        $leaveService->rejectLeaveRequest($leave);

        // Notify the employee
        $employeeUser = User::where('email', $leave->employee->email)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();
        $employeeUser?->notify(new LeaveRejected($leave));

        $this->dispatch('notify', message: 'Leave request rejected.', type: 'success');
    }

    public function render()
    {
        $tenantId = auth()->user()->tenant_id;
        
        $query = LeaveRequest::with('employee')
            ->where('tenant_id', $tenantId)
            ->latest();

        if ($this->tab === 'pending') {
            $query->where('status', 'pending');
        }

        return view('livewire.leaves.leave-approvals', [
            'leaves' => $query->paginate(10),
        ]);
    }
}
