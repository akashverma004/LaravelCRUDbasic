<div class="space-y-6 relative">
    {{-- Universal Notification --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-xs font-bold text-white shadow-2xl backdrop-blur-xl dark:bg-slate-800/90">
            <div class="bg-emerald-500 h-2 w-2 rounded-full animate-pulse"></div>
            {{ session('success') }}
        </div>
    @endif

    {{-- Profile Hero / Banner --}}
    <div class="relative overflow-hidden rounded-2xl bg-white shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="relative group h-32 w-full overflow-hidden bg-slate-100 dark:bg-white/5" wire:key="banner-{{ $employee->id }}">
            @if($cover_photo)
                <img src="{{ $cover_photo->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover">
            @elseif($employee->cover_photo)
                <img src="{{ Storage::url($employee->cover_photo) }}" alt="Cover" class="h-full w-full object-cover shadow-inner" onerror="this.src='https://images.unsplash.com/photo-1620641788421-7a1c342ea42e?auto=format&fit=crop&w=1200&q=80'">
            @else
                <div class="h-full w-full bg-[conic-gradient(at_top_right,_var(--tw-gradient-stops))] from-slate-900 via-indigo-900 to-slate-900">
                    <div class="absolute inset-0 bg-white/5 backdrop-blur-[1px]"></div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center opacity-10">
                    <svg class="h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                </div>
            @endif

            <div class="absolute right-6 bottom-4 z-20 flex flex-col items-end gap-1.5">
                <label class="flex items-center gap-2 rounded-xl bg-white/95 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-900 shadow-xl transition-all cursor-pointer hover:bg-white hover:scale-105 active:scale-95 border border-slate-200">
                    <input type="file" wire:model="cover_photo" class="hidden" accept="image/*">
                    <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                    Change Cover
                </label>
                @error('cover_photo')
                    <div class="rounded-lg bg-rose-500 px-3 py-1 text-[8px] font-black uppercase text-white shadow-lg">{{ $message }}</div>
                @enderror
            </div>

            <div wire:loading wire:target="cover_photo" class="absolute inset-0 z-30 flex items-center justify-center bg-slate-900/20 backdrop-blur-[2px]">
                <div class="flex items-center gap-3 rounded-full bg-white/90 px-4 py-2 shadow-2xl">
                    <div class="h-3 w-3 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent"></div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-900">Uploading...</span>
                </div>
            </div>
        </div>
        <div class="px-8 pb-6">
            <div class="relative flex flex-col items-center gap-6 lg:flex-row lg:items-end -mt-10">
                {{-- Photo Container --}}
                <div class="relative group">
                    <div class="h-24 w-24 rounded-2xl border-4 border-white bg-slate-100 shadow-xl overflow-hidden dark:border-slate-800 dark:bg-slate-800">
                        @if($employee->profile_photo)
                            <img src="{{ Storage::url($employee->profile_photo) }}" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center font-black text-2xl text-slate-400">
                                {{ substr($employee->full_name, 0, 1) }}
                            </div>
                        @endif
                        <label class="absolute inset-0 flex items-center justify-center bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <input type="file" wire:model="photo" class="hidden">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </label>
                    </div>
                </div>

                <div class="flex-1 text-center lg:text-left">
                    <div class="flex flex-col items-center lg:items-start gap-0.5">
                        <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">{{ $employee->full_name }}</h1>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $employee->job_title }} • {{ $employee->department?->name }}</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('dashboard') }}" wire:navigate class="rounded-xl border border-slate-200 px-4 py-2 text-[9px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 dark:border-white/5 dark:text-slate-400 dark:hover:bg-white/10 transition-all">Console</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Professional Bio --}}
            <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
                <h3 class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-3 px-1">About Me</h3>
                <p class="text-[11px] font-bold text-slate-600 dark:text-slate-400 leading-relaxed px-1">
                    {{ $employee->bio ?: "No bio defined. Add one under the Personal tab." }}
                </p>
            </div>

            {{-- Skill Badges --}}
            <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
                <div class="flex items-center justify-between mb-4 px-1">
                    <h3 class="text-[9px] font-black uppercase tracking-widest text-slate-400">Expertise</h3>
                    <button wire:click="$set('showSkillModal', true)" class="text-[9px] font-black text-cyan-600 hover:text-cyan-500 transition-colors uppercase">Add Skill</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    @forelse($employee->skills as $skill)
                        <div class="group relative flex items-center gap-2 rounded-lg bg-slate-50 px-2 by-1.5 border border-slate-100 dark:bg-slate-950 dark:border-white/5">
                            <span class="text-[10px] font-bold text-slate-900 dark:text-white uppercase">{{ $skill->name }}</span>
                            <button wire:click="removeSkill({{ $skill->id }})" class="opacity-0 group-hover:opacity-100 text-rose-500">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @empty
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest px-1">No skills listed</p>
                    @endforelse
                </div>
            </div>

            {{-- Experience & Education Summary --}}
            <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
                <div class="flex items-center justify-between mb-4 px-1">
                    <h3 class="text-[9px] font-black uppercase tracking-widest text-slate-400">Identity Security</h3>
                </div>
                <div class="space-y-4">
                     <div class="space-y-1.5 ml-1">
                         <label class="text-[9px] font-black uppercase text-slate-400">Current Password</label>
                         <input wire:model="current_password" type="password" class="w-full rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-xs font-bold dark:bg-slate-950 dark:border-white/5">
                     </div>
                     <div class="space-y-1.5 ml-1">
                         <label class="text-[9px] font-black uppercase text-slate-400">New Password</label>
                         <input wire:model="password" type="password" class="w-full rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-xs font-bold dark:bg-slate-950 dark:border-white/5">
                     </div>
                     <button wire:click="updatePassword" class="w-full rounded-xl bg-slate-900 py-2.5 text-[9px] font-black uppercase text-white shadow-lg active:scale-95 transition-all">Update Security</button>
                </div>
            </div>
        </div>

        {{-- Main Tabular Structure --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Navigation Tabs --}}
            <div class="flex gap-1.2 overflow-x-auto rounded-xl border border-slate-200 bg-white p-1.5 shadow-sm dark:border-slate-800 dark:bg-slate-900/50 hide-scrollbar" wire:ignore.self>
                @foreach([
                    'personal' => 'Personal',
                    'emergency' => 'Emergency',
                    'identity' => 'Identity',
                    'bank' => 'Banking',
                    'preferences' => 'Lifestyle',
                    'work' => 'Corporate',
                    'experience' => 'History',
                    'education' => 'Education',
                ] as $tabId => $tabLabel)
                <button
                    wire:click="$set('activeTab', '{{ $tabId }}')"
                    class="whitespace-nowrap rounded-lg px-3 py-2 text-[9px] font-black uppercase tracking-widest transition-all {{ $activeTab === $tabId ? 'bg-slate-900 text-white dark:bg-white/10 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50' }}"
                >{{ $tabLabel }}</button>
                @endforeach
            </div>

            {{-- Content Section --}}
            <div class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900/50 min-h-[450px]">
                @php
                    $inputClass  = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white transition-all disabled:opacity-50 disabled:bg-slate-100 dark:disabled:bg-white/5';
                    $labelClass  = 'block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1';
                @endphp

                <form wire:submit="submitForm">
                    @php 
                    $isEditing = $editingSection === $activeTab;
                    $isWorkTab = $activeTab === 'work';
                    @endphp

                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-8 dark:border-slate-800">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white uppercase tracking-tight">{{ ucfirst($activeTab) }} Data</h2>
                        @if(!in_array($activeTab, ['education', 'experience']))
                            @if($isEditing)
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="cancelEditing" class="rounded-lg border border-slate-200 px-3 py-2 text-[10px] font-black uppercase text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300">Discard</button>
                                    <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-[10px] font-black uppercase text-white hover:bg-indigo-600 transition-all shadow-lg shadow-indigo-500/20" wire:loading.attr="disabled">Save Changes</button>
                                </div>
                            @else
                                <button type="button" wire:click="startEditing('{{ $activeTab }}')" class="rounded-lg border border-slate-200 px-4 py-2 text-[10px] font-black uppercase text-slate-600 hover:bg-slate-50 transition-all dark:border-slate-700 dark:text-slate-300">Edit Section</button>
                            @endif
                        @endif
                    </div>

                    {{-- TAB: Personal --}}
                    @if($activeTab === 'personal')
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="{{ $labelClass }}">Full Name</label>
                            <input type="text" wire:model="form.full_name" class="{{ $inputClass }}" disabled>
                        </div>
                         <div>
                            <label class="{{ $labelClass }}">Personal Email</label>
                            <input type="email" wire:model="form.personal_email" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Phone Number</label>
                            <input type="text" wire:model="form.phone" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Date of Birth</label>
                            <input type="date" wire:model="form.date_of_birth" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                         <div>
                            <label class="{{ $labelClass }}">Gender</label>
                            <select wire:model="form.gender" class="{{ $inputClass }}" @disabled(!$isEditing)>
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                         <div>
                            <label class="{{ $labelClass }}">Blood Group</label>
                            <input type="text" wire:model="form.blood_group" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="{{ $labelClass }}">Home Address</label>
                            <textarea wire:model="form.address" rows="3" class="{{ $inputClass }}" @disabled(!$isEditing)></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="{{ $labelClass }}">Professional Bio</label>
                            <textarea wire:model="form.bio" rows="4" class="{{ $inputClass }}" @disabled(!$isEditing)></textarea>
                        </div>
                    </div>
                    @endif

                    {{-- TAB: Emergency --}}
                    @if($activeTab === 'emergency')
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="{{ $labelClass }}">Contact Person Name</label>
                            <input type="text" wire:model="form.emergency_contact_name" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Relationship</label>
                            <input type="text" wire:model="form.emergency_contact_relationship" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Phone Number</label>
                            <input type="text" wire:model="form.emergency_contact_phone" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                    </div>
                    @endif

                    {{-- TAB: Identity --}}
                    @if($activeTab === 'identity')
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="{{ $labelClass }}">PAN Number</label>
                            <input type="text" wire:model="form.pan_number" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Aadhaar Number</label>
                            <input type="text" wire:model="form.aadhaar_number" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Passport Number</label>
                            <input type="text" wire:model="form.passport_number" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Nationality</label>
                            <input type="text" wire:model="form.nationality" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                    </div>
                    @endif

                    {{-- TAB: Bank --}}
                    @if($activeTab === 'bank')
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="{{ $labelClass }}">Bank Name</label>
                            <input type="text" wire:model="form.bank_name" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Account Number</label>
                            <input type="text" wire:model="form.bank_account_number" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">IFSC Code</label>
                            <input type="text" wire:model="form.bank_ifsc" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                    </div>
                    @endif

                    {{-- TAB: Preferences --}}
                    @if($activeTab === 'preferences')
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="{{ $labelClass }}">LinkedIn Profile URL</label>
                            <input type="url" wire:model="form.linkedin_url" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Hobbies</label>
                            <input type="text" wire:model="form.hobbies" class="{{ $inputClass }}" @disabled(!$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Food Preference</label>
                            <select wire:model="form.food_preference" class="{{ $inputClass }}" @disabled(!$isEditing)>
                                <option value="">None</option>
                                <option value="veg">Vegetarian</option>
                                <option value="non-veg">Non-Vegetarian</option>
                            </select>
                        </div>
                    </div>
                    @endif

                    {{-- TAB: Corporate (Read Only for employees) --}}
                    @if($activeTab === 'work')
                    <div class="grid gap-6 sm:grid-cols-2">
                         <div>
                            <label class="{{ $labelClass }}">Official Job Title</label>
                            <input type="text" wire:model="form.job_title" class="{{ $inputClass }}" @disabled(!$this->isAdmin || !$isEditing)>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Department</label>
                            <select wire:model="form.department_id" class="{{ $inputClass }}" @disabled(!$this->isAdmin || !$isEditing)>
                                @foreach($this->departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                         <div>
                            <label class="{{ $labelClass }}">Reporting Manager</label>
                            <input type="text" value="{{ $employee->manager?->full_name ?? 'None' }}" class="{{ $inputClass }}" disabled>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Joining Date</label>
                            <input type="text" value="{{ $employee->joined_on?->format('d M Y') ?? '—' }}" class="{{ $inputClass }}" disabled>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Employment Status</label>
                            <input type="text" value="{{ ucfirst($employee->status) }}" class="{{ $inputClass }}" disabled>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Work Email</label>
                            <input type="text" value="{{ $employee->email }}" class="{{ $inputClass }}" disabled>
                        </div>
                    </div>
                    @endif

                    {{-- TAB: Education & History --}}
                    @if(in_array($activeTab, ['education', 'experience']))
                        <div class="flex items-center justify-between mb-6">
                            <p class="text-[10px] font-black uppercase text-slate-400">Section details are currently read-only. Use the sidebar to add new records.</p>
                             @if($activeTab === 'education')
                                <button type="button" wire:click="$set('showEduModal', true)" class="rounded-lg bg-indigo-50 px-4 py-2 text-[9px] font-black uppercase text-indigo-600 dark:bg-indigo-500/10">Add Record</button>
                             @else
                                <button type="button" wire:click="$set('showExpModal', true)" class="rounded-lg bg-emerald-50 px-4 py-2 text-[9px] font-black uppercase text-emerald-600 dark:bg-emerald-500/10">Add Record</button>
                             @endif
                        </div>
                        
                        @if($activeTab === 'education')
                            <div class="space-y-4">
                                @forelse($employee->educations as $edu)
                                    <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-100 dark:bg-slate-950 dark:border-white/5">
                                        <div>
                                            <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase">{{ $edu->degree }}</p>
                                            <p class="text-[9px] font-bold text-slate-500 uppercase">{{ $edu->institution }}</p>
                                        </div>
                                        <button type="button" wire:click="removeEducation({{ $edu->id }})" class="text-rose-500 hover:scale-110 transition-transform">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                @empty
                                    <p class="text-[10px] text-slate-400">No education history found.</p>
                                @endforelse
                            </div>
                        @else
                            <div class="space-y-4">
                                @forelse($employee->experiences as $exp)
                                    <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-100 dark:bg-slate-950 dark:border-white/5">
                                        <div>
                                            <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase">{{ $exp->designation }}</p>
                                            <p class="text-[9px] font-bold text-slate-500 uppercase">{{ $exp->company }}</p>
                                        </div>
                                         <button type="button" wire:click="removeExperience({{ $exp->id }})" class="text-rose-500 hover:scale-110 transition-transform">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                @empty
                                    <p class="text-[10px] text-slate-400">No work history found.</p>
                                @endforelse
                            </div>
                        @endif
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Modals for Adding Records --}}
    @if($showEduModal || $showExpModal || $showSkillModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showEduModal', false); $set('showExpModal', false); $set('showSkillModal', false);" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-slate-900 overflow-hidden border border-slate-200 dark:border-white/10">
                @if($showEduModal)
                    <div class="p-8">
                        <h2 class="text-xl font-black text-slate-900 dark:text-white mb-6 uppercase tracking-tight">Add Education</h2>
                        <div class="space-y-4">
                            <div><label class="{{ $labelClass }}">Degree</label><input wire:model="eduForm.degree" type="text" class="{{ $inputClass }}"></div>
                            <div><label class="{{ $labelClass }}">Institution</label><input wire:model="eduForm.institution" type="text" class="{{ $inputClass }}"></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="{{ $labelClass }}">Start Year</label><input wire:model="eduForm.year_from" type="number" class="{{ $inputClass }}"></div>
                                <div><label class="{{ $labelClass }}">End Year</label><input wire:model="eduForm.year_to" type="number" class="{{ $inputClass }}"></div>
                            </div>
                            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-white/5">
                                <button wire:click="$set('showEduModal', false)" class="text-[10px] font-black uppercase text-slate-400 px-4">Cancel</button>
                                <button wire:click="addEducation" class="rounded-xl bg-indigo-600 px-8 py-2 text-[10px] font-black uppercase text-white shadow-lg">Confirm</button>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Similar structures for Exp and Skill if needed within the same modal container logic --}}
                @if($showExpModal)
                    <div class="p-8">
                        <h2 class="text-xl font-black text-slate-900 dark:text-white mb-6 uppercase tracking-tight">Add Experience</h2>
                        <div class="space-y-4">
                            <div><label class="{{ $labelClass }}">Company</label><input wire:model="expForm.company" type="text" class="{{ $inputClass }}"></div>
                            <div><label class="{{ $labelClass }}">Designation</label><input wire:model="expForm.designation" type="text" class="{{ $inputClass }}"></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="{{ $labelClass }}">From</label><input wire:model="expForm.from_date" type="date" class="{{ $inputClass }}"></div>
                                <div><label class="{{ $labelClass }}">To</label><input wire:model="expForm.to_date" type="date" class="{{ $inputClass }}"></div>
                            </div>
                            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-white/5">
                                <button wire:click="$set('showExpModal', false)" class="text-[10px] font-black uppercase text-slate-400 px-4">Cancel</button>
                                <button wire:click="addExperience" class="rounded-xl bg-emerald-600 px-8 py-2 text-[10px] font-black uppercase text-white shadow-lg">Confirm</button>
                            </div>
                        </div>
                    </div>
                @endif
                @if($showSkillModal)
                    <div class="p-8">
                        <h2 class="text-xl font-black text-slate-900 dark:text-white mb-6 uppercase tracking-tight">Add Expertise</h2>
                        <div class="space-y-4">
                            <div><label class="{{ $labelClass }}">Skill Name</label><input wire:model="skillForm.name" type="text" class="{{ $inputClass }}"></div>
                            <div>
                                <label class="{{ $labelClass }}">Proficiency</label>
                                <select wire:model="skillForm.proficiency" class="{{ $inputClass }}">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="expert">Expert</option>
                                </select>
                             </div>
                            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-white/5">
                                <button wire:click="$set('showSkillModal', false)" class="text-[10px] font-black uppercase text-slate-400 px-4">Cancel</button>
                                <button wire:click="addSkill" class="rounded-xl bg-cyan-600 px-8 py-2 text-[10px] font-black uppercase text-white shadow-lg">Confirm</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>
