@props(['employee'])

<div class="flex flex-col items-center">

    {{-- Employee Card --}}
    <div class="relative">
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white px-6 py-5 shadow-sm transition-all hover:border-cyan-400 hover:shadow-md dark:border-slate-800 dark:bg-slate-900/60 min-w-[220px]">
            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-cyan-400/5 blur-xl transition-all group-hover:bg-cyan-400/10"></div>
            
            <div class="relative flex flex-col items-center text-center">
                {{-- Avatar --}}
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-sm font-bold text-slate-700 shadow-sm border border-slate-200 transition-all group-hover:bg-cyan-50 group-hover:text-cyan-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:group-hover:bg-cyan-500/20 dark:group-hover:text-cyan-400">
                    {{ substr($employee->full_name, 0, 1) }}
                </div>

                <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 text-opacity-80 group-hover:text-opacity-100 transition-opacity">
                    {{ $employee->job_title ?? 'Employee' }}
                </p>

                <p class="mt-1 text-sm font-bold tracking-tight text-slate-900 dark:text-white transition-colors">
                    {{ $employee->full_name }}
                </p>

                <p class="mt-0.5 text-[10px] font-medium text-slate-500">
                    {{ $employee->department->name ?? 'No Department' }}
                </p>
            </div>
        </div>

        {{-- Toggle Button --}}
        @if($employee->subordinates->isNotEmpty())
            <button
                @click="toggle({{ $employee->id }})"
                class="absolute -bottom-3 left-1/2 -translate-x-1/2 flex h-6 w-6 items-center justify-center rounded-lg bg-white text-slate-600 shadow-sm border border-slate-200 transition-all hover:bg-slate-50 hover:text-cyan-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-cyan-400 z-20">
                <svg class="h-3 w-3 transition-transform duration-300" :class="isOpen({{ $employee->id }}) ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
            </button>
        @endif
    </div>

    {{-- Subordinates --}}
    @if($employee->subordinates->isNotEmpty())
        <div
            x-show="isOpen({{ $employee->id }})"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="mt-8 relative"
            style="display: none;"
        >
            {{-- Vertical Line --}}
            <div class="absolute -top-8 left-1/2 -translate-x-1/2 h-8 w-[2px] bg-slate-200 dark:bg-slate-700"></div>

            {{-- Horizontal Line & Nodes --}}
            <div class="flex justify-center gap-8 relative px-4">
                {{-- Horizontal Connector Line --}}
                <div class="absolute top-0 left-4 right-4 h-[2px] bg-slate-200 dark:bg-slate-700"></div>

                @foreach($employee->subordinates as $sub)
                    <div class="relative flex flex-col items-center">
                        {{-- Secondary Vertical Line --}}
                        <div class="absolute -top-8 h-8 w-[2px] bg-slate-200 dark:bg-slate-700"></div>
                        @include('hrms.components.binary-node', ['employee' => $sub])
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
