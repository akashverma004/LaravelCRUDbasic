@extends('hrms.layouts.app')

@section('title', 'Time Off - PeopleFlow HRMS')

@section('content')
<div x-data="leaveManager()" x-init="init()" class="relative space-y-4 pb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-white/5">
        <div>
            <div class="flex items-center gap-2 mb-0.5">
                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Self Service</span>
                <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Time Off</span>
            </div>
            <h1 class="text-lg font-black tracking-tight text-slate-900 dark:text-white uppercase">
                {{ isset($employee) ? 'Hi, ' . explode(' ', $employee->full_name)[0] : 'Time Off' }}
            </h1>
            <p class="mt-0.5 text-[9px] font-bold text-slate-400 uppercase tracking-wide">
                Available balance: <span class="text-cyan-600 dark:text-cyan-400 font-black" x-text="Object.values(balances).reduce((a, b) => a + b.remaining, 0)"></span> days.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('leaves.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-[9px] font-black uppercase tracking-widest text-slate-600 shadow-sm transition-all hover:bg-slate-50 dark:border-white/5 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                <span>Calendar</span>
            </a>
            <button @click="openModal()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-4 py-2 text-[9px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Book Time Off</span>
            </button>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['Days Left', 'Object.values(balances).reduce((a, b) => a + b.remaining, 0)', 'Available balance', 'cyan'],
            ['Approved', 'stats.approved', 'Requests approved', 'emerald'],
            ['Pending', 'stats.pending', 'Awaiting review', 'amber'],
            ['Team Away', 'whoIsAway.upcoming.length', 'Next 7 days', 'indigo']
        ] as [$title, $val, $desc, $color])
        <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-all hover:shadow-md dark:border-white/5 dark:bg-slate-900/50">
            <h3 class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-2">{{ $title }}</h3>
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-xl font-black leading-none tracking-tight text-slate-900 dark:text-white" x-text="{{ $val }}"></p>
                    <p class="mt-1 text-[8px] font-bold text-slate-500 uppercase tracking-wide">{{ $desc }}</p>
                </div>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg
                    @if($color === 'cyan') bg-cyan-50 text-cyan-500 dark:bg-cyan-500/10
                    @elseif($color === 'emerald') bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10
                    @elseif($color === 'amber') bg-amber-50 text-amber-500 dark:bg-amber-500/10
                    @else bg-indigo-50 text-indigo-500 dark:bg-indigo-500/10 @endif">
                    @if($color === 'cyan') <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($color === 'emerald') <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($color === 'amber') <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg> @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Left Column: Balances & History --}}
        <div class="space-y-6 lg:col-span-8">
            {{-- Balances Grid --}}
            <section>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 flex items-center gap-3">
                        Your Allowances
                        <span class="h-px w-16 bg-slate-100 dark:bg-white/5"></span>
                    </h3>
                    <template x-if="loading">
                        <div class="flex items-center gap-2">
                            <div class="h-2.5 w-2.5 animate-spin rounded-full border-2 border-cyan-500 border-t-transparent"></div>
                            <span class="text-[9px] font-bold text-slate-400 animate-pulse">Updating...</span>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                    {{-- Skeleton Loader --}}
                    <template x-if="loading && Object.keys(balances).length === 0">
                        <template x-for="i in 4">
                            <div class="animate-pulse rounded-xl border border-slate-200 bg-white p-3 dark:border-white/5 dark:bg-slate-900/50">
                                <div class="flex justify-between mb-3">
                                    <div class="h-7 w-7 rounded-lg bg-slate-100 dark:bg-white/5"></div>
                                    <div class="h-2.5 w-10 rounded bg-slate-100 dark:bg-white/5"></div>
                                </div>
                                <div class="h-5 w-16 rounded bg-slate-100 dark:bg-white/5 mb-2.5"></div>
                                <div class="h-1 w-full rounded-full bg-slate-50 dark:bg-white/5"></div>
                            </div>
                        </template>
                    </template>

                    {{-- Actual Content --}}
                    <template x-if="!loading || Object.keys(balances).length > 0">
                        <template x-for="(bal, type) in balances" :key="type">
                            <div class="group relative rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-all hover:shadow-md dark:border-white/5 dark:bg-slate-900/50">
                                <div class="mb-3 flex items-center justify-between">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg transition-all duration-500 group-hover:scale-105"
                                        :class="{
                                            'bg-rose-50 text-rose-500 dark:bg-rose-500/10': type === 'annual',
                                            'bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10': type === 'sick',
                                            'bg-violet-50 text-violet-500 dark:bg-violet-500/10': type === 'casual',
                                            'bg-slate-50 text-slate-500 dark:bg-slate-500/10': type === 'unpaid'
                                        }">
                                        <template x-if="type === 'annual'"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg></template>
                                        <template x-if="type === 'sick'"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A3.333 3.333 0 0016.5 3c-1.833 0-3.333 1.5-3.333 3.333 0 1.833 1.5 3.333 3.333 3.333a3.333 3.333 0 003.333-3.333c0-1.833-1.5-3.333-3.333-3.333z" /></svg></template>
                                        <template x-if="type === 'casual'"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></template>
                                        <template x-if="type === 'unpaid'"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></template>
                                    </div>
                                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400" x-text="type"></span>
                                </div>
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-xl font-black tracking-tight text-slate-900 dark:text-white" x-text="bal.remaining"></span>
                                    <span class="text-[9px] font-bold text-slate-500 uppercase">/ <span x-text="bal.limit"></span></span>
                                </div>
                                <div class="mt-3 h-1 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-white/5">
                                    <div class="h-full rounded-full transition-all duration-1000" 
                                        :class="{
                                            'bg-rose-500': type === 'annual',
                                            'bg-emerald-500': type === 'sick',
                                            'bg-violet-500': type === 'casual',
                                            'bg-slate-500': type === 'unpaid'
                                        }"
                                        :style="'width: ' + usagePercent(bal) + '%'"></div>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
            </section>

            {{-- History Section --}}
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 flex items-center gap-2">
                        Recent Activity
                        <span class="h-px w-8 bg-slate-100 dark:bg-white/5"></span>
                    </h3>
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900 overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 dark:bg-white/5 border-b border-slate-100 dark:border-white/5">
                            <tr>
                                <th class="px-4 py-2 text-[8px] font-black uppercase tracking-widest text-slate-400">Type & Date</th>
                                <th class="px-4 py-2 text-[8px] font-black uppercase tracking-widest text-slate-400">Duration</th>
                                <th class="px-4 py-2 text-right text-[8px] font-black uppercase tracking-widest text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <template x-if="loading && leaves.length === 0">
                                <template x-for="i in 3">
                                    <tr class="animate-pulse">
                                        <td class="px-4 py-3"><div class="h-3 w-32 bg-slate-100 dark:bg-white/5 rounded"></div></td>
                                        <td class="px-4 py-3"><div class="h-3 w-16 bg-slate-100 dark:bg-white/5 rounded"></div></td>
                                        <td class="px-4 py-3 text-right"><div class="h-3 w-20 bg-slate-100 dark:bg-white/5 rounded ml-auto"></div></td>
                                    </tr>
                                </template>
                            </template>
                            <template x-if="!loading || leaves.length > 0">
                                <template x-for="leave in leaves" :key="leave.id">
                                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="h-8 w-8 flex items-center justify-center rounded-lg text-[9px] font-black shadow-sm"
                                                    :class="{
                                                        'bg-rose-50 text-rose-500 dark:bg-rose-500/10': leave.leave_type === 'annual',
                                                        'bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10': leave.leave_type === 'sick',
                                                        'bg-violet-50 text-violet-500 dark:bg-violet-500/10': leave.leave_type === 'casual',
                                                        'bg-slate-50 text-slate-500 dark:bg-slate-500/10': leave.leave_type === 'unpaid'
                                                    }">
                                                    <span x-text="leave.leave_type.charAt(0).toUpperCase()"></span>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight" x-text="leave.leave_type"></p>
                                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wide leading-none mt-0.5" x-text="formatDateShort(leave.start_date) + ' - ' + formatDateShort(leave.end_date)"></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest bg-slate-100 dark:bg-white/5 px-1.5 py-0.5 rounded-md" x-text="leave.days + ' Days'"></span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[8px] font-black uppercase tracking-widest"
                                                    :class="{
                                                        'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10': leave.status === 'approved',
                                                        'bg-rose-50 text-rose-600 dark:bg-rose-500/10': leave.status === 'rejected',
                                                        'bg-amber-50 text-amber-600 dark:bg-amber-500/10': leave.status === 'pending'
                                                    }">
                                                    <span class="h-1 w-1 rounded-full bg-current"></span>
                                                    <span x-text="leave.status"></span>
                                                </span>
                                                
                                                <template x-if="leave.status === 'pending'">
                                                    <div class="flex items-center gap-1">
                                                        <button @click="editLeave(leave)" class="p-1.5 text-slate-400 hover:text-cyan-500 transition-colors">
                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                        </button>
                                                        <button @click="deleteLeave(leave.id)" class="p-1.5 text-slate-400 hover:text-rose-500 transition-colors">
                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                            <template x-if="!loading && leaves.length === 0">
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">No leave history found.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- Right Column: Side Info --}}
        <div class="space-y-6 lg:col-span-4">
            {{-- Away Today --}}
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Away Today</h3>
                    <span class="rounded-lg bg-slate-100 px-2 py-0.5 text-[10px] font-black text-slate-500 dark:bg-white/5" x-text="whoIsAway.today.length"></span>
                </div>
                <div class="grid gap-3">
                    <template x-if="whoIsAway.today.length === 0">
                        <div class="rounded-2xl border border-dashed border-slate-200 p-6 text-center dark:border-white/5">
                            <p class="text-[11px] font-bold text-slate-400 italic uppercase tracking-widest">Everyone's here today</p>
                        </div>
                    </template>
                    <template x-for="person in whoIsAway.today" :key="person.id">
                        <div class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm transition-all hover:bg-slate-50 dark:border-white/5 dark:bg-slate-900/50">
                            <div class="relative">
                                <template x-if="person.photo">
                                    <img :src="person.photo" class="h-9 w-9 rounded-xl object-cover ring-2 ring-slate-100 dark:ring-white/5">
                                </template>
                                <template x-if="!person.photo">
                                    <div class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-100 text-[10px] font-black text-slate-500 dark:bg-white/5 dark:text-cyan-400" x-text="person.name.charAt(0)"></div>
                                </template>
                                <div class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white bg-rose-500 dark:border-slate-900"></div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight" x-text="person.name"></p>
                                <p class="truncate text-[9px] font-bold text-slate-500 uppercase tracking-widest" x-text="person.type"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </section>


        </div>
    </div>

    <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.away="showModal = false" class="w-full max-w-xs rounded-[20px] bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/5 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0">
            <div class="flex items-center justify-between border-b border-slate-50 px-5 py-3 dark:border-white/5">
                <h3 class="text-[9px] font-black text-slate-900 dark:text-white uppercase tracking-[0.2em]" x-text="isEditing ? 'Edit Request' : 'Book Time Off'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="px-5 py-4">
                <form @submit.prevent="saveLeave" id="leaveForm" class="space-y-4">
                    <template x-if="isAdmin">
                        <div class="space-y-1">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Team Member</label>
                            <select x-model="form.employee_id" class="w-full rounded-lg border border-slate-100 bg-slate-50/50 px-3 py-2 text-[12px] font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white" required>
                                <option value="">Select Employee</option>
                                <template x-for="emp in employees" :key="emp.id">
                                    <option :value="emp.id" x-text="emp.full_name"></option>
                                </template>
                            </select>
                        </div>
                    </template>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Leave Type</label>
                            <select x-model="form.leave_type" class="w-full rounded-lg border border-slate-100 bg-slate-50/50 px-3 py-2 text-[12px] font-bold text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white" required>
                                <option value="annual">Annual</option>
                                <option value="sick">Sick</option>
                                <option value="casual">Casual</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Duration</label>
                            <select x-model="form.leave_session" class="w-full rounded-lg border border-slate-100 bg-slate-50/50 px-3 py-2 text-[12px] font-bold text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white" required>
                                <option value="full_day">Full Day</option>
                                <option value="morning">Morning</option>
                                <option value="evening">Evening</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Start Date</label>
                            <input type="date" x-model="form.start_date" class="w-full rounded-lg border border-slate-100 bg-slate-50/50 px-3 py-2 text-[12px] font-bold text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white" required>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">End Date</label>
                            <input type="date" x-model="form.end_date" class="w-full rounded-lg border border-slate-100 bg-slate-50/50 px-3 py-2 text-[12px] font-bold text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white" required>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Message (Optional)</label>
                        <textarea x-model="form.reason" rows="2" class="w-full rounded-lg border border-slate-100 bg-slate-50/50 px-3 py-2 text-[12px] font-medium text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white" placeholder="Adding some context..."></textarea>
                    </div>
                </form>
            </div>

            <div class="flex justify-end gap-2 bg-slate-50/50 px-5 py-3 dark:bg-white/5">
                <button @click="showModal = false" class="text-[8px] font-bold uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cancel</button>
                <button form="leaveForm" @click="saveLeave()" :disabled="saving" class="group relative flex items-center justify-center gap-2 rounded-lg bg-slate-900 border border-white/10 px-4 py-2 text-[9px] font-black uppercase tracking-widest text-white shadow-xl shadow-slate-900/20 hover:bg-cyan-600 transition-all active:scale-95 disabled:opacity-50 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <span x-show="!saving" x-text="isEditing ? 'Update' : 'Request'"></span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <svg class="h-2.5 w-2.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Wait...
                    </span>
                </button>
            </div>
        </div>
    </div>
    {{-- Universal Notification --}}
    <div 
        x-show="toast.show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-4 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-4 opacity-0 scale-95"
        class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-xs font-bold text-white shadow-2xl backdrop-blur-xl"
        x-cloak
    >
        <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="h-2 w-2 rounded-full animate-pulse"></div>
        <span x-text="toast.message"></span>
    </div>
</div>
@endsection
