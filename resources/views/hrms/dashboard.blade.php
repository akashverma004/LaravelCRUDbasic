@extends('hrms.layouts.app')

@section('title', 'Dashboard - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
    <div>
    </div>
    <span class="rounded-full transition-colors duration-300 dark:border-cyan-400/30 dark:bg-cyan-500/10 dark:text-cyan-300 border border-cyan-300/50 bg-cyan-100 text-cyan-700 px-4 py-2 text-sm">{{ now()->format('d M Y') }}</span>
</div>

        <!-- Colorful Dashboard Cards -->
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

    <!-- Total Employees - Blue Card -->
    <a href="{{ route('employees.index') }}"
       class="block rounded-2xl p-5 transition-all duration-300
       border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 shadow-lg shadow-blue-200/50 hover:shadow-xl hover:border-blue-300
       dark:border-blue-800 dark:from-blue-900 dark:to-blue-800 dark:bg-gradient-to-br dark:shadow-blue-900/30 dark:hover:border-blue-500">

        <p class="text-sm font-medium text-blue-700 dark:text-blue-300">
            Total Employees
        </p>

        <p class="mt-2 text-3xl font-semibold text-blue-600 dark:text-blue-400">
            {{ $employeeCount }}
        </p>
    </a>


    <!-- Departments - Purple Card -->
    <a href="{{ route('departments.index') }}"
       class="block rounded-2xl p-5 transition-all duration-300
       border border-purple-200 bg-gradient-to-br from-purple-50 to-purple-100 shadow-lg shadow-purple-200/50 hover:shadow-xl hover:border-purple-300
       dark:border-purple-800 dark:from-purple-900 dark:to-purple-800 dark:bg-gradient-to-br dark:shadow-purple-900/30 dark:hover:border-purple-500">

        <p class="text-sm font-medium text-purple-700 dark:text-purple-300">
            Departments
        </p>

        <p class="mt-2 text-3xl font-semibold text-purple-600 dark:text-purple-400">
            {{ $departmentCount }}
        </p>
    </a>


    <!-- Pending Leave - Orange Card -->
    <a href="{{ route('leaves.pending') }}"
       class="block rounded-2xl p-5 transition-all duration-300
       border border-orange-200 bg-gradient-to-br from-orange-50 to-orange-100 shadow-lg shadow-orange-200/50 hover:shadow-xl hover:border-orange-300
       dark:border-orange-800 dark:from-orange-900 dark:to-orange-800 dark:bg-gradient-to-br dark:shadow-orange-900/30 dark:hover:border-orange-500">

        <p class="text-sm font-medium text-orange-700 dark:text-orange-300">
            Pending Leave
        </p>

        <p class="mt-2 text-3xl font-semibold text-orange-600 dark:text-orange-400">
            {{ $leavePending }}
        </p>
    </a>


    <!-- Attendance Today - Green Card -->
    <a href="{{ route('employees.index') }}"
       class="block rounded-2xl p-5 transition-all duration-300
       border border-green-200 bg-gradient-to-br from-green-50 to-green-100 shadow-lg shadow-green-200/50 hover:shadow-xl hover:border-green-300
       dark:border-green-800 dark:from-green-900 dark:to-green-800 dark:bg-gradient-to-br dark:shadow-green-900/30 dark:hover:border-green-500">

        <p class="text-sm font-medium text-green-700 dark:text-green-300">
            Attendance Today
        </p>

        <p class="mt-2 text-3xl font-semibold text-green-600 dark:text-green-400">
            {{ $attendanceToday }}
        </p>
    </a>

</div>

        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6 lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">Workforce Distribution</h2>
                </div>
                <canvas id="departmentChart" class="max-h-72"></canvas>
            </div>

            <div class="rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6">

                <h2 class="mb-4 transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">
                Quick Add Department
                </h2>

                <form method="POST" action="{{ route('departments.store') }}" class="space-y-3">
                @csrf

                <input name="name" placeholder="Department name"
                class="transition-colors duration-300 w-full rounded-lg
                dark:border-slate-700 dark:bg-slate-950 dark:text-white
                border-slate-300 border bg-white text-slate-900
                px-3 py-2 text-sm" required>

                <input name="code" placeholder="Code (e.g. ENG)"
                class="transition-colors duration-300 w-full rounded-lg
                dark:border-slate-700 dark:bg-slate-950 dark:text-white
                border-slate-300 border bg-white text-slate-900
                px-3 py-2 text-sm" required>

                <select name="lead_id"
                class="transition-colors duration-300 w-full rounded-lg
                dark:border-slate-700 dark:bg-slate-950 dark:text-white
                border-slate-300 border bg-white text-slate-900
                px-3 py-2 text-sm" required>

                <option value="">Select Department Lead</option>

                @foreach($allEmployees as $employee)
                <option value="{{ $employee->id }}">
                {{ $employee->full_name }}
                </option>
                @endforeach

                </select>

                <button class="w-full rounded-lg transition-all duration-300
                dark:bg-cyan-500 dark:text-slate-900 dark:hover:bg-cyan-400
                bg-blue-500 text-white hover:bg-blue-600 py-2 font-semibold">
                Add Department
                </button>

                </form>
                </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6">
                <h3 class="mb-3 transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">Recent Employees</h3>
                <div class="space-y-3">
                    @forelse ($employees->take(4) as $employee)
                        <div class="flex items-center justify-between rounded-xl transition-colors duration-300 dark:bg-slate-950 bg-slate-100 px-4 py-3">
                            <div>
                                <p class="transition-colors duration-300 dark:text-white text-slate-900 font-medium">{{ $employee->full_name }}</p>
                                <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-xs">{{ $employee->job_title }} · {{ $employee->department->name }}</p>
                            </div>
                            <span class="transition-colors duration-300 dark:bg-cyan-500/10 dark:text-cyan-300 rounded-full bg-blue-100 text-blue-600 px-3 py-1 text-xs font-medium">{{ ucfirst($employee->status) }}</span>
                        </div>
                    @empty
                        <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">No employees yet.</p>
                    @endforelse
                </div>
                @if ($employees->count() > 4)
                    <a href="{{ route('employees.index') }}" class="mt-4 inline-flex items-center gap-1 transition-colors duration-300 dark:text-cyan-400 dark:hover:text-cyan-300 text-blue-600 hover:text-blue-700 text-sm">
                        View More →
                    </a>
                @endif
            </div>

            <div class="rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6">
                <h3 class="mb-3 transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">Latest Leave Requests</h3>
                <div class="space-y-3">
                    @forelse ($leaveRequests->take(4) as $leave)
                        <div class="rounded-xl transition-colors duration-300 dark:bg-slate-950 bg-slate-100 px-4 py-3">
                            <p class="transition-colors duration-300 dark:text-white text-slate-900 font-medium">{{ $leave->employee->full_name }} ({{ ucfirst($leave->leave_type) }})</p>
                            <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-xs">{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M') }} · {{ ucfirst($leave->status) }}</p>
                        </div>
                    @empty
                        <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">No leave requests yet.</p>
                    @endforelse
                </div>
                @if ($leaveRequests->count() > 4)
                    <a href="{{ route('leaves.index') }}" class="mt-4 inline-flex items-center gap-1 transition-colors duration-300 dark:text-cyan-400 dark:hover:text-cyan-300 text-blue-600 hover:text-blue-700 text-sm">
                        View More →
                    </a>
                @endif
            </div>
        </div>

        <!-- Leave Statistics -->
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6">
                <h3 class="mb-4 transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">Leave Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-3 w-3 rounded-full bg-emerald-500"></span>
                            <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">Approved</p>
                        </div>
                        <p class="transition-colors duration-300 dark:text-emerald-300 text-emerald-600 font-semibold">{{ $leaveStats['approved'] }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-3 w-3 rounded-full bg-yellow-500"></span>
                            <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">Pending</p>
                        </div>
                        <p class="transition-colors duration-300 dark:text-yellow-300 text-yellow-600 font-semibold">{{ $leaveStats['pending'] }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-3 w-3 rounded-full bg-red-500"></span>
                            <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">Rejected</p>
                        </div>
                        <p class="transition-colors duration-300 dark:text-red-300 text-red-600 font-semibold">{{ $leaveStats['rejected'] }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6">
                <h3 class="mb-4 transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">Employment Type</h3>
                <div class="space-y-3">
                    @forelse ($employmentBreakdown as $type)
                        <div class="flex items-center justify-between">
                            <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm capitalize">{{ $type->employment_type }}</p>
                            <span class="transition-colors duration-300 dark:bg-cyan-500/20 dark:text-cyan-300 rounded-full bg-blue-100 text-blue-600 px-3 py-1 text-sm font-semibold">{{ $type->count }}</span>
                        </div>
                    @empty
                        <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">No data available</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6">
                <h3 class="mb-4 transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">Employee Status</h3>
                <div class="space-y-3">
                    @forelse ($employeesByStatus as $status)
                        <div class="flex items-center justify-between">
                            <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm capitalize">{{ $status->status }}</p>
                            <span class="transition-colors duration-300 dark:bg-cyan-500/20 dark:text-cyan-300 rounded-full bg-blue-100 text-blue-600 px-3 py-1 text-sm font-semibold">{{ $status->count }}</span>
                        </div>
                    @empty
                        <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">No data available</p>
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
            <div class="rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6">
                <h3 class="mb-4 transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">Team Managers</h3>
                <div class="space-y-3">
                    @forelse ($teamHeads as $manager)
                        <div class="flex items-center justify-between rounded-lg transition-colors duration-300 dark:bg-slate-950 bg-slate-100 p-3">
                            <div class="flex-1">
                                <p class="transition-colors duration-300 dark:text-white text-slate-900 font-medium">{{ $manager->full_name }}</p>
                                <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-xs">{{ $manager->job_title }}</p>
                            </div>
                            <span class="transition-colors duration-300 dark:bg-cyan-500/20 dark:text-cyan-300 ml-3 rounded-full bg-blue-100 text-blue-600 px-3 py-1 text-sm font-semibold">
                                {{ $manager->subordinates_count ?? count($manager->subordinates) }} reports
                            </span>
                        </div>
                    @empty
                        <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">No managers assigned yet</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6">
                <h3 class="mb-4 transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">Assign Manager</h3>
                <form action="{{ route('employees.assign-manager') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">Select Employee</label>
                        <select name="employee_id" class="transition-colors duration-300 mt-2 w-full rounded-lg dark:border-slate-700 dark:bg-slate-950 dark:text-white border-slate-300 border bg-white text-slate-900 px-3 py-2 text-sm" required>
                            <option value="">-- Choose Employee --</option>
                            @forelse ($allEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->department->name }})</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">Select Manager</label>
                        <select name="manager_id" class="transition-colors duration-300 mt-2 w-full rounded-lg dark:border-slate-700 dark:bg-slate-950 dark:text-white border-slate-300 border bg-white text-slate-900 px-3 py-2 text-sm" required>
                            <option value="">-- No Manager --</option>
                            @forelse ($allEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">Effective Date</label>
                        <input type="date" name="effective_date" class="transition-colors duration-300 mt-2 w-full rounded-lg dark:border-slate-700 dark:bg-slate-950 dark:text-white border-slate-300 border bg-white text-slate-900 px-3 py-2 text-sm appearance-auto" value="{{ now()->toDateString() }}" required>
                    </div>
                    <button class="w-full rounded-lg transition-all duration-300 dark:bg-cyan-500 dark:text-slate-900 dark:hover:bg-cyan-400 bg-blue-500 text-white hover:bg-blue-600 py-2 font-semibold">Assign Manager</button>
                </form>
            </div>
        </div>

        <!-- Top Departments -->
        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6">
                <h3 class="mb-4 transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">Top Departments by Headcount</h3>
                <div class="space-y-3">
                    @forelse ($topDepartments->take(4) as $dept)
                        <div class="flex items-center justify-between rounded-lg transition-colors duration-300 dark:bg-slate-950 bg-slate-100 p-3">
                            <div class="flex-1">
                                <p class="transition-colors duration-300 dark:text-white text-slate-900 font-medium">{{ $dept->name }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <div class="h-2 flex-1 rounded-full transition-colors duration-300 dark:bg-slate-700 bg-slate-300">
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r from-cyan-500 to-blue-500"
                                            style="width: {{ $employeeCount > 0 ? ($dept->employees_count / $employeeCount) * 100 : 0 }}%"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                            <span class="ml-3 min-w-fit rounded-full transition-colors duration-300 dark:bg-cyan-500/20 dark:text-cyan-300 bg-blue-100 text-blue-600 px-3 py-1 text-sm font-semibold">
                                {{ $dept->employees_count }}
                            </span>
                        </div>
                    @empty
                        <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">No departments found</p>
                    @endforelse
                </div>
                @if ($topDepartments->count() > 4)
                    <a href="{{ route('departments.index') }}" class="mt-4 inline-flex items-center gap-1 transition-colors duration-300 dark:text-cyan-400 dark:hover:text-cyan-300 text-blue-600 hover:text-blue-700 text-sm">
                        View More →
                    </a>
                @endif
            </div>

            <div class="rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6">
                <h3 class="mb-4 transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">New Joinees</h3>
                <div class="space-y-3">
                    @forelse ($newJoinees->take(4) as $employee)
                        <div class="flex items-center justify-between rounded-lg transition-colors duration-300 dark:bg-slate-950 bg-slate-100 p-3">
                            <div>
                                <p class="transition-colors duration-300 dark:text-white text-slate-900 font-medium">{{ $employee->full_name }}</p>
                                <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-xs">{{ $employee->job_title }} · {{ $employee->joined_on->format('d M Y') }}</p>
                            </div>
                            <span class="rounded-full transition-colors duration-300 dark:bg-emerald-500/20 dark:text-emerald-300 bg-emerald-100 text-emerald-600 px-3 py-1 text-xs font-medium">New</span>
                        </div>
                    @empty
                        <p class="transition-colors duration-300 dark:text-slate-400 text-slate-600 text-sm">No new joinees</p>
                    @endforelse
                </div>
                @if ($newJoinees->count() > 4)
                    <a href="{{ route('employees.index') }}" class="mt-4 inline-flex items-center gap-1 transition-colors duration-300 dark:text-cyan-400 dark:hover:text-cyan-300 text-blue-600 hover:text-blue-700 text-sm">
                        View More →
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-8 rounded-2xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900 border-slate-200 border bg-white p-6">
            <h3 class="mb-4 transition-colors duration-300 dark:text-white text-slate-900 text-lg font-semibold">Department-wise Workforce</h3>
            <canvas id="departmentChart2" class="max-h-80"></canvas>
        </div>
    </div>

    <script>
        // Department Workforce Chart
        const labels = @json($departmentBreakdown->pluck('name'));
        const data = @json($departmentBreakdown->pluck('employees_count'));
        const isDarkMode = document.documentElement.classList.contains('dark');

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
                plugins: {legend: {display: false, labels: {color: isDarkMode ? '#cbd5e1' : '#475569'}}},
                scales: {
                    x: {ticks: {color: isDarkMode ? '#94a3b8' : '#64748b'}},
                    y: {ticks: {color: isDarkMode ? '#94a3b8' : '#64748b'}, beginAtZero: true},
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
                    legend: {labels: {color: isDarkMode ? '#cbd5e1' : '#475569'}},
                    tooltip: {callbacks: {label: ctx => ctx.raw}},
                },
            },
        });
    </script>
@endsection
