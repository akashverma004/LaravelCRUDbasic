@extends('hrms.layouts.app')

@section('title', 'Leave Policy - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Leave Policy</h1>
    <p class="text-slate-600 dark:text-slate-400">Set global leave limits for all employees</p>
</div>

@if (session('status'))
    <div class="mb-6 rounded-xl border border-emerald-300/50 bg-emerald-100 px-4 py-3 text-emerald-700 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
        {{ session('status') }}
    </div>
@endif

<div class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
    <form method="POST" action="{{ route('policies.leave.update') }}" class="grid gap-4 md:grid-cols-2">
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
            <button type="submit" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Save Global Policy</button>
        </div>
    </form>
</div>
@endsection
