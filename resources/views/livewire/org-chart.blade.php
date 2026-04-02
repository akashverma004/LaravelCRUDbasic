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

    {{-- Chart Canvas with Zoom/Pan --}}
    <div x-data="binaryTree()" class="relative min-h-[600px] w-full overflow-hidden rounded-[2.5rem] border-2 border-white bg-slate-50/40 shadow-[inset_0_4px_12px_rgba(0,0,0,0.03)] backdrop-blur-xl dark:border-white/5 dark:bg-slate-900/40">
        {{-- Tactical Grid Overlay --}}
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]" 
             style="background-image: radial-gradient(#0ea5e9 0.5px, transparent 0.5px); background-size: 24px 24px;"></div>

        {{-- Controls --}}
        <div class="absolute left-6 top-6 z-30 flex flex-col gap-2">
            <div class="flex items-center gap-1 rounded-2xl border border-slate-200 bg-white/90 p-1.5 shadow-xl shadow-slate-200/20 backdrop-blur-md dark:border-white/10 dark:bg-slate-800/90 dark:shadow-none">
                <button @click="zoomIn()" class="flex h-8 w-8 items-center justify-center rounded-xl transition-all hover:bg-slate-100 hover:text-cyan-600 dark:hover:bg-white/5 dark:hover:text-cyan-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </button>
                <div class="h-4 w-[1px] bg-slate-200 dark:bg-white/10"></div>
                <button @click="zoomOut()" class="flex h-8 w-8 items-center justify-center rounded-xl transition-all hover:bg-slate-100 hover:text-cyan-600 dark:hover:bg-white/5 dark:hover:text-cyan-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" /></svg>
                </button>
                <div class="h-4 w-[1px] bg-slate-200 dark:bg-white/10"></div>
                <button @click="resetZoom()" class="flex h-8 w-8 items-center justify-center rounded-xl transition-all hover:bg-slate-100 hover:text-cyan-600 dark:hover:bg-white/5 dark:hover:text-cyan-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                </button>
            </div>
            
            <button @click="toggleOrientation()" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/90 px-4 py-2.5 shadow-xl shadow-slate-200/20 backdrop-blur-md transition-all hover:bg-slate-50 dark:border-white/10 dark:bg-slate-800/90 dark:shadow-none">
                <svg class="h-4 w-4 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" :class="horizontal ? 'rotate-90' : ''"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" /></svg>
                <span class="text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Orientation</span>
            </button>
        </div>

        {{-- Infinite Field --}}
        <div 
            @mousedown="startPan" 
            @mousemove="pan" 
            @mouseup="endPan"
            @mouseleave="endPan"
            @wheel.prevent="handleWheel"
            class="h-full w-full cursor-grab active:cursor-grabbing overflow-hidden"
        >
            <div 
                class="origin-center transition-all duration-300 ease-out"
                :style="`transform: translate(${translateX}px, ${translateY}px) scale(${zoom})`"
                :class="horizontal ? 'is-horizontal' : 'is-vertical'"
            >
                @if($ceo)
                    <div class="flex justify-center p-32">
                        @include('hrms.components.binary-node', ['employee' => $ceo])
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center min-h-[400px]">
                        <div class="h-16 w-16 flex items-center justify-center rounded-[2rem] bg-slate-100 text-slate-300 dark:bg-white/5">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" /></svg>
                        </div>
                        <p class="mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Structural Lattice Empty</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Horizontal Orientation Logic */
        .is-horizontal .binary-node-container { flex-direction: row; }
        .is-horizontal .subordinates-container { padding-top: 0; padding-left: 6rem; flex-direction: row; }
        .is-horizontal .main-connector { 
            top: 50% !important; left: -6rem !important; 
            width: 6rem !important; height: 2px !important; 
            background: linear-gradient(to right, #e2e8f0, #0ea5e980) !important;
            transform: translateY(-50%) !important;
        }
        .is-horizontal .lattice-row { flex-direction: column; }
        .is-horizontal .side-connector-bar { 
            top: 0 !important; bottom: 0 !important; left: 0 !important;
            width: 2px !important; height: 100% !important;
        }
        .is-horizontal .subordinate-node { padding-left: 6rem; }
        .is-horizontal .node-connector {
            top: 50% !important; left: -6rem !important;
            width: 6rem !important; height: 2px !important;
            transform: translateY(-50%) !important;
        }

        /* Animations */
        .node-card { backface-visibility: hidden; transform-style: preserve-3d; }
    </style>

    <script>
    function binaryTree() {
        return {
            openNodes: {},
            zoom: 0.8,
            translateX: 0,
            translateY: 0,
            isPanning: false,
            startX: 0,
            startY: 0,
            horizontal: false,

            init() {
                if ({{ $ceo ? $ceo->id : 'null' }}) {
                    this.openNodes[{{ $ceo ? $ceo->id : 0 }}] = true;
                }
            },

            toggle(id) {
                this.openNodes[id] = !this.openNodes[id]
            },

            isOpen(id) {
                return this.openNodes[id]
            },

            zoomIn() { this.zoom = Math.min(this.zoom + 0.1, 2) },
            zoomOut() { this.zoom = Math.max(this.zoom - 0.1, 0.3) },
            resetZoom() { this.zoom = 0.8; this.translateX = 0; this.translateY = 0; },
            
            handleWheel(e) {
                if (e.deltaY < 0) this.zoomIn();
                else this.zoomOut();
            },

            startPan(e) {
                if(e.button !== 0) return;
                this.isPanning = true;
                this.startX = e.clientX - this.translateX;
                this.startY = e.clientY - this.translateY;
            },

            pan(e) {
                if (!this.isPanning) return;
                this.translateX = e.clientX - this.startX;
                this.translateY = e.clientY - this.startY;
            },

            endPan() {
                this.isPanning = false;
            },

            toggleOrientation() {
                this.horizontal = !this.horizontal;
            }
        }
    }
    </script>
</div>
