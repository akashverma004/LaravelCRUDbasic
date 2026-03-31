<div class="space-y-8 pb-12">
    {{-- Header & Navigation --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-indigo-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Scheduling</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Team Roster</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Shift <span class="text-indigo-500">Roster</span>
                </h1>
                <p class="mt-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                    {{ \Carbon\Carbon::parse($weekStart)->format('d M') }} - {{ \Carbon\Carbon::parse($weekStart)->addDays(6)->format('d M Y') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center rounded-xl bg-slate-50 p-1 dark:bg-white/5 border border-slate-100 dark:border-white/5 shadow-inner">
                    <button wire:click="prevWeek" class="h-9 w-9 flex items-center justify-center rounded-lg text-slate-400 hover:bg-white hover:text-slate-900 shadow-sm transition-all dark:hover:bg-slate-800 dark:hover:text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </button>
                    <button wire:click="$set('weekStart', '{{ now()->startOfWeek()->toDateString() }}')" class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400">Today</button>
                    <button wire:click="nextWeek" class="h-9 w-9 flex items-center justify-center rounded-lg text-slate-400 hover:bg-white hover:text-slate-900 shadow-sm transition-all dark:hover:bg-slate-800 dark:hover:text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                    </button>
                </div>
                @if($isAdmin)
                    <button wire:click="$set('showShiftModal', true)" class="inline-flex h-11 items-center gap-2 rounded-xl bg-slate-900 px-6 text-[10px] font-black uppercase tracking-widest text-white shadow-lg transition-all hover:bg-indigo-600 active:scale-95 dark:bg-white/5 dark:text-indigo-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Manage Shifts</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Roster Grid --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-white/[0.02]">
                        <th class="sticky left-0 z-10 bg-slate-50/50 dark:bg-slate-950 px-6 py-6 border-r border-slate-100 dark:border-white/5 w-[250px]">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Staff Members</span>
                        </th>
                        @foreach($days as $day)
                            <th class="px-4 py-6 border-r border-slate-100 dark:border-white/5 text-center {{ $day->isToday() ? 'bg-indigo-50/30 dark:bg-indigo-500/5' : '' }}">
                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">{{ $day->format('D') }}</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-1 {{ $day->isToday() ? 'text-indigo-600 dark:text-indigo-400' : '' }}">{{ $day->format('d M') }}</p>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach($employees as $employee)
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-white/[0.01]">
                            <td class="sticky left-0 z-10 bg-white dark:bg-slate-900 border-r border-slate-100 dark:border-white/5 px-6 py-4 group-hover:bg-slate-50/50 dark:group-hover:bg-white/[0.01]">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center font-black text-[10px] text-slate-500 dark:bg-white/5 dark:text-indigo-400">
                                        {{ substr($employee->full_name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $employee->full_name }}</p>
                                        <p class="truncate text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $employee->job_title }}</p>
                                    </div>
                                </div>
                            </td>
                            @foreach($days as $day)
                                @php
                                    $dateStr = $day->toDateString();
                                    $assignment = $schedules->first(fn($s) => $s->employee_id == $employee->id && $s->date->toDateString() == $dateStr);
                                @endphp
                                <td class="p-2 border-r border-slate-100 dark:border-white/5 {{ $day->isToday() ? 'bg-indigo-50/10 dark:bg-indigo-500/[0.02]' : '' }} relative min-h-[80px]">
                                    @if($assignment)
                                        <div class="group/item relative rounded-xl border p-2 h-full flex flex-col justify-between transition-all hover:shadow-md" 
                                             style="background-color: {{ $assignment->shift->color }}08; border-color: {{ $assignment->shift->color }}30;">
                                            <div>
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-[8px] font-black uppercase tracking-widest truncate" style="color: {{ $assignment->shift->color }};">
                                                        {{ $assignment->shift->name }}
                                                    </span>
                                                    @if($isAdmin)
                                                        <button wire:click="deleteAssignment({{ $assignment->id }})" class="opacity-0 group-hover/item:opacity-100 p-0.5 rounded hover:bg-rose-50 hover:text-rose-600 transition-all">
                                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        </button>
                                                    @endif
                                                </div>
                                                <p class="text-[9px] font-bold text-slate-600 dark:text-slate-400 tabular-nums">
                                                    {{ \Carbon\Carbon::parse($assignment->shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($assignment->shift->end_time)->format('H:i') }}
                                                </p>
                                            </div>
                                            <div class="h-1 w-full rounded-full mt-2" style="background-color: {{ $assignment->shift->color }}50;"></div>
                                        </div>
                                    @elseif($isAdmin)
                                        <button wire:click="openAssignModal({{ $employee->id }}, '{{ $dateStr }}')"
                                                class="w-full h-full min-h-[60px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all rounded-xl border-2 border-dashed border-slate-100 hover:border-indigo-300 hover:bg-indigo-50/30 dark:border-white/5 dark:hover:bg-indigo-500/5">
                                            <svg class="h-5 w-5 text-slate-300 group-hover:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                        </button>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Shift Assignment Modal --}}
    @if($showAssignModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showAssignModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden">
                <div class="border-b border-slate-100 p-6 dark:border-white/5">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Assign <span class="text-indigo-500">Shift</span></h2>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Assigning for {{ \Carbon\Carbon::parse($assignmentDate)->format('l, M j') }}</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Select Shift Template</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($shifts as $shift)
                                <button wire:click="$set('selectedShiftId', {{ $shift->id }})" 
                                        class="p-4 rounded-xl border-2 text-left transition-all {{ $selectedShiftId == $shift->id ? 'border-indigo-500 bg-indigo-50/30' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200' }} dark:border-white/5 dark:bg-white/5">
                                    <p class="text-[11px] font-black uppercase tracking-tight truncate" style="color: {{ $shift->color }};">{{ $shift->name }}</p>
                                    <p class="text-[9px] font-bold text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</p>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 p-6 dark:border-white/5 dark:bg-white/5 flex justify-end gap-3">
                    <button wire:click="$set('showAssignModal', false)" class="rounded-xl border border-slate-200 bg-white px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-700 transition hover:bg-slate-50">Cancel</button>
                    <button wire:click="assignShift" class="rounded-xl bg-slate-900 px-8 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl transition-all hover:bg-indigo-600 active:scale-95">Confirm Assignment</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Manage Shifts Modal --}}
    @if($showShiftModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showShiftModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
            <div class="relative w-full max-w-xl rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden">
                <div class="border-b border-slate-100 p-6 dark:border-white/5 flex items-center justify-between">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Shift <span class="text-indigo-500">Templates</span></h2>
                    <button wire:click="$set('showShiftModal', false)" class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Create Form --}}
                    <div class="space-y-4">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400">New Template</h3>
                        <div class="space-y-4">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black uppercase text-slate-500">Shift Name</label>
                                <input wire:model="shiftName" type="text" placeholder="e.g. Morning Shift" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-black uppercase text-slate-500">Start Time</label>
                                    <input wire:model="startTime" type="time" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-black uppercase text-slate-500">End Time</label>
                                    <input wire:model="endTime" type="time" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white">
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black uppercase text-slate-500">Indicator Color</label>
                                <div class="flex flex-wrap gap-2">
                                    @php $colors = ['#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#f43f5e', '#8b5cf6']; @endphp
                                    @foreach($colors as $color)
                                        <button wire:click="$set('shiftColor', '{{ $color }}')" 
                                                class="h-7 w-7 rounded-lg border-2 ring-offset-2 {{ $shiftColor == $color ? 'ring-2 ring-slate-900 scale-110' : 'ring-0 opacity-80 hover:opacity-100' }} transition-all"
                                                style="background-color: {{ $color }}; border-color: {{ $color }};"></button>
                                    @endforeach
                                </div>
                            </div>
                            <button wire:click="createShift" class="w-full rounded-xl bg-slate-900 py-3 text-[10px] font-black uppercase tracking-widest text-white transition-all hover:bg-slate-800 active:scale-95">Add Template</button>
                        </div>
                    </div>
                    {{-- Templates List --}}
                    <div class="space-y-4">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Existing Templates</h3>
                        <div class="space-y-2 h-[280px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($shifts as $shift)
                                <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-lg shadow-sm" style="background-color: {{ $shift->color }};"></div>
                                        <div>
                                            <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase">{{ $shift->name }}</p>
                                            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</p>
                                        </div>
                                    </div>
                                    <button wire:click="$set('selectedShiftId', {{ $shift->id }})" class="p-1.5 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-colors">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
