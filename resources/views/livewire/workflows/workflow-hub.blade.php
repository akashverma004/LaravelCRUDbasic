<div class="space-y-5 pb-12 relative">
    {{-- High-Impact Glass Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white/80 px-6 py-5 shadow-sm border border-slate-200 backdrop-blur-xl dark:bg-slate-900/60 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-indigo-500/5 blur-[80px]"></div>
        <div class="absolute -bottom-20 -left-20 h-40 w-40 rounded-full bg-cyan-500/5 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Institutional Governance</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Mission Hub</span>
                </div>
                <h1 class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all">
                    Protocol <span class="text-indigo-500">Hub</span>
                </h1>
                <p class="mt-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest opacity-80 leading-none">
                    Workflow orchestration and institutional signaling.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Quick Stats --}}
                <div class="flex items-center gap-6 px-4 border-r border-slate-200 dark:border-white/10 hidden xl:flex">
                    <div class="text-right">
                        <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Active Signals</p>
                        <p class="text-sm font-black text-slate-900 dark:text-white leading-none mt-1">{{ $summary['inbox'] }}</p>
                    </div>
                </div>

                <button wire:click="openRequestModal()" class="group relative flex h-10 items-center gap-2 rounded-lg bg-slate-900 px-5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/10 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Initiate Request</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Navigation (Synchronized with MyProfile Style) --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2 border-b border-slate-100 dark:border-white/5">
        <div class="flex p-1 bg-slate-100 dark:bg-black/20 rounded-xl border border-slate-200 dark:border-white/5 shadow-inner">
            <button wire:click="$set('view', 'inbox')" 
                class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-[0.2em] transition-all {{ $view === 'inbox' ? 'bg-white shadow-sm text-cyan-600 dark:bg-white/10 dark:text-cyan-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400' }}">
                Inbox ({{ $summary['inbox'] }})
            </button>
            <button wire:click="$set('view', 'sent')" 
                class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-[0.2em] transition-all {{ $view === 'sent' ? 'bg-white shadow-sm text-cyan-600 dark:bg-white/10 dark:text-cyan-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400' }}">
                Outgoing
            </button>
            <button wire:click="$set('view', 'all')" 
                class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-[0.2em] transition-all {{ $view === 'all' ? 'bg-white shadow-sm text-cyan-600 dark:bg-white/10 dark:text-cyan-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400' }}">
                Archive
            </button>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-cyan-500 transition-colors pointer-events-none">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Trace Protocol..." class="w-56 h-10 pl-10 pr-4 rounded-xl border border-slate-200 bg-white shadow-sm text-[10px] font-black text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-cyan-500/5 focus:border-cyan-500 dark:bg-slate-900 dark:border-white/5 dark:text-white uppercase tracking-widest transition-all">
            </div>
            
            <div class="relative">
                <select wire:model.live="status" class="appearance-none h-10 pl-4 pr-10 rounded-xl border border-slate-200 bg-white shadow-sm text-[10px] font-black text-slate-900 dark:bg-slate-900 dark:border-white/5 dark:text-white uppercase tracking-widest focus:ring-4 focus:ring-cyan-500/5 focus:border-cyan-500 transition-all cursor-pointer">
                    <option value="all">Any Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="fulfilled">Fulfilled</option>
                    <option value="rejected">Rejected</option>
                </select>
                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
        {{-- Request Cards --}}
        <div class="xl:col-span-3 space-y-4">
            @forelse($requests as $req)
                <div wire:click="selectRequest({{ $req->id }})" class="group cursor-pointer relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900/60 transition-all hover:shadow-md hover:border-cyan-400/30">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            {{-- Type Icon (Compact) --}}
                            <div class="h-12 w-12 rounded-xl bg-slate-50 flex items-center justify-center font-black dark:bg-white/5 shadow-inner transition-transform group-hover:scale-105">
                                @if($req->type === 'reimbursement')
                                    <svg class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                @elseif($req->type === 'asset-request')
                                    <svg class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                                @else
                                    <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375m1.875-12h-1.25a2.25 2.25 0 00-2.25 2.25 2.25 2.25 0 00-2.25-2.25H7.5A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.25-2.25V5.25A2.25 2.25 0 0016.5 3z" /></svg>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2.5 mb-1">
                                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-400/80">{{ $req->type }}</span>
                                    <span class="h-0.5 w-0.5 rounded-full bg-slate-200"></span>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-400/80">{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight truncate group-hover:text-cyan-600 transition-colors">{{ $req->title }}</h4>
                                <div class="mt-1.5 flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                         <div class="h-5 w-5 rounded-md bg-white shadow-sm border border-slate-100 flex items-center justify-center overflow-hidden dark:bg-white/5 dark:border-white/5">
                                             @if($req->employee->profile_photo)
                                                 <img src="{{ Storage::url($req->employee->profile_photo) }}" class="h-full w-full object-cover">
                                             @else
                                                 <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest">{{ substr($req->employee->full_name, 0, 1) }}</span>
                                             @endif
                                         </div>
                                         <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">{{ $req->employee->full_name }}</span>
                                     </div>
                                    <span class="h-px w-2 bg-slate-200 dark:bg-white/5"></span>
                                    <span class="text-[9px] font-bold text-slate-400/70 uppercase tracking-widest">{{ $req->submitted_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Metadata (Compact) --}}
                        <div class="flex items-center gap-6 md:text-right">
                            @if($req->amount)
                                <div>
                                    <p class="text-[8px] font-black uppercase text-slate-400 tracking-widest mb-0.5">Quantum</p>
                                    <p class="text-[11px] font-black text-slate-900 dark:text-white">${{ number_format($req->amount, 0) }}</p>
                                </div>
                            @endif

                            <div>
                                <p class="text-[8px] font-black uppercase text-slate-400 tracking-widest mb-0.5">Status</p>
                                <span class="px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest 
                                    {{ $req->status === 'pending' ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10' : '' }}
                                    {{ $req->status === 'approved' ? 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10' : '' }}
                                    {{ $req->status === 'fulfilled' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10' : '' }}
                                    {{ $req->status === 'rejected' ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/10' : '' }}
                                ">
                                    {{ $req->status }}
                                </span>
                            </div>

                            <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 transition-all dark:bg-white/5 group-hover:bg-cyan-500 group-hover:text-white">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-32 text-center rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-white/10">
                    <div class="h-24 w-24 rounded-[2rem] bg-slate-50 flex items-center justify-center text-slate-200 dark:bg-white/5 mb-8">
                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 15.75L9 12m0 0l1.5-3.75M9 12H3" /></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Silent Broadcast</h3>
                    <p class="mt-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">No protocol signals active in current sector search.</p>
                </div>
            @endforelse

            <div class="mt-8">
                {{ $requests->links() }}
            </div>
        </div>

        <div class="xl:col-span-1 space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/5 dark:bg-slate-900/60">
                <h4 class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4">Blueprints</h4>
                
                <div class="space-y-3">
                    @foreach($templates as $templ)
                        <div wire:click="openRequestModal({{ $templ->id }})" class="cursor-pointer group flex items-center justify-between p-3 rounded-xl border border-slate-100 transition-all hover:bg-slate-50 hover:border-cyan-200 dark:border-white/5 dark:bg-white/2 dark:hover:bg-white/5">
                            <div>
                                <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight group-hover:text-cyan-600 transition-colors">{{ $templ->name }}</p>
                                <p class="text-[7px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 line-clamp-1 truncate w-32">{{ $templ->description }}</p>
                            </div>
                            <svg class="h-3 w-3 text-slate-300 group-hover:translate-x-0.5 group-hover:text-cyan-500 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm overflow-hidden relative dark:bg-slate-900/60 dark:border-white/5">
                <div class="absolute -right-8 -bottom-8 h-24 w-24 rounded-full bg-cyan-500/5 blur-2xl"></div>
                <h4 class="text-[8px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 mb-4">Live Pulse</h4>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">Pending Waves</span>
                        <span class="text-base font-black text-slate-900 dark:text-white leading-none">{{ $summary['pending'] }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 dark:border-white/5 pt-3">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">Authorized</span>
                        <span class="text-base font-black text-emerald-600 leading-none">{{ $summary['approved'] }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 dark:border-white/5 pt-3">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">Aborted Flux</span>
                        <span class="text-base font-black text-rose-500 leading-none">{{ $summary['rejected'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Details Modal --}}
    @if($showDetailsModal && $selectedRequest)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 lg:p-10">
            <div wire:click="closeModals" class="absolute inset-0 bg-slate-900/60 backdrop-blur-xl transition-opacity"></div>
            
            <div class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 flex flex-col animate-in fade-in zoom-in duration-300">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-8 py-6 border-b border-slate-100 dark:border-white/5 bg-slate-50/30 dark:bg-white/2 shrink-0">
                    <div class="flex items-center gap-5">
                        <div class="h-12 w-12 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center dark:bg-white/5 dark:border-white/5">
                            @if($selectedRequest->type === 'reimbursement')
                                <svg class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @else
                                <svg class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3m0 0l3-3m-3 3v-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5 mb-0.5">
                                <span class="text-[8px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">{{ $selectedRequest->type }}</span>
                                <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                <span class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">PF-{{ str_pad($selectedRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $selectedRequest->title }}</h2>
                        </div>
                    </div>
                    <button wire:click="closeModals" class="h-9 w-9 rounded-lg bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:border-rose-200 transition-all dark:bg-white/5 dark:border-white/5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                {{-- Modal Content --}}
                <div class="flex-1 overflow-y-auto px-8 py-8 custom-scrollbar">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                        {{-- Main Info --}}
                        <div class="lg:col-span-3 space-y-8">
                            <div class="rounded-2xl border border-slate-100 bg-slate-50/30 p-6 dark:border-white/5 dark:bg-white/2">
                                <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3">Institutional Objective</h4>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300 leading-relaxed uppercase tracking-widest">{{ $selectedRequest->description }}</p>
                            </div>

                            @if($selectedRequest->details)
                                <div>
                                    <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4 px-1">Transmission Metadata</h4>
                                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($selectedRequest->details as $key => $value)
                                            <div class="rounded-xl border border-slate-50 bg-white p-3 shadow-sm dark:border-white/5 dark:bg-slate-900/60 transition-all hover:border-cyan-400/20">
                                                <p class="text-[7px] font-black uppercase text-slate-400 tracking-widest mb-1">{{ str_replace('_', ' ', $key) }}</p>
                                                <p class="text-[10px] font-black uppercase text-slate-900 dark:text-white truncate">{{ is_array($value) ? implode(', ', $value) : $value }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($selectedRequest->attachment_path)
                                <div>
                                    <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 px-1">Support Payload</h4>
                                    <button wire:click="downloadAttachment({{ $selectedRequest->id }})" class="group flex items-center gap-3 rounded-xl border border-slate-100 bg-white p-3 shadow-sm transition-all hover:bg-slate-50 dark:border-white/5 dark:bg-slate-900/60 dark:hover:bg-white/5">
                                        <div class="h-9 w-9 rounded-lg bg-cyan-50 flex items-center justify-center text-cyan-600 dark:bg-cyan-500/10 transition-transform group-hover:scale-110">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 15.75L9 12m0 0l1.5-3.75M9 12H3" /></svg>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-[10px] font-black uppercase text-slate-900 dark:text-white">{{ $selectedRequest->attachment_name }}</p>
                                            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">{{ number_format($selectedRequest->attachment_size / 1024, 2) }} KB</p>
                                        </div>
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Timeline / Sidebar --}}
                        <div class="lg:col-span-1 space-y-8">
                            {{-- Status Card (High Fidelity) --}}
                            <div class="rounded-2xl p-5 text-center relative overflow-hidden border border-slate-100 bg-white shadow-sm dark:bg-slate-900 dark:border-white/5">
                                <div class="absolute -right-10 -top-10 h-24 w-24 rounded-full bg-cyan-500/5 blur-2xl"></div>
                                <p class="text-[7px] font-black uppercase text-slate-400 tracking-[0.2em] mb-1">Cycle Status</p>
                                <div class="flex items-center justify-center gap-2">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $selectedRequest->status === 'pending' ? 'bg-amber-500 animate-pulse' : ($selectedRequest->status === 'approved' ? 'bg-cyan-500' : 'bg-rose-500') }}"></span>
                                    <p class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tighter">{{ $selectedRequest->status }}</p>
                                </div>
                            </div>

                            {{-- Validation Chain (Tactical) --}}
                            <div class="space-y-5 relative">
                                <span class="text-[8px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 block px-1">Validation Chain</span>
                                <div class="space-y-4">
                                    @foreach($selectedRequest->approvals as $idx => $ap)
                                        <div class="relative pl-7">
                                            @if($idx < count($selectedRequest->approvals) - 1)
                                                <div class="absolute left-[13px] top-6 bottom-[-16px] w-[2px] bg-slate-100 dark:bg-white/5"></div>
                                            @endif
                                            <div class="absolute left-0 top-1 h-7 w-7 rounded-full border-2 bg-white shadow-sm flex items-center justify-center dark:bg-slate-950 transition-all 
                                                {{ $ap->decision === 'approved' ? 'border-cyan-500 text-cyan-500' : ($ap->decision === 'rejected' ? 'border-rose-500 text-rose-500' : 'border-slate-100 text-slate-200 dark:border-white/10') }}">
                                                @if($ap->decision === 'approved')
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                                @elseif($ap->decision === 'rejected')
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                @else
                                                    <div class="h-1.5 w-1.5 rounded-full bg-slate-300 dark:bg-white/20"></div>
                                                @endif
                                            </div>
                                            <div>
                                                <h5 class="text-[9px] font-black uppercase transition-colors {{ $ap->decision !== 'pending' ? 'text-slate-900 dark:text-white' : 'text-slate-400' }}">
                                                    {{ $ap->approver->name }}
                                                </h5>
                                                <p class="text-[7px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 max-w-full truncate">{{ $ap->comment ?: 'Awaiting authorization...' }}</p>
                                                @if($ap->acted_at)
                                                    <p class="text-[6px] font-black text-cyan-500 uppercase mt-1">{{ $ap->acted_at->diffForHumans() }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Footer --}}
                @php $canAct = $selectedRequest->approvals->where('approver_user_id', Auth::id())->where('decision', 'pending')->first(); @endphp
                @if($canAct)
                    <div class="px-10 py-8 border-t border-slate-50 bg-slate-50/50 dark:border-white/5 dark:bg-white/2 shrink-0">
                        <div class="flex flex-col gap-6">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-slate-400 ml-1 tracking-widest">Decision Comment (Required for Rejection)</label>
                                <textarea wire:model="decisionComment" placeholder="Enter validation narrative..." class="w-full rounded-2xl border border-slate-200 bg-white px-5 py-4 text-xs font-bold text-slate-900 focus:ring-0 focus:border-cyan-400 dark:border-white/5 dark:bg-slate-900 dark:text-white uppercase tracking-widest h-24"></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="rejectRequest" class="px-8 py-3 rounded-xl border border-rose-200 text-[10px] font-black uppercase tracking-widest text-rose-500 hover:bg-rose-500 hover:text-white transition-all">Reject Protocol</button>
                                <button wire:click="approveRequest" class="px-10 py-3 rounded-xl bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white hover:bg-cyan-600 shadow-xl transition-all">Authorize Sequence</button>
                            </div>
                        </div>
                    </div>
                @elseif(Auth::user()->hasAnyRole(['admin', 'hr_manager']) && $selectedRequest->type === 'asset-request' && $selectedRequest->status === 'approved')
                     <div class="px-10 py-8 border-t border-slate-50 bg-cyan-50/30 dark:border-white/5 dark:bg-cyan-500/5 flex items-center justify-between shrink-0">
                        <p class="text-[10px] font-black text-cyan-600 uppercase tracking-widest">This protocol is authorized. Ready for inventory fulfillment.</p>
                        <button wire:click="$set('showFulfillmentModal', true)" class="px-10 py-3 rounded-xl bg-cyan-600 text-[10px] font-black uppercase tracking-widest text-white hover:bg-cyan-700 shadow-xl transition-all">Execute Deployment</button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Request Modal --}}
    @if($showRequestModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="closeModals" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-2xl rounded-[2.5rem] bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-8 dark:border-white/5 flex items-center justify-between">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Initiate <span class="text-cyan-500">Protocol</span></h2>
                    <button wire:click="closeModals" class="h-8 w-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-500 dark:bg-white/5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <form wire:submit="submitRequest" class="p-8 space-y-8 overflow-y-auto max-h-[70vh] custom-scrollbar">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Transmission Type</label>
                            <select wire:model.live="requestType" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                                @foreach(\App\Models\WorkflowRequest::types() as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Objective Amount (Optional)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400">$</span>
                                <input wire:model="requestAmount" type="number" step="0.01" class="w-full rounded-2xl border border-slate-200 bg-slate-50 pl-8 pr-5 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Protocol Title</label>
                        <input wire:model="requestTitle" type="text" placeholder="Short Identifier..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                        @error('requestTitle') <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Broadcast Narrative</label>
                        <textarea wire:model="requestDescription" placeholder="Detailed objective description..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-xs font-bold text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest h-32 leading-relaxed"></textarea>
                    </div>

                    {{-- Dynamic Details --}}
                    @if($requestType === 'asset-request')
                         <div class="space-y-6 animate-in fade-in slide-in-from-top-4">
                            <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-500 border-b border-cyan-100 pb-2">Resource Specs</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Category</label>
                                    <input wire:model="requestDetails.category" type="text" placeholder="e.g. Laptop, Mobile" class="w-full rounded-xl border-slate-100 bg-white px-4 py-2.5 text-[10px] font-black text-slate-900 dark:bg-white/5 dark:border-white/5 dark:text-white uppercase">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Urgency</label>
                                    <select wire:model="requestDetails.urgency" class="w-full rounded-xl border-slate-100 bg-white px-4 py-2.5 text-[10px] font-black text-slate-900 dark:bg-white/5 dark:border-white/5 dark:text-white uppercase">
                                        <option value="low">Low Priority</option>
                                        <option value="medium">Standard</option>
                                        <option value="high">Critical</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Proof of Context (Attachment)</label>
                        <div 
                            x-data="{ isUploading: false, progress: 0 }" 
                            x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false; progress = 0"
                            x-on:livewire-upload-error="isUploading = false; progress = 0"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                            class="relative min-h-[120px] w-full rounded-3xl border-2 border-dashed border-slate-100 bg-slate-50 flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 hover:border-cyan-200 transition-all dark:bg-white/5 dark:border-white/5"
                        >
                            <input wire:model="requestAttachment" type="file" class="absolute inset-0 opacity-0 cursor-pointer">
                            <div class="text-center p-6">
                                <svg class="h-10 w-10 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $requestAttachment ? $requestAttachment->getClientOriginalName() : 'Inject Support Payload' }}</p>
                                <p class="mt-1 text-[8px] font-bold text-slate-300 uppercase tracking-widest">PDF, JPG, PNG (Max 10MB)</p>
                            </div>
                            
                            <div x-show="isUploading" class="absolute inset-0 bg-white/95 dark:bg-slate-950/95 flex flex-col items-center justify-center rounded-3xl">
                                <span class="text-[10px] font-black text-cyan-500 uppercase tracking-widest mb-3">Transmitting Wave... <span x-text="progress + '%'"></span></span>
                                <div class="w-48 h-1 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-cyan-500 transition-all duration-300 shadow-[0_0_10px_rgba(6,182,212,0.5)]" :style="'width: ' + progress + '%'"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end gap-4">
                        <button type="button" wire:click="closeModals" class="text-[10px] font-black uppercase text-slate-500 px-6">Abort Launch</button>
                        <button type="submit" wire:loading.attr="disabled" class="rounded-2xl bg-slate-900 px-12 py-4 text-[11px] font-black uppercase text-white shadow-2xl hover:bg-cyan-600 transition-all active:scale-95 disabled:opacity-50">Launch Protocol Stream</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Fulfillment Modal --}}
    @if($showFulfillmentModal && $selectedRequest)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div wire:click="$set('showFulfillmentModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-lg rounded-[2.5rem] bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-8 dark:border-white/5">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Deploy <span class="text-cyan-500">Inventory Asset</span></h2>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Select available resource for deployment to {{ $selectedRequest->employee->full_name }}</p>
                </div>
                
                <form wire:submit="fulfillAsset" class="p-8 space-y-6">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Target Resource (Available Inventory)</label>
                        <select wire:model="fulfillmentAssetId" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                            <option value="">Select Asset...</option>
                            @foreach($availableAssets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->name }} (SN: {{ $asset->serial_number }})</option>
                            @endforeach
                        </select>
                        @error('fulfillmentAssetId') <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Deployment Note</label>
                        <textarea wire:model="fulfillmentComment" placeholder="Condition narrative or instructions..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-xs font-bold text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase h-24"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-4">
                        <button type="button" wire:click="$set('showFulfillmentModal', false)" class="text-[10px] font-black uppercase text-slate-500 px-6">Cancel</button>
                        <button type="submit" class="rounded-2xl bg-cyan-600 px-10 py-3 text-[10px] font-black uppercase text-white shadow-xl hover:bg-cyan-700 transition-all">Authorize Deployment</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
