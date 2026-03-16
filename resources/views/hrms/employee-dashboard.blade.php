@extends('hrms.layouts.app')

@section('title', 'My Dashboard - PeopleFlow HRMS')

@section('content')
<div class="space-y-8">
    {{-- Welcome Hero Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900/50">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-[80px]"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white lg:text-3xl">
                    Welcome back, <span class="text-cyan-600 dark:text-cyan-400">{{ explode(' ', $employee->full_name)[0] }}!</span> 👋
                </h1>
                <p class="mt-1 text-[11px] font-medium text-slate-500">
                    {{ $employee->job_title }} · {{ $employee->department->name ?? 'No Department' }}
                </p>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-5 py-3 border border-slate-100 shadow-sm dark:bg-slate-900/50 dark:border-slate-800">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Today</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ now()->format('l, j M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Profile Summary & Quick Stats --}}
        <div class="space-y-6">
            {{-- Profile Card --}}
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50 dark:hover:bg-slate-900/80">
                <div class="flex flex-col items-center text-center">
                    <div class="relative mb-5">
                        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-100 text-2xl font-bold text-slate-700 shadow-sm transition-transform group-hover:scale-105 dark:bg-slate-800 dark:text-slate-300">
                            {{ substr($employee->full_name, 0, 1) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900 shadow-sm"></div>
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $employee->full_name }}</h2>
                    <div class="mt-3 flex flex-wrap justify-center gap-2">
                        <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold capitalize text-slate-600 dark:bg-slate-800 dark:text-slate-400">{{ str_replace('-', ' ', $employee->employment_type) }}</span>
                        <span class="rounded-md bg-cyan-50 px-2 py-1 text-xs font-semibold capitalize text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-400">{{ mb_strtolower($employee->status) }}</span>
                    </div>
                </div>
                
                <div class="mt-6 space-y-3 border-t border-slate-100 pt-6 dark:border-slate-800">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Manager</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $employee->manager ? $employee->manager->full_name : 'None' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Joined</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $employee->joined_on->format('M j, Y') }}</span>
                    </div>
                </div>
                
                <a href="{{ route('employees.show', $employee->id) }}" class="mt-5 flex items-center justify-center gap-2 rounded-lg bg-slate-900 border border-white/10 px-4 py-2 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-[0.98] dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    View Profile
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            {{-- Policy Quick Access --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Key Policies</h3>
                <div class="mt-4 space-y-3">
                    @foreach([
                        ['Notice Period', $noticePolicy->notice_days ?? '30', 'Days'],
                        ['Work Mode', 'Hybrid', ''],
                        ['Working Hours', 'Flexible', ''],
                    ] as [$name, $val, $tag])
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3.5 dark:bg-slate-950/40">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">{{ $name }}</p>
                            <p class="mt-0.5 text-sm font-bold text-slate-900 dark:text-white">{{ $val }} <span class="text-xs font-medium text-slate-400">{{ $tag }}</span></p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Main Dashboard Modules --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Attendance Tracking --}}
            <div x-data="attendanceCard({ punchInUrl: '{{ route('attendance.punch-in') }}', pauseUrl: '{{ route('attendance.pause') }}', resumeUrl: '{{ route('attendance.resume') }}', punchOutUrl: '{{ route('attendance.punch-out') }}', status: '{{ $todayAttendance?->status }}' })" 
                 class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                
                <div class="relative flex flex-col items-center justify-between gap-6 lg:flex-row">
                    <div class="text-center lg:text-left">
                        <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Attendance status</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">Track your working hours</p>
                    </div>

                    <div x-show="flash.show" x-transition class="fixed right-6 top-6 z-50 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-xl dark:bg-white dark:text-slate-900" style="display: none;">
                        <span x-text="flash.message"></span>
                    </div>

                    <div class="flex flex-col items-center gap-1 lg:items-end">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Logged Time</span>
                        <div id="attendance-total-timer" class="text-3xl font-black tabular-nums tracking-tight text-slate-900 dark:text-white"
                             data-base-seconds="{{ $todayAttendance?->getCompletedWorkSeconds() ?? 0 }}"
                             data-status="{{ $todayAttendance?->status ?? 'none' }}"
                             data-last-start-ms="{{ $todayAttendance && $todayAttendance->status === 'clocked_in' ? \Carbon\Carbon::parse(collect($todayAttendance->intervals)->where('type', 'work')->where('end', null)->last()['start'] ?? now())->getPreciseTimestamp(3) : 0 }}"
                             data-server-now-ms="{{ now()->getPreciseTimestamp(3) }}">
                             00:00:00
                        </div>
                    </div>
                </div>

                <div class="relative mt-8 flex flex-wrap justify-center gap-3 lg:justify-start border-t border-slate-100 pt-6 dark:border-slate-800">
                    @if(!$todayAttendance)
                        <button @click="act(punchInUrl, {}, 'Clocked in successfully.')" :disabled="loading" 
                                class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span x-text="loading ? 'Processing...' : 'Clock In'"></span>
                        </button>
                    @elseif($todayAttendance->status !== 'completed')
                        <div class="flex flex-wrap gap-2.5">
                            @if($todayAttendance->status === 'clocked_in')
                                <button @click="act(pauseUrl, { type: 'lunch' }, 'Lunch break started.')" :disabled="loading" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-[11px] font-black uppercase tracking-widest text-slate-600 transition-all hover:border-cyan-500 hover:text-cyan-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">Lunch</button>
                                <button @click="act(pauseUrl, { type: 'break' }, 'Break started.')" :disabled="loading" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-[11px] font-black uppercase tracking-widest text-slate-600 transition-all hover:border-cyan-500 hover:text-cyan-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">Break</button>
                            @else
                                <button @click="act(resumeUrl, {}, 'Resumed successfully.')" :disabled="loading" class="rounded-lg bg-emerald-500 px-5 py-2 text-[11px] font-black uppercase tracking-widest text-white shadow-sm transition-all hover:bg-emerald-600">Resume</button>
                            @endif
                            <button @click="confirm('Are you sure you want to clock out for the day?') && act(punchOutUrl, {}, 'Clocked out successfully.')" :disabled="loading" 
                                class="inline-flex items-center gap-2 rounded-lg bg-slate-900 border border-white/10 px-5 py-2 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-rose-500/10 transition-all hover:bg-rose-700 active:scale-95 disabled:opacity-50 dark:bg-white/5 dark:hover:bg-rose-500/20 dark:hover:text-rose-400">
                                <svg x-show="loading" class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                <span x-text="loading ? 'Processing' : 'Clock Out'"></span>
                            </button>
                        </div>
                    @else
                        <div class="flex items-center gap-3 rounded-xl bg-emerald-50 px-5 py-3 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 w-fit">
                            <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-sm font-bold text-emerald-800 dark:text-emerald-300">Clocked out for today</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Secondary Modules Grid --}}
            <div class="grid gap-6 md:grid-cols-2">
                {{-- Recent Leaves Module --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 rounded-t-2xl">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Leave History</h3>
                        <a href="{{ route('leaves.my') }}" class="group flex items-center gap-1 text-xs font-semibold text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300">
                            View All <svg class="h-3 w-3 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>
                    <div class="p-6 space-y-3">
                        @forelse ($myLeaves->take(3) as $leave)
                            <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white capitalize">{{ $leave->leave_type }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $leave->start_date->format('M j') }} · {{ $leave->days }} {{ Str::plural('Day', $leave->days) }}</p>
                                </div>
                                <span class="rounded-md px-2 py-1 text-xs font-semibold capitalize
                                      {{ $leave->status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : '' }}
                                      {{ $leave->status === 'approved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : '' }}
                                      {{ $leave->status === 'rejected' ? 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400' : '' }}
                                      ">{{ $leave->status }}</span>
                            </div>
                        @empty
                            <p class="py-6 text-center text-sm text-slate-500">No leave requests found.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Team Portal Module --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 rounded-t-2xl">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">My Team</h3>
                        <span class="text-xs font-semibold text-slate-500">{{ $employee->department->name ?? 'None' }}</span>
                    </div>
                    <div class="p-6 space-y-3">
                        @forelse ($colleagues->take(4) as $colleague)
                            <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white px-4 py-2 dark:border-slate-800 dark:bg-slate-900">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                    {{ substr($colleague->full_name, 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $colleague->full_name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $colleague->job_title }}</p>
                                </div>
                                <a href="mailto:{{ $colleague->email }}" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-50 hover:text-cyan-600 transition-colors dark:hover:bg-slate-800">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </a>
                            </div>
                        @empty
                            <p class="py-6 text-center text-sm text-slate-500">You are the only member.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Upcoming Holidays Module --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Upcoming Holidays</h3>
                <div class="flex gap-4 overflow-x-auto pb-2 hide-scrollbar">
                    @forelse ($upcomingHolidays as $holiday)
                        <div class="flex min-w-[200px] shrink-0 items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-900/50">
                            <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400">
                                <span class="text-[10px] font-semibold uppercase tracking-wider">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('M') }}</span>
                                <span class="text-lg font-bold leading-none">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('j') }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $holiday->name }}</p>
                                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('l') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No upcoming holidays scheduled.</p>
                    @endforelse
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timerElement = document.getElementById('attendance-total-timer');
        
        if (timerElement) {
            const status = timerElement.getAttribute('data-status');
            const baseSeconds = parseInt(timerElement.getAttribute('data-base-seconds') || 0);
            const lastStartMs = parseInt(timerElement.getAttribute('data-last-start-ms') || 0);
            const serverNowMs = parseInt(timerElement.getAttribute('data-server-now-ms') || 0);
            
            const driftOffset = Date.now() - serverNowMs;

            function updateTimer() {
                let currentSessionSeconds = 0;
                
                if (status === 'clocked_in' && lastStartMs > 0) {
                    const adjNowMs = Date.now() - driftOffset;
                    currentSessionSeconds = Math.max(0, Math.floor((adjNowMs - lastStartMs) / 1000));
                }

                const totalSeconds = baseSeconds + currentSessionSeconds;

                const hours = Math.floor(totalSeconds / 3600);
                const mins = Math.floor((totalSeconds % 3600) / 60);
                const secs = totalSeconds % 60;

                const pad = (num) => String(num).padStart(2, '0');
                timerElement.innerText = `${pad(hours)}:${pad(mins)}:${pad(secs)}`;
            }

            updateTimer();
            if (status === 'clocked_in') {
                setInterval(updateTimer, 1000);
            }
        }
    });
</script>
@endpush
