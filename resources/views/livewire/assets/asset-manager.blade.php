<div class="space-y-8 pb-12">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-orange-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-orange-600 dark:text-orange-400">Inventory Hub</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Lifecycle Management</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Asset <span class="text-orange-500">Inventory</span>
                </h1>
                <p class="mt-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-loose">
                    Track hardware, peripherals and software assigned to your team.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto self-end lg:self-center">
                <div class="relative group w-full sm:w-80">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400 group-focus-within:text-orange-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filter by Name or Serial..." 
                           class="w-full pl-12 pr-4 rounded-xl border border-slate-200 bg-slate-50 py-3 text-xs font-black text-slate-900 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                </div>
                @if($isAdmin)
                    <button wire:click="openCreateModal" class="h-12 inline-flex items-center justify-center gap-3 rounded-xl bg-slate-900 px-8 text-[11px] font-black uppercase tracking-widest text-white shadow-xl transition-all hover:bg-orange-600 active:scale-95 dark:bg-white/5 dark:text-orange-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>New Asset</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Asset Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" wire:loading.class="opacity-50">
        @forelse($assets as $asset)
            <div class="group relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:shadow-md">
                <div class="flex items-start justify-between mb-4">
                    <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center font-black dark:bg-white/5 border border-slate-100 dark:border-white/5">
                        @php
                            $icon = match($asset->category) {
                                'laptop' => '<svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>',
                                'desktop' => '<svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>',
                                'mobile' => '<svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-6 18.75h12" /></svg>',
                                default => '<svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-5.25v9" /></svg>',
                            }
                        @endphp
                        {!! $icon !!}
                    </div>
                    <div class="flex flex-col items-end">
                        @if($asset->status === 'available')
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-[9px] font-black uppercase text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Inventory</span>
                        @elseif($asset->status === 'assigned')
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-[9px] font-black uppercase text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">Assigned</span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg bg-rose-50 text-[9px] font-black uppercase text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">{{ $asset->status }}</span>
                        @endif
                    </div>
                </div>

                <div class="mb-6 min-h-[3rem]">
                    <p class="text-[12px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $asset->name }}</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">SN: {{ $asset->serial_number ?: 'NOT SET' }}</p>
                </div>

                <div class="pt-6 border-t border-slate-50 dark:border-white/5 flex items-center justify-between">
                    <div>
                        <p class="text-[8px] font-black uppercase text-slate-400 tracking-widest mb-1">Custodian</p>
                        @if($asset->employee)
                            <p class="text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase">{{ $asset->employee->full_name }}</p>
                        @else
                            <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase">Unassigned</p>
                        @endif
                    </div>
                    @if($isAdmin)
                        <button wire:click="openEditModal({{ $asset->id }})" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-orange-50 hover:text-orange-600 transition-all dark:bg-white/5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="flex flex-col items-center">
                    <div class="h-16 w-16 rounded-full bg-slate-50 flex items-center justify-center mb-4 dark:bg-white/5">
                        <svg class="h-8 w-8 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-5.25v9" /></svg>
                    </div>
                    <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Zero Assets Found</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Try refining your search or add a new asset.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $assets->links() }}
    </div>

    {{-- Asset Modal --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showEditModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-6 dark:border-white/5 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $selectedAssetId ? 'Configure' : 'New' }} <span class="text-orange-500">Asset</span></h2>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Infrastructure Management Console</p>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500 ml-1">Asset Model Name</label>
                            <input wire:model="name" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500 ml-1">Stock Category</label>
                            <select wire:model="category" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500 ml-1">Serial Number</label>
                            <input wire:model="serial_number" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500 ml-1">Inventory Status</label>
                            <select wire:model="status" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500 ml-1">Assign to Employee</label>
                        <select wire:model="employee_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                            <option value="">No Assignment (Inventory)</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500 ml-1">Maintenance Notes</label>
                        <textarea wire:model="notes" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 focus:border-orange-500 focus:ring-1 focus:ring-orange-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-tighter"></textarea>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50 p-6 dark:border-white/5 dark:bg-white/5 flex justify-end gap-3">
                    <button wire:click="$set('showEditModal', false)" class="text-[10px] font-black uppercase text-slate-500 px-4">Cancel</button>
                    <button wire:click="save" class="rounded-xl bg-slate-900 px-8 py-2.5 text-[10px] font-black uppercase text-white shadow-xl hover:bg-orange-600 transition-all">Submit Asset Record</button>
                </div>
            </div>
        </div>
    @endif
</div>
