@extends('hrms.layouts.app')

@section('title', 'Leave Request Details - PeopleFlow HRMS')

@section('content')
<div
    x-data="leaveDetailManager({
        approveUrl: '{{ route('leaves.approve', $leave->id) }}',
        rejectUrl: '{{ route('leaves.reject', $leave->id) }}',
        deleteUrl: '{{ route('leaves.destroy', $leave->id) }}',
        backUrl: '{{ auth()->user()->hasAnyRole(['admin', 'hr_manager']) ? route('leaves.index') : route('leaves.my') }}'
    })"
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
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-indigo-500/10 blur-[60px]"></div>
        <div class="absolute -bottom-20 -left-20 h-48 w-48 rounded-full bg-cyan-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white lg:text-4xl">
                    Leave <span class="text-indigo-600 dark:text-indigo-400">Details</span>
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Review and manage leave request information.
                </p>
            </div>
            <div class="flex gap-3">
                @php $user = auth()->user(); @endphp
                <a href="{{ $user->hasAnyRole(['admin', 'hr_manager']) ? route('leaves.index') : route('leaves.my') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                
                <div class="flex items-center justify-between mb-8 pb-8 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl shadow-sm
                            @if($leave->leave_type === 'annual') bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400 @elseif($leave->leave_type === 'sick') bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400 @elseif($leave->leave_type === 'casual') bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 @else bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 @endif">
                            @if($leave->leave_type === 'annual') <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918"/></svg>
                            @elseif($leave->leave_type === 'sick') <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                            @else <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white capitalize">{{ $leave->leave_type }} Leave</h2>
                            <p class="text-slate-500 font-medium text-sm mt-0.5 capitalize">{{ str_replace('_', ' ', $leave->leave_session) }}</p>
                        </div>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-semibold capitalize
                            @if($leave->status === 'approved') bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 @elseif($leave->status === 'rejected') bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 @else bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 @endif">
                            <div class="h-1.5 w-1.5 rounded-full @if($leave->status === 'pending') bg-amber-500 animate-pulse @else bg-current @endif"></div>
                            {{ $leave->status }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-8 pb-8 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-2">Start Date</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $leave->start_date->format('M j, Y') }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $leave->start_date->format('l') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-2">End Date</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $leave->end_date->format('M j, Y') }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $leave->end_date->format('l') }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-slate-500 mb-3">Reason for Leave</h3>
                    <div class="rounded-xl bg-slate-50 p-6 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800">
                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $leave->reason }}</p>
                    </div>
                </div>
            </div>

            @if($user->hasAnyRole(['admin', 'hr_manager']) && $leave->status === 'pending')
                <div class="flex items-center gap-4">
                    <button @click="act(rejectUrl, 'PATCH', 'Leave request rejected.')" :disabled="loading" type="button" class="w-full rounded-xl border border-rose-200 bg-white px-5 py-3 text-sm font-bold text-rose-600 transition-colors hover:bg-rose-50 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-500 dark:hover:bg-rose-500/20 disabled:opacity-50">
                        Reject Request
                    </button>
                    <button @click="act(approveUrl, 'PATCH', 'Leave request approved.')" :disabled="loading" type="button" class="w-full rounded-xl bg-emerald-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition-colors hover:bg-emerald-600 disabled:opacity-50">
                        Approve Request
                    </button>
                </div>
            @endif
            
            @if($leave->status === 'pending' && $leave->employee_id === auth()->user()->employee?->id)
                 <div class="flex items-center gap-4">
                    <a href="{{ route('leaves.edit', $leave->id) }}" class="w-full rounded-xl bg-cyan-500 px-5 py-3 text-center text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                        Edit Request
                    </a>
                    <button @click="confirm('Are you sure you want to cancel this leave request?') && act(deleteUrl, 'DELETE', 'Request canceled.')" :disabled="loading" type="button" class="w-full rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition-colors hover:bg-rose-50 hover:text-rose-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-rose-500/10 dark:hover:text-rose-500 dark:hover:border-rose-500/20 disabled:opacity-50">
                        Cancel Request
                    </button>
                </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-6">
            {{-- Employee Details --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Employee</h3>
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-lg font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        {{ substr($leave->employee->full_name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate font-bold text-slate-900 dark:text-white">{{ $leave->employee->full_name }}</p>
                        <p class="mt-0.5 truncate text-xs text-slate-500">{{ $leave->employee->job_title ?? 'Employee' }}</p>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-6">Timeline</h3>
                <div class="space-y-6 relative before:absolute before:inset-0 before:ml-2 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent dark:before:via-slate-800">
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-4 h-4 rounded-full border-2 border-slate-100 bg-cyan-500 z-10 text-slate-100 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2"></div>
                        <div class="w-[calc(100%-2rem)] md:w-[calc(50%-1.5rem)] ml-4 md:ml-0 md:group-odd:pl-4 md:group-even:pr-4">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">Requested</p>
                            <p class="text-[10px] text-slate-500 mt-1">{{ $leave->created_at->format('M j, Y h:i A') }}</p>
                        </div>
                    </div>
                    @if($leave->status !== 'pending')
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-4 h-4 rounded-full border-2 border-slate-100 z-10 text-slate-100 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2
                            @if($leave->status === 'approved') bg-emerald-500 @else bg-rose-500 @endif"></div>
                        <div class="w-[calc(100%-2rem)] md:w-[calc(50%-1.5rem)] ml-4 md:ml-0 md:group-odd:pl-4 md:group-even:pr-4">
                            <p class="text-xs font-bold text-slate-900 dark:text-white capitalize">{{ $leave->status }}</p>
                            <p class="text-[10px] text-slate-500 mt-1">{{ $leave->updated_at->format('M j, Y h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Reference --}}
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center dark:border-slate-800 dark:bg-slate-900/50">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Reference ID</p>
                <code class="text-xs font-medium text-slate-600 dark:text-slate-400 bg-white border border-slate-200 dark:bg-slate-800 dark:border-slate-700 px-3 py-1.5 rounded-lg inline-block">LV-{{ $leave->id }}-{{ $leave->created_at->format('y') }}</code>
            </div>
        </div>
    </div>
</div>
@endsection
