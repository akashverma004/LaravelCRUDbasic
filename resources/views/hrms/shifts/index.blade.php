@extends('hrms.layouts.app')

@section('title', 'Shifts & Rostering - PeopleFlow HRMS')

@section('content')
<div x-data="shiftManager()" x-init="init()">
    
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Shift Scheduling</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage team rotations and weekly rostering.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="changeWeek(-1)" class="rounded-lg border border-slate-200 p-2 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <span class="text-sm font-bold text-slate-700 dark:text-slate-300" x-text="periodLabel"></span>
            <button @click="changeWeek(1)" class="rounded-lg border border-slate-200 p-2 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
            
            <template x-if="isAdmin">
                <div class="ml-4 flex items-center gap-2">
                    <button @click="showShiftModal = true" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold dark:border-slate-700 dark:bg-slate-800/50">Shift Templates</button>
                    <button @click="showAssignModal = true" class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600 transition-colors shadow-sm">Assign Shift</button>
                </div>
            </template>
        </div>
    </div>

    {{-- Roster Grid --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
                    <tr>
                        <th class="sticky left-0 z-10 bg-slate-50 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700 px-6 py-4 w-64">Employee</th>
                        <template x-for="day in weekDays" :key="day.date">
                            <th class="px-6 py-4 text-center border-r border-slate-200 dark:border-slate-700 last:border-0 min-w-[120px]">
                                <div class="flex flex-col">
                                    <span x-text="day.name"></span>
                                    <span class="text-[10px] text-slate-400" x-text="day.label"></span>
                                </div>
                            </th>
                        </template>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-xs">
                    <template x-if="!isAdmin && myRoster">
                        <tr>
                            <td class="sticky left-0 z-10 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 px-6 py-4 font-bold text-slate-900 dark:text-white">My Roster</td>
                            <template x-for="day in weekDays" :key="day.date">
                                <td class="px-3 py-4 border-r border-slate-200 dark:border-slate-700 last:border-0 align-top">
                                    <div class="flex flex-col gap-1">
                                        <template x-for="sch in getSchedules(null, day.date)" :key="sch.id">
                                            <div class="rounded-lg border-l-4 px-2 py-1.5 shadow-sm" :style="'border-color: ' + sch.shift.color + '; background-color: ' + sch.shift.color + '10'">
                                                <p class="font-bold text-slate-900 dark:text-white" x-text="sch.shift.name"></p>
                                                <p class="text-[9px] text-slate-500" x-text="formatTime(sch.shift.start_time) + ' - ' + formatTime(sch.shift.end_time)"></p>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </template>

                    <template x-if="isAdmin">
                        <template x-for="emp in employees" :key="emp.id">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="sticky left-0 z-10 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 px-6 py-4 font-medium text-slate-900 dark:text-white" x-text="emp.full_name"></td>
                                <template x-for="day in weekDays" :key="day.date">
                                    <td class="group px-3 py-4 border-r border-slate-200 dark:border-slate-700 last:border-0 align-top relative min-h-[80px]">
                                        <div class="flex flex-col gap-1">
                                            <template x-for="sch in getSchedules(emp.id, day.date)" :key="sch.id">
                                                <div class="relative rounded-lg border-l-4 px-2 py-1 shadow-sm group/sch" :style="'border-color: ' + sch.shift.color + '; background-color: ' + sch.shift.color + '10'">
                                                    <p class="font-bold text-[10px] text-slate-900 dark:text-white" x-text="sch.shift.name"></p>
                                                    <button @click="deleteAssignment(sch.id)" class="absolute -top-2 -right-2 hidden group-hover/sch:block rounded-full bg-slate-900 p-0.5 text-white shadow-xl dark:bg-slate-700">
                                                        <svg class="h-2 w-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
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

    {{-- Assign Modal --}}
    <div x-show="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="showAssignModal = false" class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-md p-8 shadow-2xl">
            <h3 class="text-xl font-bold mb-6">Assign Shift</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Employee</label>
                    <select x-model="assignForm.employee_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Select Employee</option>
                        <template x-for="emp in employees" :key="emp.id">
                            <option :value="emp.id" x-text="emp.full_name"></option>
                        </template>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Shift</label>
                        <select x-model="assignForm.shift_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900">
                            <option value="">Select Shift</option>
                            <template x-for="s in shifts" :key="s.id">
                                <option :value="s.id" x-text="s.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Date</label>
                        <input type="date" x-model="assignForm.date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900">
                    </div>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button @click="assignShift()" :disabled="!assignForm.employee_id || !assignForm.shift_id || !assignForm.date || toggling" class="flex-1 rounded-xl bg-cyan-500 py-3 text-sm font-bold text-white hover:bg-cyan-600">Assign</button>
                <button @click="showAssignModal = false" class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection
