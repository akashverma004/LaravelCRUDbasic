<div class="space-y-5 pb-8 relative">
    {{-- Dynamic Glass Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white/80 px-6 py-5 shadow-sm border border-slate-200 backdrop-blur-xl dark:bg-slate-900/60 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-cyan-500/5 blur-[80px]"></div>
        <div class="absolute -bottom-20 -left-20 h-40 w-40 rounded-full bg-indigo-500/5 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Attendance</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Self Service</span>
                </div>
                <h1 class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all">
                    My <span class="text-cyan-500">Attendance</span>
                </h1>
                <p class="mt-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest opacity-80">
                    {{ now()->format('l, F j, Y') }}
                </p>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-400/80">Real-time Clock</span>
                    <span class="text-base font-black tabular-nums text-slate-900 dark:text-white uppercase tracking-tighter" x-data="{ time: '{{ now()->format('H:i:s') }}' }" x-init="setInterval(() => time = new Date().toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000)" x-text="time"></span>
                </div>
                <div class="h-8 w-px bg-slate-200 dark:bg-white/10 hidden sm:block"></div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-50 border border-slate-100 dark:bg-slate-950/40 dark:border-white/10 transition-transform hover:scale-105">
                    <svg class="h-5 w-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        {{-- Clock Area --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="relative overflow-hidden rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
                    <div class="text-center md:text-left">
                        <div class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 mb-3 
                            {{ $isClockedIn ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10' : '' }}
                            {{ $isOnBreak ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10' : '' }}
                            {{ $isCompleted ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10' : '' }}
                            {{ !$todayRecord ? 'bg-slate-50 text-slate-500 dark:bg-white/5' : '' }}
                         shadow-sm border border-current opacity-10">
                            <span class="h-1.5 w-1.5 rounded-full animate-pulse 
                                {{ $isClockedIn ? 'bg-emerald-500' : '' }}
                                {{ $isOnBreak ? 'bg-amber-500' : '' }}
                                {{ $isCompleted ? 'bg-blue-500' : '' }}
                                {{ !$todayRecord ? 'bg-slate-400' : '' }}
                            "></span>
                            <span class="text-[9px] font-black uppercase tracking-widest">
                                {{ $isClockedIn ? 'Working' : '' }}
                                {{ $isOnBreak ? 'Pause' : '' }}
                                {{ $isCompleted ? 'Finished' : '' }}
                                {{ !$todayRecord ? 'Offline' : '' }}
                            </span>
                        </div>
                        <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                            @if($isClockedIn)
                                Productive <span class="text-emerald-500">Mode</span>
                            @elseif($isOnBreak)
                                Rest <span class="text-amber-500">Period</span>
                            @elseif($isCompleted)
                                Day <span class="text-blue-500">Resolved</span>
                            @else
                                Ready to <span class="text-cyan-500">Start?</span>
                            @endif
                        </h2>
                        @if($todayRecord)
                            <p class="mt-1 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Punched in: {{ \Carbon\Carbon::parse($todayRecord->clock_in_at)->format('H:i') }}</p>
                        @endif
                    </div>

                    {{-- Timer Badge --}}
                    <div class="flex flex-col items-center md:items-end group">
                        <div x-data="{ 
                                totalSeconds: {{ $todayRecord?->getTotalWorkedSeconds() ?? 0 }},
                                status: '{{ $todayRecord?->status ?? 'none' }}',
                                interval: null,
                                draw() {
                                    const h = String(Math.floor(this.totalSeconds / 3600)).padStart(2, '0');
                                    const m = String(Math.floor((this.totalSeconds % 3600) / 60)).padStart(2, '0');
                                    const s = String(this.totalSeconds % 60).padStart(2, '0');
                                    this.$refs.timerDisplay.innerText = `${h}:${m}:${s}`;
                                }
                             }"
                             x-init="
                                draw();
                                if (status === 'clocked_in') {
                                    interval = setInterval(() => { this.totalSeconds++; draw(); }, 1000);
                                }
                                $wire.on('attendance-updated', () => {
                                    clearInterval(interval);
                                });
                             "
                             class="text-4xl font-black tabular-nums tracking-tighter text-slate-900 dark:text-white bg-slate-50 px-5 py-3 rounded-xl border border-slate-100 dark:bg-slate-950 dark:border-white/5 transition-all shadow-lg shadow-slate-100/50 dark:shadow-none"
                        >
                             <span x-ref="timerDisplay" class="bg-gradient-to-br from-slate-900 to-slate-500 bg-clip-text text-transparent dark:from-white dark:to-slate-400">00:00:00</span>
                        </div>
                        <span class="mt-2 text-[8px] font-black uppercase tracking-[0.3em] text-slate-400">Total Logged Hours</span>
                    </div>
                </div>

                {{-- Control Pad --}}
                <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-6 dark:border-white/5">
                    @if(!$todayRecord)
                        <button wire:click="punchIn" wire:loading.attr="disabled"
                                class="inline-flex h-12 items-center gap-2.5 rounded-xl bg-slate-900 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-white shadow-xl transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/10 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            <span>Clock In</span>
                        </button>
                    @elseif(!$isCompleted)
                        @if($isClockedIn)
                            <div class="flex gap-2 w-full sm:w-auto">
                                <button wire:click="pause('lunch')" wire:loading.attr="disabled"
                                        class="flex-1 sm:flex-none inline-flex h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 text-[9px] font-black uppercase tracking-widest text-slate-600 transition-all hover:border-amber-400 hover:text-amber-600 dark:bg-white/5 dark:border-white/10">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
                                    <span>Lunch</span>
                                </button>
                                <button wire:click="pause('break')" wire:loading.attr="disabled"
                                        class="flex-1 sm:flex-none inline-flex h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 text-[9px] font-black uppercase tracking-widest text-slate-600 transition-all hover:border-amber-400 hover:text-amber-600 dark:bg-white/5 dark:border-white/10">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>Break</span>
                                </button>
                            </div>
                        @else
                            <button wire:click="resume" wire:loading.attr="disabled"
                                    class="inline-flex h-11 items-center gap-2 rounded-xl bg-emerald-500 px-6 text-[9px] font-black uppercase tracking-widest text-white shadow-lg transition-all hover:bg-emerald-600 active:scale-95 shadow-emerald-500/20">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347c-.75.412-1.667-.13-1.667-.986V5.653z" /></svg>
                                <span>Resume</span>
                            </button>
                        @endif

                        <button wire:click="punchOut" wire:confirm="Clock out for today?" wire:loading.attr="disabled"
                                class="inline-flex h-11 items-center gap-2 rounded-xl bg-rose-600 px-6 text-[9px] font-black uppercase tracking-widest text-white shadow-lg transition-all hover:bg-rose-700 active:scale-95 shadow-rose-500/20 ml-auto">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25V18.75A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                            <span>Clock Out</span>
                        </button>
                    @else
                        <div class="flex items-center gap-3 rounded-xl bg-emerald-50 px-4 py-3 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/20">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-800 dark:text-emerald-300 leading-none">Shift Done</p>
                                <p class="text-[8px] font-bold text-emerald-600/70 dark:text-emerald-400/50 uppercase mt-1">Punched out at {{ \Carbon\Carbon::parse($todayRecord->clock_out_at)->format('H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Compact History --}}
            <div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                <div class="border-b border-slate-50 bg-slate-50/50 px-5 py-3.5 dark:border-white/5 dark:bg-slate-950/20">
                    <h3 class="text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-white opacity-60">Activity Records</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-white/5">
                                <th class="px-5 py-3 text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Timeline</th>
                                <th class="px-5 py-3 text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Punch In</th>
                                <th class="px-5 py-3 text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Punch Out</th>
                                <th class="px-5 py-3 text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Net Logs</th>
                                <th class="px-5 py-3 text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                            @foreach ($history as $record)
                                <tr class="group hover:bg-slate-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                    <td class="px-5 py-3">
                                        <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $record->attendance_date->format('d M, Y') }}</p>
                                        <p class="text-[8px] font-bold text-slate-400 uppercase leading-none mt-0.5">{{ $record->attendance_date->format('l') }}</p>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 font-mono">{{ \Carbon\Carbon::parse($record->clock_in_at)->format('H:i') }}</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 font-mono">{{ $record->clock_out_at ? \Carbon\Carbon::parse($record->clock_out_at)->format('H:i') : '--:--' }}</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        @php
                                            $h = floor($record->total_work_seconds / 3600);
                                            $m = floor(($record->total_work_seconds % 3600) / 60);
                                        @endphp
                                        <span class="text-[10px] font-black text-slate-900 dark:text-white">{{ $h }}h {{ $m }}m</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex rounded-md px-1.5 py-0.5 text-[8px] font-black uppercase tracking-widest
                                            {{ $record->status === 'completed' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10' : 'bg-slate-100 text-slate-400 dark:bg-white/5' }}
                                        ">
                                            {{ str_replace('_', ' ', $record->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-slate-50 dark:border-white/5">
                    {{ $history->links() }}
                </div>
            </div>
        </div>

        {{-- Sidebar Stats --}}
        <div class="space-y-5">
            {{-- Weekly Pulse --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                <h3 class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400 mb-5">Weekly Pulse</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 dark:bg-white/5 dark:border-white/5 hover:bg-slate-100 dark:hover:bg-white/[0.08] transition-colors cursor-default">
                        <div>
                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">Productivity</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white tracking-tighter">{{ $weeklyStats['total_hours'] }} <span class="text-[9px] text-slate-400 uppercase">hrs</span></p>
                        </div>
                        <div class="h-9 w-9 rounded-lg bg-cyan-50 text-cyan-500 dark:bg-cyan-500/20 dark:text-cyan-400 flex items-center justify-center">
                             <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 dark:bg-white/5 dark:border-white/5 hover:bg-slate-100 dark:hover:bg-white/[0.08] transition-colors cursor-default">
                        <div>
                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">Presence</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white tracking-tighter">{{ $weeklyStats['days_present'] }} <span class="text-[9px] text-slate-400 uppercase">/ 7</span></p>
                        </div>
                        <div class="h-9 w-9 rounded-lg bg-emerald-50 text-emerald-500 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center">
                             <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Compact Upcoming shifts --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Shift Schedule</h3>
                    <div class="h-1.5 w-1.5 rounded-full bg-cyan-500"></div>
                </div>
                <div class="space-y-4">
                    @forelse($roster as $item)
                        <div class="flex items-center gap-3">
                            <div class="flex flex-col items-center justify-center w-10 h-10 rounded-lg bg-slate-50 border border-slate-100 dark:bg-slate-950 dark:border-white/10 shrink-0">
                                <span class="text-[7px] font-black uppercase text-slate-400 leading-none mb-0.5">{{ $item->date->format('D') }}</span>
                                <span class="text-[11px] font-black text-slate-900 dark:text-white leading-none">{{ $item->date->format('j') }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight truncate">{{ $item->shift->name }}</p>
                                <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">
                                    {{ \Carbon\Carbon::parse($item->shift->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($item->shift->end_time)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center border border-dashed border-slate-100 dark:border-white/5 rounded-xl">
                            <p class="text-[9px] font-bold text-slate-400 uppercase">No scheduled shifts</p>
                        </div>
                    @endforelse
                </div>
                <a href="{{ route('shifts.index') }}" wire:navigate class="mt-5 block text-center rounded-xl bg-slate-50 border border-slate-100 py-2.5 text-[9px] font-black uppercase tracking-widest text-slate-600 transition-all hover:bg-slate-100 dark:bg-white/5 dark:border-white/5 dark:text-slate-400">
                    Roster Center
                </a>
            </div>
        </div>
    </div>
</div>
