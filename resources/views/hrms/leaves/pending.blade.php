@extends('hrms.layouts.app')

@section('title', 'Review Leave Requests - PeopleFlow HRMS')

@section('content')
<div
    x-data="leaveReviewManager({
        dataUrl: '{{ route('leaves.pending.data') }}',
        approveUrlBase: '{{ url('/leaves') }}',
        rejectUrlBase: '{{ url('/leaves') }}',
        tab: '{{ $tab ?? 'pending' }}'
    })"
    x-init="init()"
    class="space-y-8"
>
    {{-- Notifications --}}
    <div
        x-show="toast.show"
        x-transition
        class="fixed bottom-6 right-6 z-50 rounded-xl bg-slate-900 p-4 shadow-xl dark:bg-white"
        style="display: none;"
    >
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg" :class="toast.type === 'success' ? 'bg-emerald-500/20 text-emerald-300 dark:text-emerald-600' : 'bg-rose-500/20 text-rose-300 dark:text-rose-600'">
                <template x-if="toast.type === 'success'">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </template>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-100 dark:text-slate-900" x-text="toast.message"></p>
            </div>
        </div>
    </div>

    {{-- Header Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900/50">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-cyan-500/10 blur-[60px]"></div>
        <div class="absolute -bottom-20 -left-20 h-48 w-48 rounded-full bg-rose-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white lg:text-4xl">
                    Leave <span class="text-rose-600 dark:text-rose-400">Review</span>
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Review and authorize pending leave requests.
                </p>
            </div>
            <a href="{{ route('leaves.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                <span>All Leaves</span>
            </a>
        </div>
    </div>

    {{-- Controls --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="inline-flex items-center gap-1 rounded-lg bg-slate-100 p-1 dark:bg-slate-900">
            <button @click="setTab('all')" 
                class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all" 
                :class="tab === 'all' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'">
                All Requests
            </button>
            <button @click="setTab('pending')" 
                class="relative rounded-md px-4 py-1.5 text-xs font-semibold transition-all" 
                :class="tab === 'pending' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'">
                Pending
                <span x-show="leaves.length > 0 && tab !== 'pending'" class="absolute -right-1 -top-1 h-2 w-2 rounded-full bg-rose-500"></span>
            </button>
        </div>
    </div>

    {{-- Review Container --}}
    <div class="min-h-[400px]">
        {{-- Loading --}}
        <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
            <template x-for="index in 4" :key="index">
                <div class="h-48 animate-pulse rounded-2xl bg-slate-100 dark:bg-slate-900/50"></div>
            </template>
        </div>

        {{-- Stream --}}
        <div x-show="!loading && leaves.length > 0" class="grid grid-cols-1 xl:grid-cols-2 gap-6" style="display: none;">
            <template x-for="leave in leaves" :key="leave.id">
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-slate-800 dark:bg-slate-900/50">
                    
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-lg font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                <span x-text="leave.employee_name.charAt(0)"></span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white" x-text="leave.employee_name"></h3>
                                <p class="mt-0.5 text-xs text-slate-500 capitalize" x-text="leave.leave_type + ' Leave'"></p>
                            </div>
                        </div>
                        <div class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-semibold capitalize" :class="statusTone(leave.status)">
                            <div class="h-1.5 w-1.5 rounded-full" :class="leave.status === 'pending' ? 'bg-amber-500 animate-pulse' : (leave.status === 'approved' ? 'bg-emerald-500' : 'bg-rose-500')"></div>
                            <span x-text="leave.status"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6 py-4 border-y border-slate-100 dark:border-slate-800">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Duration</p>
                            <p class="text-sm font-bold text-slate-900 dark:text-white" x-text="formatDateShort(leave.start_date) + ' — ' + formatDateShort(leave.end_date)"></p>
                            <p class="text-xs text-slate-500 mt-1 capitalize" x-text="leave.leave_session.replace('_', ' ')"></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Reason</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 line-clamp-2" x-text="leave.reason"></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a :href="leave.show_url" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300 transition-colors">View Details</a>
                        
                        <div x-show="leave.status === 'pending'" class="flex gap-2" style="display: none;">
                            <button @click="decide(leave, 'reject')" :disabled="processingId === leave.id" 
                                class="rounded-lg border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-600 transition-colors hover:bg-rose-50 dark:border-rose-500/20 dark:text-rose-400 dark:hover:bg-rose-500/10 disabled:opacity-50">
                                Reject
                            </button>
                            <button @click="decide(leave, 'approve')" :disabled="processingId === leave.id" 
                                class="rounded-lg bg-emerald-500 px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-emerald-600 disabled:opacity-50">
                                Approve
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty State --}}
        <div x-show="!loading && leaves.length === 0" class="flex flex-col items-center justify-center py-20 bg-slate-50 rounded-2xl border border-slate-200 border-dashed dark:bg-slate-900/50 dark:border-slate-800" style="display: none;">
            <svg class="h-12 w-12 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <p class="text-sm font-semibold text-slate-600 dark:text-slate-400" x-text="tab === 'all' ? 'No leave requests found.' : 'You are all caught up.'"></p>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-800">
        <p class="text-xs text-slate-500" x-text="`Page ${meta.current_page || 1} of ${meta.last_page || 1}`"></p>
        <div class="flex items-center gap-2">
            <button @click="fetchData(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">Previous</button>
            <button @click="fetchData(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">Next</button>
        </div>
    </div>
</div>
@endsection
