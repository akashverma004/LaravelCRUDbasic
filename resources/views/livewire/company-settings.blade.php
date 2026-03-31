<div class="max-w-5xl">
    {{-- Header Area --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 mb-1">Administration (Livewire 3 SPA)</p>
            <h1 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">Company Settings</h1>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" form="settings-form" class="flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white shadow-lg transition-all hover:bg-slate-800 active:scale-95 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 disabled:opacity-50" wire:loading.attr="disabled">
                
                <svg wire:loading.remove class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                
                <svg wire:loading class="h-3.5 w-3.5 animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                
                <span wire:loading.remove>Save Changes</span>
                <span wire:loading>Saving...</span>
            </button>
        </div>
    </div>

    <form id="settings-form" wire:submit="save" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left Column: Basic Info --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900">
                <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3 dark:border-white/5 dark:bg-white/5">
                    <h2 class="text-[10px] font-black uppercase tracking-wider text-slate-500">Organization Profile</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400">Company Name</label>
                            <input type="text" wire:model="name" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-900 transition-all focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white" required>
                            @error('name') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400">Official Email</label>
                            <input type="email" wire:model="email" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-900 transition-all focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white" required>
                            @error('email') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400">Contact Number</label>
                            <input type="text" wire:model="phone" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-900 transition-all focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white">
                            @error('phone') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400">Registered Address</label>
                            <input type="text" wire:model="address" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-900 transition-all focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white">
                            @error('address') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-100 bg-amber-50/50 p-4 dark:border-amber-500/10 dark:bg-amber-500/5">
                <div class="flex gap-3">
                    <svg class="h-4 w-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-amber-900 dark:text-amber-400">Security Note</p>
                        <p class="mt-0.5 text-[10px] font-medium text-amber-700/80 dark:text-amber-500/70 leading-relaxed">Changes to official company details will reflect immediately on all system-generated documents including payslips, contracts, and invoices. Form saves dynamically without reloading.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Assets --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900">
                <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3 dark:border-white/5 dark:bg-white/5">
                    <h2 class="text-[10px] font-black uppercase tracking-wider text-slate-500">Document Branding</h2>
                </div>
                <div class="p-5 space-y-5">
                    {{-- Logo Upload --}}
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-slate-100 bg-slate-50 p-1 dark:border-white/5 dark:bg-white/5 relative">
                            <span wire:loading wire:target="logo" class="absolute inset-0 bg-white/70 flex items-center justify-center z-10"><svg class="animate-spin h-4 w-4 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                            @if ($logo)
                                <img src="{{ $logo->temporaryUrl() }}" class="h-full w-full object-contain">
                            @elseif($tenant->logo_path)
                                <img src="{{ Storage::url($tenant->logo_path) }}" class="h-full w-full object-contain">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-700 dark:text-white">Workspace Logo</p>
                            <input type="file" wire:model="logo" class="hidden" id="logo-input" accept="image/*">
                            <button type="button" onclick="document.getElementById('logo-input').click()" class="mt-1 text-[10px] font-bold text-cyan-500 hover:text-cyan-600 outline-none">Update Photo</button>
                            @error('logo') <span class="block text-[8px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Signature Upload --}}
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-slate-100 bg-slate-50 p-1 dark:border-white/5 dark:bg-white/5 relative">
                            <span wire:loading wire:target="signature" class="absolute inset-0 bg-white/70 flex items-center justify-center z-10"><svg class="animate-spin h-4 w-4 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                            @if ($signature)
                                <img src="{{ $signature->temporaryUrl() }}" class="h-full w-full object-contain">
                            @elseif($tenant->signature_path)
                                <img src="{{ Storage::url($tenant->signature_path) }}" class="h-full w-full object-contain">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-700 dark:text-white">Auth. Signature</p>
                            <input type="file" wire:model="signature" class="hidden" id="signature-input" accept="image/png">
                            <button type="button" onclick="document.getElementById('signature-input').click()" class="mt-1 text-[10px] font-bold text-cyan-500 hover:text-cyan-600 outline-none">Upload PNG</button>
                            <p class="text-[8px] text-slate-400 font-medium">Use transparent PNG for best results</p>
                            @error('signature') <span class="block text-[8px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Stamp Upload --}}
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-slate-100 bg-slate-50 p-1 dark:border-white/5 dark:bg-white/5 relative">
                            <span wire:loading wire:target="stamp" class="absolute inset-0 bg-white/70 flex items-center justify-center z-10"><svg class="animate-spin h-4 w-4 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                            @if ($stamp)
                                <img src="{{ $stamp->temporaryUrl() }}" class="h-full w-full object-contain opacity-80">
                            @elseif($tenant->stamp_path)
                                <img src="{{ Storage::url($tenant->stamp_path) }}" class="h-full w-full object-contain opacity-80">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-700 dark:text-white">Company Stamp</p>
                            <input type="file" wire:model="stamp" class="hidden" id="stamp-input" accept="image/png">
                            <button type="button" onclick="document.getElementById('stamp-input').click()" class="mt-1 text-[10px] font-bold text-cyan-500 hover:text-cyan-600 outline-none">Upload Seal</button>
                            <p class="text-[8px] text-slate-400 font-medium">PNG format recommended</p>
                            @error('stamp') <span class="block text-[8px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
