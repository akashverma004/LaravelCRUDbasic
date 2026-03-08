@extends('hrms.layouts.app')

@section('title', 'Edit Leave Request - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Edit Leave Request</h1>
    <p class="text-slate-500 dark:text-slate-400 font-medium">Modify your pending leave request</p>
</div>

<div class="max-w-2xl rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <form method="POST" action="{{ route('leaves.update', $leave->id) }}" class="space-y-6">
        @csrf
        @method('PATCH')

        <div @if(!$isAdminOrHR) class="hidden" @endif>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Employee</label>
            @if($isAdminOrHR)
                <select name="employee_id" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-medium text-slate-700 transition-all focus:border-indigo-500 focus:ring-0 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300" required>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @selected(old('employee_id', $leave->employee_id) == $employee->id)>{{ $employee->full_name }}</option>
                    @endforeach
                </select>
            @else
                <input type="hidden" name="employee_id" value="{{ $leave->employee_id }}">
                <div class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 font-bold text-slate-600 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    {{ $leave->employee->full_name }}
                </div>
            @endif
            @error('employee_id')
                <p class="mt-2 text-sm font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Leave Type</label>
                <select name="leave_type" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-medium text-slate-700 transition-all focus:border-indigo-500 focus:ring-0 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300" required>
                    <option value="annual" @selected(old('leave_type', $leave->leave_type) === 'annual')>Annual Leave</option>
                    <option value="sick" @selected(old('leave_type', $leave->leave_type) === 'sick')>Sick Leave</option>
                    <option value="casual" @selected(old('leave_type', $leave->leave_type) === 'casual')>Casual Leave</option>
                    <option value="unpaid" @selected(old('leave_type', $leave->leave_type) === 'unpaid')>Unpaid Leave</option>
                </select>
                @error('leave_type')
                    <p class="mt-2 text-sm font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Duration Type</label>
                <select name="leave_session" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-medium text-slate-700 transition-all focus:border-indigo-500 focus:ring-0 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300" required>
                    <option value="full_day" @selected(old('leave_session', $leave->leave_session) === 'full_day')>Full Day</option>
                    <option value="morning" @selected(old('leave_session', $leave->leave_session) === 'morning')>First Half (Morning)</option>
                    <option value="evening" @selected(old('leave_session', $leave->leave_session) === 'evening')>Second Half (Evening)</option>
                </select>
                @error('leave_session')
                    <p class="mt-2 text-sm font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-medium text-slate-700 transition-all focus:border-indigo-500 focus:ring-0 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300" required>
                @error('start_date')
                    <p class="mt-2 text-sm font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-medium text-slate-700 transition-all focus:border-indigo-500 focus:ring-0 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300" required>
                @error('end_date')
                    <p class="mt-2 text-sm font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Reason for Leave</label>
            <textarea name="reason" rows="3" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-medium text-slate-700 transition-all focus:border-indigo-500 focus:ring-0 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300" required>{{ old('reason', $leave->reason) }}</textarea>
            @error('reason')
                <p class="mt-2 text-sm font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div @if(!$isAdminOrHR) class="hidden" @endif>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Status</label>
            @if($isAdminOrHR)
                <select name="status" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-medium text-slate-700 transition-all focus:border-indigo-500 focus:ring-0 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300" required>
                    <option value="pending" @selected(old('status', $leave->status) === 'pending')>Pending Approval</option>
                    <option value="approved" @selected(old('status', $leave->status) === 'approved')>Approved</option>
                    <option value="rejected" @selected(old('status', $leave->status) === 'rejected')>Rejected</option>
                </select>
            @else
                <input type="hidden" name="status" value="{{ $leave->status }}">
                <div class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 font-bold text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400 capitalize">
                    {{ str_replace('_', ' ', $leave->status) }}
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="flex-1 rounded-2xl bg-indigo-600 py-4 text-sm font-black text-white shadow-xl shadow-indigo-500/20 transition-all hover:bg-indigo-700 active:scale-95">
                Update Request
            </button>
            <a href="{{ route('leaves.my') }}" class="flex-1 rounded-2xl border border-slate-200 py-4 text-center text-sm font-black text-slate-600 transition-all hover:bg-slate-50 dark:border-slate-800 dark:text-slate-400 dark:hover:bg-slate-800">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
