@extends('hrms.layouts.app')

@section('title', 'Edit Leave Request - PeopleFlow HRMS')

@section('content')
<div class="space-y-8">
    {{-- Header Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900/50">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-cyan-500/10 blur-[60px]"></div>
        <div class="absolute -bottom-20 -left-20 h-48 w-48 rounded-full bg-indigo-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Edit <span class="text-cyan-600 dark:text-cyan-400">Leave Request</span>
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Update the details of the requested leave.
                </p>
            </div>
            <a href="{{ $isAdminOrHR ? route('leaves.index') : route('leaves.my') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                <span>Back</span>
            </a>
        </div>
    </div>

    {{-- Form --}}
    <div x-data="asyncForm({ followRedirect: true })" class="max-w-3xl mx-auto">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900/50 relative overflow-hidden">
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-slate-50/50 blur-3xl dark:bg-white/[0.02]"></div>

            {{-- Notifications --}}
            <div x-show="toast.show" x-transition class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10" style="display: none;">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    <p class="text-sm font-medium text-emerald-900 dark:text-emerald-200" x-text="toast.message"></p>
                </div>
            </div>

            <div x-show="errorMessage" class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-500/20 dark:bg-rose-500/10" style="display: none;">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <p class="text-sm font-medium text-rose-900 dark:text-rose-200" x-text="errorMessage"></p>
                </div>
            </div>

            <form x-ref="form" @submit.prevent="submit()" method="POST" action="{{ route('leaves.update', $leave->id) }}" class="space-y-6 relative z-10">
                @csrf
                @method('PATCH')

                <div @if(!$isAdminOrHR) class="hidden" @endif class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Employee</label>
                    @if($isAdminOrHR)
                        <select name="employee_id" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" required>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected(old('employee_id', $leave->employee_id) == $employee->id) class="dark:bg-slate-900">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="hidden" name="employee_id" value="{{ $leave->employee_id }}">
                        <div class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-700 dark:border-slate-800 dark:bg-slate-900/50 dark:text-slate-300">
                            {{ $leave->employee->full_name }}
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Leave Type</label>
                        <select name="leave_type" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" required>
                            <option value="annual" @selected(old('leave_type', $leave->leave_type) === 'annual') class="dark:bg-slate-900">Annual Leave</option>
                            <option value="sick" @selected(old('leave_type', $leave->leave_type) === 'sick') class="dark:bg-slate-900">Sick Leave</option>
                            <option value="casual" @selected(old('leave_type', $leave->leave_type) === 'casual') class="dark:bg-slate-900">Casual Leave</option>
                            <option value="unpaid" @selected(old('leave_type', $leave->leave_type) === 'unpaid') class="dark:bg-slate-900">Unpaid Leave</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Duration</label>
                        <select name="leave_session" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" required>
                            <option value="full_day" @selected(old('leave_session', $leave->leave_session) === 'full_day') class="dark:bg-slate-900">Full Day</option>
                            <option value="morning" @selected(old('leave_session', $leave->leave_session) === 'morning') class="dark:bg-slate-900">Half Day - Morning</option>
                            <option value="evening" @selected(old('leave_session', $leave->leave_session) === 'evening') class="dark:bg-slate-900">Half Day - Evening</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Start Date</label>
                        <input type="date" name="start_date" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}" required>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">End Date</label>
                        <input type="date" name="end_date" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Reason</label>
                    <textarea name="reason" rows="4" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-3 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" placeholder="Reason for the leave request..." required>{{ old('reason', $leave->reason) }}</textarea>
                </div>

                <div @if(!$isAdminOrHR) class="hidden" @endif class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Status</label>
                    @if($isAdminOrHR)
                        <select name="status" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" required>
                            <option value="pending" @selected(old('status', $leave->status) === 'pending') class="dark:bg-slate-900">Pending</option>
                            <option value="approved" @selected(old('status', $leave->status) === 'approved') class="dark:bg-slate-900">Approved</option>
                            <option value="rejected" @selected(old('status', $leave->status) === 'rejected') class="dark:bg-slate-900">Rejected</option>
                        </select>
                    @else
                        <input type="hidden" name="status" value="{{ $leave->status }}">
                        <div class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-700 dark:border-slate-800 dark:bg-slate-900/50 dark:text-slate-300 capitalize">
                            {{ $leave->status }}
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit" :disabled="saving" class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-500 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600 disabled:opacity-50">
                        <span x-show="saving" class="h-4 w-4 animate-spin rounded-full border-2 border-slate-900/20 border-r-slate-900"></span>
                        <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
                    </button>
                    <a href="{{ route('leaves.my') }}" class="rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
