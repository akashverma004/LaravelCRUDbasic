@extends('hrms.layouts.app')

@section('title', 'Admin Dashboard - PeopleFlow HRMS')

@section('content')
<div x-data="adminDashboardActions({
        departmentStoreUrl: '{{ route('departments.store') }}',
        assignManagerUrl: '{{ route('employees.assign-manager') }}',
        defaultEffectiveDate: '{{ now()->toDateString() }}'
    })"
    class="space-y-8">

    {{-- Dashboard Hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-slate-900 px-8 py-10 shadow-lg border border-white/5 dark:bg-slate-950/40">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-[80px]"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white lg:text-4xl">
                    Executive <span class="text-cyan-400">Dashboard</span>
                </h1>
                <p class="mt-2 max-w-xl text-sm font-medium text-slate-400">
                    Overview of your workforce and operations.
                </p>
            </div>
            <div class="flex items-center gap-4 rounded-xl bg-slate-950/40 px-6 py-4 backdrop-blur-md border border-white/5 shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-400/10 text-cyan-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Today</p>
                    <p class="text-sm font-bold text-white">{{ now()->format('l, d F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Metrics Grid --}}
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['Employees', $employeeCount, 'Total active employees', 'indigo', route('employees.index'), 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
            ['Departments', $departmentCount, 'Total departments', 'cyan', route('departments.index'), 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6.75h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75H21m-3.75 3.75H21'],
            ['Pending Leaves', $leavePending, 'Awaiting approval', 'rose', route('leaves.index'), 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Present Today', $attendanceToday, 'Employees at work', 'emerald', route('employees.index'), 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z']
        ] as [$title, $val, $desc, $color, $link, $icon])
        <a href="{{ $link }}" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md hover:-translate-y-1 dark:border-slate-800 dark:bg-slate-900/50">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-4">{{ $title }}</h3>
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white" x-text="'{{ $val }}'"></p>
                    <p class="mt-1 text-xs text-slate-500">{{ $desc }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-{{ $color }}-50 text-{{ $color }}-500 transition-colors group-hover:bg-{{ $color }}-100 dark:bg-{{ $color }}-500/10 dark:group-hover:bg-{{ $color }}-500/20">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Employees by Department</h2>
                        <p class="text-xs text-slate-500 mt-1">Headcount across different departments</p>
                    </div>
                </div>
                <div class="h-64 relative">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                {{-- Recent Employees --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 px-6 py-4 bg-slate-50 dark:bg-slate-950/30">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Recent Employees</h3>
                        <a href="{{ route('employees.index') }}" class="text-xs font-medium text-cyan-500 hover:text-cyan-600 transition-colors">
                            View All &rarr;
                        </a>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($employees->take(5) as $employee)
                            <div class="flex items-center justify-between rounded-xl px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300">
                                        {{ substr($employee->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $employee->full_name }}</p>
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
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 px-6 py-4 bg-slate-50 dark:bg-slate-950/30">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Pending Leaves</h3>
                        <a href="{{ route('leaves.index') }}" class="text-xs font-medium text-rose-500 hover:text-rose-600 transition-colors">
                            View All &rarr;
                        </a>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($leaveRequests->take(5) as $leave)
                            <div class="flex items-center justify-between rounded-xl px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-rose-50 dark:bg-rose-500/10 text-xs font-bold text-rose-600 dark:text-rose-400">
                                        {{ substr($leave->employee->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $leave->employee->full_name }}</p>
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
        <div class="space-y-6">
            {{-- Quick Add Department --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-6">Quick Add Department</h3>
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">Department Name</label>
                        <input x-model="departmentForm.name" placeholder="Engineering" class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">Department Code</label>
                        <input x-model="departmentForm.code" placeholder="ENG-01" class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 uppercase">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">Manager</label>
                        <select x-model="departmentForm.lead_employee_id" class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 dark:text-white focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                            <option value="" class="dark:bg-slate-900">Select Manager...</option>
                            @foreach($allEmployees as $employee)
                                <option value="{{ $employee->id }}" class="dark:bg-slate-900">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button @click="submitDepartment()" :disabled="departmentSaving" class="mt-2 w-full rounded-xl bg-cyan-500 px-4 py-3 text-sm font-bold text-slate-950 shadow-sm transition-all hover:bg-cyan-400 active:scale-[0.98]">
                        <span x-text="departmentSaving ? 'Saving...' : 'Add Department'"></span>
                    </button>
                </div>
            </div>

            {{-- Assign Manager --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-6">Assign Manager</h3>
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">Employee</label>
                        <select x-model="managerForm.employee_id" class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 dark:text-white focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                            <option value="" class="dark:bg-slate-900">Select Employee...</option>
                            @foreach ($allEmployees as $emp)
                                <option value="{{ $emp->id }}" class="dark:bg-slate-900">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">Manager</label>
                        <select x-model="managerForm.manager_id" class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 dark:text-white focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                            <option value="" class="dark:bg-slate-900">Select Manager...</option>
                            @foreach ($allEmployees as $emp)
                                <option value="{{ $emp->id }}" class="dark:bg-slate-900">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button @click="submitManagerAssignment()" :disabled="managerSaving" class="mt-2 w-full rounded-xl bg-indigo-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-indigo-400 active:scale-[0.98]">
                        <span x-text="managerSaving ? 'Saving...' : 'Assign Manager'"></span>
                    </button>
                    <p class="text-[10px] text-slate-500 text-center mt-2">Ensure correct managers are assigned.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('departmentChart').getContext('2d');
        const data = @json($departmentChartData);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Employees',
                    data: data.values,
                    backgroundColor: 'rgba(34, 211, 238, 0.2)',
                    borderColor: 'rgba(34, 211, 238, 1)',
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
    });
</script>
@endsection
