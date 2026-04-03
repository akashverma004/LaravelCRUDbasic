<div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5 transition-all hover:shadow-md">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            <div @class([
                'h-2 w-2 rounded-full animate-pulse',
                'bg-emerald-500' => $isClockedIn,
                'bg-amber-500' => $isOnBreak,
                'bg-blue-500' => $isCompleted,
                'bg-slate-300' => !$todayRecord,
            ])></div>
            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">
                {{ $isClockedIn ? 'Working Now' : ($isOnBreak ? 'On Break' : ($isCompleted ? 'Shift Done' : 'Personal Tracker')) }}
            </span>
        </div>
        <a href="{{ route('attendance.my') }}" wire:navigate class="text-[9px] font-black uppercase tracking-widest text-cyan-600 hover:text-cyan-500 dark:text-cyan-400">View Hub</a>
    </div>

    <div class="flex flex-col items-center justify-center py-2">
        <div x-data="{ 
                totalSeconds: {{ $todayRecord?->getTotalWorkedSeconds() ?? 0 }},
                status: '{{ $todayRecord?->status ?? 'none' }}',
                draw() {
                    const h = String(Math.floor(this.totalSeconds / 3600)).padStart(2, '0');
                    const m = String(Math.floor((this.totalSeconds % 3600) / 60)).padStart(2, '0');
                    const s = String(this.totalSeconds % 60).padStart(2, '0');
                    this.$refs.timer.innerText = `${h}:${m}:${s}`;
                }
             }"
             x-init="
                draw();
                if (status === 'clocked_in') {
                    setInterval(() => { this.totalSeconds++; draw(); }, 1000);
                }
             "
             class="text-4xl font-black tracking-tighter text-slate-900 dark:text-white tabular-nums"
        >
            <span x-ref="timer">00:00:00</span>
        </div>
        <p class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400 mt-2">Logged Workspace Time</p>
    </div>

    <div class="mt-8 grid grid-cols-2 gap-3">
        @if(!$todayRecord)
            <button x-data="{
                    loadingLoc: false,
                    doPunch() {
                        if(this.loadingLoc) return;
                        this.loadingLoc = true;
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                (pos) => { $wire.punchIn(pos.coords.latitude, pos.coords.longitude); this.loadingLoc = false; },
                                (err) => { $wire.punchIn(); this.loadingLoc = false; },
                                { timeout: 3000, maximumAge: 0 }
                            );
                        } else {
                            $wire.punchIn();
                            this.loadingLoc = false;
                        }
                    }
                }" @click="doPunch()" :disabled="loadingLoc" wire:loading.attr="disabled" class="col-span-2 inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500 disabled:opacity-50">
                <svg x-show="!loadingLoc" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span x-show="!loadingLoc">Punch In</span>
                <span x-show="loadingLoc" style="display: none;">Locating...</span>
            </button>
        @elseif(!$isCompleted)
            @if($isClockedIn)
                <button wire:click="punchOut" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-rose-600 text-[10px] font-black uppercase tracking-widest text-white transition-all hover:bg-rose-700 active:scale-95">
                    <span>Punch Out</span>
                </button>
                <a href="{{ route('attendance.my') }}" wire:navigate class="inline-flex h-11 items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-600 transition-all hover:bg-slate-100 dark:bg-white/5 dark:border-white/5 dark:text-slate-300">
                    <span>Menu</span>
                </a>
            @else
                <a href="{{ route('attendance.my') }}" wire:navigate class="col-span-2 inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-amber-500 text-[10px] font-black uppercase tracking-widest text-white transition-all hover:bg-amber-600 active:scale-95">
                    <span>Resume at Hub</span>
                </a>
            @endif
        @else
            <div class="col-span-2 flex items-center justify-center gap-2 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/5 text-emerald-600 dark:text-emerald-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                <span class="text-[10px] font-black uppercase tracking-widest">Done for today</span>
            </div>
        @endif
    </div>
</div>
