@extends('hrms.layouts.app')

@section('title', 'Analytics Dashboard - PeopleFlow HRMS')

@section('content')
<div x-data="analyticsDashboard()" x-init="init()" class="space-y-8">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-white uppercase"><span class="text-cyan-500">People</span> Analytics</h1>
            <p class="mt-1 text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-none">Decoding organizational intelligence and personnel flow.</p>
        </div>
        <div class="flex items-center gap-3">
             <button @click="fetchData()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                <svg :class="loading ? 'animate-spin' : ''" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                <span>Synchronize</span>
            </button>
        </div>
    </div>

    {{-- Core Stats Lattice --}}
    <div class="grid gap-6 sm:grid-cols-3">
        {{-- Total Employees --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/40">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-4">Total Employees</p>
            <div class="flex items-end gap-3">
                <span class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white" x-text="stats.totalEmployees"></span>
                <span class="flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 mb-1">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
                    2%
                </span>
            </div>
            <div class="mt-6 h-1 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 transition-all duration-1000" style="width: 75%"></div>
            </div>
        </div>

        {{-- Leaves Today --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/40">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-4">Absent Today</p>
            <div class="flex items-end gap-3">
                <span class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white" x-text="stats.activeLeaves"></span>
                <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 mb-1">Leaves</span>
            </div>
            <div class="mt-6 h-1 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-rose-500 transition-all duration-1000" style="width: 25%"></div>
            </div>
        </div>

        {{-- Attendance Rate --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/40">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-4">Attendance Rate</p>
            <div class="flex items-end gap-3">
                <span class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white" x-text="Math.round((stats.presentToday / stats.totalEmployees) * 100) + '%'"></span>
                <span class="rounded-full bg-cyan-50 px-2 py-0.5 text-[10px] font-bold text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400 mb-1" x-text="stats.presentToday + ' Present'"></span>
            </div>
            <div class="mt-6 h-1 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-cyan-500 transition-all duration-1000" :style="'width: ' + ((stats.presentToday / stats.totalEmployees) * 100) + '%'"></div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        
        {{-- Attendance Chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white leading-none">Attendance Momentum</h3>
                    <p class="mt-2 text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 leading-none">Last 14-day cycle</p>
                </div>
            </div>
            <div class="flex h-56 items-end gap-3 px-2">
                <template x-for="data in attendanceTrend" :key="data.day">
                    <div class="group relative flex flex-1 flex-col items-center gap-3 h-full">
                        <div class="w-full rounded-xl bg-slate-50 border border-slate-100 transition-all relative h-full flex items-end dark:border-white/5 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 overflow-hidden shadow-sm">
                            <div class="w-full bg-cyan-500/10 group-hover:bg-cyan-500/20 transition-all relative" :style="'height: ' + data.percentage + '%'">
                                <div class="absolute inset-x-0 top-0 h-1 bg-cyan-500 shadow-[0_0_15px_rgba(6,182,212,0.5)] transition-all"></div>
                                {{-- Tooltip --}}
                                <div class="absolute -top-12 left-1/2 -translate-x-1/2 rounded-lg bg-slate-900 px-3 py-1.5 text-[10px] font-black text-white opacity-0 group-hover:opacity-100 transition-all pointer-events-none whitespace-nowrap z-10 shadow-2xl scale-90 group-hover:scale-100" x-text="data.percentage + '% ATTENDANCE'"></div>
                            </div>
                        </div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none" x-text="data.day.split(' ')[0]"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Headcount Trend --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
            <div class="mb-8">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white leading-none">Expansion Velocity</h3>
                <p class="mt-2 text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 leading-none">Cumulative workforce growth</p>
            </div>
            <div class="space-y-6">
                <template x-for="(data, index) in headcountTrend" :key="data.month">
                    <div class="group flex items-center gap-6 px-1">
                        <span class="w-20 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em]" x-text="data.month"></span>
                        <div class="h-10 flex-1 rounded-xl bg-slate-50 relative overflow-hidden border border-slate-100 dark:border-white/5 dark:bg-white/5 shadow-inner">
                            <div class="h-full bg-slate-900/5 transition-all duration-1000 group-hover:bg-cyan-500/20" :style="'width: ' + ((data.count / stats.totalEmployees) * 100) + '%'"></div>
                            <div class="absolute inset-y-0 left-4 flex items-center">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white" x-text="data.count"></span>
                                <span class="ml-2 text-[8px] font-black uppercase tracking-widest text-slate-400 opacity-0 group-hover:opacity-100 transition-all">Personnel</span>
                            </div>
                            <div class="absolute right-0 inset-y-0 flex items-center pr-4">
                                <span class="text-[8px] font-black text-slate-300 dark:text-white/10" x-text="Math.round((data.count / stats.totalEmployees) * 100) + '%'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Department Distribution --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/40">
            <div class="mb-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Department Size</h3>
                <p class="text-xs font-medium text-slate-500">Distribution of workforce</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <template x-for="dept in departmentDistribution" :key="dept.name">
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 transition-colors hover:border-emerald-500/30 hover:bg-emerald-50/50 dark:border-slate-800 dark:bg-slate-900/50 dark:hover:border-emerald-500/30 dark:hover:bg-emerald-500/5 cursor-default">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 truncate pr-2" x-text="dept.name"></div>
                            <div class="h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <div class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white" x-text="dept.count"></div>
                            <div class="text-[10px] font-medium text-slate-500">members</div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Leave Trend --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/40">
            <div class="mb-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Leave Analytics</h3>
                <p class="text-xs font-medium text-slate-500">Total absent days by month</p>
            </div>
            <div class="flex h-48 items-end gap-4">
                <template x-for="data in absenceTrend" :key="data.month">
                    <div class="group relative flex flex-1 flex-col items-center gap-2 h-full">
                        <div class="w-full rounded-md bg-slate-50 border border-slate-100 transition-colors relative h-full flex items-end dark:border-slate-800 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-800 overflow-hidden">
                            <div class="w-full bg-rose-500/20 group-hover:bg-rose-500/40 transition-colors relative" :style="'height: ' + (data.days * 4) + '%'">
                                <div class="absolute inset-x-0 top-0 h-1 bg-rose-500 group-hover:h-1.5 transition-all"></div>
                                <div class="absolute -top-10 left-1/2 -translate-x-1/2 rounded bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10" x-text="data.days + ' days'"></div>
                            </div>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase" x-text="data.month"></span>
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
