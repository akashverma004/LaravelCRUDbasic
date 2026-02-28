<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PeopleFlow HRMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100" x-data="{tab: 'employees'}">
    <div class="mx-auto max-w-7xl px-6 py-8">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-cyan-300">Modern HRMS</p>
                <h1 class="text-3xl font-bold">PeopleFlow Control Center</h1>
                <p class="text-slate-400">Laravel-based HRMS with scalable module-driven architecture for Hostinger deployment.</p>
            </div>
            <span class="rounded-full border border-cyan-400/30 bg-cyan-500/10 px-4 py-2 text-sm">{{ now()->format('d M Y') }}</span>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-200">{{ session('status') }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Total Employees', 'value' => $employeeCount],
                ['label' => 'Departments', 'value' => $departmentCount],
                ['label' => 'Pending Leave', 'value' => $leavePending],
                ['label' => 'Attendance Today', 'value' => $attendanceToday],
            ] as $card)
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-lg shadow-cyan-900/10">
                    <p class="text-sm text-slate-400">{{ $card['label'] }}</p>
                    <p class="mt-2 text-3xl font-semibold text-cyan-300">{{ $card['value'] }}</p>
                </div>
            @endforeach
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

        <div class="mt-8 rounded-2xl border border-slate-800 bg-slate-900 p-6">
            <div class="mb-4 flex gap-3">
                <button @click="tab='employees'" :class="tab === 'employees' ? 'bg-cyan-500 text-slate-900' : 'bg-slate-800'" class="rounded-lg px-4 py-2 text-sm font-medium">Add Employee</button>
                <button @click="tab='leave'" :class="tab === 'leave' ? 'bg-cyan-500 text-slate-900' : 'bg-slate-800'" class="rounded-lg px-4 py-2 text-sm font-medium">Add Leave Request</button>
            </div>

            <form x-show="tab === 'employees'" method="POST" action="{{ route('hrms.employees.store') }}" class="grid gap-3 md:grid-cols-2">
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
                <input type="date" name="joined_on" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                <select name="status" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                    <option value="active">Active</option>
                    <option value="on-leave">On Leave</option>
                    <option value="resigned">Resigned</option>
                </select>
                <button class="rounded-lg bg-cyan-500 py-2 font-semibold text-slate-900 transition hover:bg-cyan-400">Create Employee</button>
            </form>

            <form x-show="tab === 'leave'" method="POST" action="{{ route('hrms.leave.store') }}" class="grid gap-3 md:grid-cols-2">
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
                    @forelse ($employees as $employee)
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
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="mb-3 text-lg font-semibold">Latest Leave Requests</h3>
                <div class="space-y-3">
                    @forelse ($leaveRequests as $leave)
                        <div class="rounded-xl bg-slate-950 px-4 py-3">
                            <p class="font-medium">{{ $leave->employee->full_name }} ({{ ucfirst($leave->leave_type) }})</p>
                            <p class="text-xs text-slate-400">{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M') }} · {{ ucfirst($leave->status) }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No leave requests yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>
