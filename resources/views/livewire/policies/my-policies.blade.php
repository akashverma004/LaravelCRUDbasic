<div class="space-y-8 pb-12">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-white px-10 py-10 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-emerald-500/10 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">Employee Resource Cente</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Institutional Protocol</span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Our <span class="text-emerald-500">Governance</span>
                </h1>
                <p class="mt-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-loose max-w-2xl">
                    Transparency hub for organizational policies, conduct guidelines, and operational protocols governing the PeopleFlow work environment.
                </p>
            </div>
        </div>
    </div>

    {{-- Policy List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($policies as $policy)
            <div class="group relative flex flex-col rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:shadow-xl hover:border-emerald-400/30">
                <div class="flex items-start justify-between mb-6">
                    <div class="h-14 w-14 flex items-center justify-center rounded-2xl bg-emerald-50 font-black text-[15px] text-emerald-600 shadow-inner dark:bg-emerald-500/10 dark:text-emerald-400 transition-transform group-hover:scale-110">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.744c0 1.5.346 2.919.969 4.183a11.997 11.997 0 007.031 6.471l.032.012.032-.012a11.998 11.998 0 007.031-6.471c.623-1.264.969-2.683.969-4.183 0-1.29-.204-2.532-.581-3.688A11.959 11.959 0 0112 2.714z" /></svg>
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-2">
                    <span class="text-[9px] font-black uppercase text-emerald-600 tracking-widest">{{ $policy['slug'] }}</span>
                    <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                    <span class="text-[8px] font-black uppercase text-slate-400 tracking-[0.2em]">Active Protocol</span>
                </div>
                <h4 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight truncate">{{ $policy['title'] }}</h4>
                <p class="mt-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-loose line-clamp-3 uppercase h-18">{{ $policy['description'] }}</p>

                <div class="mt-8 pt-6 border-t border-slate-50 dark:border-white/5 space-y-4">
                    @foreach($policy['fields'] as $field)
                        @if($field['name'] !== 'code' && $field['name'] !== 'name' && $field['name'] !== 'description')
                            @php $val = $policy['record']->{$field['name']}; @endphp
                            @if($val !== null)
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest">{{ str_replace('_', ' ', $field['name']) }}</span>
                                    <span class="text-[9px] font-black uppercase text-slate-900 dark:text-white tracking-widest">
                                        @if(is_bool($val))
                                            {{ $val ? 'Enabled' : 'Restricted' }}
                                        @elseif(is_array($val))
                                            {{ count($val) }} Indices
                                        @else
                                            {{ $val }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
