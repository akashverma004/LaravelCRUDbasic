<div class="space-y-5 relative">
    {{-- Universal Notification --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/40 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl backdrop-blur-xl">
            <div class="bg-emerald-500 h-2 w-2 rounded-full animate-pulse"></div>
            {{ session('success') }}
        </div>
    @endif

    {{-- Profile Hero --}}
    <div class="relative overflow-hidden rounded-xl bg-white shadow-sm border border-slate-200 dark:border-white/5 dark:bg-slate-900/50">
        {{-- Cover Photo Section --}}
        <div class="relative h-28 w-full overflow-hidden bg-slate-100 dark:bg-white/5 group/cover">
            @if($employee->cover_photo)
                <img src="{{ Storage::url($employee->cover_photo) }}" alt="Cover" class="h-full w-full object-cover">
            @else
                <div class="h-full w-full bg-gradient-to-r from-slate-100 via-slate-50 to-slate-100 dark:from-slate-800 dark:via-slate-900 dark:to-slate-800 opacity-60"></div>
                <div class="absolute inset-0 flex items-center justify-center opacity-20">
                    <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                </div>
            @endif

            @if($this->isAdmin)
                <label class="absolute bottom-2.5 right-2.5 flex items-center gap-2 rounded-lg bg-slate-900/80 px-3 py-1 text-[8px] font-black uppercase tracking-widest text-white shadow-2xl backdrop-blur-md cursor-pointer opacity-0 group-hover/cover:opacity-100 transition-all hover:bg-slate-900">
                    <input type="file" wire:model="cover_photo" class="hidden">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                    <span>Update Vector</span>
                </label>
            @endif
        </div>

        <div class="relative z-10 flex flex-col items-center gap-4 lg:flex-row lg:items-center text-center lg:text-left px-5 py-4 -mt-8">
            {{-- Photo Container --}}
            <div class="relative group/photo">
                <div class="h-16 w-16 overflow-hidden rounded-xl border-2 border-white dark:border-white/5 shadow-xl transition-transform group-hover/photo:scale-105 bg-white dark:bg-slate-900">
                    @if($employee->profile_photo)
                        <img src="{{ Storage::url($employee->profile_photo) }}" alt="" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-slate-100 dark:bg-white/5">
                            <span class="text-xl font-black text-slate-300 dark:text-slate-600 uppercase">{{ substr($employee->full_name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded bg-white dark:bg-slate-900 shadow border border-slate-100 dark:border-white/10 ring-2 ring-white dark:ring-slate-950">
                    <div class="h-2 w-2 rounded-full {{ $employee->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></div>
                </div>
            </div>

            <div class="flex-1">
                <div class="flex flex-wrap items-center justify-center gap-2 lg:justify-start">
                    <h1 class="text-lg font-black tracking-tighter text-slate-900 dark:text-white uppercase transition-all">{{ $employee->full_name }}</h1>
                    <span class="rounded bg-slate-900 px-1.5 py-0.5 text-[7px] font-black uppercase tracking-widest text-white dark:bg-white/10 shadow-sm border border-white/5">ID: {{ $employee->id }}</span>
                </div>
                <p class="mt-0.5 text-[9px] font-bold text-slate-500 uppercase tracking-widest leading-none">
                    <span class="text-cyan-600 dark:text-cyan-400">{{ $employee->job_title ?? 'Employee' }}</span>
                    <span class="mx-1.5 text-slate-200 dark:text-white/5">/</span>
                    <span class="text-slate-700 dark:text-slate-300">{{ $employee->department?->name ?? 'Unassigned' }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('employees.index') }}" wire:navigate class="rounded-lg border border-slate-200 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-50 dark:border-white/5 dark:text-slate-400 dark:hover:bg-white/10 transition-all">Registry</a>
                
                @if($this->isAdmin)
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all dark:bg-white/5 dark:border-white/5">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" /></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-3 z-50 w-48 origin-top-right rounded-xl border border-slate-100 bg-white p-1 shadow-xl dark:border-white/10 dark:bg-slate-900" x-cloak>
                            <a href="{{ route('payroll.index') }}" wire:navigate class="block rounded-lg px-4 py-2 text-[9px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5 transition-colors">Payroll Interface</a>
                            <div class="my-1 border-t border-slate-50 dark:border-white/5"></div>
                            <button wire:click="purgeRecord" wire:confirm="Permanently delete this identity?" class="w-full text-left rounded-lg px-4 py-2 text-[9px] font-black uppercase tracking-widest text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10">Purge Record</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Metrics Card --}}
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 ml-0.5">Summary Metrics</h3>
                <div class="space-y-3">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest opacity-80">Line Manager</span>
                        <span class="text-[11px] font-black text-slate-900 dark:text-white uppercase truncate">{{ $employee->manager?->full_name ?? 'Unaligned' }}</span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest opacity-80">Contract State</span>
                        <span class="text-[11px] font-black text-slate-900 dark:text-white uppercase truncate capitalize">{{ str_replace('-', ' ', $employee->employment_type) }}</span>
                    </div>
                    @if($this->isAdmin)
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest opacity-80">Salary Window</span>
                        <span class="text-[11px] font-black text-slate-900 dark:text-white uppercase truncate">{{ $employee->salary ? '₹' . number_format($employee->salary) : 'Confidential' }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Counters --}}
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 ml-0.5">Activity Vectors</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div class="rounded-lg border border-slate-100 bg-slate-50 px-2 py-2.5 text-center dark:border-white/5 dark:bg-white/5">
                        <p class="text-xl font-black text-slate-900 dark:text-white leading-none">{{ collect($employee->leaveRequests)->count() }}</p>
                        <p class="mt-1 text-[7px] font-black uppercase tracking-widest text-slate-400">Leaves</p>
                    </div>
                    <div class="rounded-lg border border-slate-100 bg-slate-50 px-2 py-2.5 text-center dark:border-white/5 dark:bg-white/5">
                        <p class="text-xl font-black text-slate-900 dark:text-white leading-none">{{ collect($employee->attendanceRecords)->count() }}</p>
                        <p class="mt-1 text-[7px] font-black uppercase tracking-widest text-slate-400">Punches</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Interaction Tabs --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Unified Tabs --}}
            <div class="flex gap-1.2 overflow-x-auto rounded-xl border border-slate-200 bg-white p-1.5 shadow-sm dark:border-slate-800 dark:bg-slate-900/50 hide-scrollbar" wire:ignore.self>
                @foreach([
                    'work' => 'Work',
                    'personal' => 'Personal',
                    'emergency' => 'Emerg.',
                    'identity' => 'Identity',
                    'bank' => 'Bank',
                    'preferences' => 'Style',
                    'education' => 'Edu',
                    'experience' => 'Exp'
                ] as $tabId => $tabLabel)
                <button
                    wire:click="$set('activeTab', '{{ $tabId }}')"
                    class="whitespace-nowrap rounded-lg px-2.5 py-1.5 text-[8px] font-black uppercase tracking-widest transition-all {{ $activeTab === $tabId ? 'bg-slate-900 text-white dark:bg-white/10 dark:text-white' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/50' }}"
                >{{ $tabLabel }}</button>
                @endforeach
            </div>

            {{-- Content Module --}}
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50 min-h-[300px]">
                @php
                    $inputClass  = 'w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white transition-all disabled:opacity-50 disabled:bg-slate-100 dark:disabled:bg-white/5 shadow-inner';
                    $labelClass  = 'block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-0.5 opacity-80';
                @endphp

                <form wire:submit="submitForm">
                    @php 
                    $isEditing = $editingSection === $activeTab;
                    $canEdit = $this->isAdmin || $this->isSelf;
                    @endphp

                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5 dark:border-white/5">
                        <h2 class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-[0.2em]">{{ $activeTab }} Module</h2>
                        @if($canEdit && !in_array($activeTab, ['education', 'experience']))
                            @if($isEditing)
                                <div class="flex items-center gap-1.5">
                                    <button type="button" wire:click="cancelEditing" class="rounded px-2.5 py-1.5 text-[8px] font-black uppercase text-slate-500 hover:bg-slate-50 transition-all dark:hover:bg-white/5">Abort</button>
                                    <button type="submit" class="rounded bg-slate-900 px-3 py-1.5 text-[8px] font-black uppercase text-white hover:bg-cyan-600 transition-all dark:bg-white/10" wire:loading.attr="disabled">Save Sync</button>
                                </div>
                            @else
                                <button type="button" wire:click="startEditing('{{ $activeTab }}')" class="rounded-lg border border-slate-200 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-50 hover:text-cyan-600 dark:border-white/5 transition-all">Modify Data</button>
                            @endif
                        @endif
                    </div>

                    {{-- TAB rendering (keeping logic same, just updated classes in the loop) --}}
                    {{-- TAB: Work Info --}}
                    @if($activeTab === 'work')
                    <div class="grid gap-3.5 sm:grid-cols-2">
                        <div>
                            <label class="{{ $labelClass }}">Full Identity</label>
                            <input type="text" wire:model="form.full_name" class="{{ $inputClass }}" @disabled(!$isEditing || !$this->isAdmin)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Job Designation</label>
                            <input type="text" wire:model="form.job_title" class="{{ $inputClass }}" @disabled(!$isEditing || !$this->isAdmin)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Org Unit</label>
                            <select wire:model="form.department_id" class="{{ $inputClass }}" @disabled(!$isEditing || !$this->isAdmin)>
                                @foreach($this->departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Line Manager</label>
                            <select wire:model="form.manager_id" class="{{ $inputClass }}" @disabled(!$isEditing || !$this->isAdmin)>
                                <option value="">None Assigned</option>
                                @foreach($this->managers as $mgr)
                                    <option value="{{ $mgr->id }}">{{ $mgr->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    @if($activeTab === 'personal')
                    <div class="grid gap-3.5 sm:grid-cols-2">
                         <div><label class="{{ $labelClass }}">Access Email</label><input type="email" wire:model="form.email" class="{{ $inputClass }}" disabled></div>
                         <div><label class="{{ $labelClass }}">Personal Vector</label><input type="email" wire:model="form.personal_email" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                         <div><label class="{{ $labelClass }}">Mobile Path</label><input type="text" wire:model="form.phone" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                         <div><label class="{{ $labelClass }}">Birth Sequence</label><input type="date" wire:model="form.date_of_birth" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                    </div>
                    @endif

                    @if($activeTab === 'emergency')
                    <div class="grid gap-3.5">
                         <div><label class="{{ $labelClass }}">Rescue Contact Name</label><input type="text" wire:model="form.emergency_contact_name" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                         <div class="grid grid-cols-2 gap-3.5">
                             <div><label class="{{ $labelClass }}">Bond Type</label><input type="text" wire:model="form.emergency_contact_relationship" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                             <div><label class="{{ $labelClass }}">Quick Dial</label><input type="text" wire:model="form.emergency_contact_phone" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                         </div>
                    </div>
                    @endif

                    @if($activeTab === 'identity')
                    <div class="grid gap-3.5 sm:grid-cols-2">
                         <div><label class="{{ $labelClass }}">PAN Unique Identifier</label><input type="text" wire:model="form.pan_number" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                         <div><label class="{{ $labelClass }}">Aadhaar Metric</label><input type="text" wire:model="form.aadhaar_number" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                         <div><label class="{{ $labelClass }}">Passport Signature</label><input type="text" wire:model="form.passport_number" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                         <div><label class="{{ $labelClass }}">Origin</label><input type="text" wire:model="form.nationality" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                    </div>
                    @endif

                    @if($activeTab === 'bank')
                    <div class="grid gap-3.5 sm:grid-cols-2">
                         <div class="sm:col-span-2"><label class="{{ $labelClass }}">Vault Name</label><input type="text" wire:model="form.bank_name" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                         <div><label class="{{ $labelClass }}">Account Mapping</label><input type="text" wire:model="form.bank_account_number" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                         <div><label class="{{ $labelClass }}">IFSC Routing</label><input type="text" wire:model="form.bank_ifsc" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                    </div>
                    @endif

                    @if($activeTab === 'preferences')
                    <div class="grid gap-3.5 sm:grid-cols-2">
                         <div><label class="{{ $labelClass }}">Lattice Hobbies</label><input type="text" wire:model="form.hobbies" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                         <div><label class="{{ $labelClass }}">Consumption Style</label><select wire:model="form.food_preference" class="{{ $inputClass }}" @disabled(!$isEditing)><option value="">Select</option><option value="veg">Veg</option><option value="non-veg">Non-Veg</option></select></div>
                         <div class="sm:col-span-2"><label class="{{ $labelClass }}">LinkedIn Identity Path</label><input type="url" wire:model="form.linkedin_url" class="{{ $inputClass }}" @disabled(!$isEditing)></div>
                    </div>
                    @endif

                    @if($activeTab === 'education')
                    <div class="space-y-3">
                         @forelse($employee->educations as $edu)
                             <div class="rounded-lg border border-slate-100 bg-slate-50 p-3 dark:border-white/5 dark:bg-white/5">
                                 <div class="flex justify-between items-start">
                                     <div>
                                         <h4 class="text-[11px] font-black uppercase text-slate-900 dark:text-white transition-all">{{ $edu->degree }}</h4>
                                         <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $edu->institution }}</p>
                                     </div>
                                     <span class="text-[8px] font-black text-slate-400">{{ $edu->year_from }} — {{ $edu->year_to ?: 'Now' }}</span>
                                 </div>
                             </div>
                         @empty
                             <p class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.2em] text-center py-6">No edu records.</p>
                         @endforelse
                    </div>
                    @endif

                    @if($activeTab === 'experience')
                    <div class="space-y-3">
                         @forelse($employee->experiences as $exp)
                             <div class="rounded-lg border border-slate-100 bg-slate-50 p-3 dark:border-white/5 dark:bg-white/5">
                                  <div class="flex justify-between items-start">
                                     <div>
                                         <h4 class="text-[11px] font-black uppercase text-slate-900 dark:text-white transition-all">{{ $exp->designation }}</h4>
                                         <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $exp->company }}</p>
                                     </div>
                                     <span class="text-[8px] font-black text-slate-400">{{ \Carbon\Carbon::parse($exp->from_date)->format('Y') }} — {{ $exp->to_date ? \Carbon\Carbon::parse($exp->to_date)->format('Y') : 'Now' }}</span>
                                 </div>
                             </div>
                         @empty
                             <p class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.2em] text-center py-6">No exp records.</p>
                         @endforelse
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>
