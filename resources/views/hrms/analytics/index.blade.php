@extends('hrms.layouts.app')

@section('title', 'Analytics - PeopleFlow HRMS')

@section('content')
<div x-data="analyticsDashboard()" x-init="init()" class="relative space-y-6 pb-8">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Intelligence</span>
                <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Analytics</span>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                People <span class="text-cyan-500">Analytics</span>
            </h1>
            <p class="mt-1 text-[11px] font-medium text-slate-400 uppercase tracking-wide leading-relaxed">
                Decoding organizational intelligence and personnel flow.
            </p>
        </div>
        <div class="flex items-center gap-3">
             <button @click="fetchData()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                <svg :class="loading ? 'animate-spin' : ''" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                <span>Synchronize</span>
            </button>
        </div>
    </div>

    {{-- Core Stats Lattice --}}
    <div class="grid gap-4 sm:grid-cols-3">
        {{-- Total Employees --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Total Workforce</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white" x-text="stats.totalEmployees"></span>
                <span class="flex items-center gap-0.5 text-[10px] font-black text-emerald-500 uppercase tracking-widest">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
                    2%
                </span>
            </div>
            <div class="mt-4 h-1 w-full bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 transition-all duration-1000" style="width: 75%"></div>
            </div>
        </div>

        {{-- Leaves Today --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Absent Today</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white" x-text="stats.activeLeaves"></span>
                <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Personnel</span>
            </div>
            <div class="mt-4 h-1 w-full bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-rose-500 transition-all duration-1000" style="width: 25%"></div>
            </div>
        </div>

        {{-- Attendance Rate --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Daily Attendance</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white" x-text="Math.round((stats.presentToday / stats.totalEmployees) * 100) + '%'"></span>
                <span class="text-[10px] font-black text-cyan-500 uppercase tracking-widest" x-text="stats.presentToday + ' Present'"></span>
            </div>
            <div class="mt-4 h-1 w-full bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-cyan-500 transition-all duration-1000" :style="'width: ' + ((stats.presentToday / stats.totalEmployees) * 100) + '%'"></div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        
        {{-- Attendance Chart --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
            <div class="mb-8">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 mb-1">Momentum</h3>
                <h2 class="text-sm font-black uppercase text-slate-900 dark:text-white">Attendance Cycle</h2>
            </div>
            <div class="flex h-48 items-end gap-3 px-2">
                <template x-for="data in attendanceTrend" :key="data.day">
                    <div class="group relative flex flex-1 flex-col items-center gap-2 h-full">
                        <div class="w-full rounded-lg bg-slate-50 border border-slate-100 transition-all relative h-full flex items-end dark:border-white/5 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 overflow-hidden shadow-sm">
                            <div class="w-full bg-cyan-500/10 transition-all relative" :style="'height: ' + data.percentage + '%'">
                                <div class="absolute inset-x-0 top-0 h-1 bg-cyan-500 shadow-[0_0_10px_rgba(6,182,212,0.3)]"></div>
                                {{-- Tooltip --}}
                                <div class="absolute -top-10 left-1/2 -translate-x-1/2 rounded-lg bg-slate-900 px-2.5 py-1 text-[9px] font-black text-white opacity-0 group-hover:opacity-100 transition-all pointer-events-none whitespace-nowrap z-10" x-text="data.percentage + '%'"></div>
                            </div>
                        </div>
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest" x-text="data.day.split(' ')[0]"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Headcount Trend --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
            <div class="mb-8">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 mb-1">Velocity</h3>
                <h2 class="text-sm font-black uppercase text-slate-900 dark:text-white">Workforce Expansion</h2>
            </div>
            <div class="space-y-4">
                <template x-for="(data, index) in headcountTrend" :key="data.month">
                    <div class="group flex items-center gap-4">
                        <span class="w-16 text-[9px] font-black text-slate-500 uppercase tracking-widest" x-text="data.month"></span>
                        <div class="h-8 flex-1 rounded-lg bg-slate-50 relative overflow-hidden border border-slate-100 dark:border-white/5 dark:bg-white/5 shadow-inner">
                            <div class="h-full bg-cyan-500/10 group-hover:bg-cyan-500/20 transition-all" :style="'width: ' + ((data.count / stats.totalEmployees) * 100) + '%'"></div>
                            <div class="absolute inset-y-0 left-3 flex items-center">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white" x-text="data.count"></span>
                            </div>
                            <div class="absolute right-0 inset-y-0 flex items-center pr-3">
                                <span class="text-[8px] font-black text-slate-400" x-text="Math.round((data.count / stats.totalEmployees) * 100) + '%'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Department Distribution --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
            <div class="mb-8">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 mb-1">Architecture</h3>
                <h2 class="text-sm font-black uppercase text-slate-900 dark:text-white">Personnel Allocation</h2>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <template x-for="dept in departmentDistribution" :key="dept.name">
                    <div class="rounded-lg border border-slate-100 bg-slate-50/50 p-3 transition-colors hover:border-cyan-500/20 hover:bg-cyan-50/50 dark:border-white/5 dark:bg-white/5 dark:hover:bg-cyan-500/10 cursor-default">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 truncate" x-text="dept.name"></div>
                            <div class="h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-500"></div>
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <div class="text-xl font-black text-slate-900 dark:text-white" x-text="dept.count"></div>
                            <div class="text-[8px] font-black uppercase tracking-widest text-slate-400">Personnel</div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Leave Trend --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
            <div class="mb-8">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 mb-1">Stability</h3>
                <h2 class="text-sm font-black uppercase text-slate-900 dark:text-white">Absence Analytics</h2>
            </div>
            <div class="flex h-48 items-end gap-3 px-2">
                <template x-for="data in absenceTrend" :key="data.month">
                    <div class="group relative flex flex-1 flex-col items-center gap-2 h-full">
                        <div class="w-full rounded-lg bg-slate-50 border border-slate-100 transition-colors relative h-full flex items-end dark:border-white/5 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 overflow-hidden shadow-sm">
                            <div class="w-full bg-rose-500/10 transition-colors relative" :style="'height: ' + (data.days * 4) + '%'">
                                <div class="absolute inset-x-0 top-0 h-1 bg-rose-500"></div>
                                <div class="absolute -top-10 left-1/2 -translate-x-1/2 rounded bg-slate-900 px-2 py-1 text-[9px] font-black text-white opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10" x-text="data.days + ' Days'"></div>
                            </div>
                        </div>
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest" x-text="data.month"></span>
                    </div>
                </template>
            </div>
        </div>

    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
</style>
@endsection
