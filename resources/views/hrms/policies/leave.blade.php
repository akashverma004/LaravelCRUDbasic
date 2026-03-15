@extends('hrms.layouts.app')

@section('title', 'Leave Policy - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Leave Policy</h1>
    <p class="text-slate-600 dark:text-slate-400">Set global leave limits for all employees</p>
</div>

<div x-data="asyncForm()" class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
    <div x-show="toast.show" x-transition class="mb-4 rounded-xl px-4 py-3 text-sm font-semibold" :class="toast.type === 'success' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300'" style="display: none;">
        <span x-text="toast.message"></span>
    </div>
    <div x-show="errorMessage" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-600 dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-300" style="display: none;">
        <span x-text="errorMessage"></span>
    </div>
    <form x-ref="form" @submit.prevent="submit()" method="POST" action="{{ route('policies.leave.update') }}" class="grid gap-4 md:grid-cols-2">
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
            <button type="submit" :disabled="saving" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-slate-900 hover:bg-cyan-400 disabled:opacity-60">
                <span x-text="saving ? 'Saving...' : 'Save Global Policy'"></span>
            </button>
        </div>
    </form>
</div>
@endsection
