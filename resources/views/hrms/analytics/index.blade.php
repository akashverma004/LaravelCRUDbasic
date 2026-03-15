@extends('hrms.layouts.app')

@section('title', 'Analytics Dashboard - PeopleFlow HRMS')

@section('content')
<div x-data="analyticsDashboard()" x-init="init()" class="space-y-8">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">People Analytics</h1>
            <p class="mt-2 text-sm text-slate-500">Monitor key organizational metrics and employment trends.</p>
        </div>
        <div class="flex items-center gap-3">
             <button @click="fetchData()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                <svg :class="loading ? 'animate-spin' : ''" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                <span>Refresh Data</span>
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
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/40">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Attendance Trend</h3>
                    <p class="text-xs font-medium text-slate-500">Last 14 days</p>
                </div>
            </div>
            <div class="flex h-56 items-end gap-3">
                <template x-for="data in attendanceTrend" :key="data.day">
                    <div class="group relative flex flex-1 flex-col items-center gap-2 h-full">
                        <div class="w-full rounded-md bg-slate-50 border border-slate-100 transition-colors relative h-full flex items-end dark:border-slate-800 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-800 overflow-hidden">
                            <div class="w-full bg-cyan-500/20 group-hover:bg-cyan-500/40 transition-colors relative" :style="'height: ' + data.percentage + '%'">
                                <div class="absolute inset-x-0 top-0 h-1 bg-cyan-500 group-hover:h-1.5 transition-all"></div>
                                {{-- Tooltip --}}
                                <div class="absolute -top-10 left-1/2 -translate-x-1/2 rounded bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10" x-text="data.percentage + '%'"></div>
                            </div>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400 text-center uppercase" x-text="data.day.split(' ')[0]"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Headcount Trend --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/40">
            <div class="mb-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Headcount Growth</h3>
                <p class="text-xs font-medium text-slate-500">Employee count over time</p>
            </div>
            <div class="space-y-4">
                <template x-for="(data, index) in headcountTrend" :key="data.month">
                    <div class="group flex items-center gap-4">
                        <span class="w-16 text-xs font-semibold text-slate-500 uppercase tracking-widest" x-text="data.month"></span>
                        <div class="h-8 flex-1 rounded-lg bg-slate-50 relative overflow-hidden border border-slate-100 dark:border-slate-800 dark:bg-slate-900/50">
                            <div class="h-full bg-indigo-500/20 transition-all duration-1000 group-hover:bg-indigo-500/40" :style="'width: ' + ((data.count / stats.totalEmployees) * 100) + '%'"></div>
                            <div class="absolute inset-y-0 left-3 flex items-center">
                                <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400" x-text="data.count + ' Employees'"></span>
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
