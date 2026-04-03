<div x-data="{
        isOpen: false,
        selectedIndex: 0,
        handleKeydown(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    setTimeout(() => this.$refs.searchInput.focus(), 50);
                }
            }
            if (!this.isOpen) return;

            const items = this.$refs.results.querySelectorAll('[data-route]');
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.selectedIndex = (this.selectedIndex + 1) % items.length;
                items[this.selectedIndex].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.selectedIndex = this.selectedIndex - 1 < 0 ? items.length - 1 : this.selectedIndex - 1;
                items[this.selectedIndex].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                const route = items[this.selectedIndex].getAttribute('data-route');
                if (route) window.location.href = route;
            } else if (e.key === 'Escape') {
                this.isOpen = false;
            }
        }
    }"
    @keydown.window="handleKeydown"
    @open-command-palette.window="isOpen = true; setTimeout(() => $refs.searchInput.focus(), 50)"
    @click.away="isOpen = false"
    class="relative z-[100]"
    x-cloak>

    {{-- Global Backdrop --}}
    <div x-show="isOpen" 
         x-transition.opacity.duration.200ms
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[90]"></div>

    {{-- Spotlight Modal --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="fixed left-1/2 top-[15%] w-full max-w-2xl -translate-x-1/2 z-[100] px-4 sm:px-0">
        
        <div class="rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/5 dark:bg-slate-900 dark:ring-white/10 overflow-hidden flex flex-col items-stretch outline-none ring-offset-0 drop-shadow-[0_0_80px_rgba(139,92,246,0.15)]">
            
            {{-- Search Header --}}
            <div class="relative flex items-center px-4 pt-4 pb-3 border-b border-slate-100 dark:border-white/5 bg-white/50 backdrop-blur-md dark:bg-slate-900/50">
                <svg class="h-5 w-5 text-slate-400 shrink-0 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input x-ref="searchInput" wire:model.live.debounce.300ms="query" @input="selectedIndex = 0" type="text" placeholder="Search employees, routes, and hubs..." class="w-full bg-transparent border-none text-[13px] font-black uppercase tracking-widest text-slate-900 placeholder-slate-400 focus:ring-0 dark:text-white transition-all h-full outline-none py-1.5" autocomplete="off" autocorrect="off" spellcheck="false" maxlength="64">
                <div class="flex items-center gap-1 shrink-0 bg-slate-100 dark:bg-white/5 px-2 py-1 rounded-md ml-2 border border-slate-200 dark:border-white/5">
                    <span class="text-[8px] font-black text-slate-400">ESC</span>
                </div>
            </div>

            {{-- Results Graph --}}
            <div x-ref="results" class="max-h-[60vh] overflow-y-auto p-2 scroll-smooth">
                @if(strlen($query) > 0 && empty($employees) && empty($staticRoutes))
                    <div class="py-14 text-center">
                        <svg class="mx-auto h-8 w-8 text-slate-300 dark:text-white/10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="mt-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">No signals found matching criteria.</p>
                    </div>
                @else
                    @if(count($staticRoutes) > 0)
                        <div class="mb-2">
                            <h3 class="px-3 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1.5 mt-2">Hubs & Modules</h3>
                            <ul class="space-y-0.5">
                                @foreach($staticRoutes as $idx => $rt)
                                    <li>
                                        <a href="{{ $rt['route'] }}" wire:navigate data-route="{{ $rt['route'] }}" class="group flex items-center justify-between rounded-lg px-3 py-2.5 transition-all outline-none" :class="{ 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400': selectedIndex === {{ $idx }}, 'hover:bg-slate-50 dark:hover:bg-white/5': selectedIndex !== {{ $idx }} }">
                                            <div class="flex items-center gap-3">
                                                <div class="h-6 w-6 rounded flex items-center justify-center shrink-0 border transition-all" :class="{ 'bg-white border-violet-100 text-violet-500 shadow-sm dark:bg-slate-800 dark:border-violet-500/30': selectedIndex === {{ $idx }}, 'border-slate-100 text-slate-400 bg-slate-50 dark:bg-white/5 dark:border-white/5': selectedIndex !== {{ $idx }} }">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $rt['icon'] }}" /></svg>
                                                </div>
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-700 dark:text-slate-300 transition-colors group-hover:text-violet-600 dark:group-hover:text-violet-400" :class="{ 'text-violet-600 dark:text-violet-400': selectedIndex === {{ $idx }} }">{{ $rt['name'] }}</span>
                                            </div>
                                            <svg x-show="selectedIndex === {{ $idx }}" class="h-4 w-4 text-violet-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(count($employees) > 0)
                        <div class="mt-1">
                            <h3 class="px-3 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1.5 mt-4 border-t border-slate-100 dark:border-white/5 pt-3">Personnel Directory</h3>
                            <ul class="space-y-0.5">
                                @foreach($employees as $empIndex => $emp)
                                    @php $globalIdx = count($staticRoutes) + $empIndex; @endphp
                                    <li>
                                        <a href="{{ route('employees.show', $emp->id) }}" wire:navigate data-route="{{ route('employees.show', $emp->id) }}" class="group flex items-center justify-between rounded-lg px-3 py-2.5 transition-all outline-none" :class="{ 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400': selectedIndex === {{ $globalIdx }}, 'hover:bg-slate-50 dark:hover:bg-white/5': selectedIndex !== {{ $globalIdx }} }">
                                            <div class="flex items-center gap-3">
                                                <div class="h-7 w-7 rounded overflow-hidden shadow-sm border" :class="{ 'border-cyan-200 dark:border-cyan-500/30': selectedIndex === {{ $globalIdx }}, 'border-slate-200 dark:border-white/10': selectedIndex !== {{ $globalIdx }} }">
                                                    @if($emp->profile_photo_url)
                                                        <img src="{{ $emp->profile_photo_url }}" class="h-full w-full object-cover">
                                                    @else
                                                        <div class="h-full w-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-[8px] font-black uppercase text-slate-400">{{ substr($emp->first_name, 0, 1) }}</div>
                                                    @endif
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-black uppercase tracking-tight text-slate-800 dark:text-white transition-colors leading-none mb-0.5" :class="{ 'text-cyan-600 dark:text-cyan-400': selectedIndex === {{ $globalIdx }} }">{{ $emp->full_name }}</span>
                                                    <span class="text-[8px] font-bold text-slate-400 tracking-widest uppercase">{{ $emp->job_title ?: 'Unassigned Role' }}</span>
                                                </div>
                                            </div>
                                            <svg x-show="selectedIndex === {{ $globalIdx }}" class="h-4 w-4 text-cyan-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif
            </div>

            <div class="border-t border-slate-100 bg-slate-50 p-2.5 flex items-center gap-4 dark:border-white/5 dark:bg-black/20 justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5 opacity-60">
                        <span class="flex h-4 w-4 items-center justify-center rounded border border-slate-300 dark:border-white/20 bg-white dark:bg-slate-800 pb-0.5"><svg class="h-2.5 w-2.5 text-slate-500 dark:text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg></span>
                        <span class="flex h-4 w-4 items-center justify-center rounded border border-slate-300 dark:border-white/20 bg-white dark:bg-slate-800 pb-0.5"><svg class="h-2.5 w-2.5 text-slate-500 dark:text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg></span>
                        <span class="text-[7px] font-black uppercase text-slate-500 tracking-widest ml-1">Navigate</span>
                    </div>
                    <div class="flex items-center gap-1.5 opacity-60">
                        <span class="flex h-4 w-6 items-center justify-center rounded border border-slate-300 dark:border-white/20 bg-white dark:bg-slate-800 pb-0.5 text-[6px] font-black uppercase text-slate-500 dark:text-slate-400">RET</span>
                        <span class="text-[7px] font-black uppercase text-slate-500 tracking-widest ml-1">Execute</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                     <span class="text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest">PeopleFlow Command</span>
                </div>
            </div>
        </div>
    </div>
</div>
