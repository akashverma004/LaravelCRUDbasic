@extends('hrms.layouts.app')

@section('title', 'Workflows - PeopleFlow HRMS')

@section('content')
<div
    x-data="workflowInbox({
        dataUrl: '{{ route('workflows.data') }}',
        storeUrl: '{{ route('workflows.store') }}',
        templateStoreUrl: '{{ route('workflows.templates.store') }}',
        templateUpdateBase: '{{ url('/workflows/templates') }}',
        templateArchiveBase: '{{ url('/workflows/templates') }}',
        showUrlBase: '{{ url('/workflows') }}',
        approveUrlBase: '{{ url('/workflows') }}',
        cancelUrlBase: '{{ url('/workflows') }}',
        rejectUrlBase: '{{ url('/workflows') }}',
        resubmitUrlBase: '{{ url('/workflows') }}',
        fulfillUrlBase: '{{ url('/workflows') }}'
    })"
    x-init="init()"
    class="space-y-6 relative"
>
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
    <div class="relative mb-8">
        <div class="relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-3xl font-black tracking-tighter text-slate-900 lg:text-3xl dark:text-white uppercase">
                    Execution <span class="text-cyan-500">Engines</span>
                </h1>
                <p class="mt-2 max-w-xl text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">Systemic tracking of requests, approvals, and fulfillment cycles across the lattice.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    x-show="isAdmin"
                    @click="openTemplateModal()"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-500 shadow-sm transition-all hover:bg-slate-50 hover:text-slate-900 dark:bg-white/5 dark:border-white/10 dark:text-slate-400 dark:hover:text-white"
                    style="display: none;"
                >
                    Design Hub
                </button>
                <button
                    @click="openCreateModal()"
                    :disabled="!canCreate"
                    class="group relative flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 active:scale-95 disabled:opacity-50 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400 transition-all"
                >
                    <span>Initiate Sequence</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        <template x-for="card in summaryCards" :key="card.key">
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-xl dark:border-white/5 dark:bg-slate-900/50">
                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400" x-text="card.label"></p>
                <div class="mt-3 flex items-end justify-between">
                    <p class="text-2xl font-black text-slate-900 dark:text-white leading-none tabular-nums" x-text="summary[card.key] ?? 0"></p>
                    <span class="rounded-lg px-2 py-1 text-[8px] font-black uppercase tracking-widest shadow-sm transition-all group-hover:bg-slate-900 group-hover:text-white" :class="card.tone" x-text="card.hint"></span>
                </div>
            </div>
        </template>
    </div>

    {{-- Filter Bar --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900/50">
        <div class="grid gap-0 xl:grid-cols-4 divide-y xl:divide-y-0 xl:divide-x divide-slate-100 dark:divide-white/5">
            <div class="xl:col-span-2 p-5">
                <label class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 leading-none">Activity Stream Filters</label>
                <input x-model.trim="filters.q" @input.debounce.250ms="fetchRequests()" type="text" placeholder="Title, requester, or content..." class="mt-4 w-full rounded-xl border-0 bg-slate-50 px-5 py-3 text-[11px] font-black uppercase tracking-widest text-slate-900 placeholder-slate-300 focus:ring-4 focus:ring-cyan-500/10 dark:bg-white/5 dark:text-white transition-all">
            </div>
            <div class="grid gap-0 md:grid-cols-3 xl:col-span-2">
                <div class="p-5 border-r border-slate-100 dark:divide-white/5">
                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">View Scope</label>
                    <select x-model="filters.scope" @change="fetchRequests()" class="mt-4 w-full rounded-xl border-0 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-900 focus:ring-4 focus:ring-cyan-500/10 dark:bg-white/5 dark:text-white transition-all appearance-none cursor-pointer">
                        <option value="all">Global Matrix</option>
                        <option value="mine">My Submissions</option>
                        <option value="approvals">Awaiting me</option>
                    </select>
                </div>
                <div class="p-5 border-r border-slate-100 dark:divide-white/5">
                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">State</label>
                    <select x-model="filters.status" @change="fetchRequests()" class="mt-4 w-full rounded-xl border-0 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-900 focus:ring-4 focus:ring-cyan-500/10 dark:bg-white/5 dark:text-white transition-all appearance-none cursor-pointer">
                        <option value="all">Any Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="fulfilled">Fulfilled</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="p-5">
                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Category</label>
                    <select x-model="filters.type" @change="fetchRequests()" class="mt-4 w-full rounded-xl border-0 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-900 focus:ring-4 focus:ring-cyan-500/10 dark:bg-white/5 dark:text-white transition-all appearance-none cursor-pointer">
                        <option value="all">All Types</option>
                        @foreach ($workflowTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Requests Inbox --}}
    <div class="rounded-[2.5rem] border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:px-6">
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Activity Inbox</h2>
                <p class="mt-1 text-xs font-bold uppercase tracking-widest text-slate-400">Streamlined approval pipeline</p>
            </div>
            <button @click="fetchRequests()" class="rounded-xl border border-slate-200 p-2.5 text-slate-400 hover:bg-slate-50 hover:text-slate-600 dark:border-slate-800 dark:hover:bg-slate-950">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>

        <div x-show="loading" class="space-y-4 p-8">
            <template x-for="n in 4" :key="n">
                <div class="h-24 animate-pulse rounded-2xl bg-slate-100 dark:bg-slate-800"></div>
            </template>
        </div>

        <div x-show="!loading && requests.length" class="divide-y divide-slate-50 dark:divide-slate-800" style="display: none;">
            <template x-for="item in requests" :key="item.id">
                <div class="group px-8 py-6 transition-all hover:bg-slate-50/50 dark:hover:bg-slate-800/20">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest" :class="statusTone(item.status)" x-text="item.status"></span>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-slate-500 dark:bg-slate-950 dark:text-slate-400" x-text="item.type_label"></span>
                                <span class="text-[10px] font-bold text-slate-400" x-text="item.submitted_at_short"></span>
                            </div>
                            <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start justify-between">
                                <div class="min-w-0">
                                    <h3 class="text-lg font-black tracking-tight text-slate-900 dark:text-white" x-text="item.title"></h3>
                                    <p class="mt-1 line-clamp-1 text-sm font-medium text-slate-500 dark:text-slate-400" x-text="item.description || 'No additional notes provided.'"></p>
                                </div>
                                <template x-if="item.amount">
                                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-2 text-right dark:border-emerald-500/10 dark:bg-emerald-500/5">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Reimbursement</p>
                                        <p class="mt-0.5 text-lg font-black text-emerald-900 dark:text-emerald-300">₹<span x-text="item.amount"></span></p>
                                    </div>
                                </template>
                            </div>
                            <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                <span class="flex items-center gap-1.5"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg> <span class="text-slate-600 dark:text-slate-200" x-text="item.requester_name"></span></span>
                                <span x-show="item.pending_approvers.length" class="flex items-center gap-1.5"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> Awaiting: <span class="text-cyan-600 dark:text-cyan-400" x-text="item.pending_approvers.join(', ')"></span></span>
                                <span x-show="item.has_attachment" class="flex items-center gap-1.5"><svg class="h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg> Document attached</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                            <button @click="openTimeline(item.id)" class="rounded-xl border border-slate-200 px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:text-slate-400">View History</button>
                            <button x-show="item.can_cancel" @click="cancelRequest(item)" class="rounded-xl border border-rose-200 px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-rose-600 hover:bg-rose-50 dark:border-rose-900/40 dark:text-rose-400" style="display: none;">Cancel</button>
                            <button x-show="item.can_approve" @click="openDecisionModal(item, 'approve')" class="rounded-xl bg-emerald-500 px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:bg-emerald-600 shadow-lg shadow-emerald-500/20" style="display: none;">Approve</button>
                            <button x-show="item.can_approve" @click="openDecisionModal(item, 'reject')" class="rounded-xl bg-rose-500 px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:bg-rose-600 shadow-lg shadow-rose-500/20" style="display: none;">Reject</button>
                            <button x-show="item.can_fulfill" @click="openFulfillModal(item)" class="rounded-xl bg-cyan-400 px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-950 hover:bg-cyan-300" style="display: none;">Fulfill Asset</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty State --}}
        <div x-show="!loading && !requests.length" class="flex flex-col items-center justify-center py-20 text-center" style="display: none;">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-[2rem] bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-700">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-xl font-black text-slate-900 dark:text-white">Workflow Silence</h3>
            <p class="mt-2 text-slate-500 dark:text-slate-400">No requests currently in your pipeline. Take some time to breathe.</p>
        </div>
    </div>

    {{-- Create Request Modal --}}
    <div x-show="modals.create" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/40 p-3 sm:items-center backdrop-blur-sm" style="display: none;">
        <div @click.away="closeModal('create')" class="my-4 flex max-h-[calc(100vh-2rem)] w-full max-w-2xl flex-col overflow-hidden rounded-[2rem] border border-white/20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:px-6">
                <div>
                    <h3 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white" x-text="formMode === 'resubmit' ? 'Edit & Resubmit' : 'New Web Request'"></h3>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Initialize a new organizational process</p>
                </div>
                <button @click="closeModal('create')" class="rounded-2xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="overflow-y-auto px-5 py-5 sm:px-6 sm:py-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Process Category</label>
                        <select x-model="form.type" @change="handleTypeChange()" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-bold text-slate-900 transition-all focus:border-cyan-400 focus:bg-white focus:ring-0 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                            @foreach ($workflowTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Template Logic</label>
                        <select x-model="form.workflow_template_id" @change="applySelectedTemplate()" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-bold text-slate-900 transition-all focus:border-cyan-400 focus:bg-white focus:ring-0 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                            <option value="">No predefined template</option>
                            <template x-for="template in filteredTemplates()" :key="template.id">
                                <option :value="String(template.id)" x-text="template.name"></option>
                            </template>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Subject / Title</label>
                        <input x-model.trim="form.title" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-bold text-slate-900 placeholder-slate-300 focus:border-cyan-400 focus:bg-white focus:ring-0 dark:border-slate-800 dark:bg-slate-950 dark:text-white" placeholder="Specific goal of this request">
                    </div>
                    
                    <div x-show="form.type === 'reimbursement'" class="sm:col-span-2" style="display: none;">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Claim Amount (INR)</label>
                        <input x-model="form.amount" type="number" step="0.01" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-bold text-slate-900 transition-all focus:border-cyan-400 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Detailed Context</label>
                        <textarea x-model.trim="form.description" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-bold text-slate-900 placeholder-slate-300 focus:border-cyan-400 focus:bg-white focus:ring-0 dark:border-slate-800 dark:bg-slate-950 dark:text-white"></textarea>
                    </div>

                    {{-- Dynamic Sub-forms --}}
                    <template x-if="form.type === 'reimbursement'">
                        <div class="sm:col-span-2 grid gap-6 rounded-3xl border border-cyan-100 bg-cyan-50/20 p-6 dark:border-cyan-900/30 dark:bg-cyan-950/20">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-cyan-600">Expense Cluster</label>
                                    <select x-model="form.details.category" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900">
                                        <option value="">Select Cluster</option>
                                        <option value="travel">Logistics & Travel</option>
                                        <option value="meal">Business Meals</option>
                                        <option value="internet">Utility / ISP</option>
                                        <option value="office-supplies">Supplies</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-cyan-600">Timestamp</label>
                                    <input x-model="form.details.expense_date" type="date" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900">
                                </div>
                            </div>
                        </div>
                    </template>


                    <template x-if="form.type === 'asset-request'">
                        <div class="sm:col-span-2 grid gap-6 rounded-3xl border border-cyan-100 bg-cyan-50/20 p-6 dark:border-cyan-900/30 dark:bg-cyan-950/20">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-cyan-600">Asset Category</label>
                                    <select x-model="form.details.asset_category" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900">
                                        <option value="">Select Category</option>
                                        <option value="laptop">Laptop</option>
                                        <option value="peripheral">Peripheral</option>
                                        <option value="furniture">Furniture</option>
                                        <option value="keys">Keys</option>
                                        <option value="licence">Licence</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-cyan-600">Urgency</label>
                                    <select x-model="form.details.urgency" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900">
                                        <option value="">Select Urgency</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-cyan-600">Needed By</label>
                                    <input x-model="form.details.needed_by" type="date" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900">
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-cyan-600">Preferred Model</label>
                                    <input x-model.trim="form.details.preferred_model" type="text" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900" placeholder="Optional model or specification">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-cyan-600">Business Reason</label>
                                    <textarea x-model.trim="form.details.business_reason" rows="3" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900" placeholder="Why this asset is needed for work"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="form.type === 'asset-return'">
                        <div class="sm:col-span-2 grid gap-6 rounded-3xl border border-emerald-100 bg-emerald-50/40 p-6 dark:border-emerald-900/30 dark:bg-emerald-950/10">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-emerald-600">Assigned Asset</label>
                                    <input :value="form.details.asset_name || 'Start this flow from Assets'" type="text" disabled class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 opacity-80 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-emerald-600">Serial Number</label>
                                    <input :value="form.details.serial_number || 'Not available'" type="text" disabled class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 opacity-80 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-emerald-600">Condition</label>
                                    <select x-model="form.details.return_condition" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900">
                                        <option value="">Select Condition</option>
                                        <option value="good">Good</option>
                                        <option value="minor-issues">Minor Issues</option>
                                        <option value="damaged">Damaged</option>
                                        <option value="unknown">Unknown</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-emerald-600">Return Date</label>
                                    <input x-model="form.details.requested_return_date" type="date" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-emerald-600">Return Note</label>
                                    <textarea x-model.trim="form.details.reason" rows="3" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900" placeholder="Any handover note, issue, or context for the return"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="form.type === 'asset-repair'">
                        <div class="sm:col-span-2 grid gap-6 rounded-3xl border border-amber-100 bg-amber-50/40 p-6 dark:border-amber-900/30 dark:bg-amber-950/10">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-amber-600">Affected Asset</label>
                                    <input :value="form.details.asset_name || 'Start this flow from Assets'" type="text" disabled class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 opacity-80 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-amber-600">Serial Number</label>
                                    <input :value="form.details.serial_number || 'Not available'" type="text" disabled class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 opacity-80 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-amber-600">Issue Type</label>
                                    <select x-model="form.details.issue_type" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900">
                                        <option value="">Select Issue</option>
                                        <option value="hardware">Hardware</option>
                                        <option value="software">Software</option>
                                        <option value="accessory">Accessory</option>
                                        <option value="wear-tear">Wear & Tear</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-amber-600">Current Condition</label>
                                    <select x-model="form.details.reported_condition" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900">
                                        <option value="">Select Condition</option>
                                        <option value="working">Working</option>
                                        <option value="partially-working">Partially Working</option>
                                        <option value="not-working">Not Working</option>
                                        <option value="damaged">Damaged</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-amber-600">Reported On</label>
                                    <input x-model="form.details.reported_at" type="date" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-amber-600">Problem Summary</label>
                                    <textarea x-model.trim="form.details.reason" rows="3" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold dark:border-slate-800 dark:bg-slate-900" placeholder="Describe the issue, symptoms, and impact on work"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Attachments --}}
                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Supportive Evidence</label>
                        <div class="relative mt-2 flex h-32 w-full cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 transition-colors hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-950">
                            <input @change="handleAttachment($event)" type="file" class="absolute inset-0 z-10 opacity-0 cursor-pointer">
                            <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                            <p class="mt-2 text-xs font-bold text-slate-500" x-text="attachmentName || 'Click or drag documents here'"></p>
                        </div>
                    </div>
                </div>

                <template x-if="formErrors.length">
                    <div class="mt-6 rounded-2xl bg-rose-500/10 p-5 text-sm font-bold text-rose-500">
                        <template x-for="error in formErrors" :key="error">
                            <p x-text="error"></p>
                        </template>
                    </div>
                </template>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 px-8 py-6 dark:border-slate-800">
                <button @click="closeModal('create')" class="rounded-2xl border border-slate-200 px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:text-slate-400">Cancel</button>
                <button @click="submitRequest()" :disabled="saving" class="group relative flex items-center gap-3 overflow-hidden rounded-2xl bg-slate-900 px-8 py-4 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-cyan-500 hover:text-slate-950 disabled:opacity-50 dark:bg-white dark:text-slate-950 dark:hover:bg-cyan-400">
                    <svg x-show="saving" class="h-4 w-4 animate-spin text-current" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-text="formMode === 'resubmit' ? 'Republish Request' : 'Broadcast Request'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Decision Modal --}}
    <div x-show="modals.decision" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/40 p-3 sm:items-center backdrop-blur-sm" style="display: none;">
        <div @click.away="closeModal('decision')" class="my-4 flex max-h-[calc(100vh-2rem)] w-full max-w-md flex-col overflow-hidden rounded-[2rem] border border-white/20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md shadow-2xl">
            <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:px-6">
                <h3 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white" x-text="decisionMode === 'approve' ? 'Review & Approve' : 'Record Rejection'"></h3>
                <p class="mt-1 text-[10px] font-black uppercase tracking-widest text-slate-400" x-text="selectedRequest?.title"></p>
            </div>
            <div class="overflow-y-auto px-5 py-5 sm:px-6 sm:py-6">
                <div class="rounded-3xl bg-slate-50/50 p-6 dark:bg-slate-950/40">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Executive Summary</p>
                    <p class="mt-3 text-sm font-bold text-slate-900 dark:text-white" x-text="selectedRequest?.type_label"></p>
                    <p class="mt-2 text-xs font-medium text-slate-500 dark:text-slate-400 line-clamp-3" x-text="selectedRequest?.description"></p>
                </div>

                <div class="mt-6">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Strategic Commentary</label>
                    <textarea x-model.trim="decisionComment" rows="3" :placeholder="decisionMode === 'approve' ? 'Strategic notes for the requester...' : 'Justification for rejection is mandatory.'" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-bold text-slate-900 focus:border-cyan-400 focus:bg-white focus:ring-0 dark:border-slate-800 dark:bg-slate-950 dark:text-white"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 px-8 py-6 dark:border-slate-800">
                <button @click="closeModal('decision')" class="rounded-2xl border border-slate-200 px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:text-slate-400">Dismiss</button>
                <button @click="submitDecision()" :disabled="savingDecision" class="rounded-2xl px-8 py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl transition-all disabled:opacity-50" :class="decisionMode === 'approve' ? 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20' : 'bg-rose-500 hover:bg-rose-600 shadow-rose-500/20'">
                    <span x-text="decisionMode === 'approve' ? 'Confirm Approval' : 'Submit Rejection'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Timeline Modal --}}
    <div x-show="modals.timeline" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/40 p-3 sm:items-center backdrop-blur-sm" style="display: none;">
        <div @click.away="closeModal('timeline')" class="my-4 flex max-h-[calc(100vh-2rem)] w-full max-w-4xl flex-col overflow-hidden rounded-[2rem] border border-white/20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md shadow-2xl">
            <div class="flex items-start justify-between gap-4 overflow-hidden px-5 py-5 sm:px-6 sm:py-6">
                <div class="flex-1 min-w-0">
                    <h3 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white" x-text="selectedRequest?.title"></h3>
                    <p class="mt-2 text-sm font-medium text-slate-500 dark:text-slate-400" x-text="selectedRequest?.description"></p>
                </div>
                <button @click="closeModal('timeline')" class="rounded-2xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="overflow-y-auto px-5 pb-6 sm:px-6">
                <div class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
                    <div class="space-y-6">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Audit Trail</p>
                        <div class="relative space-y-4">
                            <div class="absolute left-6 top-4 bottom-4 w-px bg-slate-100 dark:bg-slate-800"></div>
                            <template x-for="step in timeline" :key="step.id">
                                <div class="relative flex items-start gap-4">
                                    <div class="z-10 mt-1.5 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-xs font-black shadow-sm dark:bg-slate-800" :class="decisionTone(step.decision)">
                                        <span x-text="step.approver_name.substring(0, 1)"></span>
                                    </div>
                                    <div class="flex-1 rounded-3xl border border-slate-100 bg-slate-50/40 p-5 dark:border-slate-800 dark:bg-slate-950/30">
                                        <div class="flex items-center justify-between gap-2">
                                            <div>
                                                <p class="text-sm font-black text-slate-900 dark:text-white" x-text="step.approver_name"></p>
                                                <p class="mt-0.5 text-[9px] font-black uppercase tracking-widest text-slate-400" x-text="step.step_label"></p>
                                            </div>
                                            <span class="rounded-full px-2.5 py-1 text-[9px] font-black uppercase tracking-widest" :class="decisionTone(step.decision)" x-text="step.decision"></span>
                                        </div>
                                        <p class="mt-3 text-sm font-medium text-slate-600 dark:text-slate-400" x-text="step.comment || 'Decision logged without specific commentary.'"></p>
                                        <p class="mt-3 text-[9px] font-bold text-slate-400" x-text="step.acted_at || 'Awaiting Action'"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-950/40">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Technical Payload</p>
                            <div class="mt-6 space-y-4">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Submission State</p>
                                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white" x-text="selectedRequest?.status"></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Transmission Date</p>
                                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white" x-text="selectedRequest?.submitted_at"></p>
                                </div>
                                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Structured Variables</p>
                                    <div class="mt-4 space-y-3">
                                        <template x-for="entry in detailsEntries" :key="entry.key">
                                            <div class="flex items-start justify-between gap-2">
                                                <span class="text-[11px] font-bold text-slate-400" x-text="entry.key"></span>
                                                <span class="text-[11px] font-black text-slate-900 dark:text-white" x-text="entry.value"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div x-show="selectedRequest?.attachment" class="pt-4" style="display: none;">
                                    <a :href="selectedRequest?.attachment?.download_url" class="group flex items-center justify-between rounded-2xl bg-slate-900 p-4 text-white hover:bg-cyan-500 hover:text-slate-950 transition-all dark:bg-white dark:text-slate-950 dark:hover:bg-cyan-400">
                                        <div class="min-w-0">
                                            <p class="text-[9px] font-black uppercase tracking-tight opacity-60">Verified Document</p>
                                            <p class="truncate text-[11px] font-black" x-text="selectedRequest?.attachment?.name"></p>
                                        </div>
                                        <svg class="h-5 w-5 opacity-40 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Template Management Modal --}}
    <div x-show="modals.templates" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/40 p-3 sm:items-center backdrop-blur-sm" style="display: none;">
        <div @click.away="closeModal('templates')" class="my-4 flex max-h-[calc(100vh-2rem)] w-full max-w-4xl flex-col overflow-hidden rounded-[2rem] border border-white/20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:px-6">
                <div>
                    <h3 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Workflow Governance</h3>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Configure approval hierarchies & defaults</p>
                </div>
                <button @click="closeModal('templates')" class="rounded-2xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="overflow-y-auto p-5 sm:p-6">
                <div class="grid gap-6 lg:grid-cols-[0.9fr,1.1fr]">
                    {{-- Templates Grid --}}
                    <div class="space-y-4">
                        <template x-if="!templates.length">
                            <div class="rounded-3xl border-2 border-dashed border-slate-100 py-16 text-center">
                                <p class="text-sm font-bold text-slate-300">No organizational templates defined.</p>
                            </div>
                        </template>
                        <template x-for="template in templates" :key="template.id">
                            <button @click="editTemplate(template)" class="w-full rounded-[2rem] border border-slate-100 bg-white p-6 text-left shadow-sm transition-all hover:border-cyan-400 dark:border-slate-800 dark:bg-slate-950/40">
                                <div class="flex items-start justify-between">
                                    <div class="min-w-0">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-cyan-600" x-text="template.type_label"></p>
                                        <h4 class="mt-1.5 text-md font-black tracking-tight text-slate-900 dark:text-white" x-text="template.name"></h4>
                                    </div>
                                    <span class="rounded-full px-2.5 py-1 text-[9px] font-black uppercase tracking-widest" :class="template.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'" x-text="template.is_active ? 'Active' : 'Archived'"></span>
                                </div>
                                <div class="mt-6 flex flex-wrap gap-2">
                                    <template x-for="step in template.approval_summary" :key="step">
                                        <span class="rounded-xl bg-slate-50 px-3 py-1.5 text-[10px] font-bold text-slate-500 dark:bg-slate-900" x-text="step"></span>
                                    </template>
                                </div>
                            </button>
                        </template>
                    </div>

                    {{-- Template Editor --}}
                    <div class="rounded-[1.5rem] border border-slate-100 bg-slate-50/50 p-5 dark:border-slate-800 dark:bg-slate-950/30 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-lg font-black tracking-tight text-slate-900 dark:text-white" x-text="templateForm.id ? 'Edit Configuration' : 'Design Template'"></h4>
                            <button @click="resetTemplateForm()" class="text-[9px] font-black uppercase tracking-widest text-slate-400 hover:text-cyan-500">Reset Canvas</button>
                        </div>
                        
                        <div class="grid gap-6">
                            <div>
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400">Logical Root</label>
                                <select x-model="templateForm.type" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm font-bold dark:border-slate-800 dark:bg-slate-900">
                                    @foreach ($workflowTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400">Template Identifier</label>
                                <input x-model.trim="templateForm.name" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm font-bold dark:border-slate-800 dark:bg-slate-900" placeholder="e.g. Senior Staff Reimbursement">
                            </div>
                            
                            {{-- Approval Chain --}}
                            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div class="flex items-center justify-between">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Approval Hierarchy</p>
                                    <button @click="addTemplateStep()" class="text-[10px] font-black uppercase tracking-widest text-cyan-600">+ Append Step</button>
                                </div>
                                <div class="space-y-3">
                                    <template x-for="(step, index) in templateForm.approval_steps" :key="index">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-900 text-[11px] font-black text-white" x-text="index + 1"></div>
                                            <select x-model="step.role" class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold dark:border-slate-800 dark:bg-slate-900">
                                                <option value="manager">Direct Manager</option>
                                                <option value="hr_manager">HR Specialist</option>
                                                <option value="admin">Global Admin</option>
                                            </select>
                                            <button @click="removeTemplateStep(index)" class="text-rose-500 hover:text-rose-600">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end gap-3">
                            <button @click="submitTemplate()" :disabled="savingTemplate" class="w-full rounded-2xl bg-slate-900 py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl transition-all hover:bg-cyan-500 hover:text-slate-950 dark:bg-white dark:text-slate-950 dark:hover:bg-cyan-400">
                                <span x-text="templateForm.id ? 'Push Updates' : 'Publish Template'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

