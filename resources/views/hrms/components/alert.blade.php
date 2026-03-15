@if ($type === 'success')
    <div class="mb-6 flex items-center gap-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 shadow-md animate-fade-in dark:backdrop-blur-xl">
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-500">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
        </div>
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-wider text-emerald-500">Success</p>
            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $message }}</p>
        </div>
    </div>
@elseif ($type === 'error')
    <div class="mb-6 flex items-center gap-4 rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 shadow-md animate-fade-in dark:backdrop-blur-xl">
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-500/20 text-rose-500">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
        </div>
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-wider text-rose-500">Error</p>
            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $message }}</p>
        </div>
    </div>
@endif
