@extends('hrms.layouts.app')

@section('title', 'Dashboard - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
    <div>
    </div>
    <span class="rounded-full border border-cyan-400/30 bg-cyan-500/10 px-4 py-2 text-sm">{{ now()->format('d M Y') }}</span>
</div>

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-200">{{ session('status') }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('employees.index') }}" class="block rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-lg shadow-cyan-900/10 transition hover:border-cyan-400/50 hover:bg-slate-800">
                <p class="text-sm text-slate-400">Total Employees</p>
                <p class="mt-2 text-3xl font-semibold text-cyan-300">{{ $employeeCount }}</p>
            </a>
            <a href="{{ route('departments.index') }}" class="block rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-lg shadow-cyan-900/10 transition hover:border-cyan-400/50 hover:bg-slate-800">
                <p class="text-sm text-slate-400">Departments</p>
                <p class="mt-2 text-3xl font-semibold text-cyan-300">{{ $departmentCount }}</p>
            </a>
            <a href="{{ route('leaves.pending') }}" class="block rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-lg shadow-cyan-900/10 transition hover:border-cyan-400/50 hover:bg-slate-800">
                <p class="text-sm text-slate-400">Pending Leave</p>
                <p class="mt-2 text-3xl font-semibold text-cyan-300">{{ $leavePending }}</p>
            </a>
            <a href="{{ route('employees.index') }}" class="block rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-lg shadow-cyan-900/10 transition hover:border-cyan-400/50 hover:bg-slate-800">
                <p class="text-sm text-slate-400">Attendance Today</p>
                <p class="mt-2 text-3xl font-semibold text-cyan-300">{{ $attendanceToday }}</p>
            </a>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Workforce Distribution</h2>
                </div>
                <canvas id="departmentChart" class="max-h-72"></canvas>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h2 class="mb-4 text-lg font-semibold">Quick Add Department</h2>
                <form method="POST" action="{{ route('hrms.departments.store') }}" class="space-y-3">
                    @csrf
                    <input name="name" placeholder="Department name" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                    <input name="code" placeholder="Code (e.g. ENG)" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                    <input name="lead_name" placeholder="Department lead" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                    <button class="w-full rounded-lg bg-cyan-500 py-2 font-semibold text-slate-900 transition hover:bg-cyan-400">Add Department</button>
                </form>
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-slate-800 bg-slate-900 p-6" x-data="{tab: 'employees'}">
            <div class="mb-4 flex gap-3">
                <button @click="tab='employees'" :class="tab === 'employees' ? 'bg-cyan-500 text-slate-900' : 'bg-slate-800'" class="rounded-lg px-4 py-2 text-sm font-medium">Add Employee</button>
                <button @click="tab='leave'" :class="tab === 'leave' ? 'bg-cyan-500 text-slate-900' : 'bg-slate-800'" class="rounded-lg px-4 py-2 text-sm font-medium">Add Leave Request</button>
            </div>

            <form x-show="tab === 'employees'" x-cloak method="POST" action="{{ route('hrms.employees.store') }}" class="grid gap-3 md:grid-cols-2">
                @csrf
                <input name="full_name" placeholder="Full name" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                <input type="email" name="email" placeholder="Email" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                <input name="phone" placeholder="Phone" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                <input name="job_title" placeholder="Job title" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                <select name="department_id" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                    <option value="">Department</option>
                    @foreach ($departmentBreakdown as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
                <select name="employment_type" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                    <option value="full-time">Full-time</option>
                    <option value="part-time">Part-time</option>
                    <option value="contract">Contract</option>
                    <option value="intern">Intern</option>
                </select>
                <input type="number" step="0.01" min="0" name="salary" placeholder="Salary" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
               <input
                    type="date"
                    name="joined_on"
                    class="rounded-lg border border-slate-600 bg-slate-900 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-auto"
                    required
                >
                <select name="status" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                    <option value="active">Active</option>
                    <option value="on-leave">On Leave</option>
                    <option value="resigned">Resigned</option>
                </select>
                <button class="rounded-lg bg-cyan-500 py-2 font-semibold text-slate-900 transition hover:bg-cyan-400">Create Employee</button>
            </form>

            <form x-show="tab === 'leave'" x-cloak method="POST" action="{{ route('hrms.leave.store') }}" class="grid gap-3 md:grid-cols-2">
                @csrf
                <select name="employee_id" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                    <option value="">Employee</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                    @endforeach
                </select>
                <select name="leave_type" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                    <option value="annual">Annual</option>
                    <option value="sick">Sick</option>
                    <option value="casual">Casual</option>
                    <option value="unpaid">Unpaid</option>
                </select>
                <input type="date" name="start_date" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                <input type="date" name="end_date" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                <select name="status" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <input name="reason" placeholder="Reason" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                <button class="rounded-lg bg-cyan-500 py-2 font-semibold text-slate-900 transition hover:bg-cyan-400">Submit Leave</button>
            </form>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="mb-3 text-lg font-semibold">Recent Employees</h3>
                <div class="space-y-3">
                    @forelse ($employees->take(4) as $employee)
                        <div class="flex items-center justify-between rounded-xl bg-slate-950 px-4 py-3">
                            <div>
                                <p class="font-medium">{{ $employee->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $employee->job_title }} · {{ $employee->department->name }}</p>
                            </div>
                            <span class="rounded-full bg-cyan-500/10 px-3 py-1 text-xs text-cyan-300">{{ ucfirst($employee->status) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No employees yet.</p>
                    @endforelse
                </div>
                @if ($employees->count() > 4)
                    <a href="{{ route('employees.index') }}" class="mt-4 inline-flex items-center gap-1 text-sm text-cyan-400 hover:text-cyan-300">
                        View More →
                    </a>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="mb-3 text-lg font-semibold">Latest Leave Requests</h3>
                <div class="space-y-3">
                    @forelse ($leaveRequests->take(4) as $leave)
                        <div class="rounded-xl bg-slate-950 px-4 py-3">
                            <p class="font-medium">{{ $leave->employee->full_name }} ({{ ucfirst($leave->leave_type) }})</p>
                            <p class="text-xs text-slate-400">{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M') }} · {{ ucfirst($leave->status) }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No leave requests yet.</p>
                    @endforelse
                </div>
                @if ($leaveRequests->count() > 4)
                    <a href="{{ route('leaves.index') }}" class="mt-4 inline-flex items-center gap-1 text-sm text-cyan-400 hover:text-cyan-300">
                        View More →
                    </a>
                @endif
            </div>
        </div>

        <!-- Leave Statistics -->
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="mb-4 text-lg font-semibold">Leave Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-3 w-3 rounded-full bg-emerald-500"></span>
                            <p class="text-sm text-slate-400">Approved</p>
                        </div>
                        <p class="font-semibold text-emerald-300">{{ $leaveStats['approved'] }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-3 w-3 rounded-full bg-yellow-500"></span>
                            <p class="text-sm text-slate-400">Pending</p>
                        </div>
                        <p class="font-semibold text-yellow-300">{{ $leaveStats['pending'] }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-3 w-3 rounded-full bg-red-500"></span>
                            <p class="text-sm text-slate-400">Rejected</p>
                        </div>
                        <p class="font-semibold text-red-300">{{ $leaveStats['rejected'] }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="mb-4 text-lg font-semibold">Employment Type</h3>
                <div class="space-y-3">
                    @forelse ($employmentBreakdown as $type)
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-slate-400 capitalize">{{ $type->employment_type }}</p>
                            <span class="rounded-full bg-cyan-500/20 px-3 py-1 text-sm font-semibold text-cyan-300">{{ $type->count }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No data available</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="mb-4 text-lg font-semibold">Employee Status</h3>
                <div class="space-y-3">
                    @forelse ($employeesByStatus as $status)
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-slate-400 capitalize">{{ $status->status }}</p>
                            <span class="rounded-full bg-cyan-500/20 px-3 py-1 text-sm font-semibold text-cyan-300">{{ $status->count }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Team Leads Component -->
        <div class="mt-8">
            <x-hrms.team-leads :employees="$teamHeads" />
        </div>

        <!-- Managers Management Section -->
        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="mb-4 text-lg font-semibold">Team Managers</h3>
                <div class="space-y-3">
                    @forelse ($teamHeads as $manager)
                        <div class="flex items-center justify-between rounded-lg bg-slate-950 p-3">
                            <div class="flex-1">
                                <p class="font-medium text-white">{{ $manager->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $manager->job_title }}</p>
                            </div>
                            <span class="ml-3 rounded-full bg-blue-500/20 px-3 py-1 text-sm font-semibold text-blue-300">
                                {{ $manager->subordinates_count ?? count($manager->subordinates) }} reports
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No managers assigned yet</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="mb-4 text-lg font-semibold">Assign Manager</h3>
                <form action="{{ route('employees.assign-manager') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-sm text-slate-400">Select Employee</label>
                        <select name="employee_id" class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                            <option value="">-- Choose Employee --</option>
                            @forelse ($allEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->department->name }})</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-400">Select Manager</label>
                        <select name="manager_id" class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                            <option value="">-- No Manager --</option>
                            @forelse ($allEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-400">Effective Date</label>
                        <input type="date" name="effective_date" class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" value="{{ now()->toDateString() }}" required>
                    </div>
                    <button class="w-full rounded-lg bg-cyan-500 py-2 font-semibold text-slate-900 transition hover:bg-cyan-400">Assign Manager</button>
                </form>
            </div>
        </div>

        <!-- Top Departments -->
        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="mb-4 text-lg font-semibold">Top Departments by Headcount</h3>
                <div class="space-y-3">
                    @forelse ($topDepartments->take(4) as $dept)
                        <div class="flex items-center justify-between rounded-lg bg-slate-950 p-3">
                            <div class="flex-1">
                                <p class="font-medium text-white">{{ $dept->name }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <div class="h-2 flex-1 rounded-full bg-slate-700">
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r from-cyan-500 to-blue-500"
                                            style="width: {{ ($dept->employees_count / $employeeCount) * 100 }}%"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                            <span class="ml-3 min-w-fit rounded-full bg-cyan-500/20 px-3 py-1 text-sm font-semibold text-cyan-300">
                                {{ $dept->employees_count }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No departments found</p>
                    @endforelse
                </div>
                @if ($topDepartments->count() > 4)
                    <a href="{{ route('departments.index') }}" class="mt-4 inline-flex items-center gap-1 text-sm text-cyan-400 hover:text-cyan-300">
                        View More →
                    </a>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="mb-4 text-lg font-semibold">New Joinees</h3>
                <div class="space-y-3">
                    @forelse ($newJoinees->take(4) as $employee)
                        <div class="flex items-center justify-between rounded-lg bg-slate-950 p-3">
                            <div>
                                <p class="font-medium text-white">{{ $employee->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $employee->job_title }} · {{ $employee->joined_on->format('d M Y') }}</p>
                            </div>
                            <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs text-emerald-300">New</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No new joinees</p>
                    @endforelse
                </div>
                @if ($newJoinees->count() > 4)
                    <a href="{{ route('employees.index') }}" class="mt-4 inline-flex items-center gap-1 text-sm text-cyan-400 hover:text-cyan-300">
                        View More →
                    </a>
                @endif
            </div>
        </div>
    </div>

        <div class="mt-8 rounded-2xl border border-slate-800 bg-slate-900 p-6">
            <h3 class="mb-4 text-lg font-semibold">Department-wise Workforce</h3>
            <canvas id="departmentChart2" class="max-h-80"></canvas>
        </div>
    </div>

    <script>
        // Department Workforce Chart
        const labels = @json($departmentBreakdown->pluck('name'));
        const data = @json($departmentBreakdown->pluck('employees_count'));

        new Chart(document.getElementById('departmentChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Employees',
                    data,
                    backgroundColor: 'rgba(34, 211, 238, 0.7)',
                    borderRadius: 8,
                }],
            },
            options: {
                responsive: true,
                plugins: {legend: {display: false}},
                scales: {
                    x: {ticks: {color: '#94a3b8'}},
                    y: {ticks: {color: '#94a3b8'}, beginAtZero: true},
                },
            },
        });

        // Leave Status Pie Chart
        const leaveLabels = ['Approved', 'Pending', 'Rejected'];
        const leaveData = [@json($leaveStats['approved']), @json($leaveStats['pending']), @json($leaveStats['rejected'])];

        new Chart(document.getElementById('departmentChart2'), {
            type: 'doughnut',
            data: {
                labels: leaveLabels,
                datasets: [{
                    data: leaveData,
                    backgroundColor: ['rgba(34, 197, 94, 0.7)', 'rgba(234, 179, 8, 0.7)', 'rgba(239, 68, 68, 0.7)'],
                    borderColor: ['#10b981', '#eab308', '#ef4444'],
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {labels: {color: '#cbd5e1'}},
                    tooltip: {callbacks: {label: ctx => ctx.raw}},
                },
            },
        });
    </script>
@endsection
