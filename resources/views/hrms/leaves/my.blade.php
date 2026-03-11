@extends('hrms.layouts.app')

@section('title', 'My Leave History - PeopleFlow HRMS')

@section('content')
<div x-data="leaveManager()" x-init="init()">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">My Leave History</h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium">Track and manage your leave requests</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('leaves.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">Who's Away Calendar</a>
            <button @click="openModal()" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition-all">+ New Request</button>
        </div>
    </div>

    <div class="rounded-[2rem] border border-slate-200 bg-white/50 backdrop-blur-xl overflow-hidden shadow-sm dark:border-slate-800 dark:bg-slate-900/50 relative min-h-[300px]">
        <div x-show="loading" class="absolute inset-x-0 top-0 h-1 overflow-hidden bg-slate-100 dark:bg-slate-800 rounded-t-[2rem]">
            <div class="absolute h-full w-1/3 bg-indigo-500 dark:bg-indigo-400 animate-pulse"></div>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Leave Type</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Duration</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Requested On</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Status</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <template x-for="leave in leaves" :key="leave.id">
                    <tr class="group transition-colors hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl"
                                    :class="{
                                        'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400': leave.leave_type === 'annual',
                                        'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400': leave.leave_type === 'sick',
                                        'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400': leave.leave_type === 'casual',
                                        'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400': !['annual', 'sick', 'casual'].includes(leave.leave_type)
                                    }">
                                    <template x-if="leave.leave_type === 'annual'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </template>
                                    <template x-if="leave.leave_type === 'sick'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.022.547l-2.387 2.387a2 2 0 102.828 2.828l1.414-1.414 1.414 1.414a2 2 0 002.828 0l1.414-1.414 1.414 1.414a2 2 0 002.828 0l1.414-1.414 1.414 1.414a2 2 0 102.828-2.828l-2.387-2.387z"/></svg>
                                    </template>
                                    <template x-if="!['annual', 'sick'].includes(leave.leave_type)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </template>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-700 dark:text-slate-200 capitalize" x-text="leave.leave_type + ' Leave'"></p>
                                    <p class="text-xs text-slate-400 font-medium capitalize" x-text="leave.leave_session.replace('_', ' ')"></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <p class="font-bold text-slate-700 dark:text-slate-200" x-text="formatDateShort(leave.start_date) + ' - ' + formatDateShort(leave.end_date)"></p>
                            <p class="text-xs text-slate-400 font-medium" x-text="leave.days + ' days requested'"></p>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-sm font-bold text-slate-600 dark:text-slate-400" x-text="formatDateFull(leave.created_at)"></p>
                        </td>
                        <td class="px-6 py-5">
                            <template x-if="leave.status === 'approved'">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Approved
                                </span>
                            </template>
                            <template x-if="leave.status === 'rejected'">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-3 py-1 text-[10px] font-black uppercase text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Rejected
                                </span>
                            </template>
                            <template x-if="leave.status === 'pending'">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-[10px] font-black uppercase text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                                </span>
                            </template>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <a :href="'/leaves/' + leave.id" class="text-slate-400 hover:text-indigo-600 transition-colors" title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <template x-if="leave.status === 'pending'">
                                    <div class="flex items-center gap-3">
                                        <button @click="editLeave(leave)" class="text-slate-400 hover:text-amber-500 transition-colors" title="Edit Request">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="deleteLeave(leave.id)" class="text-slate-400 hover:text-rose-500 transition-colors" title="Delete Request">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>
                <template x-if="leaves.length === 0 && !loading">
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="rounded-full bg-slate-50 p-6 dark:bg-slate-800">
                                    <svg class="w-12 h-12 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="mt-4 text-slate-500 font-bold">No leave requests found.</p>
                                <button @click="openModal()" class="mt-2 text-indigo-600 font-bold hover:underline">Submit your first request</button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- Add/Edit Leave Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="showModal = false" class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-lg p-8 shadow-2xl">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white" x-text="isEditing ? 'Update Leave Request' : 'New Leave Request'"></h3>
                <p class="text-sm text-slate-500">Provide details for your absence</p>
            </div>
            
            <form @submit.prevent="saveLeave" class="space-y-4">
                <template x-if="isAdmin">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Employee</label>
                        <select x-model="form.employee_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
                            <option value="">Select Employee</option>
                            <template x-for="emp in employees" :key="emp.id">
                                <option :value="emp.id" x-text="emp.full_name"></option>
                            </template>
                        </select>
                    </div>
                </template>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Leave Type</label>
                        <select x-model="form.leave_type" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
                            <option value="">Select Type</option>
                            <option value="annual">Annual</option>
                            <option value="sick">Sick</option>
                            <option value="casual">Casual</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Session</label>
                        <select x-model="form.leave_session" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
                            <option value="full_day">Full day</option>
                            <option value="morning">Morning</option>
                            <option value="evening">Evening</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Start Date</label>
                        <input type="date" x-model="form.start_date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm appearance-auto dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">End Date</label>
                        <input type="date" x-model="form.end_date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm appearance-auto dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Reason</label>
                    <textarea x-model="form.reason" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white" required></textarea>
                </div>

                <div class="mt-8 flex gap-3 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                    <button type="submit" class="flex-1 rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white hover:bg-indigo-700 shadow-md">Submit</button>
                    <button type="button" @click="showModal = false" class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
