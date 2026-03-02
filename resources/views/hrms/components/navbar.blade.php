<nav class="border-b border-slate-800 bg-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="font-bold text-cyan-300">PeopleFlow HRMS</a>
            <div class="flex gap-6">
                <a href="{{ route('dashboard') }}" class="text-slate-300 hover:text-cyan-300">Dashboard</a>
                <a href="{{ route('org-chart.index') }}" class="text-slate-300 hover:text-cyan-300">Organization Chart</a>
                <a href="{{ route('employees.index') }}" class="text-slate-300 hover:text-cyan-300">Employees</a>
                <a href="{{ route('departments.index') }}" class="text-slate-300 hover:text-cyan-300">Departments</a>
                <a href="{{ route('leaves.index') }}" class="text-slate-300 hover:text-cyan-300">Leave Requests</a>
            </div>
        </div>
    </div>
</nav>
