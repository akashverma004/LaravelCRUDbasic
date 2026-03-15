<footer class="mt-12 border-t border-white/5 py-8 bg-slate-950/20 backdrop-blur-xl">
    <div class="mx-auto max-w-7xl flex flex-col items-center justify-between gap-6 px-8 sm:flex-row">
        <div class="flex items-center gap-4">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-400 to-indigo-600 shadow-md">
                <span class="text-sm font-bold text-white">P</span>
            </div>
            <div>
                <p class="text-xs text-slate-400">
                    &copy; {{ now()->year }} <span class="font-medium text-slate-200">PeopleFlow HRMS</span>
                </p>
                <div class="flex items-center gap-2 mt-0.5">
                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                    <span class="text-[10px] text-emerald-500/80 uppercase tracking-wider">System Online</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-8">
            <div class="text-right">
                <p class="text-[10px] uppercase font-semibold text-slate-500 tracking-wider">Version</p>
                <p class="text-xs font-medium text-slate-400 mt-0.5">4.0.2</p>
            </div>
        </div>
    </div>
</footer>
