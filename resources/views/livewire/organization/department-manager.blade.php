<div class="space-y-5 pb-8 relative">
    {{-- High-Impact Glass Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white/80 px-5 py-4 shadow-sm border border-slate-200 backdrop-blur-xl dark:bg-slate-900/60 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-cyan-500/5 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center text-center lg:text-left">
            <div>
                <div class="flex items-center justify-center lg:justify-start gap-2 mb-0.5">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Organization</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Structure</span>
                </div>
                <h1 class="text-base font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all">
                    Department <span class="text-cyan-500">Directory</span>
                </h1>
                <p class="mt-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest opacity-80 leading-none">
                    Team mapping and organizational unit management.
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-2.5">
                <div class="flex items-center rounded-lg bg-slate-100 p-1 dark:bg-white/5 border border-slate-200/50 dark:border-white/5">
                    <button wire:click="$set('viewMode', 'list')" class="flex h-8 w-8 items-center justify-center rounded-md transition-all {{ $viewMode === 'list' ? 'bg-white shadow-sm text-cyan-500 dark:bg-slate-800' : 'text-slate-400' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" /></svg>
                    </button>
                    <button wire:click="$set('viewMode', 'grid')" class="flex h-8 w-8 items-center justify-center rounded-md transition-all {{ $viewMode === 'grid' ? 'bg-white shadow-sm text-cyan-500 dark:bg-slate-800' : 'text-slate-400' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z" /></svg>
                    </button>
                </div>

                @if ($this->canManage)
                    <button wire:click="openCreateModal" class="inline-flex h-10 items-center gap-2 rounded-lg bg-slate-900 px-5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/10 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Add Unit</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="relative min-h-[400px]">
        @if(count($this->departments) > 0)
            
            {{-- Grid View --}}
            @if($viewMode === 'grid')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($this->departments as $department)
                    <div wire:key="dept-grid-{{ $department->id }}" class="group relative rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition-all hover:border-cyan-500/30 hover:shadow-md dark:border-white/5 dark:bg-slate-900/50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-50 text-[10px] font-black text-slate-400 dark:bg-white/5 border border-slate-100 dark:border-white/5">{{ $department->code }}</div>
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg text-cyan-500 group-hover:scale-110 transition-transform">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011-1v5m-4 0h4"></path></svg>
                            </div>
                        </div>

                        <a href="{{ route('departments.show', $department->id) }}" wire:navigate class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight group-hover:text-cyan-500 transition-colors block truncate mb-1" title="{{ $department->name }}">{{ $department->name }}</a>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">{{ $department->employees_count }} Personnel</p>
                        
                        <div class="mt-4 pt-3 border-t border-slate-50 dark:border-white/5 flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Primary Lead</p>
                                <p class="text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase truncate leading-none">{{ $department->lead_name ?? 'No Lead Assigned' }}</p>
                            </div>
                            @if($this->canManage)
                            <div class="flex items-center gap-1 shrink-0 ml-2">
                                <button wire:click="deleteDepartment({{ $department->id }})" wire:confirm="Are you sure you want to delete this department?" class="p-1.5 text-slate-300 hover:text-rose-500 transition-all opacity-0 group-hover:opacity-100"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- List View (Stream Layout) --}}
            @if($viewMode === 'list')
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-5 py-3 dark:border-white/5 dark:bg-white/5">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">{{ count($this->departments) }} Organizational Units</p>
                    <button wire:click="$refresh" class="rounded-lg p-1.5 text-slate-400 hover:text-cyan-500 transition-all">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                    </button>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach($this->departments as $department)
                        <div wire:key="dept-list-{{ $department->id }}" class="group flex items-center justify-between px-5 py-3.5 hover:bg-slate-50/50 dark:hover:bg-white/[0.01] transition-all">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-slate-100 text-[10px] font-black text-slate-400 dark:bg-white/5 transition-colors group-hover:text-cyan-500 border border-slate-200/50 dark:border-white/5">
                                        <span>{{ $department->code }}</span>
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white bg-cyan-500 dark:border-slate-950 shadow-sm animate-pulse"></div>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-[11px] font-black uppercase tracking-tight text-slate-900 dark:text-white leading-none">
                                        <a href="{{ route('departments.show', $department->id) }}" wire:navigate class="hover:text-cyan-500 transition-colors">{{ $department->name }}</a>
                                    </h3>
                                    <div class="mt-1.5 flex items-center gap-2">
                                        <span class="text-[8px] font-black uppercase text-slate-400">Led By:</span>
                                        <span class="text-[9px] font-black uppercase text-slate-600 dark:text-slate-400">{{ $department->lead_name ?? 'Vacant' }}</span>
                                        <span class="h-0.5 w-0.5 rounded-full bg-slate-200 dark:bg-white/10"></span>
                                        <span class="text-[9px] font-black uppercase text-cyan-600 dark:text-cyan-400">{{ $department->employees_count }} Members</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2.5">
                                <a href="{{ route('departments.show', $department->id) }}" wire:navigate class="rounded-lg border border-slate-200 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-50 dark:border-white/5 dark:text-slate-400 dark:hover:bg-white/10 transition-all opacity-0 group-hover:opacity-100">
                                    View Grid
                                </a>
                                @if ($this->canManage)
                                    <button wire:click="deleteDepartment({{ $department->id }})" wire:confirm="Are you sure?" class="text-rose-400 hover:text-rose-600 transition-all opacity-0 group-hover:opacity-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                @endif
                                <svg class="h-4 w-4 text-slate-300 dark:text-white/10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="py-24 text-center border-2 border-dashed border-slate-200 rounded-xl dark:border-white/5">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-slate-50 text-slate-300 dark:bg-white/5">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                </div>
                <p class="mt-4 text-[11px] font-black uppercase tracking-widest text-slate-400">The organizational lattice is currently empty.</p>
            </div>
        @endif
    </div>

    {{-- Create Modal (Standardized) --}}
    @if ($showCreateModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
        <div class="w-full max-w-sm rounded-xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden">
            <div class="border-b border-slate-100 p-5 dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-white/5">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Provision <span class="text-cyan-500">Unit</span></h3>
                <button wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form wire:submit="submitCreate">
                <div class="p-5 space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-0.5">Department Identifier</label>
                        <input wire:model="form.name" type="text" placeholder="e.g. Talent Acquisition" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white transition-all">
                        @error('form.name') <span class="text-[8px] font-black text-rose-500 uppercase ml-0.5">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-0.5">Lattice Code</label>
                            <input wire:model="form.code" type="text" placeholder="TAC" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase transition-all">
                            @error('form.code') <span class="text-[8px] font-black text-rose-500 uppercase ml-0.5">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-0.5">Personnel Lead</label>
                            <select wire:model="form.lead_employee_id" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white transition-all">
                                <option value="">Select Leader</option>
                                @foreach($this->employeesList as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 bg-slate-50 px-5 py-4 dark:bg-white/5 border-t border-slate-100 dark:border-white/5">
                    <button type="button" wire:click="closeCreateModal" class="text-[9px] font-black uppercase text-slate-400 hover:text-slate-600 transition-all">Abort</button>
                    <button type="submit" class="rounded-lg bg-slate-900 px-6 py-2 text-[9px] font-black uppercase text-white shadow-xl hover:bg-cyan-600 disabled:opacity-50 transition-all active:scale-95" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submitCreate">Generate Mapping</span>
                        <span wire:loading wire:target="submitCreate">Syncing...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
