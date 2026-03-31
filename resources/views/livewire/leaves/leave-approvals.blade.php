<div class="space-y-5 pb-12 relative">
    {{-- High-Impact Glass Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white/80 px-6 py-5 shadow-sm border border-slate-200 backdrop-blur-xl dark:bg-slate-900/60 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-rose-500/5 blur-[80px]"></div>
        <div class="absolute -bottom-20 -left-20 h-40 w-40 rounded-full bg-amber-500/5 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-rose-600 dark:text-rose-400">Admin Control</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Absence Matrix</span>
                </div>
                <h1 class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all">
                    Leave <span class="text-rose-600">Review</span>
                </h1>
                <p class="mt-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest opacity-80 leading-none">
                    Team presence and authorization center.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('leaves.index') }}" wire:navigate class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 text-[9px] font-black uppercase tracking-widest text-slate-600 shadow-sm transition-all hover:bg-slate-50 dark:border-white/5 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    <span>Calendar Center</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Controls & Tabs --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="inline-flex items-center gap-1 rounded-xl bg-slate-100 p-1 dark:bg-slate-900 border border-slate-200 dark:border-white/5">
            <button wire:click="setTab('pending')" 
                class="rounded-lg px-5 py-2 text-[9px] font-black uppercase tracking-widest transition-all" 
                :class="'{{ $tab }}' === 'pending' ? 'bg-white text-slate-900 shadow-sm dark:bg-white/5 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'">
                Pending Review
            </button>
            <button wire:click="setTab('all')" 
                class="rounded-lg px-5 py-2 text-[9px] font-black uppercase tracking-widest transition-all" 
                :class="'{{ $tab }}' === 'all' ? 'bg-white text-slate-900 shadow-sm dark:bg-white/5 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'">
                All History
            </button>
        </div>
        
        <div class="flex items-center gap-3">
             {{-- Loading Indicator --}}
            <div wire:loading class="flex items-center gap-2">
                <div class="h-2 w-2 animate-ping rounded-full bg-cyan-500"></div>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Refreshing Stream...</span>
            </div>
        </div>
    </div>

    {{-- Review Stream --}}
    <div class="min-h-[400px]">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            @forelse($leaves as $leave)
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-white/5 dark:bg-slate-900/50">
                    
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            @if($leave->employee->profile_photo)
                                <img src="{{ $leave->employee->profile_photo }}" class="h-12 w-12 rounded-xl object-cover ring-2 ring-slate-100 dark:ring-white/5">
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-lg font-black text-slate-600 dark:bg-white/5 dark:text-cyan-400">
                                    <span>{{ substr($leave->employee->full_name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $leave->employee->full_name }}</h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest bg-slate-100 dark:bg-white/5 px-1.5 py-0.5 rounded-md">{{ $leave->leave_type }}</span>
                                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wide">Submitted {{ $leave->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[8px] font-black uppercase tracking-widest shadow-sm
                            @if($leave->status === 'pending') bg-amber-50 text-amber-600 dark:bg-amber-500/10 @elseif($leave->status === 'approved') bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 @else bg-rose-50 text-rose-600 dark:bg-rose-500/10 @endif">
                            <div class="h-1 w-1 rounded-full bg-current @if($leave->status === 'pending') animate-pulse @endif"></div>
                            <span>{{ $leave->status }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6 py-4 border-y border-slate-100 dark:border-white/5">
                        <div>
                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-2">Duration</p>
                            <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight">
                                {{ $leave->start_date?->format('d M') }} — {{ $leave->end_date?->format('d M Y') }}
                            </p>
                            <p class="text-[9px] text-slate-500 mt-1 font-bold uppercase tracking-widest">{{ str_replace('_', ' ', $leave->leave_session) }}</p>
                        </div>
                        <div>
                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-2">Reason</p>
                            <p class="text-[10px] text-slate-600 dark:text-slate-400 leading-relaxed italic">"{{ $leave->reason ?: 'No reason provided.' }}"</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('leaves.show', $leave->id) }}" wire:navigate class="text-[9px] font-black uppercase tracking-widest text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300 transition-colors">Details & Files</a>
                        
                        @if($leave->status === 'pending')
                        <div class="flex gap-2">
                            <button wire:click="reject({{ $leave->id }})" wire:loading.attr="disabled"
                                class="rounded-xl border border-rose-200 px-5 py-2 text-[9px] font-black uppercase tracking-widest text-rose-600 transition-all hover:bg-rose-50 dark:border-white/5 dark:text-rose-400 dark:hover:bg-rose-500/10 disabled:opacity-50">
                                Reject
                            </button>
                            <button wire:click="approve({{ $leave->id }})" wire:loading.attr="disabled"
                                class="rounded-xl bg-slate-900 px-5 py-2 text-[9px] font-black uppercase tracking-widest text-white shadow-xl shadow-slate-900/10 hover:bg-cyan-600 transition-all active:scale-95 disabled:opacity-50 dark:bg-white/10 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                                Approve
                            </button>
                        </div>
                        @else
                           <p class="text-[9px] font-bold text-slate-400 uppercase italic">Decision Finalized</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 bg-slate-50 rounded-2xl border border-slate-200 border-dashed dark:bg-slate-900/50 dark:border-white/5">
                    <svg class="h-12 w-12 text-slate-400 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor font-width=1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] italic">
                        {{ $tab === 'all' ? 'No records in database' : 'You are all caught up' }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-8 flex items-center justify-between pt-6 border-t border-slate-200 dark:border-white/5">
        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">
            Showing Page <span class="text-slate-900 dark:text-white">{{ $leaves->currentPage() }}</span> of <span class="text-slate-900 dark:text-white">{{ $leaves->lastPage() }}</span>
        </p>
        <div class="flex items-center gap-2">
            {{ $leaves->links() }}
        </div>
    </div>

    {{-- Universal Toast --}}
    <div 
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-4 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-4 opacity-0 scale-95"
        class="fixed bottom-8 right-8 z-[110] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl backdrop-blur-xl"
        style="display: none;"
    >
        <div :class="type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="h-2 w-2 rounded-full animate-pulse"></div>
        <span x-text="message"></span>
    </div>
</div>
