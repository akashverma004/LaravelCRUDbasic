@props(['employee', 'level'])

<div class="relative">

    {{-- Connector --}}
    @if($level > 0)
        <div class="absolute left-4 top-0 h-full w-px bg-slate-200 dark:bg-slate-700"></div>
    @endif

    <div class="flex items-start gap-4">

        {{-- Node Toggle --}}
        @if($employee->subordinates->isNotEmpty())
            <button
                @click="toggle({{ $employee->id }})"
                class="group z-10 mt-4 flex h-8 w-8 items-center justify-center rounded-lg bg-white border border-slate-200 shadow-sm transition-colors hover:bg-slate-50 hover:text-cyan-600 dark:bg-slate-800 dark:border-slate-700 dark:hover:bg-slate-700 dark:hover:text-cyan-400"
            >
                <div class="h-1.5 w-1.5 rounded-full bg-slate-400 transition-transform duration-300 group-hover:scale-110 dark:bg-slate-500" :class="isOpen({{ $employee->id }}) ? 'hidden' : 'block'"></div>
                <svg
                    x-show="isOpen({{ $employee->id }})"
                    class="h-4 w-4 transition-transform duration-300 text-slate-500 dark:text-slate-400"
                    fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
        @else
            <div class="mt-4 flex h-8 w-8 items-center justify-center">
                <div class="h-1 w-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
            </div>
        @endif


        {{-- Node Card --}}
        <div class="flex-1 pb-4">

            <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition-all hover:shadow-md dark:border-slate-800 dark:bg-slate-900/50 hover:border-cyan-300 dark:hover:border-slate-700">
                <div class="absolute -right-4 -top-4 h-12 w-12 rounded-full bg-cyan-500/5 blur-lg"></div>
                
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-0.5">
                            {{ $employee->job_title }}
                        </p>

                        <p class="text-sm font-bold tracking-tight text-slate-900 dark:text-white transition-colors">
                            {{ $employee->full_name }}
                        </p>

                        <p class="mt-0.5 text-[10px] font-medium text-slate-500">
                            {{ $employee->department->name ?? 'No Department' }}
                        </p>
                    </div>

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300 shadow-sm group-hover:bg-cyan-50 group-hover:text-cyan-600 dark:group-hover:bg-cyan-500/20 dark:group-hover:text-cyan-400 transition-colors border border-slate-200 dark:border-slate-700">
                        {{ substr($employee->full_name, 0, 1) }}
                    </div>
                </div>
            </div>

            {{-- Nested Nodes --}}
            @if($employee->subordinates->isNotEmpty())
                <div
                    x-show="isOpen({{ $employee->id }})"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="ml-4 mt-6 space-y-2 border-l border-slate-200 dark:border-slate-700"
                >
                    @foreach($employee->subordinates as $sub)
                        <div class="pl-6">
                            @include('hrms.components.org-node', [
                                'employee' => $sub,
                                'level' => $level + 1
                            ])
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
