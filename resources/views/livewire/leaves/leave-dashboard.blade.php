<div class="relative space-y-6 pb-6 mt-1">
    {{-- High-Impact Glass Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white/80 px-6 py-5 shadow-sm border border-slate-200 backdrop-blur-xl dark:bg-slate-900/60 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-cyan-500/5 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center text-center lg:text-left">
            <div>
                <div class="flex items-center justify-center lg:justify-start gap-2 mb-0.5">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Self Service</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Time Off</span>
                </div>
                <h1 class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all">
                    {{ $employee ? 'Hi, ' . explode(' ', $employee->full_name)[0] : 'Time Off' }}
                </h1>
                <p class="mt-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest opacity-80 leading-none">
                    Available balance: <span class="text-cyan-600 dark:text-cyan-400 font-black">{{ collect($balances)->sum('remaining') }}</span> days across all categories.
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-2.5">
                <a href="{{ route('leaves.index') }}" wire:navigate class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 text-[9px] font-black uppercase tracking-widest text-slate-600 shadow-sm transition-all hover:bg-slate-50 dark:border-white/5 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    <span>Calendar Mapping</span>
                </a>
                <button wire:click="openCreateModal" class="inline-flex h-10 items-center gap-2 rounded-lg bg-slate-900 px-5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/10 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Book Time Off</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $stats_config = [
                ['Days Left', collect($balances)->sum('remaining'), 'Available balance', 'cyan'],
                ['Approved', $stats['approved'], 'Requests approved', 'emerald'],
                ['Pending', $stats['pending'], 'Awaiting review', 'amber'],
                ['Team Away', $whoIsAwayUpcoming->count(), 'Next 7 days', 'indigo']
            ];
        @endphp
        @foreach($stats_config as [$title, $val, $desc, $color])
        <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition-all hover:shadow-md dark:border-white/5 dark:bg-slate-900/50">
            <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2.5">{{ $title }}</h3>
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-2xl font-black leading-none tracking-tight text-slate-900 dark:text-white">{{ $val }}</p>
                    <p class="mt-1 text-[9px] font-bold text-slate-500 uppercase tracking-widest opacity-80">{{ $desc }}</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-100 dark:border-white/5
                    @if($color === 'cyan') bg-cyan-50 text-cyan-500 dark:bg-cyan-500/10
                    @elseif($color === 'emerald') bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10
                    @elseif($color === 'amber') bg-amber-50 text-amber-600 dark:bg-amber-600/10
                    @else bg-indigo-50 text-indigo-500 dark:bg-indigo-500/10 @endif">
                    @if($color === 'cyan') <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($color === 'emerald') <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($color === 'amber') <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg> @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Left Column: Balances & History --}}
        <div class="space-y-6 lg:col-span-8">


            {{-- History Section --}}
            <section>
                <div class="flex items-center justify-between mb-4 px-1">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 flex items-center gap-2">
                        Activity Stream
                        <span class="h-px w-8 bg-slate-200 dark:bg-white/5"></span>
                    </h3>
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900 overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 dark:bg-white/5 border-b border-slate-100 dark:border-white/5">
                            <tr>
                                <th class="px-6 py-3 text-[9px] font-black uppercase tracking-widest text-slate-400">Metric Type</th>
                                <th class="px-6 py-3 text-[9px] font-black uppercase tracking-widest text-slate-400">Window</th>
                                <th class="px-6 py-3 text-[9px] font-black uppercase tracking-widest text-slate-400">Duration</th>
                                <th class="px-6 py-3 text-right text-[9px] font-black uppercase tracking-widest text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            @forelse($leaves as $leave)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 flex items-center justify-center rounded-xl text-[10px] font-black border border-slate-100 dark:border-white/5 shadow-inner
                                            @if($leave->leave_type === 'annual') bg-rose-50 text-rose-500 dark:bg-rose-500/10
                                            @elseif($leave->leave_type === 'sick') bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10
                                            @elseif($leave->leave_type === 'casual') bg-violet-50 text-violet-500 dark:bg-violet-500/10
                                            @else bg-slate-50 text-slate-500 dark:bg-slate-500/10 @endif">
                                            <span>{{ strtoupper(substr($leave->leave_type, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight leading-none mb-1">{{ $leave->leave_type }}</p>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest opacity-80">Category Vector</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-tight">
                                        {{ $leave->start_date?->format('d M') }} - {{ $leave->end_date?->format('d M Y') }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest bg-slate-100 dark:bg-white/5 px-2 py-1 rounded-lg border border-slate-200/50 dark:border-white/5">
                                        {{ $leave->start_date && $leave->end_date ? $leave->start_date->diffInDays($leave->end_date) + 1 : 0 }} Units
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[8px] font-black uppercase tracking-widest border
                                            @if($leave->status === 'approved') bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20
                                            @elseif($leave->status === 'rejected') bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20
                                            @else bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:border-amber-500/20 @endif">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            <span>{{ $leave->status }}</span>
                                        </span>
                                        
                                        @if($leave->status === 'pending')
                                        <div class="flex items-center gap-1.5 ml-2 opacity-0 group-hover:opacity-100 transition-all">
                                            <button wire:click="editLeave({{ $leave->id }})" class="p-2 rounded-lg text-slate-400 hover:text-cyan-500 hover:bg-cyan-50 dark:hover:bg-cyan-500/10 transition-all">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            <button wire:click="deleteLeave({{ $leave->id }})" wire:confirm="Are you sure?" class="p-2 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-all">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">No historical signals detected.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- Right Column: Side Info --}}
        <div class="space-y-6 lg:col-span-4">
            {{-- Away Today --}}
            <section>
                <div class="flex items-center justify-between mb-4 px-1">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Team Absence Index</h3>
                    <span class="rounded-lg bg-slate-100 px-2 py-0.5 text-[9px] font-black text-slate-500 dark:bg-white/5 border border-slate-200/50 dark:border-white/10">{{ $whoIsAwayToday->count() }}</span>
                </div>
                <div class="grid gap-3">
                    @forelse($whoIsAwayToday as $l)
                    <div class="group flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-all hover:border-cyan-500/20 dark:border-white/5 dark:bg-slate-900/50">
                        <div class="relative">
                            @if($l->employee->profile_photo)
                                <img src="{{ $l->employee->profile_photo }}" class="h-10 w-10 rounded-xl object-cover ring-2 ring-slate-100 dark:ring-white/5">
                            @else
                                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-100 text-[10px] font-black text-slate-500 dark:bg-white/5 dark:text-cyan-400 border border-slate-200/50 dark:border-white/5">{{ substr($l->employee->full_name, 0, 1) }}</div>
                            @endif
                            <div class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full border-2 border-white bg-rose-500 dark:border-slate-950 shadow-sm animate-pulse"></div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $l->employee->full_name }}</p>
                            <p class="truncate text-[9px] font-bold text-slate-400 uppercase tracking-widest opacity-80">{{ $l->leave_type }} Category</p>
                        </div>
                    </div>
                    @empty
                    <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center dark:border-white/5">
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Personnel presence at 100%.</p>
                    </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    {{-- Standardized Livewire Modal --}}
    @if($showLeaveModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" wire:click="closeLeaveModal"></div>
        
        <div class="relative w-full max-w-sm rounded-xl bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                <h3 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.2em]">
                    {{ $isEditing ? 'Edit Request' : 'Book Time Off' }}
                </h3>
                <button wire:click="closeLeaveModal" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="px-6 py-6 space-y-6">
                {{-- Allowances List --}}
                <div class="space-y-3">
                    <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Institutional Credits</h4>
                    <div class="grid grid-cols-2 gap-2.5">
                        @foreach($balances as $type => $bal)
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 dark:bg-white/5 border border-slate-100 dark:border-white/5 transition-all hover:bg-slate-100 dark:hover:bg-white/10">
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full 
                                    @if($type === 'annual') bg-rose-500
                                    @elseif($type === 'sick') bg-emerald-500
                                    @elseif($type === 'casual') bg-violet-500
                                    @else bg-slate-400 @endif shadow-sm"></div>
                                <span class="text-[9px] font-black text-slate-600 dark:text-slate-400 uppercase tracking-tight">{{ $type }}</span>
                            </div>
                            <span class="text-[10px] font-black text-slate-900 dark:text-white">{{ $bal['remaining'] }} Units</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="h-px bg-slate-100 dark:bg-white/5"></div>

                @if($isAdmin)
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-0.5">Primary Target</label>
                    <select wire:model="employee_id" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/10 dark:text-white transition-all">
                        <option value="">Select Target Identity</option>
                        @foreach($allEmployees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                    @error('employee_id') <span class="text-[8px] font-black text-rose-500 uppercase ml-0.5">{{ $message }}</span> @enderror
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-0.5">Metric Type</label>
                        <select wire:model="leave_type" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/10 dark:text-white transition-all">
                            <option value="annual">Annual</option>
                            <option value="sick">Sick</option>
                            <option value="casual">Casual</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                        @error('leave_type') <span class="text-[8px] font-black text-rose-500 uppercase ml-0.5">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-0.5">Window Size</label>
                        <select wire:model="leave_session" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/10 dark:text-white transition-all">
                            <option value="full_day">Full Units</option>
                            <option value="morning">Morning Vector</option>
                            <option value="evening">Evening Vector</option>
                        </select>
                        @error('leave_session') <span class="text-[8px] font-black text-rose-500 uppercase ml-0.5">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-0.5">Start Window</label>
                        <input type="date" wire:model="start_date" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/10 dark:text-white transition-all">
                        @error('start_date') <span class="text-[8px] font-black text-rose-500 uppercase ml-0.5">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-0.5">End Window</label>
                        <input type="date" wire:model="end_date" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/10 dark:text-white transition-all">
                        @error('end_date') <span class="text-[8px] font-black text-rose-500 uppercase ml-0.5">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-0.5">Reason Architecture</label>
                    <textarea wire:model="reason" rows="3" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[10px] font-medium text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/10 dark:text-white transition-all" placeholder="Optional contextual metadata..."></textarea>
                </div>
            </div>

            {{-- Gray Action Bar --}}
            <div class="flex items-center justify-end gap-3 bg-slate-50 px-6 py-4 dark:bg-white/5 border-t border-slate-100 dark:border-white/5">
                <button wire:click="closeLeaveModal" class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-600 transition-all">
                    Abort
                </button>
                <button wire:click="saveLeaveRequest" class="flex items-center gap-2 rounded-lg bg-slate-900 px-6 py-2.5 text-[9px] font-black uppercase tracking-[0.2em] text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 disabled:opacity-50 dark:bg-white/10 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <span wire:loading.remove wire:target="saveLeaveRequest">{{ $isEditing ? 'Archiving Changes' : 'Launch Request' }}</span>
                    <span wire:loading wire:target="saveLeaveRequest" class="flex items-center gap-2">
                        <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Deploying...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Standardized Notify Toast --}}
    <div 
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-4 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-4 opacity-0 scale-95"
        class="fixed bottom-8 right-8 z-[110] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl backdrop-blur-xl"
        style="display: none;"
    >
        <div :class="type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="h-2 w-2 rounded-full animate-pulse"></div>
        <span x-text="message"></span>
    </div>
</div>
