@extends('hrms.layouts.app')

@section('title', 'Payroll - PeopleFlow HRMS')

@section('content')
<div x-data="payrollManager()" x-init="init()" class="space-y-6">

    {{-- Universal Notification --}}
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-4 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-4 opacity-0 scale-95"
        class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-xs font-bold text-white shadow-2xl backdrop-blur-xl dark:bg-slate-800/90"
        x-cloak
    >
        <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="h-2 w-2 rounded-full animate-pulse"></div>
        <span x-text="toast.message"></span>
    </div>

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Payroll</h1>
            <p class="mt-1 text-[11px] font-medium text-slate-500">Manage salary structures, run payroll, and track payslips.</p>
        </div>
        <div class="flex items-center gap-3">
            <template x-if="isAdmin && activeTab === 'payslips'">
                <button @click="showGenerateModal = true" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Run Payroll</span>
                </button>
            </template>
            <template x-if="isAdmin && activeTab === 'structures'">
                <button @click="openAddStructure()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>New Salary Setting</span>
                </button>
            </template>
        </div>
    </div>

    {{-- ── Overview Stats (Admin only) ────────────────────────────── --}}
    <template x-if="isAdmin">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Payroll Cost --}}
            <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-cyan-500/30 dark:border-white/5 dark:bg-slate-900/50">
                <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-cyan-500/5 blur-lg"></div>
                <p class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Total Paid</p>
                <p class="mt-2 text-xl font-black tracking-tight text-slate-900 dark:text-white" x-text="formatCurrency(stats.totalPayroll)"></p>
                <div class="mt-3 flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                    <span class="text-[9px] font-bold text-slate-400">All time</span>
                </div>
            </div>
            {{-- Employees on Payroll --}}
            <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-indigo-500/30 dark:border-white/5 dark:bg-slate-900/50">
                <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-indigo-500/5 blur-lg"></div>
                <p class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">On Payroll</p>
                <p class="mt-2 text-xl font-black tracking-tight text-slate-900 dark:text-white" x-text="stats.totalEmployees"></p>
                <div class="mt-3 flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 rounded-full bg-indigo-500"></div>
                    <span class="text-[9px] font-bold text-slate-400">Employees</span>
                </div>
            </div>
            {{-- Draft Payslips --}}
            <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-amber-500/30 dark:border-white/5 dark:bg-slate-900/50">
                <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-amber-500/5 blur-lg"></div>
                <p class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Draft</p>
                <p class="mt-2 text-xl font-black tracking-tight text-slate-900 dark:text-white" x-text="stats.draftCount"></p>
                <div class="mt-3 flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></div>
                    <span class="text-[9px] font-bold text-slate-400">Pending payment</span>
                </div>
            </div>
            {{-- Paid Payslips --}}
            <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-emerald-500/30 dark:border-white/5 dark:bg-slate-900/50">
                <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-emerald-500/5 blur-lg"></div>
                <p class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Paid</p>
                <p class="mt-2 text-xl font-black tracking-tight text-slate-900 dark:text-white" x-text="stats.paidCount"></p>
                <div class="mt-3 flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                    <span class="text-[9px] font-bold text-slate-400">Completed</span>
                </div>
            </div>
        </div>
    </template>

    {{-- Tabs --}}
    <template x-if="isAdmin">
        <div class="flex gap-2 p-1 bg-slate-100 rounded-xl dark:bg-slate-900 w-fit">
            <button @click="activeTab = 'payslips'" class="px-4 py-1.5 text-xs font-bold rounded-lg transition-colors" :class="activeTab === 'payslips' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'">Payslips</button>
            <button @click="activeTab = 'structures'" class="px-4 py-1.5 text-xs font-bold rounded-lg transition-colors" :class="activeTab === 'structures' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'">Salary Settings</button>
        </div>
    </template>

    {{-- Content --}}
    <div class="space-y-6">

        {{-- ── Payslips Tab ──────────────────────────────────────── --}}
        <div x-show="activeTab === 'payslips'">
            {{-- Search --}}
            <div x-show="isAdmin" class="mb-4">
                <div class="relative max-w-xs">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" x-model="searchQuery" placeholder="Search by name or month..." class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-9 pr-4 text-xs text-slate-900 placeholder-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500" />
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
                {{-- Loading State --}}
                <div x-show="loading" class="flex items-center justify-center py-16">
                    <svg class="h-5 w-5 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </div>

                {{-- Empty State --}}
                <div x-show="!loading && filteredPayslips.length === 0" class="flex flex-col items-center justify-center py-16 gap-3">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                        <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-500">No payslips found</p>
                    <p class="text-[10px] text-slate-400" x-show="isAdmin">Click "Run Payroll" to generate payslips for a month.</p>
                </div>

                {{-- Table --}}
                <div x-show="!loading && filteredPayslips.length > 0" class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 dark:bg-slate-950/50 dark:border-slate-800">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Month</th>
                                <th x-show="isAdmin" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Employee</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Base</th>
                                <th class="hidden sm:table-cell px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Earnings</th>
                                <th class="hidden sm:table-cell px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Deductions</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Net Pay</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <template x-for="ps in filteredPayslips" :key="ps.id">
                                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white" x-text="ps.month"></p>
                                        {{-- Proration indicator --}}
                                        <template x-if="ps.details?.proration">
                                            <span class="mt-0.5 inline-flex items-center gap-1 rounded bg-violet-50 px-1.5 py-0.5 text-[8px] font-bold text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                                                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                                                Prorated
                                            </span>
                                        </template>
                                    </td>
                                    <td x-show="isAdmin" class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="h-6 w-6 flex items-center justify-center rounded bg-slate-100 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400" x-text="ps.employee?.full_name?.charAt(0) || '?'"></div>
                                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300" x-text="ps.employee?.full_name || 'Unknown'"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="formatCurrency(ps.base_salary)"></span>
                                    </td>
                                    <td class="hidden sm:table-cell px-6 py-4 text-right">
                                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400" x-text="'+' + formatCurrency(ps.total_allowances)"></span>
                                    </td>
                                    <td class="hidden sm:table-cell px-6 py-4 text-right">
                                        <span class="text-xs font-bold text-rose-600 dark:text-rose-400" x-text="'-' + formatCurrency(ps.total_deductions)"></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-xs font-black text-slate-900 dark:text-white" x-text="formatCurrency(ps.net_pay)"></p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                            :class="ps.status === 'paid' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'"
                                            x-text="ps.status">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-1">
                                            <button @click="viewPayslip(ps)" class="h-7 w-7 flex items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-cyan-600 dark:hover:bg-white/5 transition-colors" title="View Details">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </button>
                                            <template x-if="isAdmin && ps.status === 'draft'">
                                                <button @click="markAsPaid(ps)" class="h-7 w-7 flex items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-emerald-600 dark:hover:bg-white/5 transition-colors" title="Mark Paid">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                                </button>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Salary Settings Tab (admin only) ─────────────────── --}}
        <div x-show="isAdmin && activeTab === 'structures'" style="display: none;">
            {{-- Loading --}}
            <div x-show="loading" class="flex items-center justify-center py-16">
                <svg class="h-5 w-5 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </div>

            {{-- Empty State --}}
            <div x-show="!loading && structures.length === 0" class="flex flex-col items-center justify-center py-16 gap-3 rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/50">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                    <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p class="text-xs font-bold text-slate-500">No salary settings configured</p>
                <p class="text-[10px] text-slate-400">Click "New Salary Setting" to set up an employee's pay structure.</p>
            </div>

            {{-- Grid of structure cards --}}
            <div x-show="!loading && structures.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <template x-for="s in structures" :key="s.id">
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900/50 transition-all hover:border-cyan-500/30 dark:hover:border-cyan-500/20">
                        {{-- Employee Header --}}
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 flex items-center justify-center rounded bg-slate-100 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400" x-text="s.employee?.full_name?.charAt(0) || '?'"></div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white" x-text="s.employee?.full_name || 'Unknown'"></h4>
                                    <p class="text-[9px] text-slate-500 uppercase font-bold" x-text="s.employee?.job_title || 'No Title'"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="editStructure(s)" class="h-7 w-7 flex items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-cyan-600 dark:hover:bg-white/5 transition-colors" title="Edit">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                                </button>
                                <button @click="deleteStructure(s)" class="h-7 w-7 flex items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-rose-600 dark:hover:bg-white/5 transition-colors" title="Delete">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Resigned badge --}}
                        <template x-if="s.employee?.status === 'resigned'">
                            <div class="mb-4 flex items-center gap-1.5 rounded-lg bg-rose-50 px-3 py-1.5 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20">
                                <svg class="h-3 w-3 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                                <span class="text-[9px] font-black uppercase tracking-widest text-rose-700 dark:text-rose-400">Resigned</span>
                                <template x-if="s.employee?.last_working_day">
                                    <span class="text-[8px] font-bold text-rose-500 dark:text-rose-400/70" x-text="'· LWD: ' + new Date(s.employee.last_working_day).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })"></span>
                                </template>
                            </div>
                        </template>

                        {{-- Base Salary --}}
                        <div class="mb-4 rounded-lg bg-slate-50 p-3 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Base Salary</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white" x-text="formatCurrency(s.base_salary)"></p>
                        </div>

                        {{-- Allowances & Deductions --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Allowances</p>
                                <div class="space-y-1">
                                    <template x-if="!s.allowances || s.allowances.length === 0">
                                        <span class="text-[10px] text-slate-400 italic">None</span>
                                    </template>
                                    <template x-for="a in s.allowances" :key="a.name">
                                        <div class="flex justify-between items-center text-[10px]">
                                             <span class="text-slate-600 dark:text-slate-400 truncate pr-1" x-text="a.name"></span>
                                             <span class="font-bold text-emerald-600" x-text="'+₹' + parseFloat(a.amount).toLocaleString('en-IN')"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Deductions</p>
                                <div class="space-y-1">
                                    <template x-if="!s.deductions || s.deductions.length === 0">
                                        <span class="text-[10px] text-slate-400 italic">None</span>
                                    </template>
                                    <template x-for="d in s.deductions" :key="d.name">
                                         <div class="flex justify-between items-center text-[10px]">
                                             <span class="text-slate-600 dark:text-slate-400 truncate pr-1" x-text="d.name"></span>
                                             <span class="font-bold text-rose-600" x-text="'-₹' + parseFloat(d.amount).toLocaleString('en-IN')"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── Modals ─────────────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}

    {{-- Run Payroll Modal --}}
    <div x-show="showGenerateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="showGenerateModal = false" class="w-full max-w-xs rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Run Payroll</h3>
                <button @click="showGenerateModal = false" class="text-slate-400 hover:text-slate-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-5">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Select Month</label>
                <input type="month" x-model="generateMonth" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                <p class="mt-2 text-[9px] text-slate-400">Resigned employees with a last working day in this month will have their salary prorated automatically.</p>
            </div>
            <div class="flex justify-end gap-3 bg-slate-50 px-5 py-3 dark:bg-white/5 dark:border-white/5 items-center">
                <button @click="showGenerateModal = false" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cancel</button>
                <button @click="generatePayslips()" :disabled="!generateMonth || generating" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-6 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 disabled:opacity-50 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <span x-show="!generating" class="flex items-center gap-2">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Run
                    </span>
                    <span x-show="generating" class="flex items-center gap-2">
                        <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Processing
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- Add / Edit Salary Structure Modal --}}
    <div x-show="showStructureModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="showStructureModal = false" class="w-full max-w-md max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800 sticky top-0 bg-white dark:bg-slate-900 z-10">
                <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="isEditing ? 'Edit Salary Structure' : 'New Salary Structure'"></h3>
                <button @click="showStructureModal = false" class="text-slate-400 hover:text-slate-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-5 space-y-5">
                {{-- Employee --}}
                <div x-show="!isEditing">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Employee</label>
                    <select x-model="structureForm.employee_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white dark:bg-slate-900">
                        <option value="">Select Employee</option>
                        <template x-for="emp in availableEmployees" :key="emp.id">
                            <option :value="emp.id" x-text="emp.full_name + (emp.job_title ? ' · ' + emp.job_title : '')"></option>
                        </template>
                    </select>
                </div>

                {{-- Base Salary --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Base Salary (₹)</label>
                    <input type="number" x-model="structureForm.base_salary" placeholder="e.g. 50000" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" />
                </div>

                {{-- Allowances --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Allowances</label>
                        <button @click="addAllowance()" type="button" class="text-[9px] font-bold text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300 transition-colors">+ Add</button>
                    </div>
                    <template x-for="(a, i) in structureForm.allowances" :key="i">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" x-model="structureForm.allowances[i].name" placeholder="HRA, Travel..." class="flex-1 rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-xs text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" />
                            <input type="number" x-model="structureForm.allowances[i].amount" placeholder="Amount" class="w-24 rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-xs text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" />
                            <button @click="removeAllowance(i)" class="text-slate-400 hover:text-rose-500 transition-colors p-1">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </template>
                    <div x-show="structureForm.allowances.length === 0" class="rounded-lg bg-slate-50 p-3 text-center dark:bg-slate-950/40">
                        <p class="text-[9px] text-slate-400 italic">No allowances configured</p>
                    </div>
                </div>

                {{-- Deductions --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Deductions</label>
                        <button @click="addDeduction()" type="button" class="text-[9px] font-bold text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300 transition-colors">+ Add</button>
                    </div>
                    <template x-for="(d, i) in structureForm.deductions" :key="i">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" x-model="structureForm.deductions[i].name" placeholder="Tax, PF..." class="flex-1 rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-xs text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" />
                            <input type="number" x-model="structureForm.deductions[i].amount" placeholder="Amount" class="w-24 rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-xs text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" />
                            <button @click="removeDeduction(i)" class="text-slate-400 hover:text-rose-500 transition-colors p-1">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </template>
                    <div x-show="structureForm.deductions.length === 0" class="rounded-lg bg-slate-50 p-3 text-center dark:bg-slate-950/40">
                        <p class="text-[9px] text-slate-400 italic">No deductions configured</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 bg-slate-50 px-5 py-3 dark:bg-white/5 items-center sticky bottom-0 border-t border-slate-100 dark:border-slate-800">
                <button @click="showStructureModal = false" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cancel</button>
                <button @click="saveStructure()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-6 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    <span x-text="isEditing ? 'Update' : 'Save'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Payslip Detail Modal --}}
    <div x-show="showDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="showDetailsModal = false" class="w-full max-w-md max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800 sticky top-0 bg-white dark:bg-slate-900 z-10">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Payslip Details</h3>
                    <p class="text-[10px] text-slate-500 mt-0.5" x-text="selectedPayslip?.month"></p>
                </div>
                <button @click="showDetailsModal = false" class="text-slate-400 hover:text-slate-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <template x-if="selectedPayslip">
                <div class="p-5 space-y-5">
                    {{-- Employee Info --}}
                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-4 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800">
                        <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-cyan-50 text-sm font-black text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400" x-text="selectedPayslip.employee?.full_name?.charAt(0) || '?'"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-900 dark:text-white" x-text="selectedPayslip.employee?.full_name"></p>
                            <p class="text-[9px] text-slate-500" x-text="selectedPayslip.month"></p>
                        </div>
                        <span class="ml-auto rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                            :class="selectedPayslip.status === 'paid' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'"
                            x-text="selectedPayslip.status"></span>
                    </div>

                    {{-- Proration Notice --}}
                    <template x-if="selectedPayslip.details?.proration">
                        <div class="flex items-start gap-2 rounded-xl bg-violet-50 p-4 dark:bg-violet-500/10 border border-violet-100 dark:border-violet-500/20">
                            <svg class="h-4 w-4 text-violet-600 dark:text-violet-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-violet-800 dark:text-violet-300">Prorated Salary</p>
                                <p class="text-[9px] text-violet-600 dark:text-violet-400 mt-1" x-text="selectedPayslip.details.proration.reason"></p>
                                <p class="text-[9px] text-violet-500 mt-1" x-text="selectedPayslip.details.proration.worked_days + ' of ' + selectedPayslip.details.proration.total_days + ' days (' + Math.round(selectedPayslip.details.proration.ratio * 100) + '%)'"></p>
                            </div>
                        </div>
                    </template>

                    {{-- Earnings --}}
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Earnings</p>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center rounded-lg bg-slate-50 px-4 py-2.5 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400">Base Salary</span>
                                <span class="text-xs font-bold text-slate-900 dark:text-white" x-text="formatCurrency(selectedPayslip.base_salary)"></span>
                            </div>
                            <template x-if="selectedPayslip.details?.allowances">
                                <template x-for="a in selectedPayslip.details.allowances" :key="a.name">
                                    <div class="flex justify-between items-center rounded-lg bg-slate-50 px-4 py-2.5 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800">
                                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400" x-text="a.name"></span>
                                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400" x-text="'+' + formatCurrency(a.amount)"></span>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>

                    {{-- Deductions --}}
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Deductions</p>
                        <div class="space-y-2">
                            <template x-if="selectedPayslip.details?.deductions">
                                <template x-for="d in selectedPayslip.details.deductions" :key="d.name">
                                    <div class="flex justify-between items-center rounded-lg bg-slate-50 px-4 py-2.5 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800">
                                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400" x-text="d.name"></span>
                                        <span class="text-xs font-bold text-rose-600 dark:text-rose-400" x-text="'-' + formatCurrency(d.amount)"></span>
                                    </div>
                                </template>
                            </template>
                            <template x-if="selectedPayslip.details?.unpaid_leave_deduction">
                                <div class="flex justify-between items-center rounded-lg bg-rose-50 px-4 py-2.5 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20">
                                    <div>
                                        <span class="text-[10px] font-bold text-rose-700 dark:text-rose-400">Unpaid Leave</span>
                                        <span class="text-[8px] ml-1 font-bold text-rose-500" x-text="'(' + selectedPayslip.details.unpaid_leave_deduction.days + ' days)'"></span>
                                    </div>
                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400" x-text="'-' + formatCurrency(selectedPayslip.details.unpaid_leave_deduction.amount)"></span>
                                </div>
                            </template>
                            <div x-show="!selectedPayslip.details?.deductions?.length && !selectedPayslip.details?.unpaid_leave_deduction" class="text-center py-2">
                                <span class="text-[10px] text-slate-400 italic">No deductions</span>
                            </div>
                        </div>
                    </div>

                    {{-- Net Pay --}}
                    <div class="rounded-xl bg-gradient-to-r from-cyan-50 to-indigo-50 p-4 dark:from-cyan-500/10 dark:to-indigo-500/10 border border-cyan-100 dark:border-cyan-500/20">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Net Pay (Take Home)</p>
                            </div>
                            <p class="text-xl font-black text-slate-900 dark:text-white" x-text="formatCurrency(selectedPayslip.net_pay)"></p>
                        </div>
                    </div>

                    {{-- Period --}}
                    <div class="flex items-center justify-between text-[9px] font-bold text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span x-text="'Period: ' + new Date(selectedPayslip.period_start).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' }) + ' – ' + new Date(selectedPayslip.period_end).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })"></span>
                        <span x-text="'ID: #' + selectedPayslip.id"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
