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
    class="space-y-6"
>
    <div
        x-show="toast.show"
        x-transition
        class="fixed bottom-6 right-6 z-50 rounded-xl px-4 py-3 text-sm font-semibold shadow-2xl"
        :class="toast.type === 'success' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white'"
        style="display: none;"
    >
        <span x-text="toast.message"></span>
    </div>

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Workflows</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Track requests, approvals, and decisions without leaving the page.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                x-show="isAdmin"
                @click="openTemplateModal()"
                class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                style="display: none;"
            >
                Manage Templates
            </button>
            <button
                @click="openCreateModal()"
                :disabled="!canCreate"
                class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <span>New Request</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <template x-for="card in summaryCards" :key="card.key">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400" x-text="card.label"></p>
                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-black text-slate-900 dark:text-white" x-text="summary[card.key] ?? 0"></p>
                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider" :class="card.tone" x-text="card.hint"></span>
                </div>
            </div>
        </template>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-3 xl:flex-row xl:items-end">
            <div class="flex-1">
                <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Search</label>
                <input x-model.trim="filters.q" @input.debounce.250ms="fetchRequests()" type="text" placeholder="Title, requester, or employee" class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            </div>
            <div class="grid flex-1 gap-3 md:grid-cols-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Scope</label>
                    <select x-model="filters.scope" @change="fetchRequests()" class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="all">All visible</option>
                        <option value="mine">My requests</option>
                        <option value="approvals">Awaiting me</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Status</label>
                    <select x-model="filters.status" @change="fetchRequests()" class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="all">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="fulfilled">Fulfilled</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Type</label>
                    <select x-model="filters.type" @change="fetchRequests()" class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="all">All types</option>
                        @foreach ($workflowTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Inbox</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Compact, async approvals with timeline details in place.</p>
            </div>
            <button @click="fetchRequests()" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Refresh</button>
        </div>

        <div x-show="loading" class="space-y-3 p-6" style="display: none;">
            <template x-for="index in 4" :key="index">
                <div class="h-20 animate-pulse rounded-2xl bg-slate-100 dark:bg-slate-800"></div>
            </template>
        </div>

        <div x-show="!loading && requests.length" class="divide-y divide-slate-100 dark:divide-slate-800" style="display: none;">
            <template x-for="item in requests" :key="item.id">
                <div class="group flex flex-col gap-4 px-6 py-5 transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider" :class="statusTone(item.status)" x-text="item.status"></span>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-600 dark:bg-slate-800 dark:text-slate-300" x-text="item.type_label"></span>
                            <span class="text-[11px] font-medium text-slate-400" x-text="item.submitted_at_short"></span>
                        </div>
                        <div class="mt-3 flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-sm font-bold text-slate-900 dark:text-white" x-text="item.title"></h3>
                                <p class="mt-1 line-clamp-2 text-sm text-slate-500 dark:text-slate-400" x-text="item.description || 'No description added.'"></p>
                            </div>
                            <template x-if="item.amount">
                                <div class="rounded-2xl bg-emerald-50 px-3 py-2 text-right dark:bg-emerald-500/10">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Amount</p>
                                    <p class="text-sm font-black text-emerald-700 dark:text-emerald-300">INR <span x-text="item.amount"></span></p>
                                </div>
                            </template>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-[11px] text-slate-400">
                            <span>Requester: <span class="font-semibold text-slate-500 dark:text-slate-300" x-text="item.requester_name"></span></span>
                            <span x-show="item.employee_name">Employee: <span class="font-semibold text-slate-500 dark:text-slate-300" x-text="item.employee_name"></span></span>
                            <span x-show="item.template_name">Template: <span class="font-semibold text-slate-500 dark:text-slate-300" x-text="item.template_name"></span></span>
                            <span x-show="item.pending_approvers.length">Pending with: <span class="font-semibold text-slate-500 dark:text-slate-300" x-text="item.pending_approvers.join(', ')"></span></span>
                            <span x-show="item.details_preview">Details: <span class="font-semibold text-slate-500 dark:text-slate-300" x-text="item.details_preview"></span></span>
                            <span x-show="item.has_attachment">Attachment: <span class="font-semibold text-slate-500 dark:text-slate-300" x-text="item.attachment_name"></span></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 lg:opacity-0 lg:transition lg:group-hover:opacity-100">
                        <button @click="openTimeline(item.id)" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Timeline</button>
                        <button x-show="item.can_cancel" @click="cancelRequest(item)" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800" style="display: none;">Cancel</button>
                        <button x-show="item.can_resubmit" @click="openResubmitModal(item.id)" class="rounded-xl border border-cyan-200 px-3 py-2 text-xs font-semibold text-cyan-700 hover:bg-cyan-50 dark:border-cyan-900/40 dark:text-cyan-300 dark:hover:bg-slate-800" style="display: none;">Edit & Resubmit</button>
                        <button x-show="item.can_fulfill" @click="openFulfillModal(item)" class="rounded-xl bg-amber-400 px-3 py-2 text-xs font-semibold text-slate-950 hover:bg-amber-300" style="display: none;">Fulfill</button>
                        <button x-show="item.can_approve" @click="openDecisionModal(item, 'approve')" class="rounded-xl bg-emerald-500 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-600" style="display: none;">Approve</button>
                        <button x-show="item.can_approve" @click="openDecisionModal(item, 'reject')" class="rounded-xl bg-rose-500 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-600" style="display: none;">Reject</button>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="!loading && !requests.length" class="flex flex-col items-center justify-center px-6 py-16 text-center" style="display: none;">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">No workflow requests yet</h3>
            <p class="mt-1 max-w-md text-sm text-slate-500 dark:text-slate-400">Create the first request from this screen. Approvals and decisions stay here too.</p>
        </div>
    </div>

    <div x-show="modals.create" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/60 p-3 sm:items-center backdrop-blur-sm" style="display: none;">
        <div @click.away="closeModal('create')" class="my-4 flex max-h-[calc(100vh-2rem)] w-full max-w-3xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="formMode === 'resubmit' ? 'Edit and Resubmit Request' : 'New Workflow Request'"></h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400" x-text="formMode === 'resubmit' ? 'Update the rejected request and send it back through approvals.' : 'Submit a request without leaving the inbox.'"></p>
                </div>
                <button @click="closeModal('create')" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="overflow-y-auto px-4 py-4 sm:px-5">
                <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Type</label>
                    <select x-model="form.type" @change="handleTypeChange()" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        @foreach ($workflowTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Template</label>
                    <select x-model="form.workflow_template_id" @change="applySelectedTemplate()" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="">No template</option>
                        <template x-for="template in filteredTemplates()" :key="template.id">
                            <option :value="String(template.id)" x-text="template.name"></option>
                        </template>
                    </select>
                </div>
                <div x-show="form.type === 'reimbursement'">
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Amount</label>
                    <input x-model="form.amount" type="number" step="0.01" min="0" placeholder="Optional" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Title</label>
                    <input x-model.trim="form.title" type="text" maxlength="255" placeholder="Example: Reimbursement for March client travel" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Description</label>
                    <textarea x-model.trim="form.description" rows="3" maxlength="1500" placeholder="Add concise context for approvers." class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                </div>
                <template x-if="form.type === 'reimbursement'">
                    <div class="sm:col-span-2 grid gap-3 rounded-2xl border border-cyan-100 bg-cyan-50/70 p-3.5 dark:border-cyan-900/40 dark:bg-cyan-950/20 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Category</label>
                            <select x-model="form.details.category" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="">Select category</option>
                                <option value="travel">Travel</option>
                                <option value="meal">Meal</option>
                                <option value="internet">Internet</option>
                                <option value="office-supplies">Office Supplies</option>
                                <option value="wellness">Wellness</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Expense Date</label>
                            <input x-model="form.details.expense_date" type="date" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Merchant / Vendor</label>
                            <input x-model.trim="form.details.merchant" type="text" maxlength="255" placeholder="Example: Indigo Airlines" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Receipt Reference</label>
                            <input x-model.trim="form.details.receipt_reference" type="text" maxlength="255" placeholder="Receipt no. / invoice no." class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Claim Notes</label>
                            <textarea x-model.trim="form.details.notes" rows="2" maxlength="1000" placeholder="Short explanation for the expense." class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                        </div>
                    </div>
                </template>
                <template x-if="form.type === 'asset-request'">
                    <div class="sm:col-span-2 grid gap-3 rounded-2xl border border-amber-100 bg-amber-50/70 p-3.5 dark:border-amber-900/40 dark:bg-amber-950/20 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Asset Category</label>
                            <select x-model="form.details.asset_category" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="">Select category</option>
                                <option value="laptop">Laptop / Computer</option>
                                <option value="peripheral">Peripheral</option>
                                <option value="furniture">Furniture</option>
                                <option value="keys">Physical Keys</option>
                                <option value="licence">Software Licence</option>
                                <option value="other">Other Equipment</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Urgency</label>
                            <select x-model="form.details.urgency" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="">Select urgency</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Needed By</label>
                            <input x-model="form.details.needed_by" type="date" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Preferred Model</label>
                            <input x-model.trim="form.details.preferred_model" type="text" maxlength="255" placeholder="Example: Dell 27 inch monitor" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Business Reason</label>
                            <textarea x-model.trim="form.details.business_reason" rows="2" maxlength="1000" placeholder="Explain why the asset is needed." class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                        </div>
                    </div>
                </template>
                <template x-if="form.type === 'profile-change'">
                    <div class="sm:col-span-2 grid gap-3 rounded-2xl border border-violet-100 bg-violet-50/70 p-3.5 dark:border-violet-900/40 dark:bg-violet-950/20 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Field Name</label>
                            <input x-model.trim="form.details.field_name" type="text" maxlength="100" placeholder="Example: phone or address" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Effective From</label>
                            <input x-model="form.details.effective_from" type="date" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Current Value</label>
                            <input x-model.trim="form.details.current_value" type="text" maxlength="255" placeholder="Existing value" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Requested Value</label>
                            <input x-model.trim="form.details.requested_value" type="text" maxlength="255" placeholder="New value" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Reason</label>
                            <textarea x-model.trim="form.details.reason" rows="2" maxlength="1000" placeholder="Why this profile change is needed." class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                        </div>
                    </div>
                </template>
                <template x-if="form.type === 'salary-change'">
                    <div class="sm:col-span-2 grid gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/70 p-3.5 dark:border-emerald-900/40 dark:bg-emerald-950/20 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Change Type</label>
                            <select x-model="form.details.change_type" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="">Select change type</option>
                                <option value="raise">Raise</option>
                                <option value="promotion">Promotion</option>
                                <option value="adjustment">Adjustment</option>
                                <option value="correction">Correction</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Requested Salary</label>
                            <input x-model="form.details.requested_salary" type="number" step="0.01" min="1" placeholder="New salary" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Effective From</label>
                            <input x-model="form.details.effective_from" type="date" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Justification</label>
                            <textarea x-model.trim="form.details.justification" rows="2" maxlength="1000" placeholder="Why the salary change is requested." class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                        </div>
                    </div>
                </template>
                <template x-if="form.type === 'offboarding'">
                    <div class="sm:col-span-2 grid gap-3 rounded-2xl border border-rose-100 bg-rose-50/70 p-3.5 dark:border-rose-900/40 dark:bg-rose-950/20 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Last Working Day</label>
                            <input x-model="form.details.last_working_day" type="date" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Exit Type</label>
                            <select x-model="form.details.exit_type" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="">Select exit type</option>
                                <option value="resignation">Resignation</option>
                                <option value="termination">Termination</option>
                                <option value="retirement">Retirement</option>
                                <option value="contract-end">Contract End</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Handover Owner</label>
                            <input x-model.trim="form.details.handover_owner" type="text" maxlength="255" placeholder="Manager or teammate" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Reason</label>
                            <textarea x-model.trim="form.details.reason" rows="2" maxlength="1000" placeholder="Context for the offboarding request." class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                        </div>
                    </div>
                </template>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Attachment</label>
                    <input @change="handleAttachment($event)" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="mt-1.5 block w-full rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-cyan-500 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-950 hover:file:bg-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">
                    <p class="mt-2 text-xs text-slate-400">Optional. PDFs and image receipts up to 10 MB.</p>
                    <p x-show="attachmentName" class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-300" style="display: none;">Selected: <span x-text="attachmentName"></span></p>
                </div>
            </div>

                <template x-if="formErrors.length">
                    <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300">
                        <template x-for="error in formErrors" :key="error">
                            <p x-text="error"></p>
                        </template>
                    </div>
                </template>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 px-5 py-4 dark:border-slate-800">
                <button @click="closeModal('create')" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</button>
                <button @click="submitRequest()" :disabled="saving" class="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-3 py-2 text-xs font-semibold text-slate-950 hover:bg-cyan-400 disabled:opacity-50">
                    <svg x-show="saving" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" style="display: none;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-text="formMode === 'resubmit' ? 'Resubmit Request' : 'Submit Request'"></span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="modals.templates" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/60 p-3 sm:items-center backdrop-blur-sm" style="display: none;">
        <div @click.away="closeModal('templates')" class="my-4 flex max-h-[calc(100vh-2rem)] w-full max-w-5xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Workflow Templates</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Define approval chains and default request copy without adding more pages.</p>
                </div>
                <button @click="closeModal('templates')" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="overflow-y-auto px-4 py-4 sm:px-5">
                <div class="grid gap-4 lg:grid-cols-[1.1fr,0.9fr]">
                <div class="space-y-2">
                    <template x-if="!templates.length">
                        <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            No templates saved yet.
                        </div>
                    </template>
                    <template x-for="template in templates" :key="template.id">
                        <button type="button" @click="editTemplate(template)" class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-3 text-left transition hover:border-cyan-300 hover:bg-cyan-50 dark:border-slate-800 dark:bg-slate-950/50 dark:hover:border-cyan-700 dark:hover:bg-slate-800/70">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white" x-text="template.name"></p>
                                    <p class="mt-1 text-xs uppercase tracking-wide text-slate-400" x-text="template.type_label"></p>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider" :class="template.is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300'" x-text="template.is_active ? 'Active' : 'Inactive'"></span>
                            </div>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400" x-text="template.description || 'No description added.'"></p>
                            <div class="mt-3 flex flex-wrap gap-2 text-[11px] text-slate-500">
                                <template x-for="step in template.approval_summary" :key="step">
                                    <span class="rounded-full bg-white px-2.5 py-1 font-semibold dark:bg-slate-900" x-text="step"></span>
                                </template>
                            </div>
                            <div class="mt-3 flex justify-end">
                                <button
                                    type="button"
                                    x-show="template.is_active"
                                    @click.stop="archiveTemplate(template)"
                                    class="rounded-lg border border-rose-200 px-3 py-1.5 text-[11px] font-semibold text-rose-600 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-950/30"
                                    style="display: none;"
                                >
                                    Archive
                                </button>
                            </div>
                        </button>
                    </template>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-lg font-bold text-slate-900 dark:text-white" x-text="templateForm.id ? 'Edit Template' : 'New Template'"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Templates control default titles and approver roles.</p>
                        </div>
                        <button @click="resetTemplateForm()" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-white dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-900">Reset</button>
                    </div>

                    <div class="mt-4 grid gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Type</label>
                            <select x-model="templateForm.type" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                @foreach ($workflowTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Name</label>
                            <input x-model.trim="templateForm.name" type="text" maxlength="255" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Description</label>
                            <textarea x-model.trim="templateForm.description" rows="2" maxlength="1500" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Default Title</label>
                            <input x-model.trim="templateForm.default_title" type="text" maxlength="255" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Default Description</label>
                            <textarea x-model.trim="templateForm.default_description" rows="2" maxlength="1500" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                        </div>
                        <div>
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Approval Steps</label>
                                <button @click="addTemplateStep()" type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-white dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-900">Add Step</button>
                            </div>
                            <div class="mt-2 space-y-3">
                                <template x-for="(step, index) in templateForm.approval_steps" :key="index">
                                    <div class="grid gap-3 md:grid-cols-[0.9fr,1.1fr,auto]">
                                        <select x-model="step.role" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                            <option value="manager">Manager</option>
                                            <option value="hr_manager">HR Manager</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                        <input x-model.trim="step.label" type="text" maxlength="100" placeholder="Example: Finance Review" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                        <button @click="removeTemplateStep(index)" type="button" class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-950/30">Remove</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-600 dark:text-slate-300">
                            <input x-model="templateForm.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-cyan-500 focus:ring-cyan-500">
                            <span>Template is active</span>
                        </label>
                    </div>

                    <template x-if="templateErrors.length">
                        <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300">
                            <template x-for="error in templateErrors" :key="error">
                                <p x-text="error"></p>
                            </template>
                        </div>
                    </template>

                </div>
            </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 px-5 py-4 dark:border-slate-800">
                <button @click="closeModal('templates')" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-white dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-900">Close</button>
                <button @click="submitTemplate()" :disabled="savingTemplate" class="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-3 py-2 text-xs font-semibold text-slate-950 hover:bg-cyan-400 disabled:opacity-50">
                    <svg x-show="savingTemplate" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" style="display: none;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-text="templateForm.id ? 'Update Template' : 'Save Template'"></span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="modals.fulfill" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/60 p-3 sm:items-center backdrop-blur-sm" style="display: none;">
        <div @click.away="closeModal('fulfill')" class="my-4 flex max-h-[calc(100vh-2rem)] w-full max-w-xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Fulfill Asset Request</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400" x-text="selectedRequest?.title || 'Assign an available asset without leaving the inbox.'"></p>
                </div>
                <button @click="closeModal('fulfill')" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="overflow-y-auto px-4 py-4 sm:px-5">
            <div class="rounded-2xl bg-slate-50 p-3 dark:bg-slate-800/70">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Request Summary</p>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Employee</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white" x-text="selectedRequest?.employee_name || '-'"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Requested</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white" x-text="selectedRequest?.details_preview || '-'"></p>
                    </div>
                </div>
            </div>

            <div class="mt-4 space-y-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Available Asset</label>
                    <select x-model="fulfillForm.asset_id" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="">Select asset</option>
                        <template x-for="asset in availableAssets" :key="asset.id">
                            <option :value="String(asset.id)" x-text="assetOptionLabel(asset)"></option>
                        </template>
                    </select>
                    <p x-show="!availableAssets.length" class="mt-2 text-xs text-amber-600 dark:text-amber-300" style="display: none;">No assets are currently available in inventory.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Fulfillment Note</label>
                    <textarea x-model.trim="fulfillForm.comment" rows="2" maxlength="1000" placeholder="Optional note for the employee." class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                </div>
            </div>

            <template x-if="fulfillError">
                <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300" x-text="fulfillError"></div>
            </template>

            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 px-5 py-4 dark:border-slate-800">
                <button @click="closeModal('fulfill')" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</button>
                <button @click="submitFulfill()" :disabled="savingFulfill || !availableAssets.length" class="inline-flex items-center gap-2 rounded-lg bg-amber-400 px-3 py-2 text-xs font-semibold text-slate-950 hover:bg-amber-300 disabled:opacity-50">
                    <svg x-show="savingFulfill" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" style="display: none;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span>Assign Asset</span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="modals.decision" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/60 p-3 sm:items-center backdrop-blur-sm" style="display: none;">
        <div @click.away="closeModal('decision')" class="my-4 flex max-h-[calc(100vh-2rem)] w-full max-w-lg flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
            <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="decisionMode === 'approve' ? 'Approve Request' : 'Reject Request'"></h3>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400" x-text="selectedRequest?.title || ''"></p>
            </div>
            <div class="overflow-y-auto px-5 py-4">

            <div class="mt-5 rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Request Summary</p>
                <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white" x-text="selectedRequest?.type_label"></p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400" x-text="selectedRequest?.description || 'No description added.'"></p>
            </div>

            <div class="mt-5">
                <label class="block text-xs font-bold uppercase tracking-wide text-slate-500">Comment</label>
                <textarea x-model.trim="decisionComment" rows="3" :placeholder="decisionMode === 'approve' ? 'Optional note for the requester.' : 'Required reason for rejection.'" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
            </div>

            <template x-if="decisionError">
                <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300" x-text="decisionError"></div>
            </template>

            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 px-5 py-4 dark:border-slate-800">
                <button @click="closeModal('decision')" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</button>
                <button @click="submitDecision()" :disabled="savingDecision" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold text-white disabled:opacity-50" :class="decisionMode === 'approve' ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-rose-500 hover:bg-rose-600'">
                    <svg x-show="savingDecision" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" style="display: none;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-text="decisionMode === 'approve' ? 'Approve' : 'Reject'"></span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="modals.timeline" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/60 p-3 sm:items-center backdrop-blur-sm" style="display: none;">
        <div @click.away="closeModal('timeline')" class="my-4 flex max-h-[calc(100vh-2rem)] w-full max-w-3xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
            <div class="overflow-y-auto p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white" x-text="selectedRequest?.title || 'Timeline'"></h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400" x-text="selectedRequest?.description || 'Approval history and request details.'"></p>
                </div>
                <button @click="closeModal('timeline')" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-[1.2fr,0.8fr]">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Timeline</p>
                    <div class="mt-4 space-y-3">
                        <template x-for="step in timeline" :key="step.id">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="step.approver_name"></p>
                                        <p x-show="step.step_label" class="mt-1 text-[11px] font-bold uppercase tracking-wide text-slate-400" x-text="step.step_label" style="display: none;"></p>
                                    </div>
                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider" :class="decisionTone(step.decision)" x-text="step.decision"></span>
                                </div>
                                <p class="mt-1 text-xs text-slate-400" x-text="step.acted_at || 'Awaiting action'"></p>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400" x-text="step.comment || 'No comment added.'"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Request Details</p>
                    <div class="mt-4 space-y-3 text-sm">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Status</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-white" x-text="selectedRequest?.status || '-'"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Submitted</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-white" x-text="selectedRequest?.submitted_at || '-'"></p>
                        </div>
                        <div x-show="selectedRequest?.amount">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Amount</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-white">INR <span x-text="selectedRequest?.amount || '-'"></span></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Extra Details</p>
                            <div x-show="fulfilledAsset" class="mt-3 rounded-2xl border border-cyan-200 bg-cyan-50 p-4 dark:border-cyan-900/50 dark:bg-cyan-950/20" style="display: none;">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700 dark:text-cyan-300">Assigned Asset</p>
                                        <p class="mt-2 text-sm font-bold text-slate-900 dark:text-white" x-text="fulfilledAsset?.name || '-'"></p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400" x-text="fulfilledAsset?.category_label || 'Inventory asset'"></p>
                                    </div>
                                    <span class="rounded-full bg-cyan-600 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white">Fulfilled</span>
                                </div>
                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Serial Number</p>
                                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-200" x-text="fulfilledAsset?.serial_number || '-'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Assigned On</p>
                                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-200" x-text="fulfilledAsset?.assigned_at || fulfilledAsset?.fulfilled_at || '-'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Assigned To</p>
                                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-200" x-text="fulfilledAsset?.assigned_to || '-'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Fulfillment Note</p>
                                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-200" x-text="fulfilledAsset?.fulfillment_note || 'No note added.'"></p>
                                    </div>
                                </div>
                            </div>
                            <template x-if="!detailsEntries.length">
                                <p class="mt-1 text-slate-500 dark:text-slate-400">No structured details were provided.</p>
                            </template>
                            <div class="mt-2 space-y-2">
                                <template x-for="entry in detailsEntries" :key="entry.key">
                                    <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 dark:border-slate-800 dark:bg-slate-900">
                                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400" x-text="entry.key"></p>
                                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-200" x-text="entry.value"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div x-show="selectedRequest?.attachment">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Attachment</p>
                            <template x-if="selectedRequest?.attachment">
                                <a :href="selectedRequest.attachment.download_url" class="mt-1 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-cyan-700 hover:bg-cyan-50 dark:border-slate-800 dark:bg-slate-900 dark:text-cyan-300 dark:hover:bg-slate-800">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 12-4-4m4 4 4-4m5 8H3"></path></svg>
                                    <span x-text="selectedRequest?.attachment?.name"></span>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection
