@extends('hrms.layouts.app')

@section('title', 'Create Leave Request - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">New Leave Request</h1>
    <p class="text-slate-600 dark:text-slate-400">Submit a new leave request</p>
</div>

<div class="max-w-2xl rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6">
    <form method="POST" action="{{ route('leaves.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Employee</label>
            <select name="employee_id" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('employee_id') border-red-500 @enderror" required>
                <option value="">Select Employee</option>
                @forelse ($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->full_name }}</option>
                @empty
                @endforelse
            </select>
            @error('employee_id')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Leave Type</label>
            <select name="leave_type" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('leave_type') border-red-500 @enderror" required>
                <option value="">Select Type</option>
                <option value="annual" @selected(old('leave_type') === 'annual')>Annual</option>
                <option value="sick" @selected(old('leave_type') === 'sick')>Sick</option>
                <option value="casual" @selected(old('leave_type') === 'casual')>Casual</option>
                <option value="unpaid" @selected(old('leave_type') === 'unpaid')>Unpaid</option>
            </select>
            @error('leave_type')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Session</label>
            <select name="leave_session" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('leave_session') border-red-500 @enderror" required>
                <option value="full_day" @selected(old('leave_session', 'full_day') === 'full_day')>Full day</option>
                <option value="morning" @selected(old('leave_session') === 'morning')>Morning</option>
                <option value="evening" @selected(old('leave_session') === 'evening')>Evening</option>
            </select>
            @error('leave_session')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Start Date</label>
                        <input type="date" name="start_date" class="transition-colors duration-300 mt-1 w-full rounded-lg border border-slate-300 bg-white text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-white px-3 py-2 appearance-auto @error('start_date') border-red-500 @enderror" value="{{ old('start_date') }}" required>
                @error('start_date')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">End Date</label>
                        <input type="date" name="end_date" class="transition-colors duration-300 mt-1 w-full rounded-lg border border-slate-300 bg-white text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-white px-3 py-2 appearance-auto @error('end_date') border-red-500 @enderror" value="{{ old('end_date') }}" required>
                @error('end_date')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Reason</label>
            <textarea name="reason" rows="4" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('reason') border-red-500 @enderror" required>{{ old('reason') }}</textarea>
            @error('reason')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
            <select name="status" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('status') border-red-500 @enderror" required>
                <option value="pending" @selected(old('status') === 'pending' || !old('status'))>Pending</option>
                <option value="approved" @selected(old('status') === 'approved')>Approved</option>
                <option value="rejected" @selected(old('status') === 'rejected')>Rejected</option>
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Submit Request</button>
            <a href="{{ route('leaves.index') }}" class="rounded-lg border border-slate-300 px-6 py-2 font-semibold text-slate-700 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 hover:bg-slate-100">Cancel</a>
        </div>
    </form>
</div>
@endsection
