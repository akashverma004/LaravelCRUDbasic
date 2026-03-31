<div class="space-y-8 pb-12">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-white px-10 py-10 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400">Institutional Governance</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Audit Grid</span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Security <span class="text-cyan-500">Chronicle</span>
                </h1>
                <p class="mt-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-loose max-w-2xl">
                    Mission control for organizational traceability, administrative verification, and security signal tracking.
                </p>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative w-80">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search audit signals..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3.5 pl-11 text-[11px] font-black text-slate-900 placeholder-slate-400 focus:ring-0 focus:border-cyan-400 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest transition-all">
                </div>
            </div>
        </div>
    </div>

    {{-- Audit Log Table --}}
    <div class="relative overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-100 dark:border-white/5">
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-400">Identity Vector</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Action Signal</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Context Label</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 text-right">Temporal Marker</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                @forelse($logs as $log)
                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 flex grow-0 shrink-0 items-center justify-center rounded-xl bg-slate-100 font-black text-[10px] text-slate-500 shadow-inner dark:bg-white/5 dark:text-slate-400 uppercase">
                                    {{ substr($log->user->name ?? 'SYSTEM', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[12px] font-black text-slate-900 dark:text-white uppercase tracking-tight truncate">{{ $log->user->name ?? 'CORE SYSTEM' }}</p>
                                    <p class="mt-0.5 text-[9px] font-bold text-slate-400 uppercase tracking-widest truncate line-clamp-1">{{ $log->user->email ?? 'KERNEL@PEOPLEFLOW' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="inline-flex items-center rounded-lg bg-cyan-50 px-2 py-1 text-[9px] font-black uppercase tracking-widest text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-relaxed line-clamp-1 truncate max-w-sm">
                                {{ $log->description }}
                            </p>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tighter">{{ $log->created_at->format('d M Y') }}</p>
                            <p class="mt-1 text-[8px] font-bold text-slate-400 uppercase tracking-widest">{{ $log->created_at->format('H:i:s P') }}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-32 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-20 w-20 rounded-[1.5rem] bg-slate-50 flex items-center justify-center text-slate-200 dark:bg-white/5 mb-6">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"></path></svg>
                                </div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Silent Archives</h3>
                                <p class="mt-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest">No security signals detected in the specified temporal sector.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-8 px-4">
        {{ $logs->links() }}
    </div>
</div>
