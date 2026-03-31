<div class="space-y-8 pb-12">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-white px-10 py-10 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-600 dark:text-indigo-400">Institutional Infrastructure</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Workspace Management</span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Organizational <span class="text-indigo-500">Lattice</span>
                </h1>
                <p class="mt-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-loose max-w-2xl">
                    Mission control for multi-tenant provisioning, jurisdictional governance, and workspace configuration across the PeopleFlow constellation.
                </p>
            </div>

            <button wire:click="openModal()" class="group relative flex items-center gap-3 rounded-2xl bg-slate-900 px-8 py-4 text-[11px] font-black uppercase tracking-widest text-white shadow-2xl hover:bg-indigo-600 transition-all active:scale-95">
                <span>Provision Workspace</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-4">
            <div class="relative w-80">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search jurisdictional nodes..." class="w-full rounded-2xl border border-slate-200 bg-white px-5 py-3 text-[11px] font-black text-slate-900 placeholder-slate-400 focus:ring-0 focus:border-indigo-400 dark:border-white/5 dark:bg-slate-900/50 dark:text-white uppercase tracking-widest transition-all shadow-sm">
            </div>
            <select wire:model.live="status" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-[11px] font-black text-slate-900 dark:border-white/5 dark:bg-slate-900/50 dark:text-white uppercase tracking-widest transition-all shadow-sm">
                <option value="">Any Status</option>
                <option value="1">Operational</option>
                <option value="0">Deactivated</option>
            </select>
        </div>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($tenants as $tenant)
            <div class="group relative flex flex-col rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:shadow-xl hover:border-indigo-400/30">
                <div class="flex items-start justify-between mb-6">
                    <div class="h-14 w-14 flex items-center justify-center rounded-2xl bg-slate-50 font-black text-[13px] text-slate-400 shadow-inner dark:bg-white/5 dark:text-slate-500 transition-transform group-hover:scale-110">
                        {{ substr($tenant->name, 0, 1) }}
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="openModal({{ $tenant->id }})" class="p-2.5 rounded-xl bg-slate-50 text-slate-400 hover:bg-indigo-600 hover:text-white transition-all dark:bg-white/5">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                        </button>
                        <button wire:confirm="Are you sure you want to detach this workspace node?" wire:click="delete({{ $tenant->id }})" class="p-2.5 rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white transition-all dark:bg-white/5">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-2">
                    <span class="text-[10px] font-black uppercase text-indigo-500 tracking-widest">{{ $tenant->code }}</span>
                    <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                    <span class="text-[8px] font-black uppercase text-slate-400 tracking-[0.2em]">{{ $tenant->currency }} | {{ $tenant->timezone }}</span>
                </div>
                <h4 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight truncate">{{ $tenant->name }}</h4>
                <p class="mt-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-loose line-clamp-1 truncate">{{ $tenant->email ?: 'NO DEPLOYMENT CONTACT' }}</p>

                <div class="mt-8 pt-6 border-t border-slate-50 dark:border-white/5 flex items-center justify-between">
                    <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest 
                        {{ $tenant->is_active ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400' }}">
                        {{ $tenant->is_active ? 'Operational' : 'Hibernating' }}
                    </span>
                    <div class="flex items-center gap-2 text-[10px] font-black uppercase text-slate-400 tracking-widest">
                        <span>Configured</span>
                        <div class="h-1.5 w-1.5 rounded-full {{ $tenant->setup_completed ? 'bg-cyan-500' : 'bg-amber-500' }}"></div>
                    </div>
                </div>
            </div>
        @endforeach

        <div wire:click="openModal()" class="group relative cursor-pointer flex flex-col items-center justify-center rounded-[2.5rem] border-4 border-dashed border-slate-100 p-12 text-center transition-all hover:bg-slate-50 hover:border-indigo-400 dark:border-white/5 dark:hover:bg-white/2">
            <div class="h-16 w-16 flex items-center justify-center rounded-2xl bg-white shadow-sm border border-slate-100 text-slate-300 dark:bg-slate-900 dark:border-white/10 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-all">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            </div>
            <h4 class="mt-6 text-[12px] font-black text-slate-400 uppercase tracking-[0.25em] group-hover:text-indigo-600 transition-colors">Deploy New Workspace</h4>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $tenants->links() }}
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 lg:p-10">
            <div wire:click="$set('showModal', false)" class="absolute inset-0 bg-slate-900/60 backdrop-blur-xl transition-opacity"></div>
            
            <div class="relative w-full max-w-2xl max-h-[90vh] overflow-hidden rounded-[3rem] bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 flex flex-col animate-in fade-in zoom-in duration-300">
                <div class="flex items-center justify-between px-10 py-8 border-b border-slate-50 dark:border-white/5 shrink-0">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Workspace <span class="text-indigo-500">Configuration</span></h2>
                    <button wire:click="$set('showModal', false)" class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-500 dark:bg-white/5 transition-all">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 overflow-y-auto px-10 py-10 custom-scrollbar space-y-8">
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Institutional Name</label>
                            <input wire:model="name" type="text" placeholder="Organizational Identity..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest focus:ring-0 focus:border-indigo-400">
                            @error('name') <span class="text-[9px] font-bold text-rose-500 uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Unique Sector Code</label>
                            <input wire:model="code" type="text" placeholder="E.G. DE-BERLIN..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest focus:ring-0 focus:border-indigo-400" {{ $editingTenantId ? 'disabled' : '' }}>
                            @error('code') <span class="text-[9px] font-bold text-rose-500 uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Security Email</label>
                            <input wire:model="email" type="email" placeholder="admin@domain.com" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Operational Currency</label>
                            <input wire:model="currency" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Temporal Sector (Timezone)</label>
                            <input wire:model="timezone" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Jurisdictional Status</label>
                            <div class="flex items-center gap-4 py-3">
                                <button type="button" @click="$wire.set('isActive', true)" :class="$wire.isActive ? 'bg-emerald-600 text-white shadow-lg' : 'bg-slate-100 text-slate-400 dark:bg-white/5'" class="flex-1 py-1 px-4 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Active</button>
                                <button type="button" @click="$wire.set('isActive', false)" :class="!$wire.isActive ? 'bg-rose-600 text-white shadow-lg' : 'bg-slate-100 text-slate-400 dark:bg-white/5'" class="flex-1 py-1 px-4 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Suspended</button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 dark:border-white/5 flex justify-end gap-4">
                        <button type="button" wire:click="$set('showModal', false)" class="text-[11px] font-black uppercase text-slate-500 px-8 transition-colors hover:text-slate-800 dark:hover:text-slate-200">Abort</button>
                        <button type="submit" class="rounded-2xl bg-indigo-600 px-12 py-4 text-[11px] font-black uppercase text-white shadow-2xl hover:bg-indigo-700 transition-all active:scale-95">Synchronize Jurisdictional Node</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
