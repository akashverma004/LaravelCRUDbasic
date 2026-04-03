<div class="space-y-4 pb-8">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-orange-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-3 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-orange-600 dark:text-orange-400">Inventory Hub</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Lifecycle Management</span>
                </div>
                <h1 class="text-base font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Asset <span class="text-orange-500">Inventory</span>
                </h1>
                <p class="mt-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-loose">
                    Track hardware, peripherals and software assigned to your team.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto self-end lg:self-center">
                <div class="relative group w-full sm:w-80">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400 group-focus-within:text-orange-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filter by Name or Serial..." 
                           class="w-full pl-9 pr-3 rounded-xl border border-slate-200 bg-slate-50 py-2.5 text-[10px] font-black text-slate-900 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        
        {{-- Shimmering Skeleton State --}}
        <div wire:loading class="contents">
            @for($i = 0; $i < 8; $i++)
                <div class="relative overflow-hidden rounded-xl border border-slate-100 bg-white/60 p-4 shadow-sm backdrop-blur-md dark:border-white/5 dark:bg-slate-900/60 isolate">
                    {{-- Shimmer Effect --}}
                    <div class="absolute inset-0 -translate-x-full animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-transparent via-white/40 to-transparent dark:via-white/10 z-10 w-[200%]"></div>
                    
                    <div class="flex items-start justify-between mb-3 relative z-0">
                        <div class="h-10 w-10 shrink-0 rounded-xl bg-slate-200/80 dark:bg-slate-800/80"></div>
                        <div class="h-6 w-20 rounded-xl bg-slate-200/80 dark:bg-slate-800/80"></div>
                    </div>
                    <div class="mb-4 space-y-2 relative z-0">
                        <div class="h-4 w-3/4 rounded bg-slate-200/80 dark:bg-slate-800/80"></div>
                        <div class="h-3 w-1/2 rounded bg-slate-200/80 dark:bg-slate-800/80"></div>
                    </div>
                    <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between relative z-0">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-xl bg-slate-200/80 dark:bg-slate-800/80"></div>
                            <div class="space-y-1.5">
                                <div class="h-2 w-12 rounded bg-slate-200/80 dark:bg-slate-800/80"></div>
                                <div class="h-2 w-20 rounded bg-slate-200/80 dark:bg-slate-800/80"></div>
                            </div>
                        </div>
                        <div class="h-8 w-8 rounded-xl bg-slate-200/80 dark:bg-slate-800/80"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Real Verified Content --}}
        <div wire:loading.remove class="contents">
            @forelse($assets as $asset)
            <div class="group relative overflow-hidden rounded-xl border-2 border-white bg-white/60 p-4 shadow-[0_4px_12px_rgba(0,0,0,0.05)] backdrop-blur-2xl transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_20px_rgba(0,0,0,0.08)] hover:border-orange-400/50 dark:border-white/5 dark:bg-slate-900/60">
                {{-- Dynamic Accent Glow based on status --}}
                @php
                    $glowColor = match($asset->status) {
                        'available' => 'bg-emerald-400/10 group-hover:bg-emerald-400/20',
                        'assigned' => 'bg-indigo-400/10 group-hover:bg-indigo-400/20',
                        default => 'bg-rose-400/10 group-hover:bg-rose-400/20',
                    };
                @endphp
                <div class="absolute -right-8 -top-5 h-24 w-24 rounded-full blur-2xl transition-all {{ $glowColor }}"></div>
                
                <div class="relative flex items-start justify-between mb-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm border border-slate-100 transition-all font-black dark:bg-slate-800 dark:border-white/10 group-hover:scale-105">
                        @php
                            $iconColor = match($asset->status) {
                                'available' => 'text-emerald-500',
                                'assigned' => 'text-indigo-500',
                                default => 'text-rose-500',
                            };
                            $icon = match($asset->category) {
                                'laptop' => '<svg class="h-5 w-5 ' . $iconColor . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>',
                                'desktop' => '<svg class="h-5 w-5 ' . $iconColor . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>',
                                'mobile' => '<svg class="h-5 w-5 ' . $iconColor . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-6 18.75h12" /></svg>',
                                default => '<svg class="h-5 w-5 ' . $iconColor . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-5.25v9" /></svg>',
                            }
                        @endphp
                        {!! $icon !!}
                    </div>
                    <div class="flex flex-col items-end">
                        @if($asset->status === 'available')
                            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-[9px] font-black uppercase text-emerald-600 shadow-[0_2px_10px_rgba(16,185,129,0.1)] dark:bg-emerald-500/10 dark:text-emerald-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Inventory
                            </span>
                        @elseif($asset->status === 'assigned')
                            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 text-[9px] font-black uppercase text-indigo-600 shadow-[0_2px_10px_rgba(99,102,241,0.1)] dark:bg-indigo-500/10 dark:text-indigo-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span> Assigned
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-[9px] font-black uppercase text-rose-600 shadow-[0_2px_10px_rgba(244,63,94,0.1)] dark:bg-rose-500/10 dark:text-rose-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> {{ $asset->status }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="relative mb-3 min-h-[3rem]">
                    <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight leading-snug">{{ $asset->name }}</p>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">SN: <span class="text-slate-600 dark:text-slate-300">{{ $asset->serial_number ?: 'NOT SET' }}</span></p>
                </div>

                <div class="relative pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($asset->employee)
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white p-0.5 shadow-sm border border-slate-100 dark:border-white/10 dark:bg-slate-800">
                                @if($asset->employee->profile_photo)
                                    <img src="{{ Storage::url($asset->employee->profile_photo) }}" class="h-full w-full rounded-[10px] object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center rounded-[10px] bg-slate-50 text-[10px] font-black text-slate-400 dark:bg-white/5 uppercase">
                                        {{ substr($asset->employee->full_name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-[7px] font-black uppercase text-slate-400 tracking-widest leading-none mb-1">Custodian</p>
                                <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase leading-none">{{ $asset->employee->full_name }}</p>
                            </div>
                        @else
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-50 border border-slate-100 dark:border-white/5 dark:bg-white/5">
                                <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1" /></svg>
                            </div>
                            <div>
                                <p class="text-[7px] font-black uppercase text-slate-400 tracking-widest leading-none mb-1">Status Base</p>
                                <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase leading-none">Unassigned / Reserve</p>
                            </div>
                        @endif
                    </div>
                    
                    @if($isAdmin)
                        <button wire:click="openEditModal({{ $asset->id }})" class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-slate-400 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition-all shadow-sm dark:bg-white/5 dark:border-white/10 dark:hover:bg-orange-500/20 dark:hover:text-orange-400 dark:hover:border-orange-500/50">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="flex flex-col items-center">
                    <div class="h-16 w-16 rounded-[2rem] bg-slate-50 flex items-center justify-center mb-4 dark:bg-white/5 border border-slate-100 dark:border-white/10 shadow-sm">
                        <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-5.25v9" /></svg>
                    </div>
                    <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Zero Assets Found</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Try refining your search or initialize a new asset record.</p>
                </div>
            </div>
        @endforelse
        </div>
    </div>

    <div class="mt-8">
        {{ $assets->links() }}
    </div>

    {{-- Universal Glass Configuration Modal --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-4" x-data="{ appear: false }" x-init="setTimeout(() => appear = true, 10)">
            {{-- Backdrop --}}
            <div wire:click="$set('showEditModal', false)" 
                 class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity duration-300"
                 :class="appear ? 'opacity-100' : 'opacity-0'"></div>
            
            {{-- Modal Panel --}}
            <div class="relative w-full max-w-lg overflow-hidden rounded-[2rem] border-[1.5px] border-white bg-white/90 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] backdrop-blur-2xl transition-all duration-300 dark:border-white/10 dark:bg-slate-900/90"
                 :class="appear ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-8 scale-95'">
                
                {{-- Ambient Modal Header --}}
                <div class="relative overflow-hidden border-b border-slate-100 px-5 py-3.5 dark:border-white/5">
                    <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-orange-400/20 blur-3xl"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[8px] font-black uppercase tracking-[0.2em] text-orange-600 dark:text-orange-400">Asset Engine</span>
                                <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                <span class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Config</span>
                            </div>
                            <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $selectedAssetId ? 'Modify' : 'Initialize' }} <span class="text-orange-500">Asset</span></h2>
                        </div>
                        <button wire:click="$set('showEditModal', false)" class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition-all hover:bg-rose-50 hover:text-rose-500 dark:bg-white/5 dark:hover:bg-rose-500/20 dark:hover:text-rose-400 shrink-0">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="p-4 space-y-3.5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label class="text-[7px] font-black uppercase text-slate-500 tracking-widest pl-1">Hardware / Model Name</label>
                            <input wire:model="name" type="text" class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-1.5 text-[9px] font-black text-slate-900 shadow-sm transition-colors focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white uppercase" placeholder="e.g. MacBook Pro M3">
                             @error('name') <span class="text-[9px] font-bold text-rose-500 ml-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[7px] font-black uppercase text-slate-500 tracking-widest pl-1">Equipment Class</label>
                            <div class="relative">
                                <select wire:model="category" class="w-full appearance-none rounded-lg border border-slate-200 bg-white px-3.5 py-1.5 pr-8 text-[9px] font-black text-slate-900 shadow-sm transition-colors focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white uppercase">
                                    <option value="">-- Assign Class --</option>
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-5 text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                                </div>
                            </div>
                             @error('category') <span class="text-[9px] font-bold text-rose-500 ml-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label class="text-[7px] font-black uppercase text-slate-500 tracking-widest pl-1">Unique Identifier (SN)</label>
                            <input wire:model="serial_number" type="text" class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-1.5 text-[9px] font-black text-slate-900 shadow-sm transition-colors focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white uppercase" placeholder="e.g. C02YM...">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[7px] font-black uppercase text-slate-500 tracking-widest pl-1">Operational State</label>
                            <div class="relative">
                                <select wire:model="status" class="w-full appearance-none rounded-lg border border-slate-200 bg-white px-3.5 py-1.5 pr-8 text-[9px] font-black text-slate-900 shadow-sm transition-colors focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white uppercase">
                                    @foreach($statuses as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[7px] font-black uppercase text-slate-500 tracking-widest pl-1">Lattice Custodian Transfer</label>
                        <div class="relative">
                            <select wire:model="employee_id" class="w-full appearance-none rounded-lg border border-slate-200 bg-white px-3.5 py-1.5 pr-8 text-[9px] font-black text-slate-900 shadow-sm transition-colors focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white uppercase">
                                <option value="">No Assignment (Retain in Inventory)</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[7px] font-black uppercase text-slate-500 tracking-widest pl-1">Maintenance / Logs</label>
                        <textarea wire:model="notes" rows="2" class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-1.5 text-[9px] font-bold text-slate-900 shadow-sm transition-colors focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white leading-relaxed tracking-wide" placeholder="Record maintenance history, damage reports, or operational notes..."></textarea>
                    </div>
                </div>

                {{-- Action Engine --}}
                <div class="border-t border-slate-100 bg-slate-50/50 p-3.5 flex flex-col sm:flex-row justify-end gap-2.5 dark:border-white/5 dark:bg-white/5">
                    <button wire:click="$set('showEditModal', false)" class="rounded-lg border border-slate-200 bg-white px-5 py-1.5 text-[8px] font-black uppercase tracking-widest text-slate-600 shadow-sm hover:bg-slate-50 transition-all dark:border-white/10 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">Abort Mission</button>
                    <button wire:click="save" class="rounded-lg border-none bg-slate-900 px-5 py-1.5 text-[8px] font-black uppercase tracking-widest text-white shadow-md hover:bg-orange-600 active:scale-95 transition-all dark:bg-orange-600 dark:hover:bg-orange-500">
                        Commit Core Data
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
