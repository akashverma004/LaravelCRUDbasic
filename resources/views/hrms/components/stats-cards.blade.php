<div class="grid gap-8 md:grid-cols-2 xl:grid-cols-4">
    @foreach ($stats as $card)
        <div class="group relative overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm transition-all hover:shadow-2xl hover:-translate-y-1 dark:border-slate-800 dark:bg-slate-900/50">
            {{-- Decorative Accent --}}
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-cyan-500/5 blur-2xl group-hover:bg-cyan-500/10 transition-all"></div>
            
            <h3 class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-6">{{ $card['label'] }}</h3>
            
            <div class="flex items-baseline gap-4">
                <span class="text-5xl font-black tracking-tight text-slate-900 dark:text-white">{{ $card['value'] }}</span>
                @if(isset($card['trend']))
                    <span class="text-[9px] font-black uppercase tracking-widest {{ $card['trend'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} bg-white/5 px-2 py-1 rounded-lg">
                        {{ $card['trend'] >= 0 ? '+' : '' }}{{ $card['trend'] }}%
                    </span>
                @endif
            </div>

            <div class="mt-8 h-1 w-full bg-slate-50 dark:bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-cyan-400 rounded-full shadow-[0_0_8px_rgba(34,211,238,0.5)]" style="width: 70%"></div>
            </div>
        </div>
    @endforeach
</div>
