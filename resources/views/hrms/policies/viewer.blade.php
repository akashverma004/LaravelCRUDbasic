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

<div x-data="{ activeTab: '{{ $policies[0]['slug'] ?? '' }}' }" class="space-y-6">
    
    {{-- Top Tabs Navigation --}}
    @if(count($policies) > 0)
        <div class="flex gap-1.5 overflow-x-auto rounded-xl border border-slate-200 bg-white p-1.5 shadow-sm dark:border-slate-800 dark:bg-slate-900/50 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
            @foreach($policies as $policy)
                <button @click="activeTab = '{{ $policy['slug'] }}'" 
                    class="whitespace-nowrap rounded-lg px-3 py-1.5 text-[10px] font-black uppercase tracking-widest transition-colors shrink-0"
                    :class="activeTab === '{{ $policy['slug'] }}' ? 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white'">
                    {{ $policy['title'] }}
                </button>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            @forelse($policies as $policy)
                <div x-show="activeTab === '{{ $policy['slug'] }}'" x-cloak class="relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-cyan-500/30 dark:border-white/5 dark:bg-slate-900/50" x-transition.opacity>
                    <div class="mb-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-slate-900 border border-slate-100 dark:bg-white/5 dark:text-cyan-400 dark:border-white/5">
                                @if($policy['slug'] === 'attendance')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @elseif($policy['slug'] === 'wfh')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                @elseif($policy['slug'] === 'notice-period')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-sm font-black text-slate-900 dark:text-white leading-none uppercase tracking-tight">{{ $policy['title'] }}</h2>
                                <div class="mt-1.5 flex items-center gap-1.5">
                                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                                        Active Protocol
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="prose prose-slate dark:prose-invert max-w-none">
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide leading-relaxed">{{ $policy['description'] }}</p>
                        
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($policy['fields'] as $field)
                                @if(!in_array($field['name'], ['name', 'description', 'is_active', 'code', 'rules', 'exceptions', 'metadata']) && isset($policy['record']->{$field['name']}))
                                    <div class="rounded-xl flex flex-col justify-center bg-slate-50 p-3 border border-slate-100 dark:bg-white/5 dark:border-white/5">
                                        <dt class="text-[8px] font-black uppercase tracking-[0.15em] text-slate-400 mb-1">{{ $field['label'] }}</dt>
                                        <dd class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight">
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
                        <div class="mt-6 border-t border-slate-100 pt-4 dark:border-white/5">
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Last updated {{ $policy['record']->updated_at->diffForHumans() }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border-2 border-dashed border-slate-200 p-8 text-center dark:border-white/5">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">No active policies found.</p>
                </div>
            @endforelse
        </div>
        
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 dark:border-white/5 dark:bg-slate-900/50">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600 mb-3 dark:bg-cyan-500/10 dark:text-cyan-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                </div>
                <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white mb-1.5">Guidelines & Clarification</h3>
                <p class="text-[9px] font-bold text-slate-400 uppercase leading-relaxed tracking-wide mb-4">Please reach out to the Human Resources department for specific protocol inquiries or exemption requests.</p>
                <a href="mailto:hr@example.com" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-slate-900 px-3 py-2 text-[9px] font-black uppercase tracking-widest text-white shadow-md transition-all hover:bg-cyan-500 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500">
                    Contact HR Support
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
