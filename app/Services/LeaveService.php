<?php

namespace App\Services;

use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Collection;

class LeaveService
{
    public function createLeaveRequest(array $data): LeaveRequest
    {
        return LeaveRequest::create($data);
    }

    public function updateLeaveRequest(LeaveRequest $leave, array $data): LeaveRequest
    {
        $leave->update($data);
        return $leave;
    }

    public function deleteLeaveRequest(LeaveRequest $leave): bool
    {
        return $leave->delete();
    }

    public function getLeaveRequestsForEmployee(int $employeeId)
    {
        return LeaveRequest::where('employee_id', $employeeId)
            ->with('employee')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getPendingLeaveRequests()
    {
        return LeaveRequest::with('employee')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getAllLeaveRequests()
    {
        return LeaveRequest::with('employee')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function approveLeaveRequest(LeaveRequest $leave): LeaveRequest
    {
        $leave->update(['status' => 'approved']);
        return $leave;
    }

    public function rejectLeaveRequest(LeaveRequest $leave): LeaveRequest
    {
        $leave->update(['status' => 'rejected']);
        return $leave;
    }
}
