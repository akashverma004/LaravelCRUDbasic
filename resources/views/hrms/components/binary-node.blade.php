@props(['employee'])

<div class="binary-node-container flex flex-col items-center relative transition-all duration-500">

    {{-- Employee Card --}}
    <div x-data="{ showInfo: false }" class="relative group node-card">
        {{-- Elite Holographic Card --}}
        <div class="relative overflow-hidden rounded-[2rem] border-2 border-white bg-white/60 p-5 shadow-[0_12px_40px_-12px_rgba(0,0,0,0.08)] backdrop-blur-2xl transition-all duration-300 hover:scale-[1.03] hover:shadow-[0_20px_50px_-12px_rgba(0,0,0,0.12)] hover:border-cyan-400/50 dark:border-white/5 dark:bg-slate-900/60 min-w-[240px]">
            {{-- Accent Light --}}
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-cyan-400/10 blur-2xl transition-all group-hover:bg-cyan-400/20"></div>
            
            <div class="relative flex items-center gap-4 text-left">
                {{-- High-Fidelity Avatar Cluster --}}
                <div class="relative">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white p-0.5 shadow-md border border-slate-100 transition-all group-hover:border-cyan-300 dark:border-white/10 dark:bg-slate-800">
                        @if($employee->profile_photo)
                            <img src="{{ Storage::url($employee->profile_photo) }}" class="h-full w-full rounded-[14px] object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center rounded-[14px] bg-slate-50 text-xs font-black text-slate-400 dark:bg-white/5 uppercase">
                                {{ substr($employee->full_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 leading-none mb-1 text-opacity-80 group-hover:text-opacity-100 italic">
                        {{ $employee->job_title ?? 'Staff' }}
                    </p>
                    <h3 class="truncate text-sm font-black tracking-tight text-slate-900 dark:text-white uppercase transition-colors">
                        {{ $employee->full_name }}
                    </h3>
                    <div class="mt-1 flex items-center gap-1.5 opacity-60">
                         <span class="text-[8px] font-bold text-slate-500 uppercase tracking-widest truncate">
                            {{ $employee->department->name ?? 'Global Unit' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Tactical Metadata Strip --}}
            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 dark:border-white/5">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col">
                        <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest leading-none">Status</span>
                        <span class="mt-1 flex items-center gap-1 text-[8px] font-bold text-slate-900 dark:text-white uppercase">
                            <span class="h-1 w-1 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active
                        </span>
                    </div>

                    {{-- Info Trigger --}}
                    <button @click.stop="showInfo = true" class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-slate-400 transition-colors hover:bg-cyan-50 hover:text-cyan-600 dark:bg-slate-800 dark:text-slate-500 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400 ml-1" title="View Node Intel">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                    </button>
                </div>
                
                {{-- Interactive Sector Drill --}}
                @if($employee->subordinates->isNotEmpty())
                    <button
                        @click.stop="toggle({{ $employee->id }})"
                        class="flex items-center gap-2 rounded-xl bg-slate-900 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest text-white shadow-lg transition-all hover:bg-cyan-600 active:scale-95"
                    >
                        <span x-text="isOpen({{ $employee->id }}) ? 'Hide' : 'Expand'">Expand</span>
                        <svg class="h-2.5 w-2.5 transition-transform duration-300" :class="isOpen({{ $employee->id }}) ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                @endif
            </div>

        </div>

        {{-- Info Popover Overlay (Floating Extracted Node) --}}
        <div x-show="showInfo" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-90 translate-y-2" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="opacity-0 scale-90 translate-y-2" 
             @click.outside="showInfo = false" 
             class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[100] min-w-[280px] flex flex-col justify-center rounded-[2rem] bg-white/95 p-6 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.3)] ring-1 ring-slate-900/10 backdrop-blur-xl dark:bg-slate-900/95 dark:shadow-[0_30px_60px_-15px_rgba(255,255,255,0.15)] dark:ring-white/20" 
             style="display: none;">
            
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-white/10 pb-3">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-800 dark:text-white">Node Intel</h4>
                <button @click.stop="showInfo = false" class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-colors dark:bg-slate-800 dark:text-slate-500 dark:hover:bg-rose-500/20 dark:hover:text-rose-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="space-y-4 text-left">
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Sector / Department</p>
                    <p class="text-[13px] font-bold text-slate-900 dark:text-white mt-1.5">{{ $employee->department->name ?? 'Global Unit' }}</p>
                </div>
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Tier Lead</p>
                    <p class="text-[13px] font-bold text-slate-900 dark:text-white mt-1.5">{{ $employee->manager ? $employee->manager->full_name : 'Organizational Head' }}</p>
                </div>
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Contact Point</p>
                    <div class="flex items-center gap-2.5 mt-1">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-50 dark:bg-cyan-500/10">
                            <svg class="h-3.5 w-3.5 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        </div>
                        <p class="text-[11px] font-bold text-slate-600 dark:text-slate-400 break-all leading-tight">{{ $employee->email ?? 'No comms node' }}</p>
                    </div>
                    @if($employee->phone)
                    <div class="flex items-center gap-2.5 mt-2">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-500/10">
                            <svg class="h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.54-4.24-7.136-7.136l1.292-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                        </div>
                        <p class="text-[11px] font-bold text-slate-600 dark:text-slate-400 leading-tight">{{ $employee->phone }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Subordinates Engine --}}
    @if($employee->subordinates->isNotEmpty())
        <div
            x-show="isOpen({{ $employee->id }})"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 -translate-y-12 scale-95"
            x-transition:enter-end="opacity-100 translate-0 scale-100"
            class="subordinates-container relative pt-24"
            style="display: none;"
        >
            {{-- Primary Structural Connector --}}
            <div class="connector-line absolute top-0 left-1/2 -translate-x-1/2 h-24 w-[2px] bg-gradient-to-b from-slate-200 to-cyan-500/50 dark:from-white/10 main-connector"></div>

            <div class="flex flex-row justify-center gap-12 relative items-start lattice-row">
                {{-- Structural Lattice Grid --}}
                <div class="absolute top-0 left-0 right-0 h-[2px] bg-slate-200 dark:bg-white/10 opacity-50 side-connector-bar"></div>

                @foreach($employee->subordinates as $sub)
                    <div class="relative subordinate-node">
                        {{-- Node Connectors --}}
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 h-4 w-[2px] bg-slate-200 dark:bg-white/10 node-connector"></div>
                        @include('hrms.components.binary-node', ['employee' => $sub])
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
