<div class="space-y-5 pb-8 relative">
    {{-- High-Impact Glass Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white/80 px-4 py-3 shadow-sm border border-slate-200 backdrop-blur-xl dark:bg-slate-900/60 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-cyan-500/5 blur-[80px]"></div>
        <div class="absolute -bottom-20 -left-20 h-40 w-40 rounded-full bg-indigo-500/5 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-2 lg:flex-row lg:items-center">
            <div>
                <div class="flex items-center gap-1.5 mb-0.5">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Payroll Ops</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Financial Lattice</span>
                </div>
                <h1 class="text-base font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all leading-none mt-1">
                    Payroll <span class="text-cyan-500">Hub</span>
                </h1>
                <p class="mt-0.5 text-[8px] font-bold text-slate-500 uppercase tracking-widest opacity-80 leading-none">
                    Cycle Management: {{ $selectedMonth ?: 'Auto-Detected' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Global Stats --}}
                <div class="flex items-center gap-2 px-3 border-r border-slate-200 dark:border-white/10 hidden xl:flex">
                    <div class="text-right">
                        <p class="text-[7px] font-black uppercase tracking-widest text-slate-400">Active Nodes</p>
                        <p class="text-xs font-black text-slate-900 dark:text-white leading-none mt-0.5">{{ $totalEmployeesCount }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[7px] font-black uppercase tracking-widest text-slate-400">Cycle Sum</p>
                        <p class="text-xs font-black text-emerald-600 leading-none mt-0.5">₹{{ number_format($totalPayrollSum, 0) }}</p>
                    </div>
                </div>

                {{-- Tab Switcher (Unified) --}}
                <div class="flex p-1 bg-slate-100 dark:bg-black/20 rounded-xl border border-slate-200 dark:border-white/5">
                    <button wire:click="$set('activeTab', 'generator')"
                        class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'generator' ? 'bg-white shadow-sm text-slate-900 dark:bg-white/10 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400' }}">
                        Generation
                    </button>
                    <button wire:click="$set('activeTab', 'structures')"
                        class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'structures' ? 'bg-white shadow-sm text-slate-900 dark:bg-white/10 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400' }}">
                        Pay Models
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($activeTab === 'generator')
        {{-- Generator Section: Stats, Controls, and Table --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach([
                ['Employees', $totalEmployeesCount, 'Eligible for pay', 'cyan'],
                ['Total Paid', '₹'.number_format($totalPayrollSum, 2), 'Cumulative history', 'emerald'],
                ['Drafts', $draftCount, 'Ready to generate', 'amber'],
                ['Verified', $paidCount, 'Completed cycles', 'indigo']
            ] as [$title, $val, $desc, $color])
            <div class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                <h3 class="text-[7px] font-black uppercase tracking-widest text-slate-400 leading-none mb-1">{{ $title }}</h3>
                <p class="text-[11px] font-black text-slate-900 dark:text-white leading-none">{{ $val }}</p>
                <p class="text-[7px] font-bold text-slate-500 mt-1 leading-none">{{ $desc }}</p>
            </div>
            @endforeach
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-2">
            <div class="flex flex-1 items-center max-w-md relative group">
                <div class="absolute left-4 text-slate-400 pointer-events-none group-focus-within:text-cyan-500 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" wire:model.live="searchGenerator" placeholder="Search employee database..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-[9px] font-bold text-slate-900 shadow-sm focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white transition-all">
            </div>

            <div class="flex items-center gap-3">
                <input type="month" wire:model.live="selectedMonth" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[9px] font-bold text-slate-900 dark:border-white/10 dark:bg-slate-900 dark:text-white">
                <button wire:click="generate" wire:loading.attr="disabled" class="relative group flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-[9px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/20 transition-all hover:bg-slate-800 active:scale-95 disabled:opacity-50 dark:bg-white dark:text-slate-900">
                    <span wire:loading.remove wire:target="generate">Process Payroll</span>
                    <span wire:loading wire:target="generate" class="flex items-center gap-2">
                        <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Queueing...
                    </span>
                </button>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-white/5 border-b border-slate-100 dark:border-white/5">
                            <th class="px-3 py-2 text-[6px] font-black uppercase tracking-widest text-slate-500">Employee</th>
                            <th class="px-3 py-2 text-[6px] font-black uppercase tracking-widest text-slate-500">Month</th>
                            <th class="px-3 py-2 text-[6px] font-black uppercase tracking-widest text-slate-500">Earnings</th>
                            <th class="px-3 py-2 text-[6px] font-black uppercase tracking-widest text-slate-500">Deductions</th>
                            <th class="px-3 py-2 text-[6px] font-black uppercase tracking-widest text-slate-500">Net Pay</th>
                            <th class="px-3 py-2 text-[6px] font-black uppercase tracking-widest text-slate-500">State</th>
                            <th class="px-3 py-2 text-[6px] font-black uppercase tracking-widest text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse($payslips as $payslip)
                            <tr class="hover:bg-slate-50/50 transition-colors group dark:hover:bg-white/[0.01]">
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded bg-white shadow-sm border border-slate-100 flex items-center justify-center overflow-hidden dark:bg-white/5 dark:border-white/5 hidden sm:flex">
                                            @if($payslip->employee->profile_photo)
                                                <img src="{{ Storage::url($payslip->employee->profile_photo) }}" class="h-full w-full object-cover">
                                            @else
                                                <span class="text-[6px] font-black text-slate-400 uppercase tracking-widest">{{ substr($payslip->employee->full_name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-[8px] font-black text-slate-900 dark:text-white uppercase transition-all group-hover:text-cyan-600 leading-none mb-0.5 truncate max-w-[120px]">{{ $payslip->employee->full_name }}</p>
                                            <p class="text-[6px] font-bold text-slate-400 uppercase leading-none truncate max-w-[120px]">{{ $payslip->employee->job_title }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-[7px] font-black text-slate-600 dark:text-slate-400 uppercase">{{ $payslip->month }}</td>
                                <td class="px-3 py-2">
                                    <div class="flex flex-col">
                                        <span class="text-[8px] font-black text-emerald-600 leading-none mb-0.5">₹{{ number_format($payslip->base_salary + $payslip->total_allowances, 0) }}</span>
                                        <span class="text-[6px] font-bold text-slate-400 leading-none">Incl. ₹{{ number_format($payslip->total_allowances, 0) }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-[8px] font-black text-rose-500">₹{{ number_format($payslip->total_deductions, 0) }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex rounded bg-slate-900 px-2 py-0.5 text-[8px] font-black text-white dark:bg-white dark:text-slate-900 shadow-sm leading-none">₹{{ number_format($payslip->net_pay, 0) }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    @if($payslip->status === 'paid')
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center gap-1 rounded bg-emerald-50 px-1.5 py-0.5 text-[6px] font-black uppercase text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 leading-none">
                                                Verified
                                            </span>
                                            @if(isset($payslip->details['manually_edited']))
                                                <span class="inline-flex items-center rounded bg-purple-50 px-1.5 py-0.5 text-[6px] font-black uppercase text-purple-600 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20 leading-none">AMENDED</span>
                                            @endif
                                            <button wire:click="revertToDraft({{ $payslip->id }})" wire:confirm="Initiating emergency amendment. Continue?" class="opacity-0 group-hover:opacity-10 hover:!opacity-90 transition-all text-slate-400 hover:text-amber-600" title="Emergency Rollback">
                                                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                                            </button>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded bg-amber-50 px-1.5 py-0.5 text-[6px] font-black uppercase text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20 leading-none">
                                            <div class="h-1 w-1 rounded-full bg-amber-500 animate-pulse"></div> Processing
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-end gap-1.5 text-slate-400">
                                        <button wire:click="openEditPayslipModal({{ $payslip->id }})" title="Edit Ad-hoc" class="h-7 w-7 flex items-center justify-center rounded bg-white border border-slate-200 text-slate-500 hover:text-cyan-600 transition-all dark:bg-white/5 dark:border-white/10 shadow-sm">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                        </button>
                                        <a href="{{ route('payroll.view-pdf', $payslip->id) }}" target="_blank" title="Preview" class="h-7 w-7 flex items-center justify-center rounded bg-white border border-slate-200 text-slate-500 hover:text-cyan-600 transition-all dark:bg-white/5 dark:border-white/10 shadow-sm">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.399 8.049 7.21 5 12 5c4.79 0 8.611 3.049 9.964 6.678.055.149.055.308 0 .457C20.611 15.951 16.79 19 12 19c-4.79 0-8.611-3.049-9.964-6.678z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </a>
                                        <a href="{{ route('payroll.download-pdf', $payslip->id) }}" title="Download PDF" class="h-7 w-7 flex items-center justify-center rounded bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 transition-all dark:bg-white/5 dark:border-white/10 shadow-sm">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 11.25L12 15.75m0 0l4.5-4.5M12 15.75V3" /></svg>
                                        </a>
                                        @if($payslip->status === 'draft')
                                            <button wire:click="markAsPaid({{ $payslip->id }})" title="Mark as Paid" class="h-7 w-7 flex items-center justify-center rounded bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100 transition-all dark:bg-emerald-500/10 dark:border-white/10 shadow-sm">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-14 w-14 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300 dark:bg-white/5"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg></div>
                                        <p class="mt-4 text-[8px] font-black uppercase tracking-widest text-slate-400">No payroll records found for this cycle</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payslips->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5">{{ $payslips->links() }}</div>
            @endif
        </div>

    @else
        {{-- Pay Structures Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-2">
            <div class="flex flex-1 items-center max-w-md relative group">
                <div class="absolute left-4 text-slate-400 pointer-events-none group-focus-within:text-violet-500 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" wire:model.live="searchStructures" placeholder="Sync by name or ID..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-[9px] font-bold text-slate-900 shadow-sm focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white transition-all">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($employees as $employee)
            <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md dark:border-white/5 dark:bg-slate-900/50">
                <div class="flex items-start justify-between mb-4">
                    <div class="h-8 w-8 flex items-center justify-center rounded-xl bg-white shadow-sm border border-slate-100 overflow-hidden dark:bg-white/5 dark:border-white/5">
                        @if($employee->profile_photo)
                            <img src="{{ Storage::url($employee->profile_photo) }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ substr($employee->full_name, 0, 1) }}</span>
                        @endif
                    </div>
                    <button wire:click="openEditModal({{ $employee->id }})" class="p-2 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-cyan-600 transition-all dark:hover:bg-white/5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                    </button>
                </div>
                <div class="mb-4">
                    <h3 class="text-[9px] font-black text-slate-900 dark:text-white uppercase truncate">{{ $employee->full_name }}</h3>
                    <p class="text-[9px] font-bold text-slate-400 mt-0.5 uppercase tracking-wider">{{ $employee->job_title }}</p>
                </div>
                <div class="space-y-2 border-t border-slate-50 pt-4 dark:border-white/5">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Base Salary</span>
                        <span class="text-[9px] font-black text-slate-900 dark:text-white">₹{{ number_format($employee->payStructure?->base_salary ?? 0) }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center bg-white rounded-xl border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">No organizational structures found</p>
            </div>
            @endforelse
        </div>
        <div class="mt-6">{{ $employees->links() }}</div>
    @endif

    {{-- Edit Modal --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div wire:click="$set('showEditModal', false)" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-2xl rounded-xl bg-white shadow-2xl dark:bg-slate-900 overflow-hidden border border-white/10">
            <div class="p-4">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-1">Financial Modeling</p>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Pay Structure: {{ $selectedEmployeeName }}</h2>
                    </div>
                    <button wire:click="$set('showEditModal', false)" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>

                <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-4 custom-scrollbar">
                    <div class="space-y-2">
                        <label class="text-[8px] font-black uppercase tracking-widest text-slate-500">Annual CTC (Base Salary)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                            <input type="number" wire:model="baseSalary" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-black focus:border-cyan-500 dark:border-white/10 dark:bg-white/5">
                        </div>
                    </div>

                    {{-- Allowances --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[8px] font-black uppercase tracking-widest text-emerald-600">Earnings Vector</h3>
                            <button wire:click="addAllowance" class="text-[8px] font-black text-cyan-600 uppercase">+ Add Row</button>
                        </div>
                        @foreach($allowances as $index => $allowance)
                        <div class="flex gap-3 items-center">
                            <input type="text" wire:model="allowances.{{ $index }}.name" placeholder="Component Name" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-[9px] font-bold dark:bg-white/5 dark:border-white/5">
                            <input type="number" wire:model="allowances.{{ $index }}.amount" placeholder="Amount" class="w-32 px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-[9px] font-bold dark:bg-white/5 dark:border-white/5">
                            <button wire:click="removeAllowance({{ $index }})" class="p-2 text-rose-400 hover:text-rose-600"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                        </div>
                        @endforeach
                    </div>

                    {{-- Deductions --}}
                    <div class="space-y-3 pt-4 border-t border-slate-50 dark:border-white/5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[8px] font-black uppercase tracking-widest text-rose-500">Constraint Vector</h3>
                            <button wire:click="addDeduction" class="text-[8px] font-black text-cyan-600 uppercase">+ Add Row</button>
                        </div>
                        @foreach($deductions as $index => $deduction)
                        <div class="flex gap-3 items-center">
                            <input type="text" wire:model="deductions.{{ $index }}.name" placeholder="Component Name" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-[9px] font-bold dark:bg-white/5 dark:border-white/5">
                            <input type="number" wire:model="deductions.{{ $index }}.amount" placeholder="Amount" class="w-32 px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-[9px] font-bold dark:bg-white/5 dark:border-white/5">
                            <button wire:click="removeDeduction({{ $index }})" class="p-2 text-rose-400 hover:text-rose-600"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-10 flex justify-end gap-3 pt-8 border-t border-slate-50 dark:border-white/5">
                    <button wire:click="$set('showEditModal', false)" class="px-6 py-2.5 text-[8px] font-black uppercase text-slate-400 hover:text-slate-600 transition-all">Cancel</button>
                    <button wire:click="saveStructure" class="rounded-xl bg-slate-900 px-10 py-2.5 text-[8px] font-black uppercase text-white shadow-xl shadow-cyan-500/20 active:scale-95 dark:bg-white dark:text-slate-900 transition-all">Save Matrix</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Edit Payslip Modal (Ad-hoc adjustment) --}}
    @if($showEditPayslipModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div wire:click="$set('showEditPayslipModal', false)" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-2xl rounded-xl bg-white shadow-2xl dark:bg-slate-900 overflow-hidden border border-white/10">
            <div class="p-4">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-1">Ad-hoc Adjustment</p>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Recalculate: {{ $editingPayslipEmployeeName }}</h2>
                    </div>
                    <button wire:click="$set('showEditPayslipModal', false)" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-4 custom-scrollbar">
                    {{-- Base Salary (Prorated) --}}
                    <div class="space-y-2">
                            <label class="text-[8px] font-black uppercase tracking-widest text-slate-500">Effective Base Salary (This Month)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                                <input type="number" wire:model="editPayslipBaseSalary" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-black focus:border-indigo-500 dark:border-white/10 dark:bg-white/5">
                            </div>
                            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest leading-none">Manual overrides bypass automatic proration logic.</p>
                    </div>

                    {{-- Allowances --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[8px] font-black uppercase tracking-widest text-emerald-600">Earnings Adjustment</h3>
                            <button wire:click="addEditPayslipAllowance" class="text-[8px] font-black text-cyan-600 uppercase">+ Add Component</button>
                        </div>
                        @foreach($editPayslipAllowances as $index => $allowance)
                        <div class="flex gap-3 items-center">
                            <input type="text" wire:model="editPayslipAllowances.{{ $index }}.name" placeholder="E.g. Bonus" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-[9px] font-bold dark:bg-white/5 dark:border-white/5">
                            <input type="number" wire:model="editPayslipAllowances.{{ $index }}.amount" placeholder="Amount" class="w-32 px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-[9px] font-bold dark:bg-white/5 dark:border-white/5">
                            <button wire:click="removeEditPayslipAllowance({{ $index }})" class="p-2 text-rose-400 hover:text-rose-600"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                        </div>
                        @endforeach
                    </div>

                    {{-- Deductions --}}
                    <div class="space-y-3 pt-4 border-t border-slate-50 dark:border-white/5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[8px] font-black uppercase tracking-widest text-rose-500">Deduction Adjustment</h3>
                            <button wire:click="addEditPayslipDeduction" class="text-[8px] font-black text-cyan-600 uppercase">+ Add Component</button>
                        </div>
                        @foreach($editPayslipDeductions as $index => $deduction)
                        <div class="flex gap-3 items-center">
                            <input type="text" wire:model="editPayslipDeductions.{{ $index }}.name" placeholder="E.g. LWP" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-[9px] font-bold dark:bg-white/5 dark:border-white/5">
                            <input type="number" wire:model="editPayslipDeductions.{{ $index }}.amount" placeholder="Amount" class="w-32 px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-[9px] font-bold dark:bg-white/5 dark:border-white/5">
                            <button wire:click="removeEditPayslipDeduction({{ $index }})" class="p-2 text-rose-400 hover:text-rose-600"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-10 flex justify-end gap-3 pt-8 border-t border-slate-50 dark:border-white/5">
                    <button wire:click="$set('showEditPayslipModal', false)" class="px-6 py-2.5 text-[8px] font-black uppercase text-slate-400 hover:text-slate-600 transition-all">Discard</button>
                    <button wire:click="savePayslipEdit" class="rounded-xl bg-slate-900 px-10 py-2.5 text-[8px] font-black uppercase text-white shadow-xl shadow-indigo-500/20 active:scale-95 dark:bg-white dark:text-slate-900 transition-all">Commit Changes</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
