@extends('hrms.layouts.app')

@section('title', 'Leave Policy - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Leave Policy</h1>
        <p class="text-slate-600 dark:text-slate-400">Set leave limits for {{ $employee->full_name }}</p>
    </div>
    <a href="{{ route('employees.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Back</a>
</div>

<div class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-6 rounded-xl bg-slate-100 p-4 dark:bg-slate-950">
        <p class="font-semibold">{{ $employee->full_name }}</p>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $employee->department->name ?? 'No department' }}</p>
    </div>

    <form method="POST" action="{{ route('employees.leave-policy.update', $employee->id) }}" class="grid gap-4 md:grid-cols-2">
        @csrf
        @method('PATCH')

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Annual Leave</label>
            <input type="number" min="0" max="365" name="annual_limit" value="{{ old('annual_limit', $policy->annual_limit) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-950 @error('annual_limit') border-red-500 @enderror" required>
            @error('annual_limit')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Sick Leave</label>
            <input type="number" min="0" max="365" name="sick_limit" value="{{ old('sick_limit', $policy->sick_limit) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-950 @error('sick_limit') border-red-500 @enderror" required>
            @error('sick_limit')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Casual Leave</label>
            <input type="number" min="0" max="365" name="casual_limit" value="{{ old('casual_limit', $policy->casual_limit) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-950 @error('casual_limit') border-red-500 @enderror" required>
            @error('casual_limit')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Unpaid Leave</label>
            <input type="number" min="0" max="365" name="unpaid_limit" value="{{ old('unpaid_limit', $policy->unpaid_limit) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-950 @error('unpaid_limit') border-red-500 @enderror" required>
            @error('unpaid_limit')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2 pt-2">
            <button type="submit" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Save Policy</button>
        </div>
    </form>
</div>
@endsection
