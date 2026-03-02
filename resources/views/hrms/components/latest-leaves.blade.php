<div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
    <h3 class="mb-3 text-lg font-semibold">Latest Leave Requests</h3>
    <div class="space-y-3">
        @forelse ($leaveRequests as $leave)
            <div class="rounded-xl bg-slate-950 px-4 py-3">
                <p class="font-medium">{{ $leave->employee->full_name }} ({{ ucfirst($leave->leave_type) }})</p>
                <p class="text-xs text-slate-400">{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M') }} ·
                    <span class="@if($leave->status === 'pending') text-yellow-400 @elseif($leave->status === 'approved') text-green-400 @else text-red-400 @endif">
                        {{ ucfirst($leave->status) }}
                    </span>
                </p>
            </div>
        @empty
            <p class="text-sm text-slate-400">No leave requests yet.</p>
        @endforelse
    </div>
    <a href="{{ route('leaves.pending') }}" class="mt-4 inline-block text-sm text-cyan-400 hover:text-cyan-300">View Pending Requests →</a>
</div>
