@props(['employee', 'level'])

<div class="relative">

    <!-- Connector Line -->
    @if($level > 0)
        <div class="absolute left-4 top-0 h-full w-px bg-slate-300 dark:bg-slate-700"></div>
    @endif

    <div class="flex items-start gap-4">

        <!-- Toggle Button -->
        @if($employee->subordinates->isNotEmpty())
            <button
                @click="toggle({{ $employee->id }})"
                class="mt-4 flex h-6 w-6 items-center justify-center rounded-full
                       bg-slate-200 text-slate-600 transition
                       hover:bg-cyan-500 hover:text-white
                       dark:bg-slate-700 dark:text-slate-300"
            >
                <svg
                    :class="isOpen({{ $employee->id }}) ? 'rotate-90' : ''"
                    class="h-4 w-4 transition-transform duration-300"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7" />
                </svg>
            </button>
        @else
            <div class="mt-4 h-6 w-6"></div>
        @endif


        <!-- Employee Card -->
        <div class="flex-1">

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition
                        hover:shadow-md hover:border-cyan-400/40
                        dark:border-slate-700 dark:bg-slate-900">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-cyan-600 dark:text-cyan-300">
                            {{ $employee->job_title }}
                        </p>

                        <p class="text-lg font-bold text-slate-800 dark:text-white">
                            {{ $employee->full_name }}
                        </p>

                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $employee->department->name ?? 'N/A' }}
                        </p>
                    </div>

                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full
                                 bg-cyan-100 text-lg font-semibold text-cyan-700
                                 dark:bg-cyan-500/20 dark:text-cyan-300">
                        {{ substr($employee->full_name, 0, 1) }}
                    </span>
                </div>
            </div>

            <!-- Subordinates -->
            @if($employee->subordinates->isNotEmpty())
                <div
                    x-show="isOpen({{ $employee->id }})"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="ml-10 mt-4 space-y-4"
                >
                    @foreach($employee->subordinates as $sub)
                        @include('hrms.components.org-node', [
                            'employee' => $sub,
                            'level' => $level + 1
                        ])
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
