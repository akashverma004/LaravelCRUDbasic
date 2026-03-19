@extends('hrms.layouts.app')

@section('title', 'Admin Dashboard - PeopleFlow HRMS')

@section('content')
<div x-data="adminDashboardActions({
        departmentStoreUrl: '{{ route('departments.store') }}',
        assignManagerUrl: '{{ route('employees.assign-manager') }}',
        defaultEffectiveDate: '{{ now()->toDateString() }}',
        leaveTypeChartData: @js($leaveTypeChartData)
    })"
    class="relative space-y-5">

    {{-- Dashboard Header --}}
    <div class="relative mb-4">
        <div class="flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-lg font-black tracking-tight text-slate-900 dark:text-white lg:text-[1.6rem]">
                    Executive <span class="bg-gradient-to-r from-cyan-500 to-indigo-500 bg-clip-text text-transparent">Dashboard</span>
                </h1>
                <p class="mt-1 max-w-xl text-[9px] font-medium text-slate-400">
                    Your organization at a glance. Manage operations and workforce with ease.
                </p>
            </div>
            <div class="flex items-center gap-2.5 rounded-xl border border-slate-100 bg-white px-3 py-1.5 shadow-sm dark:border-white/5 dark:bg-slate-900">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 text-slate-400 dark:bg-white/5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                </div>
                <div>
                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Today</p>
                    <p class="text-[10px] font-bold text-slate-900 dark:text-white">{{ now()->format('l, d F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Metrics Grid --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        @foreach([
            ['Employees', $employeeCount, 'Total active employees', 'indigo', route('employees.index'), 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
            ['Departments', $departmentCount, 'Total departments', 'cyan', route('departments.index'), 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6.75h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75H21m-3.75 3.75H21'],
            ['Pending Leaves', $leavePending, 'Awaiting approval', 'rose', route('leaves.index'), 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Approval Inbox', $workflowPending, 'Pending workflows', 'indigo', route('workflows.index'), 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
            ['Present Today', $attendanceToday, 'Employees at work', 'emerald', route('employees.index'), 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z']
        ] as [$title, $val, $desc, $color, $link, $icon])
        <a href="{{ $link }}" class="group relative overflow-hidden rounded-xl border p-4 shadow-[0_10px_24px_rgba(15,23,42,0.05)] transition-all hover:-translate-y-0.5 hover:shadow-[0_14px_28px_rgba(15,23,42,0.08)] dark:shadow-[0_18px_40px_rgba(2,6,23,0.35)]
            @if($color === 'indigo') border-blue-200 bg-gradient-to-br from-blue-50 via-indigo-50 to-blue-100/80 dark:border-blue-500/20 dark:bg-gradient-to-br dark:from-slate-950 dark:via-blue-950/35 dark:to-slate-900
            @elseif($color === 'cyan') border-fuchsia-200 bg-gradient-to-br from-fuchsia-50 via-violet-50 to-purple-100/80 dark:border-violet-500/20 dark:bg-gradient-to-br dark:from-slate-950 dark:via-violet-950/35 dark:to-slate-900
            @elseif($color === 'rose') border-orange-200 bg-gradient-to-br from-orange-50 via-amber-50 to-orange-100/80 dark:border-orange-500/20 dark:bg-gradient-to-br dark:from-slate-950 dark:via-orange-950/35 dark:to-slate-900
            @elseif($color === 'emerald') border-emerald-200 bg-gradient-to-br from-emerald-50 via-teal-50 to-emerald-100/80 dark:border-emerald-500/20 dark:bg-gradient-to-br dark:from-slate-950 dark:via-emerald-950/35 dark:to-slate-900
            @else border-slate-200 bg-white @endif">
            <h3 class="mb-3 text-[8px] font-bold tracking-tight
                @if($color === 'indigo') text-blue-700 dark:text-blue-200
                @elseif($color === 'cyan') text-violet-700 dark:text-violet-200
                @elseif($color === 'rose') text-orange-700 dark:text-orange-200
                @elseif($color === 'emerald') text-emerald-700 dark:text-emerald-200
                @else text-slate-500 @endif">{{ $title }}</h3>
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-[1.8rem] font-black leading-none tracking-tight
                        @if($color === 'indigo') text-blue-600 dark:text-blue-100
                        @elseif($color === 'cyan') text-violet-600 dark:text-violet-100
                        @elseif($color === 'rose') text-orange-600 dark:text-orange-100
                        @elseif($color === 'emerald') text-emerald-600 dark:text-emerald-100
                        @else text-slate-900 dark:text-white @endif" x-text="'{{ $val }}'"></p>
                    <p class="mt-1.5 line-clamp-1 text-[9px] font-medium text-slate-500 dark:text-slate-400">{{ $desc }}</p>
                </div>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/60 bg-white/70 shadow-sm transition-transform group-hover:scale-105 dark:border-white/10 dark:bg-white/5">
                    <svg class="h-4 w-4
                        @if($color === 'indigo') text-blue-500 dark:text-blue-200
                        @elseif($color === 'cyan') text-violet-500 dark:text-violet-200
                        @elseif($color === 'rose') text-orange-500 dark:text-orange-200
                        @elseif($color === 'emerald') text-emerald-500 dark:text-emerald-200
                        @else text-slate-500 @endif" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="grid gap-4 xl:grid-cols-[minmax(0,1.8fr)_minmax(300px,0.95fr)]">
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Employees by Department</h2>
                        <p class="text-[10px] text-slate-500 mt-0.5">Headcount across different departments</p>
                    </div>
                </div>
                <div class="relative h-56">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>

            {{-- Leaves Overview --}}
            <div class="rounded-xl border border-slate-200 bg-[#f8fbfa] p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Leaves Overview</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <select id="leaveTrendRange" class="h-8 min-w-[110px] rounded-lg border border-slate-200 bg-white px-3 text-[10px] font-semibold text-slate-600 shadow-sm transition-colors focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-slate-950 dark:text-slate-300">
                            <option value="7">Past 7 days</option>
                            <option value="30" selected>Past 30 days</option>
                            <option value="60">Past 60 days</option>
                            <option value="90">Past 90 days</option>
                        </select>
                        <a href="{{ route('leaves.my') }}" class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 shadow-sm transition-colors hover:bg-slate-50 hover:text-slate-700 dark:border-white/5 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-900">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-12">
                    {{-- Doughnut Chart --}}
                    <div class="lg:col-span-5">
                        <p class="mb-4 text-[11px] font-bold text-slate-900 dark:text-white">Leave Requests by Type ({{ now()->format('M Y') }})</p>
                        <div class="flex items-center gap-4">
                            <div class="h-28 w-28 shrink-0">
                                <canvas id="leaveTypeChart"></canvas>
                            </div>
                            <div class="space-y-2">
                                <template x-for="(label, index) in leaveTypeData.labels" :key="index">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full" :style="`background-color: ${leaveTypeColors[index]}`"></div>
                                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400" x-text="`${label} ${leaveTypeData.values.reduce((a,b) => a+b, 0) > 0 ? Math.round((leaveTypeData.values[index] / leaveTypeData.values.reduce((a,b) => a+b, 0)) * 100) : 0}%`"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Trend Line Chart --}}
                    <div class="lg:col-span-7">
                        <p class="mb-4 text-[11px] font-bold text-slate-900 dark:text-white">Leave Bookings vs. Capacity</p>
                        <div class="relative h-32">
                            <canvas id="leaveTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                {{-- Recent Employees --}}
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3 dark:border-white/5 dark:bg-slate-950/30">
                        <h3 class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-widest text-slate-400">Recent Employees</h3>
                        <a href="{{ route('employees.index') }}" class="text-[10px] font-bold text-cyan-500 hover:text-cyan-600 transition-colors uppercase tracking-wider">
                            View All &rarr;
                        </a>
                    </div>
                    <div class="space-y-1.5 p-3">
                        @forelse ($employees->take(5) as $employee)
                            <div class="flex items-center justify-between rounded-lg px-3 py-2.5 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        {{ substr($employee->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-[13px] font-semibold text-slate-900 dark:text-white">{{ $employee->full_name }}</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $employee->job_title ?? 'Employee' }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="py-8 text-center text-sm text-slate-500">No recent employees</p>
                        @endforelse
                    </div>
                </div>

                {{-- Pending Leaves --}}
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3 dark:border-white/5 dark:bg-slate-950/30">
                        <h3 class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-widest text-slate-400">Pending Leaves</h3>
                        <a href="{{ route('leaves.index') }}" class="text-[10px] font-bold text-rose-500 hover:text-rose-600 transition-colors uppercase tracking-wider">
                            View All &rarr;
                        </a>
                    </div>
                    <div class="space-y-1.5 p-3">
                        @forelse ($leaveRequests->take(5) as $leave)
                            <div class="flex items-center justify-between rounded-lg px-3 py-2.5 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-50 text-xs font-bold text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                                        {{ substr($leave->employee->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-[13px] font-semibold text-slate-900 dark:text-white">{{ $leave->employee->full_name }}</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $leave->start_date->format('d M') }} — {{ $leave->end_date->format('d M') }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="py-8 text-center text-sm text-slate-500">No pending requests</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="space-y-4">
            <div class="relative rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Quick Add Department</h3>
                    <button @click="submitDepartment()" :disabled="departmentSaving" class="group relative flex h-7 w-7 items-center justify-center rounded-lg bg-slate-900 border border-white/10 text-white shadow-lg shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-90 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                        <svg x-show="!departmentSaving" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <svg x-show="departmentSaving" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </button>
                </div>
                <div class="space-y-3">
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400">Department Name</label>
                        <input x-model="departmentForm.name" placeholder="Engineering" class="w-full rounded-lg border border-slate-200 dark:border-white/10 bg-transparent px-3 py-2 text-[12px] font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400">Department Code</label>
                        <input x-model="departmentForm.code" placeholder="ENG-01" class="w-full rounded-lg border border-slate-200 dark:border-white/10 bg-transparent px-3 py-2 text-[12px] font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 uppercase">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400">Manager</label>
                        <select x-model="departmentForm.lead_employee_id" class="w-full rounded-lg border border-slate-200 dark:border-white/10 bg-transparent px-3 py-2 text-[12px] font-medium text-slate-900 dark:text-white focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                            <option value="" class="dark:bg-slate-900">Select Manager...</option>
                            @foreach($allEmployees as $employee)
                                <option value="{{ $employee->id }}" class="dark:bg-slate-900">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="relative rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Assign Manager</h3>
                    <button @click="submitManagerAssignment()" :disabled="managerSaving" class="group relative flex h-7 w-7 items-center justify-center rounded-lg bg-slate-900 border border-white/10 text-white shadow-lg shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-90 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                        <svg x-show="!managerSaving" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <svg x-show="managerSaving" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </button>
                </div>
                <div class="space-y-3">
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400">Employee</label>
                        <select x-model="managerForm.employee_id" class="w-full rounded-lg border border-slate-200 dark:border-white/10 bg-transparent px-3 py-2 text-[12px] font-medium text-slate-900 dark:text-white focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                            <option value="" class="dark:bg-slate-900">Select Employee...</option>
                            @foreach ($allEmployees as $emp)
                                <option value="{{ $emp->id }}" class="dark:bg-slate-900">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400">Manager</label>
                        <select x-model="managerForm.manager_id" class="w-full rounded-lg border border-slate-200 dark:border-white/10 bg-transparent px-3 py-2 text-[12px] font-medium text-slate-900 dark:text-white focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                            <option value="" class="dark:bg-slate-900">Select Manager...</option>
                            @foreach ($allEmployees as $emp)
                                <option value="{{ $emp->id }}" class="dark:bg-slate-900">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-[9px] text-slate-400 text-center mt-3">Ensure correct managers are assigned.</p>
                </div>
            </div>

            {{-- Active Sessions --}}
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400">Active Sessions</h3>
                    <button class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                    </button>
                </div>
                <div class="space-y-4">
                    @foreach($activeSessions as $session)
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 shrink-0 flex items-center justify-center rounded-full bg-slate-100 text-slate-600 font-bold dark:bg-white/5 dark:text-slate-400">
                                {{ $session['avatar'] }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $session['name'] }}</p>
                                <p class="text-[10px] font-medium text-slate-500">As {{ $session['last_activity'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deptCtx = document.getElementById('departmentChart').getContext('2d');
        const deptData = @js($departmentChartData);
        const deptBarColors = [
            'rgba(139, 92, 246, 0.28)',
            'rgba(99, 102, 241, 0.28)',
            'rgba(79, 70, 229, 0.28)',
            'rgba(168, 85, 247, 0.28)',
            'rgba(129, 140, 248, 0.28)',
            'rgba(124, 58, 237, 0.28)',
        ];
        const deptBorderColors = [
            'rgba(139, 92, 246, 1)',
            'rgba(99, 102, 241, 1)',
            'rgba(79, 70, 229, 1)',
            'rgba(168, 85, 247, 1)',
            'rgba(129, 140, 248, 1)',
            'rgba(124, 58, 237, 1)',
        ];
        
        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: deptData.labels,
                datasets: [{
                    label: 'Employees',
                    data: deptData.values,
                    backgroundColor: deptData.labels.map((_, index) => deptBarColors[index % deptBarColors.length]),
                    borderColor: deptData.labels.map((_, index) => deptBorderColors[index % deptBorderColors.length]),
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.1)', drawBorder: false },
                        ticks: { color: 'rgba(148, 163, 184, 0.8)', font: { size: 11, family: 'Inter' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: 'rgba(148, 163, 184, 0.8)', font: { size: 11, family: 'Inter' } }
                    }
                }
            }
        });

        // Leave Type Doughnut Chart
        const leaveTypeCtx = document.getElementById('leaveTypeChart').getContext('2d');
        const leaveTypeDataRaw = @js($leaveTypeChartData);
        const leaveTypeColors = ['#1e40af', '#059669', '#9ca3af', '#92400e'];

        new Chart(leaveTypeCtx, {
            type: 'doughnut',
            data: {
                labels: leaveTypeDataRaw.labels,
                datasets: [{
                    data: leaveTypeDataRaw.values,
                    backgroundColor: leaveTypeColors,
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                }
            }
        });

        // Leave Trend Line Chart
        const leaveTrendCtx = document.getElementById('leaveTrendChart').getContext('2d');
        const leaveTrendRange = document.getElementById('leaveTrendRange');
        const leaveTrendDataUrl = @js(route('dashboard.leave-trend-data'));
        const leaveTrendData = @js($leaveTrendChartData);
        
        const leaveTrendChart = new Chart(leaveTrendCtx, {
            type: 'line',
            data: {
                labels: leaveTrendData.labels,
                datasets: [
                    {
                        label: 'Bookings',
                        data: leaveTrendData.bookings,
                        borderColor: '#1e40af',
                        backgroundColor: 'rgba(30, 64, 175, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointBackgroundColor: '#1e40af'
                    },
                    {
                        label: 'Capacity',
                        data: leaveTrendData.capacity,
                        borderColor: '#059669',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 2,
                        pointBackgroundColor: '#059669'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.05)', drawBorder: false },
                        ticks: { color: '#94a3b8', font: { size: 9, family: 'Inter' }, maxTicksLimit: 5 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 9, family: 'Inter' }, maxTicksLimit: 6 }
                    }
                }
            }
        });

        const updateLeaveTrendChart = async (days) => {
            try {
                const { data } = await axios.get(leaveTrendDataUrl, {
                    params: { days },
                    headers: { Accept: 'application/json' },
                });

                leaveTrendChart.data.labels = data.data.labels || [];
                leaveTrendChart.data.datasets[0].data = data.data.bookings || [];
                leaveTrendChart.data.datasets[1].data = data.data.capacity || [];
                leaveTrendChart.update();
            } catch (error) {
                console.error('Failed to refresh leave trend chart.', error);
            }
        };

        leaveTrendRange?.addEventListener('change', (event) => {
            updateLeaveTrendChart(event.target.value);
        });
    });
</script>

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
</div>
@endsection
