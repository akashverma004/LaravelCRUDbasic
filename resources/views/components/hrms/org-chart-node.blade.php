@props(['employee', 'level' => 0])

<div class="mb-4" style="margin-left: {{ $level * 30 }}px">
    <div class="rounded-lg border border-cyan-500/30 bg-cyan-500/10 p-4 shadow-lg">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-cyan-300">{{ $employee->job_title }}</p>
                <p class="mt-1 text-lg font-bold text-white">{{ $employee->full_name }}</p>
                <p class="mt-2 text-xs text-slate-400">{{ $employee->department->name ?? 'N/A' }}</p>
                <div class="mt-2 flex gap-2">
                    <span class="rounded-full bg-slate-700/50 px-2 py-1 text-xs text-slate-300">{{ $employee->email }}</span>
                    <span class="rounded-full bg-slate-700/50 px-2 py-1 text-xs text-slate-300">{{ $employee->phone }}</span>
                </div>
            </div>
            <span class="rounded-full bg-cyan-500/20 px-3 py-1 text-xs font-medium text-cyan-300">
                {{ $employee->subordinates->count() }} direct reports
            </span>
        </div>
    </div>

    @if($employee->subordinates->isNotEmpty())
        <div style="border-left: 2px solid rgba(6, 182, 212, 0.3); margin-top: 1rem">
            @foreach($employee->subordinates->sortBy('full_name') as $subordinate)
                <x-hrms.org-chart-node :employee="$subordinate" :level="$level + 1" />
            @endforeach
        </div>
    @endif
</div>
