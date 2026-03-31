<div class="space-y-5 pb-8 relative">
    {{-- High-Impact Glass Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white/80 px-6 py-5 shadow-sm border border-slate-200 backdrop-blur-xl dark:bg-slate-900/60 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-cyan-500/5 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center text-center lg:text-left">
            <div>
                <div class="flex items-center justify-center lg:justify-start gap-2 mb-0.5">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Institutional</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Structural Lattice</span>
                </div>
                <h1 class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all">
                    Company <span class="text-cyan-500">Hierarchy</span>
                </h1>
                <p class="mt-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest opacity-80 leading-none">
                    Reporting architecture and organizational node mapping.
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-6 text-[9px] font-black uppercase tracking-widest text-slate-400">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                    <span class="text-slate-900 dark:text-white">{{ $stats['totalEmployees'] }} Nodes</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                    <span class="text-slate-900 dark:text-white">{{ $stats['managers'] }} Tier Leads</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Canvas --}}
    <div class="relative min-h-[400px]">
        @if($ceo)
            <div x-data="binaryTree()" class="relative overflow-x-auto rounded-xl border border-slate-200 bg-slate-50/30 p-8 shadow-inner dark:border-white/5 dark:bg-black/10 custom-scrollbar scroll-smooth">
                <div class="flex justify-center min-w-max">
                    @include('hrms.components.binary-node', ['employee' => $ceo])
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-24 bg-slate-50/50 rounded-xl border border-slate-200 border-dashed dark:bg-black/10 dark:border-white/5">
                <div class="h-14 w-14 flex items-center justify-center rounded-xl bg-slate-100 text-slate-400 mb-4 dark:bg-white/5">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1" /></svg>
                </div>
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest leading-loose">No structural hierarchy detected in current sector.</p>
            </div>
        @endif
    </div>

    <script>
    function binaryTree() {
        return {
            openNodes: {},
            toggle(id) {
                this.openNodes[id] = !this.openNodes[id]
            },
            isOpen(id) {
                return this.openNodes[id]
            }
        }
    }
    </script>
</div>
