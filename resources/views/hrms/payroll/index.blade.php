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
            <p class="mt-1 text-[11px] font-medium text-slate-500">Manage earnings, payslips, and salary settings.</p>
        </div>
        <div class="flex items-center gap-3">
            <template x-if="isAdmin && activeTab === 'payslips'">
                <button @click="showGenerateModal = true" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Run Payroll</span>
                </button>
            </template>
            <template x-if="isAdmin && activeTab === 'structures'">
                <button @click="showStructureModal = true" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>New Salary Setting</span>
                </button>
            </template>
        </div>
    </div>

    {{-- Tabs --}}
    <template x-if="isAdmin">
        <div class="flex gap-2 p-1 bg-slate-100 rounded-xl dark:bg-slate-900 w-fit">
            <button @click="activeTab = 'payslips'" class="px-4 py-1.5 text-xs font-bold rounded-lg transition-colors" :class="activeTab === 'payslips' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'">Payslips</button>
            <button @click="activeTab = 'structures'" class="px-4 py-1.5 text-xs font-bold rounded-lg transition-colors" :class="activeTab === 'structures' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'">Salary Settings</button>
        </div>
    </template>

    {{-- Content --}}
    <div class="space-y-6">
        
        {{-- Payslips --}}
        <div x-show="activeTab === 'payslips'" class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 dark:bg-slate-950/50 dark:border-slate-800">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Month</th>
                            <th x-show="isAdmin" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Employee</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Take Home</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <template x-for="ps in payslips" :key="ps.id">
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-xs font-bold text-slate-900 dark:text-white" x-text="ps.month"></td>
                                <td x-show="isAdmin" class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 flex items-center justify-center rounded bg-slate-100 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400" x-text="ps.employee.full_name.charAt(0)"></div>
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300" x-text="ps.employee.full_name"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white" x-text="'$ ' + parseFloat(ps.net_pay).toLocaleString()"></p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                        :class="ps.status === 'paid' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'"
                                        x-text="ps.status">
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="viewPayslip(ps)" class="h-7 w-7 flex items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-cyan-600 dark:hover:bg-white/5" title="View">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </button>
                                        <template x-if="isAdmin && ps.status === 'draft'">
                                            <button @click="markAsPaid(ps)" class="h-7 w-7 flex items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-emerald-600 dark:hover:bg-white/5" title="Mark Paid">
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

        {{-- Salary Settings --}}
        <div x-show="isAdmin && activeTab === 'structures'" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" style="display: none;">
            <template x-for="s in structures" :key="s.id">
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 flex items-center justify-center rounded bg-slate-100 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400" x-text="s.employee.full_name.charAt(0)"></div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white" x-text="s.employee.full_name"></h4>
                                <p class="text-[9px] text-slate-500 uppercase font-bold" x-text="s.employee.job_title || 'Role'"></p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-slate-900 dark:text-white" x-text="'$ ' + parseFloat(s.base_salary).toLocaleString()"></span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Bonus</p>
                            <div class="space-y-1">
                                <template x-if="!s.allowances || s.allowances.length === 0">
                                    <span class="text-[10px] text-slate-400 italic">None</span>
                                </template>
                                <template x-for="a in s.allowances" :key="a.name">
                                    <div class="flex justify-between items-center text-[10px]">
                                         <span class="text-slate-600 dark:text-slate-400 truncate pr-1" x-text="a.name"></span>
                                         <span class="font-bold text-emerald-600" x-text="'+$' + a.amount"></span>
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
                                         <span class="font-bold text-rose-600" x-text="'-$' + d.amount"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

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
</div>
@endsection
