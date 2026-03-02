@extends('hrms.layouts.app')

@section('title', 'Pending Leave Requests - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Pending Leave Requests</h1>
    <p class="text-slate-400">Review and approve leave requests</p>
</div>

<div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
    @forelse ($leaves as $leave)
        <div class="mb-4 rounded-xl border border-slate-800 bg-slate-950 p-4 last:mb-0">
            <div class="mb-3 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold">{{ $leave->employee->full_name }}</h3>
                    <p class="text-sm text-slate-400">{{ ucfirst($leave->leave_type) }} Leave</p>
                </div>
                <span class="rounded-full bg-yellow-500/10 px-3 py-1 text-xs text-yellow-300">Pending</span>
            </div>

            <div class="mb-3 text-sm text-slate-400">
                <p><strong>Dates:</strong> {{ $leave->start_date->format('d M Y') }} - {{ $leave->end_date->format('d M Y') }}</p>
                <p><strong>Reason:</strong> {{ $leave->reason }}</p>
            </div>

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
        </div>
    @empty
        <p class="text-center text-slate-400">No pending leave requests.</p>
    @endforelse
</div>

<div class="mt-6">
    {{ $leaves->links('pagination::tailwind') }}
</div>
@endsection
