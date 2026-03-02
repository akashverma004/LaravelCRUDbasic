@props(['employee'])

<div class="flex flex-col items-center">

    <!-- Employee Card -->
    <div class="relative">

        <div class="rounded-xl border border-slate-200 bg-white px-6 py-4 shadow-sm
                    transition hover:shadow-md hover:border-cyan-400/40
                    dark:border-slate-700 dark:bg-slate-900">

            <div class="text-center">
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
        </div>

        @if($employee->subordinates->isNotEmpty())
            <button
                @click="toggle({{ $employee->id }})"
                class="absolute -bottom-3 left-1/2 -translate-x-1/2 rounded-full
                       bg-cyan-500 text-white px-2 py-1 text-xs shadow-md">
                ⌄
            </button>
        @endif
    </div>

    <!-- Children -->
    @if($employee->subordinates->isNotEmpty())
        <div
            x-show="isOpen({{ $employee->id }})"
            x-transition
            class="mt-12 relative"
        >

            <!-- Vertical connector -->
            <div class="absolute -top-12 left-1/2 -translate-x-1/2 h-12 w-px bg-slate-300 dark:bg-slate-700"></div>

            <!-- Horizontal children wrapper -->
            <div class="flex justify-center gap-16 relative">

                <!-- Horizontal connector line -->
                <div class="absolute top-0 left-0 right-0 h-px bg-slate-300 dark:bg-slate-700"></div>

                @foreach($employee->subordinates as $sub)
                    <div class="relative flex flex-col items-center">

                        <!-- Vertical line to each child -->
                        <div class="absolute -top-12 h-12 w-px bg-slate-300 dark:bg-slate-700"></div>

                        @include('hrms.components.binary-node', ['employee' => $sub])
                    </div>
                @endforeach

            </div>
        </div>
    @endif

</div>
