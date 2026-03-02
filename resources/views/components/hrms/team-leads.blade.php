@props(['employees'])

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm
            dark:border-slate-800 dark:bg-slate-900">

    <h3 class="mb-6 text-xl font-semibold text-slate-800
               dark:text-white">
        Team Leads
    </h3>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($employees as $employee)

            <div class="group rounded-xl border border-slate-200 bg-slate-50 p-4 transition-all duration-300
                        hover:shadow-md hover:border-cyan-400/40 hover:bg-white
                        dark:border-slate-700 dark:bg-slate-950
                        dark:hover:border-cyan-500/50 dark:hover:bg-slate-900">

                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm font-medium text-cyan-600
                                  dark:text-cyan-300">
                            {{ $employee->job_title }}
                        </p>

                        <p class="mt-1 text-lg font-bold text-slate-800
                                  dark:text-white">
                            {{ $employee->full_name }}
                        </p>
                    </div>

                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full
                                 bg-cyan-100 text-lg font-semibold text-cyan-700
                                 dark:bg-cyan-500/20 dark:text-cyan-300">
                        {{ substr($employee->full_name, 0, 1) }}
                    </span>
                </div>

                <p class="text-xs text-slate-500 mb-2
                          dark:text-slate-400">
                    {{ $employee->department->name ?? 'N/A' }}
                </p>

                <div class="mb-3 pt-3 border-t border-slate-200
                            dark:border-slate-700">
                    <p class="text-xs text-slate-500 mb-2">
                        {{ $employee->email }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ $employee->phone }}
                    </p>
                </div>

                @if($employee->subordinates->isNotEmpty())
                    <div class="flex items-center gap-2 pt-3 border-t border-slate-200
                                dark:border-slate-700">

                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full
                                     bg-blue-100 text-xs font-semibold text-blue-700
                                     dark:bg-blue-500/20 dark:text-blue-300">
                            {{ $employee->subordinates->count() }}
                        </span>

                        <p class="text-xs text-slate-500
                                  dark:text-slate-400">
                            Direct Reports
                        </p>
                    </div>
                @endif

            </div>

        @empty
            <div class="col-span-full rounded-xl border border-slate-200 bg-slate-50 p-6 text-center
                        dark:border-slate-700 dark:bg-slate-950">
                <p class="text-slate-500 dark:text-slate-400">
                    No team leads found
                </p>
            </div>
        @endforelse
    </div>
</div>
