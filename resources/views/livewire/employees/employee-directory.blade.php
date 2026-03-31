<div class="space-y-5 relative">
    {{-- Glass Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white/80 px-6 py-5 shadow-sm border border-slate-200 backdrop-blur-xl dark:bg-slate-900/60 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-cyan-500/5 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center text-center lg:text-left">
            <div>
                <div class="flex items-center justify-center lg:justify-start gap-2 mb-0.5">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Personnel</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Registry</span>
                </div>
                <h1 class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all">
                    People <span class="text-cyan-500">Hub</span>
                </h1>
                <p class="mt-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest opacity-80 leading-none">
                    Browse and manage active organizational identity records.
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-2.5">
                @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                    <button wire:click="$set('showCreateModal', true)" class="inline-flex h-10 items-center gap-2 rounded-lg bg-slate-900 px-5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/10 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Add Person</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Compact Filters --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <label class="block text-[8px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1.5 ml-0.5">Registry Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input wire:model.live.debounce.250ms="search" type="text" placeholder="Identity Label, Email, or Sector..." class="w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 py-2 text-[11px] font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white transition-all">
                </div>
            </div>
            <div>
                <label class="block text-[8px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1.5 ml-0.5">Deployment Sector</label>
                <select wire:model.live="department_id" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-cyan-500 transition-all dark:border-white/5 dark:bg-white/5 dark:text-white appearance-none">
                    <option value="">Global Spectrum</option>
                    @foreach($this->departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[8px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1.5 ml-0.5">Access Rank</label>
                <select wire:model.live="role_id" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-cyan-500 transition-all dark:border-white/5 dark:bg-white/5 dark:text-white appearance-none">
                    <option value="">All Tiers</option>
                    @foreach($this->roles as $role)
                        <option value="{{ $role->id }}">{{ $role->display_name ?? ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Employee Stream --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900/50 overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-5 py-2 dark:border-white/5 dark:bg-white/5">
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $this->employees->total() }} Active Identity Records</p>
            <div wire:loading class="text-cyan-500">
                <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
            </div>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-white/5" wire:loading.class="opacity-50 pointer-events-none transition-opacity">
            @forelse($this->employees as $employee)
                <div wire:key="employee-{{ $employee->id }}" class="group flex items-center justify-between px-5 py-3 hover:bg-slate-50 dark:hover:bg-white/[0.01] transition-all">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            @if($employee->profile_photo)
                                <img src="{{ Storage::url($employee->profile_photo) }}" alt="" class="h-9 w-9 rounded-lg object-cover ring-2 ring-slate-100 dark:ring-white/5">
                            @else
                                <div class="h-9 w-9 flex items-center justify-center rounded-lg bg-slate-100 text-[10px] font-black text-slate-400 dark:bg-white/5 ring-2 ring-slate-100 dark:ring-white/5">
                                    <span>{{ substr($employee->full_name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white dark:border-slate-900 {{ $employee->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></div>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-[11px] font-black uppercase tracking-tight text-slate-900 dark:text-white leading-none">
                                    <a href="{{ route('employees.show', $employee->id) }}" wire:navigate class="hover:text-cyan-500 transition-colors">{{ $employee->full_name }}</a>
                                </h3>
                                @if($employee->trashed())
                                    <span class="rounded bg-amber-50 px-1.5 py-0.5 text-[7px] font-black uppercase tracking-widest text-amber-600 dark:bg-amber-500/10">Archived</span>
                                @endif
                            </div>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="text-[8px] font-black uppercase tracking-widest text-slate-400 opacity-80">{{ $employee->job_title ?? 'Employee' }}</span>
                                <span class="h-0.5 w-0.5 rounded-full bg-slate-200 dark:bg-white/10"></span>
                                <span class="text-[8px] font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400 opacity-80">{{ $employee->department->name ?? 'Unaligned' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                        <a href="{{ route('employees.show', $employee->id) }}" wire:navigate class="rounded-lg border border-slate-200 px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-50 dark:border-white/5 dark:text-slate-400 dark:hover:bg-white/10 transition-all">
                            Details
                        </a>
                        @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                            <button wire:click="{{ $employee->trashed() ? 'restoreEmployee('.$employee->id.')' : 'deleteEmployee('.$employee->id.')' }}" class="text-[8px] font-black uppercase tracking-widest {{ $employee->trashed() ? 'text-emerald-500' : 'text-rose-400 hover:text-rose-600' }} transition-colors">
                                {{ $employee->trashed() ? 'Restore' : 'Purge' }}
                            </button>
                        @endif
                        <svg class="h-3.5 w-3.5 text-slate-300 dark:text-white/10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">No identity signatures detected.</p>
                </div>
            @endforelse
        </div>
        
        @if($this->employees->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/50 p-3 dark:border-white/5 dark:bg-white/5">
                {{ $this->employees->links('hrms.components.pagination') }}
            </div>
        @endif
    </div>
</div>
