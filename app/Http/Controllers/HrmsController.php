<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HrmsController extends Controller
{
    public function dashboard(): View
    {
        $employeeCount = Employee::count();
        $departmentCount = Department::count();
        $leavePending = LeaveRequest::where('status', 'pending')->count();
        $attendanceToday = AttendanceRecord::whereDate('attendance_date', now()->toDateString())->count();

        $departmentBreakdown = Department::query()
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        $employees = Employee::query()
            ->with('department')
            ->latest()
            ->take(8)
            ->get();

        $leaveRequests = LeaveRequest::query()
            ->with('employee')
            ->latest()
            ->take(6)
            ->get();

        return view('hrms.dashboard', compact(
            'employeeCount',
            'departmentCount',
            'leavePending',
            'attendanceToday',
            'departmentBreakdown',
            'employees',
            'leaveRequests',
        ));
    }

    public function storeDepartment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30', 'unique:departments,code'],
            'lead_name' => ['required', 'string', 'max:255'],
        ]);

        Department::create($data);

        return back()->with('status', 'Department created successfully.');
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'phone' => ['required', 'string', 'max:30'],
            'job_title' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', 'in:full-time,part-time,contract,intern'],
            'salary' => ['required', 'numeric', 'min:0'],
            'joined_on' => ['required', 'date'],
            'status' => ['required', 'in:active,on-leave,resigned'],
        ]);

        Employee::create($data);

        return back()->with('status', 'Employee added successfully.');
    }

    public function storeLeave(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type' => ['required', 'in:annual,sick,casual,unpaid'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:500'],
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        LeaveRequest::create($data);

        return back()->with('status', 'Leave request submitted.');
    }
}
