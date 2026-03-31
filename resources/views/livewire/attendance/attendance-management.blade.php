<div class="space-y-5 pb-8">
    {{-- Admin Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white px-6 py-5 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-cyan-500/5 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Monitoring</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Administration</span>
                </div>
                <h1 class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all">
                    Attendance <span class="text-cyan-500">Management</span>
                </h1>
                <p class="mt-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest opacity-80 leading-none">
                    Real-time presence tracking across the organization.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <input type="date" wire:model.live="date" class="h-10 rounded-lg border border-slate-200 bg-white px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-700 shadow-sm transition hover:border-cyan-200 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white">
                <button wire:click="$refresh" class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-slate-900 text-white shadow-lg transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:text-cyan-400">
                    <svg wire:loading.class="animate-spin" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @php
            $stats_config = [
                ['Total Duration', $stats['total_hours'], 'hrs', 'cyan', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Present Staff', $stats['present_today'], 'Staff', 'emerald', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Active Now', $stats['active_now'], 'Online', 'amber', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z']
            ];
        @endphp
        
        @foreach($stats_config as [$label, $val, $suffix, $color, $svg])
        <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl transition-transform group-hover:scale-110 
                    @if($color === 'cyan') bg-cyan-50 text-cyan-500 dark:bg-cyan-500/10 
                    @elseif($color === 'emerald') bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10
                    @else bg-amber-50 text-amber-600 dark:bg-amber-500/10 @endif">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $svg }}" /></svg>
                </div>
                <div>
                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</p>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">{{ $val }}</span>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $suffix }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Main Activity Table --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900 overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-slate-100 p-5 dark:border-white/5">
            <div class="relative w-full max-w-sm">
                <svg class="absolute left-4 top-2.5 h-4 w-4 text-slate-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input wire:model.live.debounce.300ms="q" type="text" placeholder="Filter by personnel..." class="h-9 w-full rounded-lg border border-slate-100 bg-slate-50 pl-11 pr-4 text-[11px] font-bold text-slate-900 placeholder:text-slate-400/60 focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white transition-all">
            </div>
            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400/70">{{ $records->total() }} Activity Logs Found</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 text-[8px] font-black uppercase tracking-[0.25em] text-slate-400/80 dark:bg-white/[0.02] border-b border-slate-100 dark:border-white/5">
                    <tr>
                        <th class="px-5 py-3">Team Member</th>
                        <th class="px-5 py-3">Activity Date</th>
                        <th class="px-5 py-3 text-center">First In</th>
                        <th class="px-5 py-3 text-center">Last Out</th>
                        <th class="px-5 py-3 text-right">Net Worked</th>
                        <th class="px-5 py-3 text-center">Current Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                    @forelse ($records as $record)
                        <tr wire:click="editRecord({{ $record->id }})" class="group hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-all cursor-pointer">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-[10px] font-black text-slate-500 shadow-sm dark:bg-white/10 dark:text-cyan-400 group-hover:scale-105 transition-transform">
                                        {{ substr($record->employee->full_name, 0, 1) }}{{ str_contains($record->employee->full_name, ' ') ? substr(strrchr($record->employee->full_name, ' '), 1, 1) : '' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight group-hover:text-cyan-500 transition-colors">{{ $record->employee->full_name }}</p>
                                        <p class="truncate text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $record->employee->job_title }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight leading-none">{{ $record->attendance_date->format('M j, Y') }}</p>
                                <p class="text-[8px] font-bold text-slate-400 uppercase leading-none mt-1">{{ $record->attendance_date->format('l') }}</p>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="rounded-md bg-slate-50 px-2 py-1 font-mono text-[9px] font-black text-slate-600 border border-slate-100 dark:bg-white/5 dark:text-slate-400 dark:border-white/5">
                                    {{ $record->clock_in_at ? \Carbon\Carbon::parse($record->clock_in_at)->format('H:i') : '--:--' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="rounded-md bg-slate-50 px-2 py-1 font-mono text-[9px] font-black text-slate-600 border border-slate-100 dark:bg-white/5 dark:text-slate-400 dark:border-white/5">
                                    {{ $record->clock_out_at ? \Carbon\Carbon::parse($record->clock_out_at)->format('H:i') : '--:--' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                @php
                                    $h = floor($record->total_work_seconds / 3600);
                                    $m = floor(($record->total_work_seconds % 3600) / 60);
                                @endphp
                                <span class="text-[10px] font-black text-slate-900 dark:text-white">{{ $h }}h {{ $m }}m</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-8px font-black uppercase tracking-widest
                                    {{ $record->status === 'clocked_in' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10' : '' }}
                                    {{ $record->status === 'completed' ? 'bg-slate-100 text-slate-500 dark:bg-white/10' : '' }}
                                    {{ str_contains($record->status, 'on_') ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10' : '' }}
                                ">
                                    {{ str_replace('_', ' ', $record->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-300">No organizational data for this filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 dark:border-white/5">
            {{ $records->links() }}
        </div>
    </div>

    {{-- Edit Modal --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showEditModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
            
            <div class="relative w-full max-w-sm rounded-xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden transition-all">
                <div class="border-b border-slate-100 p-5 dark:border-white/5 flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Modify <span class="text-cyan-500">Record</span></h2>
                    <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-900 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-5 space-y-5">
                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3.5 dark:bg-white/5">
                        <div class="h-9 w-9 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center font-black text-xs">{{ substr($employeeName, 0, 1) }}</div>
                        <div>
                            <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $employeeName }}</p>
                            <p class="text-[8px] font-bold text-slate-400 uppercase">{{ $this->date }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-400">First In</label>
                            <input type="time" wire:model="clock_in" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-[10px] font-black focus:border-cyan-500 dark:bg-white/5 dark:border-white/10 dark:text-white">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-400">Last Out</label>
                            <input type="time" wire:model="clock_out" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-[10px] font-black focus:border-cyan-500 dark:bg-white/5 dark:border-white/10 dark:text-white">
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50/80 p-5 dark:bg-white/5 flex gap-3 justify-end items-center border-t border-slate-100 dark:border-white/5">
                    <button wire:click="$set('showEditModal', false)" class="text-[9px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-all">Cancel</button>
                    <button wire:click="saveEdit" class="rounded-lg bg-slate-900 px-6 py-2 text-[10px] font-black uppercase text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95">Update Logs</button>
                </div>
            </div>
        </div>
    @endif
</div>
