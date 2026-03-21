@extends('hrms.layouts.app')

@section('title', 'My Dashboard - PeopleFlow HRMS')

@section('content')
<div class="space-y-8">
    {{-- Welcome Hero Section --}}
    <div class="relative overflow-hidden rounded-xl bg-white px-6 py-5 shadow-sm border border-slate-200 dark:border-white/5 dark:bg-slate-900">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-cyan-500/10 blur-[60px]"></div>
        <div class="absolute -bottom-20 -left-20 h-48 w-48 rounded-full bg-indigo-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-5 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-lg font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Welcome back, <span class="text-cyan-500">{{ explode(' ', $employee->full_name)[0] }}!</span> 👋
                </h1>
                <p class="mt-1 text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                    {{ $employee->job_title }} · {{ $employee->department->name ?? 'No Department' }}
                </p>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-2 border border-slate-100 shadow-sm dark:bg-slate-900/50 dark:border-white/5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Today</p>
                    <p class="text-[10px] font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ now()->format('l, j M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Profile Summary & Quick Stats --}}
        <div class="space-y-6">
            {{-- Profile Card --}}
            <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-cyan-500/30 dark:border-white/5 dark:bg-slate-900/50 dark:hover:bg-slate-900/80">
                <div class="flex flex-col items-center text-center">
                    <div class="relative mb-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-cyan-50 border border-cyan-100 text-xl font-black text-cyan-600 shadow-sm transition-transform group-hover:scale-105 dark:bg-cyan-500/10 dark:border-white/5 dark:text-cyan-400">
                            {{ substr($employee->full_name, 0, 1) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900 shadow-sm"></div>
                    </div>
                    <h2 class="text-sm font-black tracking-tight text-slate-900 dark:text-white uppercase">{{ $employee->full_name }}</h2>
                    <div class="mt-2.5 flex flex-wrap justify-center gap-1.5">
                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-slate-500 dark:bg-slate-800 dark:text-slate-400">{{ str_replace('-', ' ', $employee->employment_type) }}</span>
                        <span class="rounded bg-cyan-50 px-1.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400">{{ $employee->status }}</span>
                    </div>
                </div>
                
                <div class="mt-5 space-y-3 border-t border-slate-50 pt-5 dark:border-white/5">
                    <div class="flex justify-between items-center">
                        <span class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Manager</span>
                        <span class="text-[10px] font-bold text-slate-900 dark:text-white uppercase">{{ $employee->manager ? $employee->manager->full_name : 'None' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Joined</span>
                        <span class="text-[10px] font-bold text-slate-900 dark:text-white uppercase">{{ $employee->joined_on->format('M j, Y') }}</span>
                    </div>
                </div>
                
                <a href="{{ route('employees.show', $employee->id) }}" class="mt-5 flex items-center justify-center gap-2 rounded-lg bg-slate-900 border border-white/10 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-white shadow-lg transition-all hover:bg-cyan-600 active:scale-[0.98] dark:bg-white/5 dark:hover:bg-cyan-500">
                    View Profile
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            {{-- Policy Quick Access --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Key Policies</h3>
                <div class="mt-4 space-y-2">
                    @foreach([
                        ['Notice Period', $noticePolicy->notice_days ?? '30', 'Days'],
                        ['Work Mode', 'Hybrid', ''],
                        ['Working Hours', 'Flexible', ''],
                    ] as [$name, $val, $tag])
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 dark:bg-slate-950/40 border border-slate-100 dark:border-white/5">
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">{{ $name }}</p>
                        <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $val }} <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">{{ $tag }}</span></p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Main Dashboard Modules --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Attendance Tracking --}}
            <div x-data="attendanceCard({ punchInUrl: '{{ route('attendance.punch-in') }}', pauseUrl: '{{ route('attendance.pause') }}', resumeUrl: '{{ route('attendance.resume') }}', punchOutUrl: '{{ route('attendance.punch-out') }}', status: '{{ $todayAttendance?->status }}' })" 
                 class="relative overflow-hidden rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                
                <div class="relative flex flex-col items-center justify-between gap-5 lg:flex-row">
                    <div class="text-center lg:text-left">
                        <h3 class="text-sm font-black uppercase tracking-tight text-slate-900 dark:text-white">Attendance Status</h3>
                        <p class="mt-1 text-[9px] font-bold uppercase tracking-widest text-slate-400">Track your working hours</p>
                    </div>

                    <div x-show="flash.show" x-transition class="fixed right-6 top-6 z-50 rounded-xl bg-slate-900 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-xl dark:bg-white dark:text-slate-900" style="display: none;">
                        <span x-text="flash.message"></span>
                    </div>

                    <div class="flex flex-col items-center gap-1.5 lg:items-end">
                        <span class="text-[8px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Logged Time</span>
                        <div x-data="{
                                status: '{{ $todayAttendance?->status ?? 'none' }}',
                                totalSeconds: {{ $todayAttendance?->getTotalWorkedSeconds() ?? 0 }},
                                drawTimer() {
                                    const pad = (n) => String(n).padStart(2, '0');
                                    this.$el.innerText = `${pad(Math.floor(this.totalSeconds / 3600))}:${pad(Math.floor((this.totalSeconds % 3600) / 60))}:${pad(this.totalSeconds % 60)}`;
                                },
                                init() {
                                    this.drawTimer();
                                    if (this.status === 'clocked_in') {
                                        setInterval(() => {
                                            this.totalSeconds++;
                                            this.drawTimer();
                                        }, 1000);
                                    }
                                }
                             }"
                             id="attendance-total-timer" class="text-2xl font-black tabular-nums tracking-tight text-slate-900 dark:text-white bg-slate-50 px-4 py-2 rounded-lg border border-slate-100 dark:bg-slate-950/40 dark:border-white/5">
                             00:00:00
                        </div>
                    </div>
                </div>

                <div class="relative mt-6 flex flex-wrap justify-center gap-2 lg:justify-start border-t border-slate-50 pt-5 dark:border-white/5">
                    @if(!$todayAttendance)
                        <button @click="act(punchInUrl, {}, 'Clocked in successfully.')" :disabled="loading" 
                                class="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-md transition-all hover:bg-cyan-600 active:scale-95">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span x-text="loading ? 'Processing...' : 'Clock In'"></span>
                        </button>
                    @elseif($todayAttendance->status !== 'completed')
                        <div class="flex flex-wrap gap-2">
                            @if($todayAttendance->status === 'clocked_in')
                                <button @click="act(pauseUrl, { type: 'lunch' }, 'Lunch break started.')" :disabled="loading" class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-[9px] font-black uppercase tracking-widest text-slate-600 transition-all hover:border-cyan-500 hover:text-cyan-600 dark:border-white/5 dark:bg-slate-900 dark:text-slate-400">Lunch</button>
                                <button @click="act(pauseUrl, { type: 'break' }, 'Break started.')" :disabled="loading" class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-[9px] font-black uppercase tracking-widest text-slate-600 transition-all hover:border-cyan-500 hover:text-cyan-600 dark:border-white/5 dark:bg-slate-900 dark:text-slate-400">Break</button>
                            @else
                                <button @click="act(resumeUrl, {}, 'Resumed successfully.')" :disabled="loading" class="rounded-lg bg-emerald-500 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-md transition-all hover:bg-emerald-600 active:scale-95">Resume</button>
                            @endif
                            <button @click="confirm('Are you sure you want to clock out for the day?') && act(punchOutUrl, {}, 'Clocked out successfully.')" :disabled="loading" 
                                class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-md transition-all hover:bg-rose-600 active:scale-95 disabled:opacity-50 dark:bg-white/5">
                                <svg x-show="loading" class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                <span x-text="loading ? 'Processing' : 'Clock Out'"></span>
                            </button>
                        </div>
                    @else
                        <div class="flex items-center gap-2 rounded-lg bg-emerald-50 px-4 py-2.5 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 w-fit">
                            <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-800 dark:text-emerald-300">Clocked out for today</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Secondary Modules Grid --}}
            <div class="grid gap-4 md:grid-cols-3">
                {{-- Recent Leaves Module --}}
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                    <div class="flex items-center justify-between border-b border-slate-50 px-5 py-3 dark:border-white/5 bg-slate-50 dark:bg-slate-900/50 rounded-t-xl">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Leave History</h3>
                        <a href="{{ route('leaves.my') }}" class="group flex items-center gap-1 text-[9px] font-black tracking-widest uppercase text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300">
                            View All <svg class="h-2.5 w-2.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($myLeaves->take(3) as $leave)
                            <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-white px-3 py-2.5 dark:border-white/5 dark:bg-slate-900">
                                <div>
                                    <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $leave->leave_type }}</p>
                                    <p class="text-[8px] font-bold text-slate-400 mt-0.5 uppercase tracking-widest">{{ $leave->start_date->format('M j') }} · {{ $leave->days }} {{ Str::plural('Day', $leave->days) }}</p>
                                </div>
                                <span class="rounded px-2 py-0.5 text-[8px] font-black uppercase tracking-widest
                                      {{ $leave->status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : '' }}
                                      {{ $leave->status === 'approved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : '' }}
                                      {{ $leave->status === 'rejected' ? 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400' : '' }}
                                      ">{{ $leave->status }}</span>
                            </div>
                        @empty
                            <p class="py-4 text-center text-[9px] font-bold uppercase tracking-widest text-slate-400">No leave requests found.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Leave Analytics Module --}}
                <div class="rounded-xl border border-slate-200 bg-[#f8fbfa] shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                    <div class="flex items-center justify-between border-b border-slate-50 px-5 py-3 dark:border-white/5 bg-slate-50 dark:bg-slate-900/50 rounded-t-xl">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Leave Breakdown</h3>
                        <span class="text-[8px] font-black uppercase tracking-widest text-slate-400">Approved</span>
                    </div>
                    <div class="p-4 flex flex-col items-center justify-center gap-4">
                        <div class="h-24 w-24 shrink-0 relative">
                            <canvas id="employeeLeaveChart"></canvas>
                        </div>
                        <div class="w-full space-y-2">
                            @php
                                $leaveColors = ['#1e40af', '#059669', '#9ca3af', '#92400e'];
                            @endphp
                            @foreach($leaveChartData['labels'] as $index => $label)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full shadow-sm" style="background-color: {{ $leaveColors[$index % 4] }}"></div>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-400">{{ $label }}</span>
                                    </div>
                                    <span class="text-[9px] font-bold text-slate-900 dark:text-white">{{ $leaveChartData['values'][$index] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Team Portal Module --}}
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                    <div class="flex items-center justify-between border-b border-slate-50 px-5 py-3 dark:border-white/5 bg-slate-50 dark:bg-slate-900/50 rounded-t-xl">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">My Team</h3>
                        <span class="text-[8px] font-black uppercase tracking-widest text-slate-400">{{ $employee->department->name ?? 'None' }}</span>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($colleagues->take(4) as $colleague)
                            <div class="flex items-center gap-2 rounded-lg border border-slate-100 bg-white px-3 py-2 dark:border-white/5 dark:bg-slate-900">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded bg-slate-100 text-[10px] font-black text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                    {{ substr($colleague->full_name, 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[10px] font-black text-slate-900 dark:text-white uppercase">{{ $colleague->full_name }}</p>
                                    <p class="truncate text-[8px] font-bold text-slate-400 uppercase tracking-widest">{{ $colleague->job_title }}</p>
                                </div>
                                <a href="mailto:{{ $colleague->email }}" class="rounded p-1 text-slate-400 hover:bg-slate-50 hover:text-cyan-600 transition-colors dark:hover:bg-slate-800">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </a>
                            </div>
                        @empty
                            <p class="py-4 text-center text-[9px] font-bold uppercase tracking-widest text-slate-400">You are the only member.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                {{-- Upcoming Holidays Module --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white mb-3">Upcoming Holidays</h3>
                    <div class="flex gap-3 overflow-x-auto pb-1 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                        @forelse ($upcomingHolidays as $holiday)
                            <div class="flex min-w-[160px] shrink-0 items-center gap-2.5 rounded-lg border border-slate-100 bg-slate-50 p-2.5 dark:border-white/5 dark:bg-slate-950/40">
                                <div class="flex h-10 w-10 shrink-0 flex-col items-center justify-center rounded bg-indigo-50/50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400">
                                    <span class="text-[8px] font-black uppercase tracking-widest">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('M') }}</span>
                                    <span class="text-sm font-black leading-none">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('j') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[9px] font-black text-slate-900 dark:text-white uppercase">{{ $holiday->name }}</p>
                                    <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('l') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">No upcoming holidays scheduled.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Leave Trend Module --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/5 dark:bg-slate-900/50 flex flex-col">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white mb-3">Leave Bookings Trend</h3>
                    <div class="relative flex-1 min-h-[120px]">
                        <canvas id="employeeLeaveTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('employeeLeaveChart')?.getContext('2d');
        if (ctx) {
            const chartDataRaw = @js($leaveChartData);
            const chartColors = ['#1e40af', '#059669', '#9ca3af', '#92400e'];
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartDataRaw.labels,
                    datasets: [{
                        data: chartDataRaw.values,
                        backgroundColor: chartColors,
                        borderWidth: 0,
                        cutout: '75%'
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
        }

        // Employee Leave Trend Line Chart
        const trendCtx = document.getElementById('employeeLeaveTrendChart')?.getContext('2d');
        if (trendCtx) {
            const trendData = @js($leaveTrendChartData);
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendData.labels,
                    datasets: [{
                        label: 'Leaves Taken',
                        data: trendData.bookings,
                        borderColor: '#0284c7', // Sky-600
                        backgroundColor: 'rgba(2, 132, 199, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointBackgroundColor: '#0284c7'
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
                            grid: { color: 'rgba(148, 163, 184, 0.05)', drawBorder: false },
                            ticks: { color: '#94a3b8', font: { size: 9, family: 'Inter' }, maxTicksLimit: 5, stepSize: 1 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { size: 9, family: 'Inter' }, maxTicksLimit: 6 }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush


