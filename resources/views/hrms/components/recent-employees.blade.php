<div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
    <h3 class="mb-3 text-lg font-semibold">Recent Employees</h3>
    <div class="space-y-3">
        @forelse ($employees as $employee)
            <div class="flex items-center justify-between rounded-xl bg-slate-950 px-4 py-3">
                <div>
                    <a href="{{ route('employees.show', $employee->id) }}" class="font-medium hover:text-cyan-300">{{ $employee->full_name }}</a>
                    <p class="text-xs text-slate-400">{{ $employee->job_title }} · {{ $employee->department->name }}</p>
                </div>
                <span class="rounded-full bg-cyan-500/10 px-3 py-1 text-xs text-cyan-300">{{ ucfirst($employee->status) }}</span>
            </div>
        @empty
            <p class="text-sm text-slate-400">No employees yet.</p>
        @endforelse
    </div>
    <a href="{{ route('employees.index') }}" class="mt-4 inline-block text-sm text-cyan-400 hover:text-cyan-300">View All Employees →</a>
</div>
