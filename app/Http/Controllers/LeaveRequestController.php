<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequest;
use App\Models\LeaveRequest;
use App\Services\LeaveService;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function __construct(
        private LeaveService $leaveService,
        private EmployeeService $employeeService
    ) {}

    public function index(): View
    {
        $leaves = $this->leaveService->getAllLeaveRequests();
        return view('hrms.leaves.index', compact('leaves'));
    }

    public function pending(): View
    {
        $leaves = $this->leaveService->getPendingLeaveRequests();
        return view('hrms.leaves.pending', compact('leaves'));
    }

    public function create(): View
    {
        $employees = $this->employeeService->getAllEmployees();
        return view('hrms.leaves.create', compact('employees'));
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $this->leaveService->createLeaveRequest($request->validated());
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
}
