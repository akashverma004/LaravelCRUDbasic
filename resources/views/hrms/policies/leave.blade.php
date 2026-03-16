@extends('hrms.layouts.app')

@section('title', 'Leave Policy - PeopleFlow HRMS')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">Leave <span class="text-cyan-500">Policy</span></h1>
            <p class="mt-1 text-[11px] font-medium text-slate-500">Set global leave limits for all employees.</p>
        </div>
        <a href="{{ route('policies.index') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center px-4 py-2">Back to Hub</a>
    </div>

<div x-data="asyncForm()" class="max-w-2xl mt-8 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
    {{-- Universal Notification --}}
    <div 
        x-show="toast.show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-4 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-4 opacity-0 scale-95"
        class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-xs font-bold text-white shadow-2xl backdrop-blur-xl dark:bg-slate-800/90"
        x-cloak
    >
        <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="h-2 w-2 rounded-full animate-pulse"></div>
        <span x-text="toast.message"></span>
    </div>

    <div x-show="errorMessage" class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[11px] font-bold text-rose-600 dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-300" x-cloak>
        <span x-text="errorMessage"></span>
    </div>
    <form x-ref="form" @submit.prevent="submit()" method="POST" action="{{ route('policies.leave.update') }}" class="grid gap-4 md:grid-cols-2">
        @csrf
        @method('PATCH')

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Annual Leave</label>
            <input type="number" min="0" max="365" name="annual_limit" value="{{ old('annual_limit', $policy->annual_limit) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white @error('annual_limit') border-red-500 @enderror" required>
            @error('annual_limit')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Sick Leave</label>
            <input type="number" min="0" max="365" name="sick_limit" value="{{ old('sick_limit', $policy->sick_limit) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white @error('sick_limit') border-red-500 @enderror" required>
            @error('sick_limit')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Casual Leave</label>
            <input type="number" min="0" max="365" name="casual_limit" value="{{ old('casual_limit', $policy->casual_limit) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white @error('casual_limit') border-red-500 @enderror" required>
            @error('casual_limit')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Unpaid Leave</label>
            <input type="number" min="0" max="365" name="unpaid_limit" value="{{ old('unpaid_limit', $policy->unpaid_limit) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white @error('unpaid_limit') border-red-500 @enderror" required>
            @error('unpaid_limit')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2 pt-4">
            <button type="submit" :disabled="saving" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-8 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-xl transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400 disabled:opacity-50">
                <span x-show="!saving" class="flex items-center gap-2">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    Save Global Policy
                </span>
                <span x-show="saving" class="flex items-center gap-2">
                    <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Processing
                </span>
            </button>
        </div>
    </form>
</div>
@endsection
