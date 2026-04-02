<div class="space-y-3 pb-6 relative mt-1">
    {{-- Dynamic Glass Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white/80 px-4 py-3 shadow-sm border border-slate-200 backdrop-blur-xl dark:bg-slate-900/60 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-32 w-32 rounded-full bg-cyan-500/10 blur-[50px] pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 h-32 w-32 rounded-full bg-indigo-500/10 blur-[50px] pointer-events-none"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-2 lg:flex-row lg:items-center">
            <div>
                <div class="flex items-center gap-1.5 mb-1">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Self Service</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Time / Attd</span>
                </div>
                <h1 class="text-base font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all mt-1 leading-none">
                    My <span class="text-cyan-500">Attendance</span>
                </h1>
                <p class="mt-1 text-[8px] font-bold text-slate-500 uppercase tracking-widest opacity-80 leading-none">
                    {{ now()->format('l, F j, Y') }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-[7px] font-black uppercase tracking-widest text-slate-400/80 mb-0.5 leading-none">Real-time Clock</span>
                    <span class="text-[10px] font-black tabular-nums text-slate-900 dark:text-white uppercase tracking-tighter bg-slate-50 px-2 py-1 rounded border border-slate-100 dark:bg-white/5 dark:border-white/5 leading-none" x-data="{ time: '{{ now()->format('H:i:s') }}' }" x-init="setInterval(() => time = new Date().toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000)" x-text="time"></span>
                </div>
                <div class="h-5 w-px bg-slate-200 dark:bg-white/10 hidden sm:block"></div>
                <div class="flex h-8 w-8 items-center justify-center rounded bg-cyan-50 border border-cyan-100 dark:bg-cyan-500/10 dark:border-cyan-500/20 transition-transform hover:scale-105">
                    <svg class="h-3.5 w-3.5 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-3 lg:grid-cols-3">
        {{-- Clock Area --}}
        <div class="lg:col-span-2 space-y-3">
            <div class="relative overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 relative z-10">
                    <div class="text-center md:text-left">
                        <div class="inline-flex items-center gap-1.5 rounded px-2 py-0.5 mb-2 
                            {{ $isClockedIn ? 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20' : '' }}
                            {{ $isOnBreak ? 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:border-amber-500/20' : '' }}
                            {{ $isCompleted ? 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-500/10 dark:border-blue-500/20' : '' }}
                            {{ !$todayRecord ? 'bg-slate-50 text-slate-500 border-slate-200 dark:bg-white/5 dark:border-white/10' : '' }}
                         border shadow-sm">
                            <span class="h-1.5 w-1.5 rounded-full {{ $isClockedIn || $isOnBreak || $isCompleted ? 'animate-pulse' : '' }} 
                                {{ $isClockedIn ? 'bg-emerald-500' : '' }}
                                {{ $isOnBreak ? 'bg-amber-500' : '' }}
                                {{ $isCompleted ? 'bg-blue-500' : '' }}
                                {{ !$todayRecord ? 'bg-slate-400' : '' }}
                            "></span>
                            <span class="text-[7px] font-black uppercase tracking-[0.2em] leading-none">
                                {{ $isClockedIn ? 'Shift Active' : '' }}
                                {{ $isOnBreak ? 'On Break' : '' }}
                                {{ $isCompleted ? 'Shift Concluded' : '' }}
                                {{ !$todayRecord ? 'Offline' : '' }}
                            </span>
                        </div>
                        <h2 class="text-lg font-black tracking-tight text-slate-900 dark:text-white uppercase leading-none">
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
                            <p class="mt-1.5 text-[7px] font-black uppercase tracking-[0.2em] text-slate-400 bg-slate-50 dark:bg-white/5 px-2 py-0.5 rounded inline-block border border-slate-100 dark:border-white/5 leading-none">
                                Init: <span class="text-slate-600 dark:text-slate-300 font-mono">{{ \Carbon\Carbon::parse($todayRecord->clock_in_at)->format('H:i') }}</span>
                            </p>
                        @endif
                    </div>

                    {{-- Premium Minimalist Timer --}}
                    <div class="flex flex-col items-center md:items-end group relative">
                        <div x-data="{ 
                                totalSeconds: {{ $todayRecord?->getTotalWorkedSeconds() ?? 0 }},
                                status: '{{ $todayRecord?->status ?? 'none' }}',
                                interval: null,
                                draw() {
                                    const h = String(Math.floor(this.totalSeconds / 3600)).padStart(2, '0');
                                    const m = String(Math.floor((this.totalSeconds % 3600) / 60)).padStart(2, '0');
                                    const s = String(this.totalSeconds % 60).padStart(2, '0');
                                    this.$refs.h.innerText = h;
                                    this.$refs.m.innerText = m;
                                    this.$refs.s.innerText = s;
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
                             class="flex items-center gap-1.5 p-1"
                        >
                            <div class="flex flex-col items-center justify-center h-12 w-12 rounded-lg bg-slate-900 text-white shadow-lg dark:bg-white dark:text-slate-900">
                                <span x-ref="h" class="text-[1.3rem] font-black font-mono tracking-tighter leading-none mt-1">00</span>
                                <span class="text-[5px] font-black uppercase tracking-[0.2em] opacity-60 mt-1">Hrs</span>
                            </div>
                            <span class="text-xl font-black text-slate-300 dark:text-white/20 -mt-2">:</span>
                            <div class="flex flex-col items-center justify-center h-12 w-12 rounded-lg bg-slate-900 text-white shadow-lg dark:bg-white dark:text-slate-900">
                                <span x-ref="m" class="text-[1.3rem] font-black font-mono tracking-tighter leading-none mt-1">00</span>
                                <span class="text-[5px] font-black uppercase tracking-[0.2em] opacity-60 mt-1">Min</span>
                            </div>
                            <span class="text-xl font-black text-slate-300 dark:text-white/20 -mt-2">:</span>
                            <div class="flex flex-col items-center justify-center h-12 w-12 rounded-lg bg-slate-900 text-white shadow-lg dark:bg-white dark:text-slate-900">
                                <span x-ref="s" class="text-[1.3rem] font-black font-mono tracking-tighter leading-none mt-1">00</span>
                                <span class="text-[5px] font-black uppercase tracking-[0.2em] opacity-60 mt-1">Sec</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Control Pad --}}
                <div class="mt-4 flex flex-wrap items-center justify-between gap-2.5 border-t border-slate-100 pt-4 dark:border-white/5 relative z-10 bg-slate-50/50 -mx-4 -mb-4 px-4 pb-4 dark:bg-slate-950/20">
                    @if(!$todayRecord)
                        <button wire:click="punchIn" wire:loading.attr="disabled"
                                class="w-full sm:w-auto flex-1 inline-flex h-8 items-center justify-center gap-1.5 rounded bg-slate-900 px-4 text-[8px] font-black uppercase tracking-[0.2em] text-white shadow transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/10 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400 disabled:opacity-50">
                            <span wire:loading.remove wire:target="punchIn" class="flex items-center gap-1.5">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                <span>Initialize Shift</span>
                            </span>
                            <span wire:loading wire:target="punchIn">Processing...</span>
                        </button>
                    @elseif(!$isCompleted)
                        <div class="flex w-full sm:w-auto items-center gap-1.5">
                            @if($isClockedIn)
                                <button wire:click="pause('lunch')" wire:loading.attr="disabled"
                                        class="flex-1 sm:flex-none inline-flex h-8 items-center justify-center gap-1.5 rounded border border-slate-200 bg-white px-3 text-[7px] font-black uppercase tracking-widest text-slate-600 transition-all hover:border-amber-400 hover:text-amber-600 hover:bg-amber-50 shadow-sm dark:bg-white/5 dark:border-white/10 dark:text-slate-300 dark:hover:bg-amber-500/10 dark:hover:text-amber-400 disabled:opacity-50">
                                    <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
                                    <span>Food</span>
                                </button>
                                <button wire:click="pause('break')" wire:loading.attr="disabled"
                                        class="flex-1 sm:flex-none inline-flex h-8 items-center justify-center gap-1.5 rounded border border-slate-200 bg-white px-3 text-[7px] font-black uppercase tracking-widest text-slate-600 transition-all hover:border-amber-400 hover:text-amber-600 hover:bg-amber-50 shadow-sm dark:bg-white/5 dark:border-white/10 dark:text-slate-300 dark:hover:bg-amber-500/10 dark:hover:text-amber-400 disabled:opacity-50">
                                    <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>Pause</span>
                                </button>
                            @else
                                <button wire:click="resume" wire:loading.attr="disabled"
                                        class="flex-1 sm:flex-none inline-flex h-8 items-center justify-center gap-1.5 rounded bg-emerald-500 px-4 text-[7px] font-black uppercase tracking-widest text-white shadow-sm shadow-emerald-500/20 transition-all hover:bg-emerald-600 active:scale-95 disabled:opacity-50">
                                    <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347c-.75.412-1.667-.13-1.667-.986V5.653z" /></svg>
                                    <span>Resume</span>
                                </button>
                            @endif
                        </div>

                        <button wire:click="punchOut" wire:confirm="Finalize logic shift for today? This cannot be undone natively." wire:loading.attr="disabled"
                                class="w-full sm:w-auto inline-flex h-8 items-center justify-center gap-1.5 rounded border border-rose-200 bg-white px-4 text-[7px] font-black uppercase tracking-widest text-rose-500 transition-all hover:bg-rose-500 hover:text-white hover:border-rose-500 active:scale-95 disabled:opacity-50 dark:bg-white/5 dark:border-white/10 dark:text-rose-400 ml-auto shadow-sm">
                            <span wire:loading.remove wire:target="punchOut" class="flex items-center gap-1.5">
                                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25V18.75A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                                <span>Terminate Shift</span>
                            </span>
                            <span wire:loading wire:target="punchOut">Concluding...</span>
                        </button>
                    @else
                        <div class="flex items-center gap-2 rounded bg-emerald-50 px-3 py-1.5 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/20 shadow-sm w-full">
                            <div class="flex h-5 w-5 items-center justify-center rounded border border-emerald-200/50 bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <div class="flex-1 flex items-center justify-between">
                                <p class="text-[7px] font-black uppercase tracking-widest text-emerald-800 dark:text-emerald-300 leading-none">Shift Done</p>
                                <p class="text-[7px] font-bold text-emerald-600/70 dark:text-emerald-400/50 uppercase leading-none">Terminated: <span class="font-mono text-emerald-700 dark:text-emerald-300">{{ \Carbon\Carbon::parse($todayRecord->clock_out_at)->format('H:i') }}</span></p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Compact History --}}
            <div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                <div class="border-b border-slate-50 bg-slate-50/50 px-3 py-2 dark:border-white/5 dark:bg-slate-950/20">
                    <h3 class="text-[7px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-white opacity-60">Log Trajectory (7 Days)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-white/5">
                                <th class="px-3 py-1.5 text-[6px] font-black uppercase tracking-[0.2em] text-slate-400">Timeline / Scope</th>
                                <th class="px-3 py-1.5 text-[6px] font-black uppercase tracking-[0.2em] text-slate-400">In / Out Vectors</th>
                                <th class="px-3 py-1.5 text-[6px] font-black uppercase tracking-[0.2em] text-slate-400">Net Delta</th>
                                <th class="px-3 py-1.5 text-[6px] font-black uppercase tracking-[0.2em] text-slate-400">Resolved State</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                            @foreach ($history as $record)
                                <tr class="group hover:bg-slate-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                    <td class="px-3 py-2">
                                        <p class="text-[8px] font-black text-slate-900 dark:text-white uppercase tracking-tight leading-none mb-0.5">{{ $record->attendance_date->format('d M, Y') }}</p>
                                        <p class="text-[6px] font-bold text-slate-400 uppercase leading-none">{{ $record->attendance_date->format('l') }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center gap-1.5 opacity-80">
                                            <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 font-mono">{{ \Carbon\Carbon::parse($record->clock_in_at)->format('H:i') }}</span>
                                            <span class="text-[8px] text-slate-300">→</span>
                                            <span class="text-[9px] font-black text-rose-500 dark:text-rose-400 font-mono">{{ $record->clock_out_at ? \Carbon\Carbon::parse($record->clock_out_at)->format('H:i') : '--:--' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        @php
                                            $h = floor($record->total_work_seconds / 3600);
                                            $m = floor(($record->total_work_seconds % 3600) / 60);
                                        @endphp
                                        <span class="inline-flex items-center gap-1 text-[8px] font-black text-slate-900 dark:text-white bg-slate-50 dark:bg-white/5 px-1.5 py-0.5 rounded border border-slate-100 dark:border-white/5 leading-none">
                                            <svg class="h-2 w-2 text-cyan-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                            {{ $h }}h {{ $m }}m
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex rounded text-[6px] py-0.5 px-1.5 border font-black uppercase tracking-widest
                                            {{ $record->status === 'completed' ? 'bg-emerald-50/50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20' : 'bg-slate-50 text-slate-400 border-slate-100 dark:bg-white/5 dark:border-white/10' }}
                                         leading-none">
                                            {{ str_replace('_', ' ', $record->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-3 py-1.5 border-t border-slate-50 bg-slate-50/30 dark:border-white/5 dark:bg-slate-950/20 text-[8px]">
                    {{ $history->links() }}
                </div>
            </div>
        </div>

        {{-- Sidebar Stats --}}
        <div class="space-y-3">
            {{-- Weekly Pulse --}}
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                <h3 class="text-[7px] font-black uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400 mb-3 px-1">Weekly Pulse Check</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2.5 rounded border border-slate-100 dark:border-white/5 hover:border-cyan-200 transition-colors shadow-sm bg-white dark:bg-white/5">
                        <div>
                            <p class="text-[6px] font-black uppercase tracking-widest text-slate-400 opacity-80 mb-0.5">Productive Metric</p>
                            <p class="text-sm font-black text-slate-900 dark:text-white tracking-tighter leading-none">{{ $weeklyStats['total_hours'] }} <span class="text-[6px] text-slate-400 uppercase tracking-widest">Hrs</span></p>
                        </div>
                        <div class="h-6 w-6 rounded bg-cyan-50 border border-cyan-100 text-cyan-500 dark:bg-cyan-500/10 dark:border-cyan-500/20 dark:text-cyan-400 flex items-center justify-center">
                             <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded border border-slate-100 dark:border-white/5 hover:border-emerald-200 transition-colors shadow-sm bg-white dark:bg-white/5">
                        <div>
                            <p class="text-[6px] font-black uppercase tracking-widest text-slate-400 opacity-80 mb-0.5">Physical Presence</p>
                            <p class="text-sm font-black text-slate-900 dark:text-white tracking-tighter leading-none">{{ $weeklyStats['days_present'] }} <span class="text-[6px] text-slate-400 uppercase tracking-widest">/ 7 Days</span></p>
                        </div>
                        <div class="h-6 w-6 rounded bg-emerald-50 border border-emerald-100 text-emerald-500 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400 flex items-center justify-center">
                             <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Compact Upcoming shifts --}}
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900/50 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 h-20 w-20 rounded-full bg-slate-200/50 dark:bg-white/5 blur-xl pointer-events-none"></div>
                <div class="flex items-center justify-between mb-3 px-1">
                    <h3 class="text-[7px] font-black uppercase tracking-[0.2em] text-slate-400">Target Shift Vectors</h3>
                    <div class="h-1 w-1 rounded-full bg-cyan-500"></div>
                </div>
                <div class="space-y-2.5 relative z-10">
                    @forelse($roster as $item)
                        <div class="flex items-center gap-2.5">
                            <div class="flex flex-col items-center justify-center w-8 h-8 rounded bg-white border border-slate-100 shadow-sm dark:bg-slate-950 dark:border-white/10 shrink-0">
                                <span class="text-[5px] font-black uppercase text-cyan-600 dark:text-cyan-400 tracking-widest leading-none mt-0.5">{{ $item->date->format('D') }}</span>
                                <span class="text-[10px] font-black text-slate-900 dark:text-white leading-none mt-0.5 tracking-tighter">{{ $item->date->format('j') }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[8px] font-black text-slate-900 dark:text-white uppercase tracking-tight truncate leading-none">{{ $item->shift->name }}</p>
                                <p class="text-[6px] font-bold text-slate-500 uppercase tracking-widest mt-1 font-mono leading-none">
                                    {{ \Carbon\Carbon::parse($item->shift->start_time)->format('H:i') }} <span class="text-slate-300">→</span> {{ \Carbon\Carbon::parse($item->shift->end_time)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center border border-dashed border-slate-200 dark:border-white/10 rounded bg-slate-50/50 dark:bg-white/2">
                            <p class="text-[7px] font-bold text-slate-400 uppercase tracking-widest">Awaiting allocation</p>
                        </div>
                    @endforelse
                </div>
                <a href="{{ route('shifts.index') }}" wire:navigate class="mt-3 block text-center rounded bg-slate-50 border border-slate-100 py-1.5 text-[7px] font-black uppercase tracking-widest text-slate-500 transition-all hover:bg-slate-100 hover:text-slate-700 dark:bg-white/5 dark:border-white/5 dark:text-slate-400 dark:hover:bg-white/10 relative z-10 hover:border-slate-300 dark:hover:border-white/10">
                    Roster Center
                </a>
            </div>
        </div>
    </div>
</div>
