@extends('hrms.layouts.app')

@section('title', 'Company Policies - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Company Policies</h1>
        <p class="text-slate-500 dark:text-slate-400 font-medium">Official guidelines for {{ auth()->user()->tenant->name }}</p>
    </div>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Dashboard
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1 space-y-4">
        <div class="rounded-3xl border border-slate-200 bg-white/50 p-6 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/50">
            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Quick Links</h2>
            <nav class="space-y-1">
                @foreach($policies as $policy)
                    <a href="#policy-{{ $policy['slug'] }}" class="group flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold text-slate-600 transition-all hover:bg-white hover:text-indigo-600 hover:shadow-md dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400">
                        {{ $policy['title'] }}
                        <svg class="w-4 h-4 opacity-0 -translate-x-2 transition-all group-hover:opacity-100 group-hover:translate-x-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-purple-700 p-8 text-white shadow-xl shadow-indigo-500/20">
            <h3 class="text-xl font-bold mb-2">Need clarification?</h3>
            <p class="text-indigo-100 text-sm mb-6 font-medium leading-relaxed">If you have specific questions about these policies, please reach out to your HR representative.</p>
            <a href="mailto:hr@example.com" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-6 py-4 text-sm font-black text-indigo-600 shadow-lg transition-transform hover:scale-105 active:scale-95">
                Contact HR Support
            </a>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-8">
        @forelse($policies as $policy)
            <div id="policy-{{ $policy['slug'] }}" class="group relative overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white p-8 transition-all hover:border-indigo-200 hover:shadow-2xl dark:border-slate-800 dark:bg-slate-900 dark:hover:border-indigo-900">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">
                            @if($policy['slug'] === 'attendance')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($policy['slug'] === 'wfh')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            @elseif($policy['slug'] === 'notice-period')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white leading-none">{{ $policy['title'] }}</h2>
                            <span class="mt-2 inline-block rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                Active & Effective
                            </span>
                        </div>
                    </div>
                </div>

                <div class="prose prose-slate dark:prose-invert max-w-none">
                    <p class="text-base text-slate-600 dark:text-slate-400 leading-relaxed">{{ $policy['description'] }}</p>
                    
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($policy['fields'] as $field)
                            @if(!in_array($field['name'], ['name', 'description', 'is_active', 'code', 'rules', 'exceptions', 'metadata']) && isset($policy['record']->{$field['name']}))
                                <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 dark:bg-slate-950 dark:border-slate-800">
                                    <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">{{ $field['label'] }}</dt>
                                    <dd class="text-sm font-black text-slate-700 dark:text-slate-300">
                                        @if(is_bool($policy['record']->{$field['name']}))
                                            {{ $policy['record']->{$field['name']} ? 'Yes' : 'No' }}
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
