<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
    <div class="mb-6 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Recent Leaves</h3>
        <span class="rounded-full bg-cyan-50 px-2.5 py-1 text-[10px] font-semibold text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400">Updates</span>
    </div>

    <div class="space-y-3">
        @forelse ($leaveRequests as $leave)
            <div class="group flex flex-col gap-2 rounded-xl border border-slate-100 bg-slate-50 p-4 transition-colors hover:bg-slate-100 dark:border-slate-800/50 dark:bg-slate-950/50 dark:hover:bg-slate-800">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $leave->employee->full_name }}</p>
                    <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider dark:bg-slate-800 {{ $leave->status === 'pending' ? 'text-amber-500' : ($leave->status === 'approved' ? 'text-emerald-500' : 'text-rose-500') }}">
                        {{ $leave->status }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-medium text-slate-500">{{ $leave->leave_type }}</p>
                    <p class="text-[10px] font-medium text-slate-500">{{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d') }}</p>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-6 text-center">
                <p class="text-xs text-slate-500">No recent leaves.</p>
            </div>
        @endforelse
    </div>
    
    <a href="{{ route('leaves.pending') }}" class="mt-6 flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-2 text-xs font-bold text-cyan-600 hover:bg-slate-50 hover:text-cyan-700 transition-colors dark:border-slate-700 dark:bg-slate-800 dark:text-cyan-400 dark:hover:bg-slate-700 dark:hover:text-cyan-300">
        View Pending Leaves
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
    </a>
</div>
