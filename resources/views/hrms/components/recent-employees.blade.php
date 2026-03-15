<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
    <div class="mb-6 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Recent Employees</h3>
        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-semibold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">Directory</span>
    </div>

    <div class="space-y-3">
        @forelse ($employees as $employee)
            <div class="group flex flex-col gap-2 rounded-xl border border-slate-100 bg-slate-50 p-4 transition-colors hover:bg-slate-100 dark:border-slate-800/50 dark:bg-slate-950/50 dark:hover:bg-slate-800">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <a href="{{ route('employees.show', $employee->id) }}" class="text-xs font-bold text-slate-900 transition-colors group-hover:text-cyan-600 dark:text-white dark:group-hover:text-cyan-400" x-text="'@js($employee->full_name)'"></a>
                        <p class="mt-0.5 truncate text-[10px] font-medium text-slate-500">{{ $employee->job_title }} · {{ $employee->department->name }}</p>
                    </div>
                    <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                        {{ $employee->status }}
                    </span>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-6 text-center">
                <p class="text-xs text-slate-500">No recent employees.</p>
            </div>
        @endforelse
    </div>
    
    <a href="{{ route('employees.index') }}" class="mt-6 flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-2 text-xs font-bold text-cyan-600 hover:bg-slate-50 hover:text-cyan-700 transition-colors dark:border-slate-700 dark:bg-slate-800 dark:text-cyan-400 dark:hover:bg-slate-700 dark:hover:text-cyan-300">
        View All Employees
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
    </a>
</div>
