<div class="relative space-y-6 pb-12">

    {{-- Standardized Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Organization</span>
                <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                <a href="{{ route('departments.index') }}" wire:navigate class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-cyan-500 transition-colors">Departments</a>
                <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">{{ $department->name }}</span>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase truncate max-w-xl">
                Department <span class="text-cyan-500">{{ $department->name }}</span>
            </h1>
            <p class="mt-1 text-[11px] font-medium text-slate-400 uppercase tracking-wide leading-relaxed">
                Unit Identifier: <span class="font-black text-slate-600 dark:text-slate-300">{{ $department->code }}</span>
            </p>
        </div>
        
        @if ($this->canManage)
            <div class="flex items-center gap-2">
                <button wire:click="openEditModal" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                    <span>Edit Unit</span>
                </button>
                
                <button wire:click="deleteDepartment" wire:confirm="Archive this department? Operation cannot be undone." class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-rose-500 shadow-sm hover:border-rose-100 dark:bg-white/5 dark:border-white/5 dark:text-rose-400 dark:hover:bg-rose-500/10 transition-all active:scale-95">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                    <span>Archive</span>
                </button>
            </div>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-4">
        {{-- Left Analysis Col --}}
        <div class="space-y-4 lg:col-span-1">
            <div class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-cyan-500/30 dark:border-white/5 dark:bg-slate-900/50">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Unit Leadership</p>
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 text-cyan-500 dark:bg-white/5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-[11px] font-black text-slate-400 ring-2 ring-slate-100 dark:bg-white/5 dark:ring-white/5">
                        {{ substr($department->lead_name ?? '?', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $department->lead_name ?? 'Vacant' }}</p>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Department Head</p>
                    </div>
                </div>
            </div>

            <div class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-cyan-500/30 dark:border-white/5 dark:bg-slate-900/50">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Total Headcount</p>
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 text-emerald-500 dark:bg-white/5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900 dark:text-white">{{ collect($department->employees)->count() }}</span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Collaborators</span>
                </div>
            </div>
        </div>

        {{-- Right Roster Col --}}
        <div class="lg:col-span-3">
            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-white/5">
                    <h2 class="text-[10px] font-black uppercase tracking-widest text-slate-500">Resource Roster</h2>
                    <span class="text-[9px] font-black bg-slate-50 dark:bg-white/5 px-2 py-1 rounded-lg text-slate-400 uppercase tracking-widest">{{ collect($department->employees)->count() }} Records</span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-white/5 max-h-[600px] overflow-y-auto" style="scrollbar-width:thin">
                    @forelse ($department->employees as $employee)
                        <div wire:key="employee-{{ $employee->id }}" class="group flex items-center justify-between p-4 hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="relative h-10 w-10 flex-shrink-0">
                                    <div class="h-full w-full overflow-hidden rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 shadow-sm">
                                        @if($employee->profile_photo)
                                            <img src="{{ Storage::url($employee->profile_photo) }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-xs font-black text-slate-400 uppercase">
                                                {{ substr($employee->full_name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 h-3.5 w-3.5 rounded-full border-2 border-white dark:border-slate-900 {{ $employee->status === 'active' ? 'bg-emerald-500' : 'bg-amber-500' }}"></div>
                                </div>
                                <div>
                                    <a href="{{ route('employees.show', $employee->id) }}" wire:navigate class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight group-hover:text-cyan-500 transition-colors block">{{ $employee->full_name }}</a>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5">{{ $employee->job_title }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-8">
                                <div class="hidden xl:block">
                                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-0.5 text-right">Identifier</p>
                                    <p class="text-[10px] font-black text-slate-600 dark:text-slate-400 uppercase text-right">{{ $employee->employee_id ?? '---' }}</p>
                                </div>
                                <div class="w-20 text-right">
                                    <span class="inline-flex items-center rounded-lg px-2 py-1 text-[8px] font-black uppercase tracking-widest 
                                        {{ $employee->status === 'active' ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10' : 'text-amber-600 bg-amber-50 dark:bg-amber-500/10' }}">
                                        {{ $employee->status }}
                                    </span>
                                </div>
                                <a href="{{ route('employees.show', $employee->id) }}" wire:navigate class="p-2 text-slate-300 hover:text-cyan-500 transition-colors opacity-0 group-hover:opacity-100">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-20 bg-slate-50/30 dark:bg-transparent">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-sm dark:bg-white/5 mb-4">
                                <svg class="h-6 w-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Deployment Empty</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Panel --}}
    @if($isEditing)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="w-full max-w-sm rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-white/5">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white">Modify Unit</h3>
                <button wire:click="closeEditModal" class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form wire:submit="submitEdit">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Department Name</label>
                        <input type="text" wire:model="form.name" class="w-full rounded-xl border border-slate-200 bg-transparent px-3.5 py-2.5 text-xs font-black uppercase-input focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-white/10 dark:text-white">
                        @error('form.name') <span class="text-[9px] font-black text-rose-500 uppercase">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Unit Code</label>
                        <input type="text" wire:model="form.code" class="w-full rounded-xl border border-slate-200 bg-transparent px-3.5 py-2.5 text-xs font-black uppercase uppercase-input focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-white/10 dark:text-white">
                        @error('form.code') <span class="text-[9px] font-black text-rose-500 uppercase">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Lead Personnel</label>
                        <select wire:model="form.lead_employee_id" class="w-full rounded-xl border border-slate-200 bg-transparent px-3.5 py-2.5 text-xs font-black focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-white/10 dark:text-white dark:bg-slate-900">
                            <option value="" class="dark:bg-slate-900 text-slate-400">--- No Lead ---</option>
                            @foreach ($this->employeesList as $emp)
                                <option value="{{ $emp->id }}" class="dark:bg-slate-900">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                        @error('form.lead_employee_id') <span class="text-[9px] font-black text-rose-500 uppercase">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 bg-slate-50/50 px-5 py-4 dark:bg-white/[0.02]">
                    <button type="button" wire:click="closeEditModal" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Abort</button>
                    <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 disabled:opacity-50 transition-all dark:bg-white/10 dark:hover:bg-cyan-500" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submitEdit">Update Unit</span>
                        <span wire:loading wire:target="submitEdit">Recalculating...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
    
    {{-- Error Flash Notification --}}
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-xs font-bold text-white shadow-2xl backdrop-blur-xl dark:bg-slate-800/90">
            <div class="bg-rose-500 h-2 w-2 rounded-full animate-pulse"></div>
            {{ session('error') }}
        </div>
    @endif
    
    {{-- Success Notification --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-xs font-bold text-white shadow-2xl backdrop-blur-xl dark:bg-slate-800/90">
            <div class="bg-emerald-500 h-2 w-2 rounded-full animate-pulse"></div>
            {{ session('success') }}
        </div>
    @endif
</div>
