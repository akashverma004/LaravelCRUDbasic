@extends('hrms.layouts.app')

@section('title', 'Regulatory Architecture - PeopleFlow HRMS')

@section('content')
<div class="space-y-12">

    {{-- Header Section --}}
    <div class="relative overflow-hidden rounded-[3rem] bg-slate-900 px-10 py-16 shadow-2xl dark:bg-slate-950/60 dark:backdrop-blur-3xl">
        <div class="absolute -right-20 -top-20 h-96 w-96 rounded-full bg-cyan-500/10 blur-[120px]"></div>
        <div class="absolute -bottom-20 -left-20 h-96 w-96 rounded-full bg-indigo-500/10 blur-[120px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-10 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-5xl font-black tracking-tighter text-white lg:text-7xl">
                    Governance <span class="text-cyan-400">Hub</span>
                </h1>
                <p class="mt-6 max-w-xl text-lg font-bold text-slate-400 uppercase tracking-wide leading-relaxed">
                    Centralized directive management, regulatory coordination, and organizational policy architecture.
                </p>
            </div>
            <div class="h-16 w-16 flex items-center justify-center rounded-[2rem] bg-white/5 border border-white/10 backdrop-blur-xl">
                 <svg class="h-8 w-8 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>
            </div>
        </div>
    </div>

    {{-- Policy Lattice Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {{-- Holiday Policies --}}
        <a href="{{ route('policies.holiday-policies.index') }}" class="group relative overflow-hidden rounded-[3rem] border border-cyan-400/20 bg-cyan-400/5 p-10 shadow-sm transition-all hover:shadow-2xl hover:-translate-y-1 dark:bg-cyan-400/[0.03]">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-cyan-400/10 blur-3xl group-hover:bg-cyan-400/20 transition-all"></div>
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-cyan-400 text-slate-950 shadow-2xl shadow-cyan-400/20 mb-8 transition-transform group-hover:scale-110">
                 <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" /></svg>
            </div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-white uppercase transition-colors group-hover:text-cyan-400">Holiday Policies</h2>
            <p class="mt-6 text-[11px] font-black uppercase tracking-widest leading-relaxed text-slate-500 dark:text-slate-400">Architect state-wise holiday protocols, weekend definitions, and active status segments.</p>
            <div class="mt-10 flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-cyan-400">
                <span>Configure Protocol</span>
                <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
            </div>
        </a>

        {{-- Calendar Manager --}}
        <a href="{{ route('policies.holiday-calendar.index') }}" class="group relative overflow-hidden rounded-[3rem] border border-indigo-400/20 bg-indigo-400/5 p-10 shadow-sm transition-all hover:shadow-2xl hover:-translate-y-1 dark:bg-indigo-400/[0.03]">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-indigo-400/10 blur-3xl group-hover:bg-indigo-400/20 transition-all"></div>
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500 text-white shadow-2xl shadow-indigo-500/20 mb-8 transition-transform group-hover:scale-110">
                 <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
            </div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-white uppercase transition-colors group-hover:text-indigo-400">Calendar Manager</h2>
            <p class="mt-6 text-[11px] font-black uppercase tracking-widest leading-relaxed text-slate-500 dark:text-slate-400">Select active policies and manage immutable holiday markers within the organizational timeline.</p>
            <div class="mt-10 flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-indigo-400">
                <span>Manage Timeline</span>
                <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
            </div>
        </a>

        {{-- Dynamic Policies --}}
        @foreach ($types as $item)
            <a href="{{ $item['route'] }}" class="group relative overflow-hidden rounded-[3rem] border border-slate-200 bg-white p-10 shadow-sm transition-all hover:shadow-2xl hover:-translate-y-1 dark:border-slate-800 dark:bg-slate-900/50">
                <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-slate-100 blur-3xl dark:bg-white/[0.02] group-hover:bg-cyan-400/10 transition-all"></div>
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-950 text-white shadow-2xl dark:bg-white dark:text-slate-950 mb-8 transition-all group-hover:bg-cyan-400 group-hover:text-slate-950 group-hover:scale-110">
                     <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                </div>
                <h2 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-white uppercase transition-colors group-hover:text-cyan-400 truncate">{{ $item['title'] }}</h2>
                <p class="mt-6 text-[11px] font-black uppercase tracking-widest leading-relaxed text-slate-500 dark:text-slate-400">{{ $item['description'] }}</p>
                <div class="mt-10 flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-cyan-400">
                    <span>Initialize Interface</span>
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
