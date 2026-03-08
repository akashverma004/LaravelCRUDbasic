@extends('hrms.layouts.app')

@section('title', 'Leave Request Details - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Leave Request Details</h1>
        <p class="text-slate-500 dark:text-slate-400 font-medium">Detailed information about the leave application</p>
    </div>
    <div class="flex gap-3">
        @php $user = auth()->user(); @endphp
        <a href="{{ $user->hasAnyRole(['admin', 'hr_manager']) ? route('leaves.index') : route('leaves.my') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
            Back to List
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl 
                        @if($leave->leave_type === 'annual') bg-orange-100 text-orange-600 @elseif($leave->leave_type === 'sick') bg-blue-100 text-blue-600 @elseif($leave->leave_type === 'casual') bg-emerald-100 text-emerald-600 @else bg-slate-100 text-slate-600 @endif">
                        @if($leave->leave_type === 'annual') <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @elseif($leave->leave_type === 'sick') <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.022.547l-2.387 2.387a2 2 0 102.828 2.828l1.414-1.414 1.414 1.414a2 2 0 002.828 0l1.414-1.414 1.414 1.414a2 2 0 002.828 0l1.414-1.414 1.414 1.414a2 2 0 102.828-2.828l-2.387-2.387z"/></svg>
                        @else <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white capitalize">{{ $leave->leave_type }} Leave</h2>
                        <p class="text-slate-500 font-bold uppercase tracking-widest text-[10px] mt-1">{{ str_replace('_', ' ', $leave->leave_session) }} Session</p>
                    </div>
                </div>
                <div>
                   @if ($leave->status === 'approved')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-4 py-2 text-xs font-black uppercase text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                            Approved
                        </span>
                    @elseif ($leave->status === 'rejected')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-4 py-2 text-xs font-black uppercase text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                            Rejected
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-4 py-2 text-xs font-black uppercase text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                            Pending Approval
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 py-8 border-y border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Start Date</h3>
                    <p class="text-xl font-black text-slate-800 dark:text-white">{{ $leave->start_date->format('l, d M Y') }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">End Date</h3>
                    <p class="text-xl font-black text-slate-800 dark:text-white">{{ $leave->end_date->format('l, d M Y') }}</p>
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Reason for Absence</h3>
                <div class="rounded-2xl bg-slate-50 p-6 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800">
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                        {{ $leave->reason }}
                    </p>
                </div>
            </div>
        </div>

        @if($user->hasAnyRole(['admin', 'hr_manager']) && $leave->status === 'pending')
            <div class="flex items-center gap-4">
                <form action="{{ route('leaves.approve', $leave->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full rounded-2xl bg-emerald-600 py-4 text-sm font-black text-white shadow-xl shadow-emerald-500/20 transition-all hover:bg-emerald-700 active:scale-95">
                        Approve Request
                    </button>
                </form>
                <form action="{{ route('leaves.reject', $leave->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full rounded-2xl bg-rose-600 py-4 text-sm font-black text-white shadow-xl shadow-rose-500/20 transition-all hover:bg-rose-700 active:scale-95">
                        Reject Request
                    </button>
                </form>
            </div>
        @endif
        
        @if($leave->status === 'pending' && $leave->employee_id === $user->employee->id)
             <div class="flex items-center gap-4">
                <a href="{{ route('leaves.edit', $leave->id) }}" class="flex-1 rounded-2xl bg-indigo-600 py-4 text-center text-sm font-black text-white shadow-xl shadow-indigo-500/20 transition-all hover:bg-indigo-700 active:scale-95">
                    Edit Request
                </a>
                <form action="{{ route('leaves.destroy', $leave->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Cancel this leave request?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-2xl border border-rose-200 py-4 text-sm font-black text-rose-600 transition-all hover:bg-rose-50 dark:border-rose-900/30 dark:text-rose-400 dark:hover:bg-rose-950/30">
                        Cancel Request
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Sidebar Stats/Info -->
    <div class="space-y-6">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-6">Requested By</h3>
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-lg font-black text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                    {{ \Illuminate\Support\Str::of($leave->employee->full_name)->explode(' ')->map(fn ($word) => \Illuminate\Support\Str::substr($word, 0, 1))->take(2)->implode('') }}
                </div>
                <div>
                    <p class="font-black text-slate-800 dark:text-white">{{ $leave->employee->full_name }}</p>
                    <p class="text-xs font-bold text-slate-400">{{ $leave->employee->job_title ?? 'Employee' }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Request Timeline</h3>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="mt-1 h-2 w-2 rounded-full bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)]"></div>
                    <div>
                        <p class="text-xs font-black text-slate-700 dark:text-slate-200">Submitted</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $leave->created_at->format('d M, Y - h:i A') }}</p>
                    </div>
                </div>
                @if($leave->status !== 'pending')
                    <div class="flex gap-3">
                        <div class="mt-1 h-2 w-2 rounded-full @if($leave->status === 'approved') bg-emerald-500 @else bg-rose-500 @endif"></div>
                        <div>
                            <p class="text-xs font-black text-slate-700 dark:text-slate-200 capitalize">{{ $leave->status }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $leave->updated_at->format('d M, Y - h:i A') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
