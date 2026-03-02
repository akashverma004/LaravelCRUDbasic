@extends('hrms.layouts.app')

@section('title', 'Employee Details - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">{{ $employee->full_name }}</h1>
        <p class="text-slate-400">{{ $employee->job_title }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('employees.edit', $employee->id) }}" class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-cyan-400">Edit</a>
        <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600" onclick="return confirm('Are you sure?')">Delete</button>
        </form>
    </div>
</div>

<div class="grid gap-6 md:grid-cols-3">
    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 md:col-span-2">
        <h2 class="mb-4 text-lg font-semibold">Employee Information</h2>
        <div class="grid gap-4">
            <div>
                <p class="text-sm text-slate-400">Email</p>
                <p class="mt-1 text-white">{{ $employee->email }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Phone</p>
                <p class="mt-1 text-white">{{ $employee->phone }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Department</p>
                <p class="mt-1 text-white">{{ $employee->department->name }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Employment Type</p>
                <p class="mt-1 text-white">{{ ucfirst(str_replace('-', ' ', $employee->employment_type)) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Salary</p>
                <p class="mt-1 text-white">{{ number_format($employee->salary, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Joined On</p>
                <p class="mt-1 text-white">{{ $employee->joined_on->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Status</p>
                <p class="mt-1">
                    <span class="rounded-full bg-cyan-500/10 px-3 py-1 text-sm text-cyan-300">{{ ucfirst($employee->status) }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
        <h2 class="mb-4 text-lg font-semibold">Quick Stats</h2>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-slate-400">Leave Requests</p>
                <p class="mt-1 text-2xl font-semibold">{{ $employee->leaveRequests->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Attendance Records</p>
                <p class="mt-1 text-2xl font-semibold">{{ $employee->attendanceRecords->count() }}</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 rounded-2xl border border-slate-800 bg-slate-900 p-6">
    <h2 class="mb-4 text-lg font-semibold">Recent Leave Requests</h2>
    @forelse ($employee->leaveRequests->take(5) as $leave)
        <div class="mb-3 flex items-center justify-between rounded-lg bg-slate-950 p-3 last:mb-0">
            <div>
                <p class="text-sm font-medium">{{ ucfirst($leave->leave_type) }} - {{ $leave->start_date->format('d M') }} to {{ $leave->end_date->format('d M') }}</p>
                <p class="text-xs text-slate-400">{{ $leave->reason }}</p>
            </div>
            <span class="rounded-full px-3 py-1 text-xs {{ $leave->status === 'pending' ? 'bg-yellow-500/10 text-yellow-300' : ($leave->status === 'approved' ? 'bg-green-500/10 text-green-300' : 'bg-red-500/10 text-red-300') }}">
                {{ ucfirst($leave->status) }}
            </span>
        </div>
    @empty
        <p class="text-slate-400">No leave requests.</p>
    @endforelse
</div>
@endsection
