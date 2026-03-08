@extends('hrms.layouts.app')

@section('title', 'My Dashboard - PeopleFlow HRMS')

@section('content')
<div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Welcome back, {{ explode(' ', $employee->full_name)[0] }}! 👋</h1>
        <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $employee->job_title }} · {{ $employee->department->name ?? 'No Department' }}</p>
    </div>
    <span class="rounded-full border border-cyan-300/50 bg-cyan-100 px-4 py-2 text-sm text-cyan-700 transition-colors duration-300 dark:border-cyan-400/30 dark:bg-cyan-500/10 dark:text-cyan-300">
        {{ now()->format('l, d F Y') }}
    </span>
</div>

<div class="grid gap-6 lg:grid-cols-3 mt-8">
    
    <!-- Profile Summary Card -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 flex flex-col">
        <div class="flex items-center gap-4 border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-500 shadow-lg text-xl font-bold text-white">
                {{ substr($employee->full_name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $employee->full_name }}</h2>
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-400/20">
                        {{ $employee->employment_type }}
                    </span>
                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-400/20 capitalize">
                        {{ $employee->status }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="space-y-3 flex-1 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400 whitespace-nowrap">Email</span>
                <span class="text-slate-900 dark:text-white font-medium text-right break-all">{{ $employee->email }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400 whitespace-nowrap">Phone</span>
                <span class="text-slate-900 dark:text-white font-medium text-right break-all">{{ $employee->phone }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400 whitespace-nowrap">Manager</span>
                <span class="text-slate-900 dark:text-white font-medium text-right border-b border-slate-300 dark:border-slate-600 border-dashed pb-0.5">{{ $employee->manager ? $employee->manager->full_name : 'None Assigned' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400 whitespace-nowrap">Joined On</span>
                <span class="text-slate-900 dark:text-white font-medium text-right">{{ $employee->joined_on->format('d M Y') }}</span>
            </div>
        </div>
        
        <a href="{{ route('employees.show', $employee->id) }}" class="mt-4 block text-center w-full rounded-lg bg-slate-100 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
            View Full Profile
        </a>
    </div>

    <div class="lg:col-span-2 flex flex-col gap-6">
        <!-- Quick Actions & Stats -->
        
        <!-- Attendance Punch In/Out Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 flex flex-col items-center justify-center text-center shadow-lg bg-gradient-to-br from-indigo-50 to-white dark:from-slate-800 dark:to-slate-900">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Today's Attendance</h3>
            
            @if(!$todayAttendance)
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">You have not clocked in yet today. Ready to start?</p>
                <form action="{{ route('attendance.punch-in') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-3 text-lg font-bold text-white shadow-lg transition-all hover:scale-105 hover:from-emerald-400 hover:to-emerald-500 hover:shadow-emerald-500/25">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Start Shift
                    </button>
                </form>
            @elseif($todayAttendance->status !== 'completed')
                <div class="flex flex-wrap items-center justify-center gap-4 mb-6">
                    <div class="flex flex-col items-center">
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Status</span>
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $todayAttendance->status === 'clocked_in' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }} capitalize">
                            {{ str_replace('_', ' ', $todayAttendance->status) }}
                        </span>
                    </div>
                    <div class="w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
                    <div class="flex flex-col items-center">
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Total Worked</span>
                        @php
                            $completedSeconds = $todayAttendance->getCompletedWorkSeconds();
                            $status = $todayAttendance->status;
                            $lastStartMs = 0;
                            if ($status === 'clocked_in') {
                                $lastWork = collect($todayAttendance->intervals)->where('type', 'work')->where('end', null)->last();
                                if ($lastWork) {
                                    $lastStartMs = \Carbon\Carbon::parse($lastWork['start'])->getPreciseTimestamp(3); // milliseconds
                                }
                            }
                        @endphp
                        <span id="attendance-total-timer" class="font-mono font-bold text-slate-700 dark:text-slate-300" 
                              data-base-seconds="{{ $completedSeconds }}"
                              data-status="{{ $status }}"
                              data-last-start-ms="{{ $lastStartMs }}"
                              data-server-now-ms="{{ now()->getPreciseTimestamp(3) }}">
                            @php
                                $total = $todayAttendance->getTotalWorkedSeconds();
                                $h = floor($total / 3600);
                                $m = floor(($total % 3600) / 60);
                                $s = $total % 60;
                            @endphp
                            {{ sprintf('%02d:%02d:%02d', $h, $m, $s) }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 justify-center">
                    @if($todayAttendance->status === 'clocked_in')
                        <form action="{{ route('attendance.pause') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="lunch">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-100 px-4 py-2 text-sm font-bold text-orange-600 transition-all hover:bg-orange-200">
                                🍕 Lunch Break
                            </button>
                        </form>
                        <form action="{{ route('attendance.pause') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="break">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-100 px-4 py-2 text-sm font-bold text-blue-600 transition-all hover:bg-blue-200">
                                ☕ Short Break
                            </button>
                        </form>
                    @else
                        <form action="{{ route('attendance.resume') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-600 transition-all hover:bg-emerald-200">
                                ▶️ Resume Work
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('attendance.punch-out') }}" method="POST" onsubmit="return confirm('Are you sure you want to mark the shift as completed? No more work can be logged for today.')">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-rose-500 px-4 py-2 text-sm font-bold text-white transition-all hover:bg-rose-600 shadow-sm">
                            🏁 Complete Shift
                        </button>
                    </form>
                </div>
            @else
                <p class="text-emerald-600 dark:text-emerald-400 font-bold text-lg mb-2">Shift Completed! 🎉</p>
                <div class="flex gap-4 mb-4 justify-center">
                    <div class="flex flex-col">
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Started</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($todayAttendance->clock_in_at)->format('H:i A') }}</span>
                    </div>
                    <div class="w-px bg-slate-200 dark:bg-slate-700"></div>
                    <div class="flex flex-col">
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Ended</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($todayAttendance->clock_out_at)->format('H:i A') }}</span>
                    </div>
                </div>
                <div class="inline-flex px-4 py-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-semibold text-sm">
                    @php $s = $todayAttendance->getTotalWorkedSeconds(); @endphp
                    Total Effort: {{ floor($s / 3600) }}h {{ floor(($s % 3600) / 60) }}m {{ $s % 60 }}s
                </div>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('leaves.create') }}" class="group flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-4 transition-all hover:border-cyan-300 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900 dark:hover:border-cyan-700">
                <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 group-hover:scale-110 shadow-sm transition-transform">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 text-center">Request Leave</span>
            </a>
            
            <a href="{{ route('leaves.my') }}" class="group flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-4 transition-all hover:border-cyan-300 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900 dark:hover:border-cyan-700">
                <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 group-hover:scale-110 shadow-sm transition-transform">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 002-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 text-center">My Leave History</span>
            </a>

            <!-- Can show leave balances here if implemented -->
            <div class="group flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-4 transition-all dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                    {{ $myLeaves->where('status', 'approved')->count() }}
                </div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 text-center uppercase tracking-wide">Approved Leaves</span>
            </div>

            <div class="group flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-4 transition-all dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-1 text-2xl font-bold text-yellow-600 dark:text-yellow-400 group-hover:scale-110 transition-transform">
                    {{ $myLeaves->where('status', 'pending')->count() }}
                </div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 text-center uppercase tracking-wide">Pending Leaves</span>
            </div>
        </div>

        <!-- Recent Leave Requests Table -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 flex-1">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Recent Leave Requests</h3>
                <a href="{{ route('leaves.my') }}" class="text-sm font-medium text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300">View All</a>
            </div>
            
            <div class="space-y-3">
                @forelse ($myLeaves as $leave)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 transition-colors dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800 shadow-sm">
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white capitalize">{{ str_replace('_', ' ', $leave->leave_type) }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M') }} ({{ $leave->days }} days)</p>
                        </div>
                        
                        @if ($leave->status === 'approved')
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-400/20">Approved</span>
                        @elseif ($leave->status === 'rejected')
                            <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10 dark:bg-red-900/30 dark:text-red-300 dark:ring-red-400/20">Rejected</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20 dark:bg-yellow-900/30 dark:text-yellow-300 dark:ring-yellow-400/20">Pending</span>
                        @endif
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-6 text-center">
                        <svg class="h-10 w-10 text-slate-300 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">You haven't requested any leaves yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

                <!-- Policy Hub (Zoho/BambooHR Style) -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Company Guidelines
                </h2>
                <a href="{{ route('policies.viewer') }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">View All Policies</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Notice Period Policy Card -->
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white/80 p-5 backdrop-blur-md transition-all hover:shadow-xl dark:border-slate-700 dark:bg-slate-800/80">
                    <div class="flex items-start justify-between mb-4">
                        <div class="rounded-xl bg-amber-100 p-2 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Exit Policy</span>
                    </div>
                    <h3 class="mb-1 text-base font-bold text-slate-800 dark:text-white">Notice Period</h3>
                    @if(isset($noticePolicy) && $noticePolicy)
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">{{ $noticePolicy->description }}</p>
                        <div class="flex items-center gap-2 mt-auto">
                            <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $noticePolicy->notice_days }}</span>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter leading-tight">Days<br>Required</span>
                        </div>
                    @else
                        <p class="text-sm italic text-slate-400 mb-4">Standard notice period applies as per your contract.</p>
                        <span class="text-xs font-semibold text-slate-500">Contact HR if resigning</span>
                    @endif
                </div>

                <!-- WFH Policy Card -->
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white/80 p-5 backdrop-blur-md transition-all hover:shadow-xl dark:border-slate-700 dark:bg-slate-800/80">
                    <div class="flex items-start justify-between mb-4">
                        <div class="rounded-xl bg-blue-100 p-2 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Work Mode</span>
                    </div>
                    <h3 class="mb-1 text-base font-bold text-slate-800 dark:text-white">Remote Work</h3>
                    @if(isset($wfhPolicy) && $wfhPolicy)
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">{{ $wfhPolicy->description }}</p>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                                {{ $wfhPolicy->is_active ? 'Hybrid Allowed' : 'Onsite Only' }}
                            </span>
                        </div>
                    @else
                        <p class="text-sm italic text-slate-400 mb-4">Refer to your department's specific WFH guidelines.</p>
                        <span class="text-xs font-semibold text-slate-500 capitalize">Shared office model</span>
                    @endif
                </div>

                <!-- Attendance Policy Card -->
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white/80 p-5 backdrop-blur-md transition-all hover:shadow-xl dark:border-slate-700 dark:bg-slate-800/80">
                    <div class="flex items-start justify-between mb-4">
                        <div class="rounded-xl bg-purple-100 p-2 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Timekeeping</span>
                    </div>
                    <h3 class="mb-1 text-base font-bold text-slate-800 dark:text-white">Attendance Rules</h3>
                    @if(isset($attendancePolicy) && $attendancePolicy)
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">{{ $attendancePolicy->description }}</p>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/40 px-2 py-0.5 rounded uppercase tracking-wider">Mandatory Punch</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active</span>
                        </div>
                    @else
                        <p class="text-sm italic text-slate-400 mb-4">Standard 9-to-6 rules with strict punctuality tracking.</p>
                        <span class="text-xs font-semibold text-slate-500">Live system sync</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-2 mt-6">
    <!-- Colleague/Team Directory -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">My Team ({{ $employee->department->name ?? 'None' }})</h3>
        </div>
        
        <div class="space-y-3">
            @forelse ($colleagues as $colleague)
                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 transition-colors dark:bg-slate-950/50">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-200 text-sm font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            {{ substr($colleague->full_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $colleague->full_name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $colleague->job_title }}</p>
                        </div>
                    </div>
                    <a href="mailto:{{ $colleague->email }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </a>
                </div>
            @empty
                <p class="py-4 text-center text-sm text-slate-500 dark:text-slate-400">You are the first member of this department.</p>
            @endforelse
        </div>
        <a href="{{ route('employees.index') }}" class="mt-4 block text-center w-full rounded-lg bg-slate-100 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
            View Company Directory
        </a>
    </div>

    <!-- Upcoming Holidays -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Upcoming Holidays</h3>
        </div>
        
        <div class="space-y-3">
            @forelse ($upcomingHolidays as $holiday)
                <div class="flex items-center gap-4 rounded-xl bg-slate-50 px-4 py-3 transition-colors dark:bg-slate-950/50">
                    <div class="flex h-12 w-12 flex-col items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 shrink-0">
                        <span class="text-xs font-semibold leading-tight uppercase">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('M') }}</span>
                        <span class="text-lg font-bold leading-none">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('d') }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $holiday->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('l, jS F') }}</p>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-6 text-center">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No upcoming holidays scheduled.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
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
            
            // Calculate clock drift between server and client
            // If positive, client clock is ahead. If negative, server clock is ahead.
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

            // Run once immediately, then every second
            updateTimer();
            if (status === 'clocked_in') {
                setInterval(updateTimer, 1000);
            }
        }
    });
</script>
@endpush
