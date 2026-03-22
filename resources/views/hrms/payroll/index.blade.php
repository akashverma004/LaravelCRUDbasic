@extends('hrms.layouts.app')

@section('title', 'Payroll - PeopleFlow HRMS')

@section('content')
<div x-data="payrollManager()" x-init="init()" class="space-y-6">

    {{-- ── Toast ─────────────────────────────────────────────── --}}
    <div
        x-show="toast.show" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 right-6 z-[200] flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-900/95 py-3 px-5 text-xs font-bold text-white shadow-2xl backdrop-blur-xl dark:bg-slate-800/95"
    >
        <span :class="toast.type==='success' ? 'bg-emerald-400' : 'bg-rose-400'" class="h-2 w-2 rounded-full animate-pulse shrink-0"></span>
        <span x-text="toast.message"></span>
    </div>

    {{-- ── Slide-in Compensation Panel (Deel-style) ──────────── --}}
    {{-- Backdrop --}}
    <div x-show="showStructureModal" x-cloak @click="showStructureModal = false"
        class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-end="opacity-0">
    </div>

    {{-- Slide Panel --}}
    <div x-show="showStructureModal" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 bottom-0 z-50 w-full max-w-md bg-white shadow-2xl dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 flex flex-col overflow-hidden"
    >
        {{-- Panel Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <template x-if="structureModalEmployee">
                    <div class="h-9 w-9 rounded-full flex items-center justify-center text-sm font-black bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400"
                        x-text="structureModalEmployee?.full_name?.split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase()">
                    </div>
                </template>
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white" x-text="structureModalEmployee?.full_name || 'Employee'"></h3>
                    <p class="text-[10px] text-slate-400 mt-0.5" x-text="structureModalEmployee?.job_title || ''"></p>
                </div>
            </div>
            <button @click="showStructureModal = false" class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-white transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- Panel Title --}}
        <div class="px-6 pt-5 pb-4 shrink-0 border-b border-slate-100 dark:border-slate-800">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-500 dark:text-indigo-400 mb-1">Compensation & Salary</p>
            <p class="text-[11px] text-slate-500">Set the Annual CTC (Package), allowances, and deductions. Monthly Net Pay will be dynamically calculated.</p>
        </div>

        {{-- Panel Body (scrollable) --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">

            {{-- Base Salary --}}
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Annual Package / CTC (₹)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">₹</span>
                    <input type="number" x-model="structureForm.base_salary" placeholder="e.g. 500000"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-8 pr-4 py-3 text-sm font-bold text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white transition-all" />
                </div>
                <p x-show="structureForm.base_salary" class="mt-2 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                    <span x-text="'Monthly Base: ₹' + (parseFloat(structureForm.base_salary) / 12).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                </p>
            </div>

            {{-- Allowances --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Earnings / Allowances</p>
                        <p class="text-[9px] text-slate-400 mt-0.5">Added on top of base salary</p>
                    </div>
                    <button @click="addAllowance()" type="button"
                        class="flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/20 transition-colors">
                        <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Add
                    </button>
                </div>
                <div class="space-y-2">
                    <template x-for="(a, i) in structureForm.allowances" :key="'a'+i">
                        <div class="flex items-center gap-2 group rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5">
                            <input type="text" x-model="structureForm.allowances[i].name" placeholder="e.g. HRA, Travel..."
                                class="flex-1 bg-transparent text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 border-none outline-none focus:outline-none" />
                            <div class="relative w-28 shrink-0">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] text-emerald-500 font-bold">+₹</span>
                                <input type="number" x-model="structureForm.allowances[i].amount" placeholder="0"
                                    class="w-full bg-emerald-50 dark:bg-emerald-500/10 rounded-lg pl-6 pr-2 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-400 border-none outline-none focus:outline-none" />
                            </div>
                            <button @click="removeAllowance(i)" class="text-slate-300 hover:text-rose-500 dark:hover:text-rose-400 transition-colors opacity-0 group-hover:opacity-100">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </template>
                    <div x-show="structureForm.allowances.length === 0" class="rounded-xl border border-dashed border-slate-200 dark:border-slate-700 py-4 text-center">
                        <p class="text-[10px] text-slate-400">No allowances added</p>
                    </div>
                </div>
            </div>

            {{-- Deductions --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Deductions</p>
                        <p class="text-[9px] text-slate-400 mt-0.5">Subtracted from total earnings</p>
                    </div>
                    <button @click="addDeduction()" type="button"
                        class="flex items-center gap-1 rounded-lg bg-rose-50 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-rose-600 hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/20 transition-colors">
                        <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Add
                    </button>
                </div>
                <div class="space-y-2">
                    <template x-for="(d, i) in structureForm.deductions" :key="'d'+i">
                        <div class="flex items-center gap-2 group rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5">
                            <input type="text" x-model="structureForm.deductions[i].name" placeholder="e.g. PF, Tax, ESI..."
                                class="flex-1 bg-transparent text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 border-none outline-none focus:outline-none" />
                            <div class="relative w-28 shrink-0">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] text-rose-500 font-bold">-₹</span>
                                <input type="number" x-model="structureForm.deductions[i].amount" placeholder="0"
                                    class="w-full bg-rose-50 dark:bg-rose-500/10 rounded-lg pl-6 pr-2 py-1 text-xs font-bold text-rose-700 dark:text-rose-400 border-none outline-none focus:outline-none" />
                            </div>
                            <button @click="removeDeduction(i)" class="text-slate-300 hover:text-rose-500 dark:hover:text-rose-400 transition-colors opacity-0 group-hover:opacity-100">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </template>
                    <div x-show="structureForm.deductions.length === 0" class="rounded-xl border border-dashed border-slate-200 dark:border-slate-700 py-4 text-center">
                        <p class="text-[10px] text-slate-400">No deductions added</p>
                    </div>
                </div>
            </div>

            {{-- Live Net Pay Preview --}}
            <div class="rounded-2xl bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-500/10 dark:to-violet-500/10 border border-indigo-100 dark:border-indigo-500/20 p-4">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-500 dark:text-indigo-400 mb-3">Estimated Net Pay</p>
                <div class="space-y-1.5 text-[11px]">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Base Salary</span>
                        <span class="font-bold" x-text="formatCurrency(structureForm.base_salary || 0)"></span>
                    </div>
                    <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                        <span>+ Allowances</span>
                        <span class="font-bold" x-text="'+' + formatCurrency(structureForm.allowances.reduce((s,a) => s + parseFloat(a.amount||0), 0))"></span>
                    </div>
                    <div class="flex justify-between text-rose-600 dark:text-rose-400">
                        <span>- Deductions</span>
                        <span class="font-bold" x-text="'-' + formatCurrency(structureForm.deductions.reduce((s,d) => s + parseFloat(d.amount||0), 0))"></span>
                    </div>
                    <div class="border-t border-indigo-200 dark:border-indigo-500/30 pt-2 flex justify-between">
                        <span class="font-black text-slate-900 dark:text-white text-sm">Net Pay / Month</span>
                        <span class="font-black text-indigo-700 dark:text-indigo-300 text-sm"
                            x-text="formatCurrency(
                                parseFloat(structureForm.base_salary||0) +
                                structureForm.allowances.reduce((s,a) => s + parseFloat(a.amount||0), 0) -
                                structureForm.deductions.reduce((s,d) => s + parseFloat(d.amount||0), 0)
                            )">
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Footer --}}
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/80 shrink-0 flex items-center justify-between gap-3">
            <template x-if="structureModalEmployee?.pay_structure">
                <button @click="deleteStructure(structureModalEmployee); showStructureModal = false"
                    class="text-[10px] font-black uppercase tracking-widest text-rose-500 hover:text-rose-700 transition-colors flex items-center gap-1">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                    Remove
                </button>
            </template>
            <div x-show="!structureModalEmployee?.pay_structure"></div>
            <div class="flex items-center gap-3">
                <button @click="showStructureModal = false" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors">Cancel</button>
                <button @click="saveStructure()"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 active:scale-95 transition-all">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    Save Compensation
                </button>
            </div>
        </div>
    </div>

    {{-- ── Page Header ─────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Payroll</h1>
            <p class="mt-1 text-[11px] font-medium text-slate-500">Manage compensation, run payroll, and track payslips.</p>
        </div>
        <div class="flex items-center gap-3">
            <template x-if="isAdmin && activeTab === 'payslips'">
                <button @click="showGenerateModal = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 active:scale-95 transition-all">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Run Payroll
                </button>
            </template>
        </div>
    </div>

    {{-- ── Stats Cards ─────────────────────────────────────────── --}}
    <template x-if="isAdmin">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/5 dark:bg-slate-900/50">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Total Paid Out</p>
                <p class="mt-2 text-xl font-black text-slate-900 dark:text-white" x-text="formatCurrency(stats.totalPayroll)"></p>
                <p class="mt-2 text-[9px] font-bold text-emerald-500">All time</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/5 dark:bg-slate-900/50">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">On Payroll</p>
                <p class="mt-2 text-xl font-black text-slate-900 dark:text-white" x-text="stats.totalEmployees"></p>
                <p class="mt-2 text-[9px] font-bold text-indigo-500" x-text="employees.length + ' total employees'"></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/5 dark:bg-slate-900/50">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Pending Payment</p>
                <p class="mt-2 text-xl font-black text-slate-900 dark:text-white" x-text="stats.draftCount"></p>
                <p class="mt-2 text-[9px] font-bold text-amber-500">Draft payslips</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/5 dark:bg-slate-900/50">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Paid</p>
                <p class="mt-2 text-xl font-black text-slate-900 dark:text-white" x-text="stats.paidCount"></p>
                <p class="mt-2 text-[9px] font-bold text-emerald-500">Completed payslips</p>
            </div>
        </div>
    </template>

    {{-- ── Tabs ─────────────────────────────────────────────────── --}}
    <template x-if="isAdmin">
        <div class="flex gap-1 border-b border-slate-200 dark:border-slate-800">
            <button @click="activeTab = 'payslips'"
                class="px-4 py-2.5 text-xs font-bold border-b-2 transition-colors"
                :class="activeTab === 'payslips' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-white'">
                Payslips
            </button>
            <button @click="activeTab = 'structures'"
                class="px-4 py-2.5 text-xs font-bold border-b-2 transition-colors"
                :class="activeTab === 'structures' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-white'">
                Compensation
                <span class="ml-1.5 rounded-full bg-indigo-50 dark:bg-indigo-500/15 px-1.5 py-0.5 text-[9px] font-black text-indigo-600 dark:text-indigo-400"
                    x-text="stats.totalEmployees + '/' + employees.length"></span>
            </button>
        </div>
    </template>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- ── PAYSLIPS TAB ─────────────────────────────────────── --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'payslips'">
        <div x-show="isAdmin" class="mb-4 flex items-center gap-3">
            <div class="relative max-w-xs w-full">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="text" x-model="searchQuery" placeholder="Search employee or month…"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-4 text-xs text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white placeholder-slate-400" />
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
            <div x-show="loading" class="flex items-center justify-center py-16">
                <div class="h-5 w-5 animate-spin rounded-full border-2 border-slate-200 border-t-indigo-500"></div>
            </div>
            <div x-show="!loading && filteredPayslips.length === 0" class="flex flex-col items-center justify-center py-16 gap-3">
                <div class="h-14 w-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                </div>
                <p class="text-xs font-bold text-slate-500">No payslips found</p>
                <p class="text-[10px] text-slate-400" x-show="isAdmin">Click "Run Payroll" to generate payslips for a month.</p>
            </div>
            <div x-show="!loading && filteredPayslips.length > 0" class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <th class="px-6 py-3.5 text-[10px] font-black uppercase tracking-wider text-slate-400">Month</th>
                            <th x-show="isAdmin" class="px-6 py-3.5 text-[10px] font-black uppercase tracking-wider text-slate-400">Employee</th>
                            <th class="px-6 py-3.5 text-right text-[10px] font-black uppercase tracking-wider text-slate-400">Base</th>
                            <th class="hidden md:table-cell px-6 py-3.5 text-right text-[10px] font-black uppercase tracking-wider text-slate-400">Earnings</th>
                            <th class="hidden md:table-cell px-6 py-3.5 text-right text-[10px] font-black uppercase tracking-wider text-slate-400">Deductions</th>
                            <th class="px-6 py-3.5 text-right text-[10px] font-black uppercase tracking-wider text-slate-400">Net Pay</th>
                            <th class="px-6 py-3.5 text-center text-[10px] font-black uppercase tracking-wider text-slate-400">Status</th>
                            <th class="px-6 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <template x-for="ps in filteredPayslips" :key="ps.id">
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white" x-text="ps.month"></p>
                                    <template x-if="ps.details?.proration">
                                        <span class="inline-flex items-center gap-1 rounded bg-violet-50 dark:bg-violet-500/10 px-1.5 py-0.5 text-[8px] font-black text-violet-600 dark:text-violet-400">Prorated</span>
                                    </template>
                                </td>
                                <td x-show="isAdmin" class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 flex items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-500/15 text-[9px] font-black text-indigo-600 dark:text-indigo-400"
                                            x-text="ps.employee?.full_name?.charAt(0) || '?'"></div>
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300" x-text="ps.employee?.full_name || 'Unknown'"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right"><span class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="formatCurrency(ps.base_salary)"></span></td>
                                <td class="hidden md:table-cell px-6 py-4 text-right"><span class="text-xs font-bold text-emerald-600 dark:text-emerald-400" x-text="'+' + formatCurrency(ps.total_allowances)"></span></td>
                                <td class="hidden md:table-cell px-6 py-4 text-right"><span class="text-xs font-bold text-rose-600 dark:text-rose-400" x-text="'-' + formatCurrency(ps.total_deductions)"></span></td>
                                <td class="px-6 py-4 text-right"><span class="text-sm font-black text-slate-900 dark:text-white" x-text="formatCurrency(ps.net_pay)"></span></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="rounded-full px-2.5 py-1 text-[9px] font-black uppercase tracking-wider"
                                        :class="ps.status === 'paid' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'"
                                        x-text="ps.status">
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <button @click="viewPayslip(ps)" class="h-7 w-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-indigo-600 dark:hover:bg-white/5 transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </button>
                                        <template x-if="isAdmin && ps.status === 'draft'">
                                            <button @click="markAsPaid(ps)" class="h-7 w-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-emerald-600 dark:hover:bg-white/5 transition-colors" title="Mark Paid">
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

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- ── COMPENSATION TAB (Deel-style) ───────────────────── --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div x-show="isAdmin && activeTab === 'structures'" style="display:none">

        {{-- Sub-header bar --}}
        <div class="mb-4 flex items-center gap-3">
            <div class="relative max-w-xs w-full">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="text" x-model="employeeSearch" placeholder="Search employees…"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-4 text-xs text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white placeholder-slate-400" />
            </div>
            <div class="ml-auto text-[10px] font-bold text-slate-400" x-text="stats.totalEmployees + ' of ' + employees.length + ' configured'"></div>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="flex items-center justify-center py-16">
            <div class="h-5 w-5 animate-spin rounded-full border-2 border-slate-200 border-t-indigo-500"></div>
        </div>

        {{-- Employee Compensation Table --}}
        <div x-show="!loading" class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40">
                        <th class="px-6 py-3.5 text-[10px] font-black uppercase tracking-wider text-slate-400">Employee</th>
                        <th class="px-6 py-3.5 text-[10px] font-black uppercase tracking-wider text-slate-400">Role</th>
                        <th class="hidden md:table-cell px-6 py-3.5 text-right text-[10px] font-black uppercase tracking-wider text-slate-400">Annual CTC (Package)</th>
                        <th class="hidden lg:table-cell px-6 py-3.5 text-right text-[10px] font-black uppercase tracking-wider text-slate-400">Monthly Allowances</th>
                        <th class="hidden lg:table-cell px-6 py-3.5 text-right text-[10px] font-black uppercase tracking-wider text-slate-400">Monthly Deductions</th>
                        <th class="px-6 py-3.5 text-right text-[10px] font-black uppercase tracking-wider text-slate-400">Net / Month</th>
                        <th class="px-6 py-3.5 text-center text-[10px] font-black uppercase tracking-wider text-slate-400">Status</th>
                        <th class="px-4 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <template x-for="emp in filteredEmployees" :key="emp.id">
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors cursor-pointer" @click="emp.pay_structure ? openEditStructure(emp) : openSetupStructure(emp)">

                            {{-- Employee --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-[10px] font-black shrink-0"
                                        :class="emp.pay_structure ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'"
                                        x-text="emp.full_name?.split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase() || '?'">
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900 dark:text-white" x-text="emp.full_name"></p>
                                        {{-- Status badges --}}
                                        <template x-if="emp.status === 'resigned'">
                                            <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest">Resigned</span>
                                        </template>
                                        <template x-if="emp.status !== 'resigned'">
                                            <span class="text-[8px] font-bold text-slate-400 capitalize" x-text="emp.status || 'active'"></span>
                                        </template>
                                    </div>
                                </div>
                            </td>

                            {{-- Role --}}
                            <td class="px-6 py-4">
                                <span class="text-xs text-slate-500 dark:text-slate-400" x-text="emp.job_title || '—'"></span>
                            </td>

                            {{-- Base Pay --}}
                            <td class="hidden md:table-cell px-6 py-4 text-right">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                    x-text="emp.pay_structure ? formatCurrency(emp.pay_structure.base_salary) : '—'"></span>
                            </td>

                            {{-- Allowances total --}}
                            <td class="hidden lg:table-cell px-6 py-4 text-right">
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400"
                                    x-text="emp.pay_structure && emp.pay_structure.allowances?.length ? '+' + formatCurrency(emp.pay_structure.allowances.reduce((s,a) => s+parseFloat(a.amount||0),0)) : '—'"></span>
                            </td>

                            {{-- Deductions total --}}
                            <td class="hidden lg:table-cell px-6 py-4 text-right">
                                <span class="text-xs font-bold text-rose-600 dark:text-rose-400"
                                    x-text="emp.pay_structure && emp.pay_structure.deductions?.length ? '-' + formatCurrency(emp.pay_structure.deductions.reduce((s,d) => s+parseFloat(d.amount||0),0)) : '—'"></span>
                            </td>

                            {{-- Net Pay --}}
                            <td class="px-6 py-4 text-right">
                                <template x-if="emp.pay_structure">
                                    <span class="text-sm font-black text-slate-900 dark:text-white"
                                        x-text="formatCurrency(
                                            (parseFloat(emp.pay_structure.base_salary||0) / 12) +
                                            (emp.pay_structure.allowances||[]).reduce((s,a)=>s+parseFloat(a.amount||0),0) -
                                            (emp.pay_structure.deductions||[]).reduce((s,d)=>s+parseFloat(d.amount||0),0)
                                        )">
                                    </span>
                                </template>
                                <template x-if="!emp.pay_structure">
                                    <span class="text-xs text-slate-400">Not set</span>
                                </template>
                            </td>

                            {{-- Status pill --}}
                            <td class="px-6 py-4 text-center">
                                <template x-if="emp.pay_structure">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 text-[9px] font-black text-emerald-600 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Configured
                                    </span>
                                </template>
                                <template x-if="!emp.pay_structure">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 dark:bg-amber-500/10 px-2.5 py-1 text-[9px] font-black text-amber-600 dark:text-amber-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                        Not Set
                                    </span>
                                </template>
                            </td>

                            {{-- Action button --}}
                            <td class="px-4 py-4">
                                <div class="flex justify-end">
                                    <template x-if="emp.pay_structure">
                                        <button class="opacity-0 group-hover:opacity-100 transition-opacity inline-flex items-center gap-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-[9px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 hover:border-indigo-300 hover:text-indigo-600 dark:hover:text-indigo-400">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z" /></svg>
                                            Edit
                                        </button>
                                    </template>
                                    <template x-if="!emp.pay_structure">
                                        <button class="opacity-0 group-hover:opacity-100 transition-opacity inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3 py-1.5 text-[9px] font-black uppercase tracking-wider text-white hover:bg-indigo-700">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                            Set Up
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>

                    {{-- Empty search result --}}
                    <tr x-show="filteredEmployees.length === 0">
                        <td colspan="8" class="px-6 py-12 text-center text-xs text-slate-400">No employees match your search.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- ── Run Payroll Modal ────────────────────────────────── --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div x-show="showGenerateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display:none" x-transition>
        <div @click.away="showGenerateModal = false" class="w-full max-w-sm rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-black text-slate-900 dark:text-white">Run Payroll</h3>
                <button @click="showGenerateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Pay Period</label>
                    <input type="month" x-model="generateMonth"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 transition-all" />
                </div>
                <div class="rounded-xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 px-4 py-3 text-[10px] text-indigo-700 dark:text-indigo-300 space-y-1">
                    <p class="font-black">What happens when you run payroll:</p>
                    <p>• Payslips generated for all employees with a salary configured</p>
                    <p>• Mid-month resignees get prorated salary automatically</p>
                    <p>• Employees who joined mid-month are also prorated</p>
                    <p>• Unpaid leave deductions are applied automatically</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 bg-slate-50 dark:bg-white/5 border-t border-slate-100 dark:border-slate-800">
                <button @click="showGenerateModal = false" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors">Cancel</button>
                <button @click="generatePayslips()" :disabled="!generateMonth || generating"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:bg-indigo-700 disabled:opacity-50 active:scale-95 transition-all">
                    <span x-show="!generating">Run Payroll</span>
                    <span x-show="generating" class="flex items-center gap-2">
                        <div class="h-3 w-3 animate-spin rounded-full border-2 border-white/30 border-t-white"></div>
                        Processing…
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- ── Payslip Detail Modal ─────────────────────────────── --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div x-show="showDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display:none" x-transition>
        <div @click.away="showDetailsModal = false" class="w-full max-w-md max-h-[90vh] overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 sticky top-0 bg-white dark:bg-slate-900 z-10">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Payslip</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5" x-text="selectedPayslip?.month"></p>
                </div>
                <button @click="showDetailsModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <template x-if="selectedPayslip">
                <div class="p-6 space-y-5">
                    {{-- Employee --}}
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                        <div class="h-10 w-10 rounded-full flex items-center justify-center text-sm font-black bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400"
                            x-text="selectedPayslip.employee?.full_name?.charAt(0) || '?'"></div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-slate-900 dark:text-white" x-text="selectedPayslip.employee?.full_name"></p>
                            <p class="text-[9px] text-slate-400" x-text="selectedPayslip.month"></p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-[9px] font-black uppercase"
                            :class="selectedPayslip.status === 'paid' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'"
                            x-text="selectedPayslip.status">
                        </span>
                    </div>

                    {{-- Proration notice --}}
                    <template x-if="selectedPayslip.details?.proration">
                        <div class="flex items-start gap-3 rounded-xl bg-violet-50 dark:bg-violet-500/10 border border-violet-100 dark:border-violet-500/20 p-4">
                            <svg class="h-4 w-4 text-violet-600 dark:text-violet-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                            <div>
                                <p class="text-[10px] font-black text-violet-800 dark:text-violet-300">Prorated Salary</p>
                                <p class="text-[9px] text-violet-600 dark:text-violet-400 mt-0.5" x-text="selectedPayslip.details.proration.reason"></p>
                                <p class="text-[9px] text-violet-500 mt-0.5"
                                    x-text="selectedPayslip.details.proration.worked_days + ' of ' + selectedPayslip.details.proration.total_days + ' days (' + Math.round(selectedPayslip.details.proration.ratio * 100) + '%)'">
                                </p>
                            </div>
                        </div>
                    </template>

                    {{-- Breakdown --}}
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2.5 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400">Base Salary</span>
                            <span class="text-xs font-bold text-slate-900 dark:text-white" x-text="formatCurrency(selectedPayslip.base_salary)"></span>
                        </div>
                        <template x-if="selectedPayslip.details?.allowances">
                            <template x-for="a in selectedPayslip.details.allowances" :key="a.name">
                                <div class="flex justify-between items-center py-2.5 px-4 rounded-xl bg-emerald-50/60 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/10">
                                    <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400" x-text="a.name"></span>
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400" x-text="'+' + formatCurrency(a.amount)"></span>
                                </div>
                            </template>
                        </template>
                        <template x-if="selectedPayslip.details?.deductions">
                            <template x-for="d in selectedPayslip.details.deductions" :key="d.name">
                                <div class="flex justify-between items-center py-2.5 px-4 rounded-xl bg-rose-50/60 dark:bg-rose-500/5 border border-rose-100 dark:border-rose-500/10">
                                    <span class="text-[11px] font-bold text-rose-700 dark:text-rose-400" x-text="d.name"></span>
                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400" x-text="'-' + formatCurrency(d.amount)"></span>
                                </div>
                            </template>
                        </template>
                        <template x-if="selectedPayslip.details?.unpaid_leave_deduction">
                            <div class="flex justify-between items-center py-2.5 px-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20">
                                <div>
                                    <span class="text-[11px] font-bold text-rose-700 dark:text-rose-400">Unpaid Leave</span>
                                    <span class="ml-1.5 text-[9px] font-bold text-rose-500" x-text="'(' + selectedPayslip.details.unpaid_leave_deduction.days + ' days)'"></span>
                                </div>
                                <span class="text-xs font-bold text-rose-600 dark:text-rose-400" x-text="'-' + formatCurrency(selectedPayslip.details.unpaid_leave_deduction.amount)"></span>
                            </div>
                        </template>

                        {{-- Adjustments (Reimbursements, Bonuses, etc.) --}}
                        <template x-if="selectedPayslip.details?.adjustments && selectedPayslip.details.adjustments.length > 0">
                            <template x-for="adj in selectedPayslip.details.adjustments" :key="adj.label">
                                <div class="flex justify-between items-center py-2.5 px-4 rounded-xl border"
                                    :class="adj.type === 'addition'
                                        ? 'bg-violet-50/60 dark:bg-violet-500/5 border-violet-100 dark:border-violet-500/10'
                                        : 'bg-rose-50/60 dark:bg-rose-500/5 border-rose-100 dark:border-rose-500/10'">
                                    <div>
                                        <span class="text-[11px] font-bold"
                                            :class="adj.type === 'addition' ? 'text-violet-700 dark:text-violet-400' : 'text-rose-700 dark:text-rose-400'"
                                            x-text="adj.label"></span>
                                        <span class="ml-1.5 text-[8px] font-black uppercase tracking-widest"
                                            :class="adj.type === 'addition' ? 'text-violet-400' : 'text-rose-400'">
                                            Reimbursement
                                        </span>
                                    </div>
                                    <span class="text-xs font-bold"
                                        :class="adj.type === 'addition' ? 'text-violet-600 dark:text-violet-400' : 'text-rose-600 dark:text-rose-400'"
                                        x-text="(adj.type === 'addition' ? '+' : '-') + formatCurrency(adj.amount)"></span>
                                </div>
                            </template>
                        </template>
                    </div>

                    {{-- Net Pay --}}
                    <div class="rounded-2xl bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-500/10 dark:to-violet-500/10 border border-indigo-100 dark:border-indigo-500/20 p-4 flex justify-between items-center">
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Net Pay (Take Home)</p>
                        <p class="text-xl font-black text-slate-900 dark:text-white" x-text="formatCurrency(selectedPayslip.net_pay)"></p>
                    </div>

                    {{-- Period --}}
                    <div class="flex justify-between text-[9px] text-slate-400 font-bold border-t border-slate-100 dark:border-slate-800 pt-3">
                        <span x-text="'Period: ' + new Date(selectedPayslip.period_start).toLocaleDateString('en-GB',{day:'2-digit',month:'short'}) + ' – ' + new Date(selectedPayslip.period_end).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'})"></span>
                        <span x-text="'Ref #' + selectedPayslip.id"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
