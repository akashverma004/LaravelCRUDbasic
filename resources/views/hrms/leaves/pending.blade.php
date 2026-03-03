@extends('hrms.layouts.app')

@section('title', 'Pending Leave Requests - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Leave Reviews</h1>
    <p class="text-slate-600 dark:text-slate-400">Review and manage leave requests</p>
</div>

<div class="mb-6 flex gap-4">
    <a href="{{ route('leaves.pending', ['tab' => 'all']) }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ ($tab ?? 'pending') === 'all' ? 'bg-cyan-500 text-slate-900' : 'bg-slate-200 dark:bg-slate-800' }}">All Requests</a>
    <a href="{{ route('leaves.pending', ['tab' => 'pending']) }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ ($tab ?? 'pending') === 'pending' ? 'bg-cyan-500 text-slate-900' : 'bg-slate-200 dark:bg-slate-800' }}">Pending</a>
    <a href="{{ route('leaves.index') }}" class="ml-auto rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Who's Away</a>
</div>

<div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6">
    @forelse ($leaves as $leave)
        <div class="mb-4 rounded-xl border border-slate-200 bg-slate-100 p-4 last:mb-0 dark:border-slate-800 dark:bg-slate-950">
            <div class="mb-3 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold">{{ $leave->employee->full_name }}</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        {{ ucfirst($leave->leave_type) }} Leave ·
                        {{ $leave->leave_session === 'full_day' ? 'Full day' : ucfirst($leave->leave_session) }}
                    </p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs {{ $leave->status === 'pending' ? 'bg-yellow-500/10 text-yellow-300' : ($leave->status === 'approved' ? 'bg-emerald-500/10 text-emerald-300' : 'bg-rose-500/10 text-rose-300') }}">
                    {{ ucfirst($leave->status) }}
                </span>
            </div>

            <div class="mb-3 text-sm text-slate-600 dark:text-slate-400">
                <p><strong>Dates:</strong> {{ $leave->start_date->format('d M Y') }} - {{ $leave->end_date->format('d M Y') }}</p>
                <p><strong>Reason:</strong> {{ $leave->reason }}</p>
            </div>

            @if ($leave->status === 'pending')
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('leaves.approve', $leave->id) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-lg bg-green-500 px-4 py-2 text-sm font-semibold text-white hover:bg-green-600">Approve</button>
                    </form>

                    <form method="POST" action="{{ route('leaves.reject', $leave->id) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600">Reject</button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <p class="text-center text-slate-600 dark:text-slate-400">
            {{ ($tab ?? 'pending') === 'all' ? 'No leave requests.' : 'No pending leave requests.' }}
        </p>
    @endforelse
</div>

<div class="mt-6">
    {{ $leaves->appends(['tab' => $tab ?? 'pending'])->links('pagination::tailwind') }}
</div>
@endsection
