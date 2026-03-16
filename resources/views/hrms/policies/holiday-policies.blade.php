@extends('hrms.layouts.app')

@section('title', 'Holiday Policies - PeopleFlow HRMS')

@section('content')
<div class="space-y-6">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">Holiday <span class="text-cyan-500">Policies</span></h1>
            <p class="mt-1 text-[11px] font-medium text-slate-500">Set up regional holidays and weekend schedules.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('policies.holiday-calendar.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                <span>Calendar View</span>
            </a>
            <a href="{{ route('policies.index') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center px-4 py-2">Back to Hub</a>
        </div>
    </div>

    @php
        $weekdays = $weekdays ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    @endphp

    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Creation Form --}}
        <div class="xl:col-span-1">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h2 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-6">Create New Policy</h2>
                <div x-data="asyncForm({ reloadOnSuccess: true })">
                    <div x-show="toast.show" x-transition class="mb-4 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-[10px] font-bold text-white shadow-2xl backdrop-blur-xl dark:bg-slate-800/90" x-cloak>
                        <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="h-1.5 w-1.5 rounded-full animate-pulse mr-2 inline-block"></div>
                        <span x-text="toast.message"></span>
                    </div>
                    
                    <form x-ref="form" @submit.prevent="submit()" method="POST" action="{{ route('policies.holiday-policies.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Policy Name</label>
                            <input type="text" name="name" placeholder="e.g. Standard UK" required class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Country Code</label>
                                <input type="text" name="country_code" placeholder="GB" required maxlength="3" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm uppercase dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">State/Region</label>
                                <input type="text" name="state_code" placeholder="ENG" required maxlength="3" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm uppercase dark:border-slate-700 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Internal Code</label>
                            <input type="text" name="code" placeholder="e.g. HOL-01" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm dark:border-slate-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Weekend Days</label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($weekdays as $day)
                                    <label class="flex items-center gap-1.5 cursor-pointer group">
                                        <input type="checkbox" name="weekend_days[]" value="{{ $day }}" class="h-3.5 w-3.5 rounded border-slate-300 text-cyan-500 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-800" @checked(in_array($day, ['saturday', 'sunday'], true))>
                                        <span class="text-[10px] font-bold text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white capitalize" x-text="'{{ $day }}'.substring(0, 3)"></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" :disabled="saving" class="w-full rounded-xl bg-slate-900 border border-white/10 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400 disabled:opacity-50">
                            <span x-text="saving ? 'Processing...' : 'Create Policy'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Policy List --}}
        <div class="xl:col-span-2 space-y-4">
            @forelse ($policies as $policy)
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white" x-text="'{{ $policy->name }}'"></h2>
                            <p class="text-[10px] font-bold text-cyan-600 dark:text-cyan-400 uppercase tracking-wider" x-text="'Code: {{ $policy->code ?: 'N/A' }}'"></p>
                        </div>
                        <a href="{{ route('policies.holiday-calendar.index', ['policy_id' => $policy->id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-600 hover:bg-slate-900 hover:text-white dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-white dark:hover:text-slate-900 transition-colors">
                            Manage Calendar
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </a>
                    </div>

                    <div x-data="asyncForm({ reloadOnSuccess: true })">
                        <form x-ref="form" @submit.prevent="submit()" method="POST" action="{{ route('policies.holiday-policies.update', $policy) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <div class="flex items-center gap-4 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="h-3.5 w-3.5 rounded border-slate-300 text-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800" @checked((bool) $policy->is_active)>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Active Policy</span>
                                </label>
                                <div class="h-4 w-px bg-slate-200 dark:bg-slate-800"></div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider" x-text="'{{ $policy->holiday_dates_count }} Holidays Listed'"></span>
                            </div>

                            <div class="flex justify-between items-center bg-slate-50 p-2 rounded-lg dark:bg-white/5">
                                <button type="submit" :disabled="saving" class="px-4 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg bg-slate-900 border border-white/10 text-white hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                                    <span x-text="saving ? 'Saving...' : 'Update Policy'"></span>
                                </button>
                                
                                <form x-ref="deleteForm" @submit.prevent="if (confirm('Permanently delete this policy?')) { const original = $refs.form; $refs.form = $refs.deleteForm; submit().finally(() => { $refs.form = original; }); }" method="POST" action="{{ route('policies.holiday-policies.destroy', $policy) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" :disabled="saving" class="px-4 py-1.5 text-[9px] font-black uppercase tracking-widest text-rose-500 hover:text-rose-700 transition-colors">Delete</button>
                                </form>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">No holiday policies found.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
