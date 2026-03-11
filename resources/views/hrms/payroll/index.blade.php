@extends('hrms.layouts.app')

@section('title', 'Payroll - PeopleFlow HRMS')

@section('content')
<div x-data="payrollManager()" x-init="init()">
    
    {{-- Toast Notification --}}
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl px-5 py-3 shadow-2xl"
        :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
        style="display: none;"
    >
        <span x-text="toast.message" class="text-sm font-medium"></span>
    </div>

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Payroll Management</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage salary structures, generate payslips, and track payments.</p>
        </div>
        <div class="flex items-center gap-2">
            <template x-if="isAdmin && activeTab === 'payslips'">
                <button @click="showGenerateModal = true" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600 transition-colors">
                    Bulk Generate Payslips
                </button>
            </template>
            <template x-if="isAdmin && activeTab === 'structures'">
                <button @click="showStructureModal = true" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600 transition-colors">
                    Add Salary Structure
                </button>
            </template>
        </div>
    </div>

    {{-- Tabs --}}
    <template x-if="isAdmin">
        <div class="mb-6 flex overflow-x-auto rounded-xl border border-slate-200 bg-white p-1 dark:border-slate-700 dark:bg-slate-800/50">
            <button @click="activeTab = 'payslips'" class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition-all" :class="activeTab === 'payslips' ? 'bg-cyan-500 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-700'">Payslips History</button>
            <button @click="activeTab = 'structures'" class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition-all" :class="activeTab === 'structures' ? 'bg-cyan-500 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-700'">Salary Structures</button>
        </div>
    </template>

    {{-- Content --}}
    <div class="space-y-6">
        
        {{-- List of Payslips --}}
        <div x-show="activeTab === 'payslips'" x-transition>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800/50">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Month</th>
                            <th x-show="isAdmin" class="px-6 py-4">Employee</th>
                            <th class="px-6 py-4 text-right">Net Pay</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <template x-for="ps in payslips" :key="ps.id">
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white" x-text="ps.month"></td>
                                <td x-show="isAdmin" class="px-6 py-4 text-slate-600 dark:text-slate-400" x-text="ps.employee.full_name"></td>
                                <td class="px-6 py-4 text-right font-bold text-slate-900 dark:text-white" x-text="'$' + parseFloat(ps.net_pay).toLocaleString()"></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                        :class="ps.status === 'paid' ? 'bg-green-100 text-green-700 dark:bg-green-500/20' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20'"
                                        x-text="ps.status"></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="viewPayslip(ps)" class="rounded-lg p-1.5 text-slate-400 hover:bg-cyan-50 hover:text-cyan-600 dark:hover:bg-cyan-500/10" title="View Details">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                        <template x-if="isAdmin && ps.status === 'draft'">
                                            <button @click="markAsPaid(ps)" class="rounded-lg p-1.5 text-slate-400 hover:bg-green-50 hover:text-green-600 dark:hover:bg-green-500/10" title="Mark as Paid">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
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

        {{-- Salary Structures (Admin Only) --}}
        <div x-show="isAdmin && activeTab === 'structures'" x-transition>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <template x-for="s in structures" :key="s.id">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="mb-4 flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 dark:text-white" x-text="s.employee.full_name"></h4>
                            <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400" x-text="'Base: $' + parseFloat(s.base_salary).toLocaleString()"></span>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Allowances</p>
                                <div class="mt-1 flex flex-wrap gap-2 text-xs">
                                    <template x-for="a in s.allowances" :key="a.name">
                                        <span class="bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400 px-2 py-1 rounded-md" x-text="a.name + ': $' + a.amount"></span>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Deductions</p>
                                <div class="mt-1 flex flex-wrap gap-2 text-xs">
                                    <template x-for="d in s.deductions" :key="d.name">
                                        <span class="bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 px-2 py-1 rounded-md" x-text="d.name + ': $' + d.amount"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Generate Payslips Modal --}}
    <div x-show="showGenerateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="showGenerateModal = false" class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-sm p-8 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 text-slate-900 dark:text-white">Generate Payslips</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Month (YYYY-MM)</label>
                    <input type="month" x-model="generateMonth" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button @click="generatePayslips()" :disabled="!generateMonth || generating" class="flex-1 rounded-xl bg-cyan-500 py-3 text-sm font-bold text-white hover:bg-cyan-600 disabled:opacity-50">
                    <span x-show="!generating">Generate</span>
                    <span x-show="generating">Generating...</span>
                </button>
                <button @click="showGenerateModal = false" class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700/50">Cancel</button>
            </div>
        </div>
    </div>

    {{-- Add Structure Modal --}}
    <div x-show="showStructureModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm overflow-y-auto" style="display: none;">
        <div @click.away="showStructureModal = false" class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-lg p-8 shadow-2xl my-8">
            <h3 class="text-xl font-bold mb-6 text-slate-900 dark:text-white">Add Salary Structure</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Employee</label>
                    <select x-model="structureForm.employee_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                        <option value="">Select Employee</option>
                        <template x-for="emp in availableEmployees" :key="emp.id">
                            <option :value="emp.id" x-text="emp.full_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Base Salary</label>
                    <input type="number" step="0.01" x-model="structureForm.base_salary" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                </div>
                
                {{-- Example default allowance --}}
                <div class="p-4 rounded-xl bg-cyan-50 dark:bg-cyan-900/20 border border-cyan-100 dark:border-cyan-800">
                    <p class="text-sm text-cyan-700 dark:text-cyan-400">Basic allowances and deductions will be set as an empty array for this snippet.</p>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button @click="saveStructure()" :disabled="!structureForm.employee_id || !structureForm.base_salary" class="flex-1 rounded-xl bg-cyan-500 py-3 text-sm font-bold text-white hover:bg-cyan-600 disabled:opacity-50">Save</button>
                <button @click="showStructureModal = false" class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700/50">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection
