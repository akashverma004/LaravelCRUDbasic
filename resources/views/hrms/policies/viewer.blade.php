@extends('hrms.layouts.app')

@section('title', 'Company Policies - PeopleFlow HRMS')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">Company <span class="text-cyan-500">Policies</span></h1>
            <p class="mt-1 text-[11px] font-medium text-slate-500">Official guidelines for {{ auth()->user()->tenant->name }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center px-4 py-2">Back to Dashboard</a>
    </div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1 space-y-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
            <h2 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Quick Navigation</h2>
            <nav class="space-y-1">
                @foreach($policies as $policy)
                    <a href="#policy-{{ $policy['slug'] }}" class="group flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold text-slate-600 transition-all hover:bg-white hover:text-indigo-600 hover:shadow-md dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400">
                        {{ $policy['title'] }}
                        <svg class="w-4 h-4 opacity-0 -translate-x-2 transition-all group-hover:opacity-100 group-hover:translate-x-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="rounded-2xl bg-slate-900 border border-white/10 p-6 text-white shadow-xl shadow-cyan-500/5 dark:bg-white/5">
            <h3 class="text-sm font-black uppercase tracking-tight mb-2">Need clarification?</h3>
            <p class="text-[11px] text-slate-400 mb-6 font-medium leading-relaxed uppercase tracking-wide">Reach out to HR for specific protocol inquiries.</p>
            <a href="mailto:hr@example.com" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-950 shadow-lg transition-all hover:bg-cyan-400 active:scale-95">
                Contact HR Support
            </a>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-8">
        @forelse($policies as $policy)
            <div id="policy-{{ $policy['slug'] }}" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 transition-all hover:border-cyan-500/30 hover:shadow-xl dark:border-white/5 dark:bg-slate-900/50">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-50 text-slate-900 border border-slate-100 dark:bg-white/5 dark:text-cyan-400 dark:border-white/5">
                            @if($policy['slug'] === 'attendance')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($policy['slug'] === 'wfh')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            @elseif($policy['slug'] === 'notice-period')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900 dark:text-white leading-none uppercase tracking-tight">{{ $policy['title'] }}</h2>
                            <div class="mt-2 flex items-center gap-2">
                                <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                                    Active Protocol
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="prose prose-slate dark:prose-invert max-w-none">
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">{{ $policy['description'] }}</p>
                    
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($policy['fields'] as $field)
                            @if(!in_array($field['name'], ['name', 'description', 'is_active', 'code', 'rules', 'exceptions', 'metadata']) && isset($policy['record']->{$field['name']}))
                                <div class="rounded-xl bg-slate-50 p-4 border border-slate-100 dark:bg-white/5 dark:border-white/5">
                                    <dt class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 mb-1.5">{{ $field['label'] }}</dt>
                                    <dd class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">
                                        @if(is_bool($policy['record']->{$field['name']}))
                                            {{ $policy['record']->{$field['name']} ? 'Enabled' : 'Disabled' }}
                                        @elseif(is_array($policy['record']->{$field['name']}))
                                            {{ count($policy['record']->{$field['name']}) }} Items
                                        @else
                                            {{ $policy['record']->{$field['name']} }}
                                        @endif
                                    </dd>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                @if($policy['record']->updated_at)
                    <div class="mt-8 border-t border-slate-100 pt-6 dark:border-slate-800">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Last updated {{ $policy['record']->updated_at->diffForHumans() }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-3xl border-2 border-dashed border-slate-200 p-12 text-center dark:border-slate-800">
                <p class="text-slate-500 font-bold">No active policies found for your organization.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
