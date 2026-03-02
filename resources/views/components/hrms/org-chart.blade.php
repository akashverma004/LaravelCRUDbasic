@props(['employees', 'departmentName' => null])

<div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
    <div class="mb-6 flex items-center justify-between">
        <h3 class="text-xl font-semibold text-white">
            {{ $departmentName ? "Organization - {$departmentName}" : 'Complete Organization Hierarchy' }}
        </h3>
        <span class="rounded-full bg-slate-800 px-3 py-1 text-sm text-slate-300">
            {{ $employees->sum(fn($e) => 1 + $e->subordinates->count()) }} employees
        </span>
    </div>

    <div class="space-y-4">
        @forelse($employees as $employee)
            <x-hrms.org-chart-node :employee="$employee" />
        @empty
            <div class="rounded-lg border border-slate-700 bg-slate-950 p-6 text-center">
                <p class="text-slate-400">No organizational structure found</p>
            </div>
        @endforelse
    </div>
</div>
