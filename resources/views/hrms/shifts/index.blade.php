@extends('hrms.layouts.app')

@section('title', 'Shifts & Scheduling - PeopleFlow HRMS')

@section('content')
<div x-data="shiftManager()" x-init="init()" class="space-y-8">
    
    {{-- System Toast --}}
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-6 right-6 z-[100] min-w-[280px] rounded-xl border border-slate-200 bg-white p-4 shadow-xl dark:border-white/10 dark:bg-slate-900"
        style="display: none;"
    >
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Success</p>
                <p class="text-xs font-medium text-slate-900 dark:text-white" x-text="toast.message"></p>
            </div>
        </div>
    </div>

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Shift Scheduling</h1>
            <p class="mt-2 text-sm text-slate-500">Manage employee rosters and work schedules.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-1 rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <button @click="changeWeek(-1)" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                </button>
                <div class="px-4 text-center min-w-[120px]">
                     <span class="text-xs font-bold text-slate-900 dark:text-white" x-text="periodLabel"></span>
                </div>
                <button @click="changeWeek(1)" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                </button>
            </div>
            
            <template x-if="isAdmin">
                <div class="flex items-center gap-2">
                    <button @click="showShiftModal = true" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">Templates</button>
                    <button @click="showAssignModal = true" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Assign Shift</span>
                    </button>
                </div>
            </template>
        </div>
    </div>

    {{-- Roster Grid --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950/50">
                        <th class="sticky left-0 z-20 bg-slate-50 px-6 py-4 min-w-[200px] border-r border-slate-200 dark:border-slate-800 dark:bg-slate-950/50">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Employee</h3>
                        </th>
                        <template x-for="day in weekDays" :key="day.date">
                            <th class="px-6 py-4 text-center border-r border-slate-200 last:border-0 min-w-[140px] dark:border-slate-800">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500" x-text="day.name"></span>
                                <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white" x-text="day.label"></p>
                            </th>
                        </template>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    {{-- Personal Roster --}}
                    <template x-if="!isAdmin && myRoster">
                        <tr class="bg-cyan-50/50 hover:bg-cyan-50 transition-colors dark:bg-cyan-900/10 dark:hover:bg-cyan-900/20">
                            <td class="sticky left-0 z-10 bg-cyan-50/90 px-6 py-4 border-r border-slate-200 dark:border-slate-800 dark:bg-slate-900/90 backdrop-blur-sm">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">My Schedule</p>
                                    </div>
                                </div>
                            </td>
                            <template x-for="day in weekDays" :key="day.date">
                                <td class="px-4 py-4 border-r border-slate-200 last:border-0 align-top dark:border-slate-800">
                                    <div class="flex flex-col gap-2">
                                        <template x-for="sch in getSchedules(null, day.date)" :key="sch.id">
                                            <div class="rounded-lg border-l-4 px-3 py-2 shadow-sm" :style="'border-color: ' + sch.shift.color + '; background-color: ' + sch.shift.color + '10'">
                                                <p class="text-xs font-bold text-slate-900 dark:text-white" x-text="sch.shift.name"></p>
                                                <div class="mt-1 flex items-center gap-1 text-slate-600 dark:text-slate-400">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    <span class="text-[10px] font-medium" x-text="formatTime(sch.shift.start_time) + ' - ' + formatTime(sch.shift.end_time)"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </template>

                    {{-- Admin Roster View --}}
                    <template x-if="isAdmin">
                        <template x-for="emp in employees" :key="emp.id">
                            <tr class="group transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="sticky left-0 z-10 bg-white dark:bg-slate-900 px-6 py-4 border-r border-slate-200 dark:border-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300 transition-colors group-hover:bg-cyan-100 group-hover:text-cyan-600 dark:group-hover:bg-cyan-500/20 dark:group-hover:text-cyan-400" x-text="emp.full_name.charAt(0)"></div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate max-w-[150px]" x-text="emp.full_name"></p>
                                        </div>
                                    </div>
                                </td>
                                <template x-for="day in weekDays" :key="day.date">
                                    <td class="group/cell px-4 py-4 border-r border-slate-200 last:border-0 align-top relative min-h-[80px] dark:border-slate-800">
                                        <div class="flex flex-col gap-2">
                                            <template x-for="sch in getSchedules(emp.id, day.date)" :key="sch.id">
                                                <div class="group/sch relative rounded-lg border-l-4 px-3 py-2 shadow-sm transition-transform hover:-translate-y-0.5" :style="'border-color: ' + sch.shift.color + '; background-color: ' + sch.shift.color + '15'">
                                                    <p class="text-[10px] font-bold text-slate-900 dark:text-white truncate" x-text="sch.shift.name"></p>
                                                    <div class="mt-1 flex items-center justify-between">
                                                         <span class="text-[9px] font-medium text-slate-600 dark:text-slate-400" x-text="formatTime(sch.shift.start_time)"></span>
                                                         <button @click="deleteAssignment(sch.id)" class="opacity-0 group-hover/sch:opacity-100 flex h-4 w-4 items-center justify-center rounded hover:bg-rose-100 text-rose-500 transition-colors dark:hover:bg-rose-500/20">
                                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                            @if($isAdmin)
                                                <button @click="openQuickAssign(emp, day.date)" class="opacity-0 group-hover/cell:opacity-100 mt-1 w-full rounded border border-dashed border-slate-300 py-1 text-[10px] font-semibold text-slate-500 hover:border-cyan-500 hover:text-cyan-600 transition-colors dark:border-slate-600 dark:text-slate-400 dark:hover:border-cyan-500 dark:hover:text-cyan-400">
                                                    + Assign
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </template>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Assign Shift Modal --}}
    <div x-show="showAssignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition>
        <div @click.away="showAssignModal = false" class="relative w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
            
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Assign Shift</h3>
                <button @click="showAssignModal = false" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Employee</label>
                    <select x-model="assignForm.employee_id" class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm font-medium text-slate-900 appearance-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white dark:bg-slate-900">
                        <option value="">Select Employee...</option>
                        <template x-for="emp in employees" :key="emp.id">
                            <option :value="emp.id" x-text="emp.full_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Shift</label>
                    <select x-model="assignForm.shift_id" class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm font-medium text-slate-900 appearance-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white dark:bg-slate-900">
                        <option value="">Select Shift</option>
                        <template x-for="s in shifts" :key="s.id">
                            <option :value="s.id" x-text="s.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Date</label>
                    <input type="date" x-model="assignForm.date" class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm font-medium text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-900/50">
                <button @click="showAssignModal = false" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors dark:text-slate-400 dark:hover:bg-slate-800">Cancel</button>
                <button @click="assignShift()" :disabled="!assignForm.employee_id || !assignForm.shift_id || !assignForm.date || toggling" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600 disabled:opacity-50">
                    <span x-text="toggling ? 'Assigning...' : 'Save Assignment'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Shift Template Modal --}}
    <div x-show="showShiftModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition>
        <div @click.away="showShiftModal = false" class="relative w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
            
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Shift Template</h3>
                <button @click="showShiftModal = false" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Template Name</label>
                    <input type="text" x-model="shiftForm.name" placeholder="E.g., Morning Shift" class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Start Time</label>
                        <input type="time" x-model="shiftForm.start_time" class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm font-medium text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">End Time</label>
                        <input type="time" x-model="shiftForm.end_time" class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm font-medium text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Tag Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" x-model="shiftForm.color" class="h-8 w-14 cursor-pointer rounded border-0 p-0">
                        <span class="text-xs text-slate-500">Color used to identify this shift.</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-900/50">
                <button @click="showShiftModal = false" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors dark:text-slate-400 dark:hover:bg-slate-800">Cancel</button>
                <button @click="saveShift()" :disabled="!shiftForm.name || !shiftForm.start_time || !shiftForm.end_time || toggling" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600 disabled:opacity-50">
                    <span x-text="toggling ? 'Saving...' : 'Save Template'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
