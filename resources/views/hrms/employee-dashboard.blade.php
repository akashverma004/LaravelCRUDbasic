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
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('leaves.create') }}" class="group flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-4 transition-all hover:border-cyan-300 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900 dark:hover:border-cyan-700">
                <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 group-hover:scale-110 shadow-sm transition-transform">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 text-center">Request Leave</span>
            </a>
            
            <a href="{{ route('leaves.index') }}" class="group flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-4 transition-all hover:border-cyan-300 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900 dark:hover:border-cyan-700">
                <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 group-hover:scale-110 shadow-sm transition-transform">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
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
                <a href="{{ route('leaves.index') }}" class="text-sm font-medium text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300">View All</a>
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
