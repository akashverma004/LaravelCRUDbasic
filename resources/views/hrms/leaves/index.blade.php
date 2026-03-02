@extends('hrms.layouts.app')

@section('title', 'Leave Requests - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Leave Requests</h1>
        <p class="text-slate-400">Manage employee leave</p>
    </div>
    <a href="{{ route('leaves.create') }}" class="rounded-lg bg-cyan-500 px-4 py-2 font-semibold text-slate-900 hover:bg-cyan-400">+ New Request</a>
</div>

<div class="mb-6 flex gap-4">
    <a href="{{ route('leaves.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ request()->route()->getName() === 'leaves.index' ? 'bg-cyan-500 text-slate-900' : 'bg-slate-800' }}">All Requests</a>
    <a href="{{ route('leaves.pending') }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ request()->route()->getName() === 'leaves.pending' ? 'bg-cyan-500 text-slate-900' : 'bg-slate-800' }}">Pending</a>
</div>

<div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
    <table class="w-full">
        <thead>
            <tr class="border-b border-slate-800">
                <th class="px-4 py-3 text-left text-sm font-semibold">Employee</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Type</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Dates</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($leaves as $leave)
                <tr class="border-b border-slate-800 hover:bg-slate-800/50">
                    <td class="px-4 py-3">{{ $leave->employee->full_name }}</td>
                    <td class="px-4 py-3 text-sm">{{ ucfirst($leave->leave_type) }}</td>
                    <td class="px-4 py-3 text-sm text-slate-400">{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M') }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-3 py-1 text-xs {{ $leave->status === 'pending' ? 'bg-yellow-500/10 text-yellow-300' : ($leave->status === 'approved' ? 'bg-green-500/10 text-green-300' : 'bg-red-500/10 text-red-300') }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-400">No leave requests</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $leaves->links('pagination::tailwind') }}
</div>
@endsection
