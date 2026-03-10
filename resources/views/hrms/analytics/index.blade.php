@extends('hrms.layouts.app')

@section('title', 'Analytics - PeopleFlow HRMS')

@section('content')
<div x-data="analyticsDashboard()" x-init="init()">
    
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">People Analytics</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Visual insights into your workspace's growth and health</p>
        </div>
        <button @click="fetchData()" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors dark:bg-slate-800 dark:text-white dark:hover:bg-slate-700">
            <svg class="h-4 w-4" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Refresh Data
        </button>
    </div>

    {{-- Stat Cards --}}
    <div class="mb-6 grid gap-6 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Employees</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900 dark:text-white" x-text="stats.totalEmployees"></span>
                <span class="text-xs font-medium text-green-500">+2 from last month</span>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">On Leave Today</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900 dark:text-white" x-text="stats.activeLeaves"></span>
                <span class="text-xs font-medium text-slate-500">Across all depts</span>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Attendance Rate</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900 dark:text-white" x-text="Math.round((stats.presentToday / stats.totalEmployees) * 100) + '%'"></span>
                <span class="text-xs font-medium text-cyan-500" x-text="stats.presentToday + ' present today'"></span>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        
        {{-- Attendance Heatmap/Trends (Simplified) --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
            <h3 class="mb-6 text-sm font-semibold text-slate-900 dark:text-white">Attendance Trend (Last 14 Days)</h3>
            <div class="flex h-48 items-end gap-2 px-2">
                <template x-for="data in attendanceTrend" :key="data.day">
                    <div class="group relative flex flex-1 flex-col items-center gap-2">
                        <div class="w-full rounded-t-lg bg-cyan-500/20 group-hover:bg-cyan-500/40 transition-all duration-500 relative" :style="'height: ' + data.percentage + '%'">
                            <div class="absolute inset-x-0 top-0 h-1 bg-cyan-500 rounded-full"></div>
                            {{-- Tooltip --}}
                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 rounded bg-slate-900 px-1.5 py-0.5 text-[10px] font-bold text-white opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" x-text="data.percentage + '%'"></div>
                        </div>
                        <span class="text-[9px] font-bold uppercase text-slate-400 dark:text-slate-500" x-text="data.day.split(' ')[0]"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Headcount Growth (Visualized Labels) --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
            <h3 class="mb-6 text-sm font-semibold text-slate-900 dark:text-white">Headcount Growth</h3>
            <div class="space-y-4">
                <template x-for="(data, index) in headcountTrend" :key="data.month">
                    <div class="flex items-center gap-4">
                        <span class="w-16 text-xs font-semibold text-slate-500 dark:text-slate-400" x-text="data.month"></span>
                        <div class="h-6 flex-1 rounded-lg bg-slate-100 dark:bg-slate-700 relative overflow-hidden">
                            <div class="h-full bg-cyan-500/30 transition-all duration-1000" :style="'width: ' + ((data.count / stats.totalEmployees) * 100) + '%'"></div>
                            <span class="absolute inset-y-0 left-3 flex items-center text-[10px] font-bold text-cyan-600 dark:text-cyan-400" x-text="data.count + ' Employees'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Absence / Leave Distribution --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
            <h3 class="mb-6 text-sm font-semibold text-slate-900 dark:text-white">Monthly Absences (Days)</h3>
            <div class="flex h-32 items-end gap-3 px-2">
                <template x-for="data in absenceTrend" :key="data.month">
                    <div class="group relative flex flex-1 flex-col items-center gap-2">
                        <div class="w-full rounded-t-lg bg-rose-500/20 group-hover:bg-rose-500/40 transition-all duration-500 relative" :style="'height: ' + (data.days * 5) + '%'">
                            <div class="absolute inset-x-0 top-0 h-1 bg-rose-500 rounded-full"></div>
                        </div>
                        <span class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500" x-text="data.month"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Department Distribution --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
            <h3 class="mb-6 text-sm font-semibold text-slate-900 dark:text-white">Department Distribution</h3>
            <div class="grid grid-cols-2 gap-4">
                <template x-for="dept in departmentDistribution" :key="dept.name">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-slate-700 dark:bg-slate-900/30">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider" x-text="dept.name"></div>
                        <div class="mt-1 text-xl font-bold text-slate-900 dark:text-white" x-text="dept.count"></div>
                    </div>
                </template>
            </div>
        </div>

    </div>
</div>
@endsection
