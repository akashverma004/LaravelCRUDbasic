<div class="mb-8">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-white uppercase"><span class="text-cyan-500">Add</span> Employee</h1>
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-1">Initiating a new personnel registry natively with Livewire 3.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.index') }}" wire:navigate class="rounded-xl px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white transition-all">
                Cancel
            </a>
            <button wire:click="save" class="flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400 disabled:opacity-50" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">Deploy Registry</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="h-3.5 w-3.5 animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Saving...
                </span>
            </button>
        </div>
    </div>

    @php
        $input  = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white transition-all';
        $label  = 'block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1';
    @endphp

    <div class="rounded-2xl bg-white shadow-sm dark:bg-slate-900/50 border border-slate-200 dark:border-white/5 overflow-hidden">
        <form wire:submit="save" id="employee-form" novalidate>
            <div class="space-y-12 p-8 sm:p-10 divide-y divide-slate-100 dark:divide-white/5">
                
                {{-- ── Work Details ────────────────────────────────────────── --}}
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">Employment Credentials</h2>
                        <p class="mt-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">Core identity and logistical data for the new personnel.</p>
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="{{ $label }}">Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="full_name" class="{{ $input }} @error('full_name') border-rose-500 @enderror" placeholder="e.g. Jane Smith">
                            @error('full_name') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" wire:model="email" class="{{ $input }} @error('email') border-red-400 @enderror" placeholder="jane@company.com">
                            @error('email') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Temporary Password <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="password" class="{{ $input }} @error('password') border-red-400 @enderror" placeholder="Secure password">
                            @error('password') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Phone <span class="text-red-500">*</span></label>
                            <input type="tel" wire:model="phone" class="{{ $input }} @error('phone') border-red-400 @enderror" placeholder="+91 98765 43210">
                            @error('phone') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Job Title <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="job_title" class="{{ $input }} @error('job_title') border-red-400 @enderror" placeholder="Product Designer">
                            @error('job_title') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Department <span class="text-red-500">*</span></label>
                            @if($this->departments->isEmpty())
                                <div class="mt-1 rounded-lg border border-amber-300 bg-amber-50 dark:bg-amber-950/50 dark:border-amber-700 px-3 py-2 text-sm text-amber-700 dark:text-amber-300">
                                    No departments. <a href="{{ route('departments.index') }}" class="font-semibold underline">Manage Departments.</a>
                                </div>
                            @else
                                <select wire:model="department_id" class="{{ $input }} @error('department_id') border-red-400 @enderror">
                                    <option value="">Select Department</option>
                                    @foreach ($this->departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('department_id') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Direct Manager</label>
                            <select wire:model="manager_id" class="{{ $input }}">
                                <option value="">No Manager</option>
                                @foreach ($this->managers as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->full_name }}{{ $manager->department ? ' ('.$manager->department->name.')' : '' }}</option>
                                @endforeach
                            </select>
                            @error('manager_id') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">System Role</label>
                            <select wire:model="role_id" class="{{ $input }}">
                                <option value="">Select Role</option>
                                @foreach ($this->roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name ?? ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Employment Type <span class="text-red-500">*</span></label>
                            <select wire:model="employment_type" class="{{ $input }} @error('employment_type') border-red-400 @enderror">
                                <option value="">Select Type</option>
                                <option value="full-time">Full-time</option>
                                <option value="part-time">Part-time</option>
                                <option value="contract">Contract</option>
                                <option value="intern">Intern</option>
                            </select>
                            @error('employment_type') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Annual Salary <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-xs font-black pointer-events-none">₹</span>
                                <input type="number" step="0.01" min="0" wire:model="salary" class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-8 pr-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white transition-all @error('salary') border-rose-500 @enderror" placeholder="500000">
                            </div>
                            @error('salary') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Join Date <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="joined_on" max="{{ date('Y-m-d') }}" class="{{ $input }} @error('joined_on') border-red-400 @enderror">
                            @error('joined_on') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Account Status <span class="text-red-500">*</span></label>
                            <select wire:model="status" class="{{ $input }} @error('status') border-red-400 @enderror">
                                <option value="active">Active</option>
                                <option value="on-leave">On Leave</option>
                            </select>
                            @error('status') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- ── Location ─────────────────────────────────────────────── --}}
                <div class="grid grid-cols-1 gap-x-8 gap-y-10 md:grid-cols-3 pt-12">
                    <div class="md:col-span-1">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Location details</h2>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Where is this employee based? Essential for payroll and holidays.</p>
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="{{ $label }}">Country <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="country" list="country-options" maxlength="3" class="{{ $input }} @error('country') border-red-400 @enderror" placeholder="e.g. IN">
                            <datalist id="country-options">
                                @foreach ($this->countries as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </datalist>
                            @error('country') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">State / Province <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="state" list="state-options" maxlength="3" class="{{ $input }} @error('state') border-red-400 @enderror" placeholder="e.g. MH">
                            <datalist id="state-options">
                                @foreach ($this->states as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </datalist>
                            @error('state') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">City <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="city" class="{{ $input }} @error('city') border-red-400 @enderror" placeholder="e.g. Mumbai">
                            @error('city') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Zip / Postal Code</label>
                            <input type="text" wire:model="zip_code" class="{{ $input }} @error('zip_code') border-red-400 @enderror" placeholder="e.g. 400001">
                            @error('zip_code') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="{{ $label }}">Address Line <span class="text-red-500">*</span></label>
                            <textarea wire:model="address" rows="2" class="{{ $input }} @error('address') border-red-400 @enderror" placeholder="Full residential address..."></textarea>
                            @error('address') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- ── Personal Preferences ─────────────────────────────────── --}}
                <div class="grid grid-cols-1 gap-x-8 gap-y-10 md:grid-cols-3 pt-12">
                    <div class="md:col-span-1">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Cultural Fit</h2>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Optional details to help teams organize get-togethers and events.</p>
                    </div>
                    
                    <div class="md:col-span-2 grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="{{ $label }}">Hobbies</label>
                            <textarea wire:model="hobbies" rows="2" class="{{ $input }}" placeholder="Reading, hiking..."></textarea>
                            @error('hobbies') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Food Preference</label>
                            <select wire:model="food_preference" class="{{ $input }}">
                                <option value="">No preference</option>
                                <option value="veg">Vegetarian</option>
                                <option value="non-veg">Non-Vegetarian</option>
                            </select>
                            @error('food_preference') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Allergies / Health Note</label>
                            <input type="text" wire:model="health_issues" class="{{ $input }}" placeholder="e.g. Peanut allergy...">
                            @error('health_issues') <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
