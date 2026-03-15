<div x-data="commandPalette()" 
     @open-command-palette.window="open()"
     @keydown.window.prevent.cmd.k="open()" 
     @keydown.window.prevent.ctrl.k="open()"
     @keydown.window.escape="close()"
     class="relative z-[100]" 
     x-show="isOpen" 
     style="display: none;">
    
    {{-- Global Backdrop --}}
    <div x-show="isOpen" 
         x-transition.opacity 
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm dark:bg-slate-950/80"></div>

    <div class="fixed inset-0 overflow-y-auto p-4 sm:p-6 md:p-12">
        <div x-show="isOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="close()"
             class="mx-auto max-w-xl transform overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-white/10">
            
            {{-- Search Bar --}}
            <div class="relative px-4 py-4 border-b border-slate-100 dark:border-white/5">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" x-ref="searchInput" x-model="query"
                           class="w-full border-0 bg-transparent p-0 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:ring-0 dark:text-white dark:placeholder:text-slate-500" 
                           placeholder="Search..."
                           @keydown.arrow-down.prevent="next()"
                           @keydown.arrow-up.prevent="prev()"
                           @keydown.enter.prevent="select()">
                    <div class="hidden sm:block">
                        <kbd class="rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">ESC</kbd>
                    </div>
                </div>
            </div>

            {{-- Results List --}}
            <div x-show="filteredItems.length > 0" class="py-2">
                <p class="px-4 text-[10px] font-semibold uppercase tracking-wider text-slate-500 my-1">Results</p>
                <ul class="max-h-72 overflow-y-auto custom-scrollbar px-2 space-y-0.5">
                    <template x-for="(item, index) in filteredItems" :key="item.url">
                        <li class="cursor-pointer select-none rounded-xl px-3 py-2 transition-colors"
                            :class="selectedIndex === index ? 'bg-cyan-50 dark:bg-cyan-500/10' : 'hover:bg-slate-50 dark:hover:bg-slate-800/80'"
                            @mouseenter="selectedIndex = index"
                            @click="select()">
                            <div class="flex items-center gap-3">
                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg"
                                      :class="selectedIndex === index ? 'bg-cyan-100/50 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'">
                                    <template x-if="item.type === 'page'">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.25 11.25L10.5 15.75m9.75-4.875c0 5.591-4.409 10.125-10 10.125a10.02 10.02 0 01-5.63-1.688l-3.396.947a.75.75 0 01-.947-.947l.947-3.396A10.02 10.02 0 012.25 12c0-5.591 4.409-10 10-10a10.02 10.02 0 015.63 1.688l3.396-.947a.75.75 0 01.947.947l-.947 3.396c1.303 1.258 2.103 3.018 2.103 4.965z" /></svg>
                                    </template>
                                    <template x-if="item.type === 'action'">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.122 6.412l-2.26 2.258m3.434-3.414l.97-.966a1.125 1.125 0 011.59 1.59l-.966.97m-1.594-1.594l2.71 2.71m-2.71-2.71l-3.378 3.378m.749-3.08a1.125 1.125 0 011.59 1.59l-3.38 3.38a1.125 1.125 0 01-1.59-1.59l3.38-3.38zm-5.744 5.744a1.125 1.125 0 011.59 1.59l-3.38 3.38a1.125 1.125 0 01-1.59-1.59l3.38-3.38z" /></svg>
                                    </template>
                                </div>
                                <div class="flex-grow min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate dark:text-white" x-text="item.title"></p>
                                    <p class="text-[10px] font-medium text-slate-500 mt-0.5 truncate" x-text="item.category"></p>
                                </div>
                                <template x-if="selectedIndex === index">
                                    <span class="text-[10px] font-semibold text-cyan-600 dark:text-cyan-400 hidden sm:inline-block">Select</span>
                                </template>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>

            {{-- Empty State --}}
            <div x-show="query.length > 0 && filteredItems.length === 0" class="px-6 py-12 text-center" style="display: none;">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 mb-3 shadow-sm dark:bg-slate-800 dark:text-slate-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">No results found</h3>
                <p class="mt-1 text-xs text-slate-500">We couldn't find anything matching your search.</p>
            </div>

            {{-- Footer Hints --}}
            <div class="flex items-center justify-between px-4 py-3 border-t border-slate-100 bg-slate-50 dark:border-white/5 dark:bg-slate-900">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5 text-[10px] font-medium text-slate-500"><kbd class="rounded border border-slate-200 bg-white px-1.5 py-0.5 dark:border-slate-700 dark:bg-slate-800">Enter</kbd> to select</span>
                    <span class="flex items-center gap-1.5 text-[10px] font-medium text-slate-500"><kbd class="rounded border border-slate-200 bg-white px-1.5 py-0.5 dark:border-slate-700 dark:bg-slate-800">↑↓</kbd> to navigate</span>
                </div>
            </div>
        </div>
    </div>
</div>
