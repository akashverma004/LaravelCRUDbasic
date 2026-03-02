@props(['employee', 'level' => 0])

<div class="relative" x-data="{ showSubs{{ $employee->id }}: false }">
    <!-- Main Employee Card -->
    <div class="inline-flex flex-col items-center">
        <div class="group relative w-56 rounded-xl border-2 border-cyan-500/30 bg-gradient-to-br from-slate-800 to-slate-900 p-4 transition hover:border-cyan-500 hover:shadow-2xl hover:shadow-cyan-500/20">
            <!-- Avatar Circle -->
            <div class="mb-3 flex justify-center">
                <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 text-2xl font-bold text-white shadow-lg">
                    {{ substr($employee->full_name, 0, 1) }}
                </div>
            </div>

            <!-- Name -->
            <h3 class="text-center text-lg font-bold text-white">{{ $employee->full_name }}</h3>

            <!-- Title -->
            <p class="mt-1 text-center text-sm text-cyan-300">{{ $employee->job_title }}</p>

            <!-- Department -->
            <p class="mt-1 text-center text-xs text-slate-400">{{ $employee->department->name ?? 'N/A' }}</p>

            <!-- Hover Info -->
            <div class="absolute left-1/2 top-full z-10 mt-2 hidden -translate-x-1/2 transform rounded-lg bg-slate-950 p-2 text-xs text-white shadow-xl group-hover:block whitespace-nowrap">
                <p>{{ $employee->email }}</p>
                <p>{{ $employee->phone }}</p>
            </div>

            <!-- Direct Reports Badge -->
            @if($employee->subordinates->count() > 0)
                <div class="absolute right-2 top-2 inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-500/30 text-xs font-bold text-blue-300">
                    {{ $employee->subordinates->count() }}
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-4 flex gap-2">
                <!-- Info Button -->
                <button
                    @click="loadEmployeeDetails({{ $employee->id }})"
                    class="flex-1 rounded-lg bg-cyan-500/20 px-3 py-2 text-xs font-medium text-cyan-300 transition hover:bg-cyan-500/40"
                    title="View profile details"
                >
                    ℹ️ Info
                </button>

                <!-- Subordinates Toggle Button (only if has direct reports) -->
                @if($employee->subordinates->count() > 0)
                    <button
                        @click="showSubs{{ $employee->id }} = !showSubs{{ $employee->id }}"
                        :class="showSubs{{ $employee->id }} ? 'bg-blue-500/40 text-blue-300' : 'bg-slate-700/50 text-slate-300'"
                        class="flex-1 rounded-lg px-3 py-2 text-xs font-medium transition hover:bg-blue-500/30"
                        title="Show/hide direct reports"
                    >
                        👥 <span x-text="showSubs{{ $employee->id }} ? 'Hide' : 'Show'"></span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Subordinates (Hidden by default, shown on click) -->
        @if($employee->subordinates->isNotEmpty())
            <div x-show="showSubs{{ $employee->id }}" x-cloak class="relative mt-8">
                <!-- Vertical Line Down -->
                <div class="absolute left-1/2 top-0 h-8 w-1 -translate-x-1/2 transform bg-gradient-to-b from-cyan-500/50 to-transparent"></div>

                <!-- Horizontal Line Across -->
                <div class="absolute top-8 h-1 bg-cyan-500/30" style="left: 0; right: 0; width: {{ (count($employee->subordinates) * 240) - 20 }}px; margin-left: calc(50% - {{ ((count($employee->subordinates) * 240) - 20) / 2 }}px);"></div>

                <!-- Subordinates Container -->
                <div class="flex gap-8 justify-center flex-wrap">
                    @foreach($employee->subordinates->sortBy('full_name') as $subordinate)
                        <div class="relative">
                            <!-- Vertical Line Up -->
                            <div class="absolute left-1/2 -top-8 h-8 w-1 -translate-x-1/2 transform bg-gradient-to-b from-cyan-500/30 to-cyan-500/30"></div>

                            <!-- Subordinate Card -->
                            <x-hrms.org-chart-interactive :employee="$subordinate" :level="$level + 1" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
