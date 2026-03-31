<div class="max-w-7xl mx-auto space-y-12 pb-20 mt-8">
    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase"><span class="text-cyan-500">Account</span> Settings</h1>
        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-1">Configure your primary user identity and security protocols.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Section 1: Identity --}}
        <div class="flex flex-col gap-2">
            <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">Identity Registry</h3>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">Update your account's primary name and email coordinate.</p>
        </div>
        
        <div class="lg:col-span-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-10 shadow-sm dark:border-white/5 dark:bg-white/5 overflow-hidden relative">
                <form wire:submit="updateProfileInformation" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Full Identity</label>
                            <input wire:model="name" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-6 py-3.5 text-xs font-black text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                            @error('name') <p class="text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Email Coordinate</label>
                            <input wire:model="email" type="email" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-6 py-3.5 text-xs font-black text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                            @error('email') <p class="text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-white/5">
                        <button type="submit" class="rounded-2xl bg-slate-900 px-8 py-3.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                             Seal Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Section 2: Security --}}
        <div class="flex flex-col gap-2">
            <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">Security Protocol</h3>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">Modify your access cipher to maintain lattice security.</p>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-10 shadow-sm dark:border-white/5 dark:bg-white/5">
                <form wire:submit="updatePassword" class="space-y-8">
                    <div class="space-y-1.5 max-w-md">
                        <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Current Cipher</label>
                        <input wire:model="current_password" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-6 py-3.5 text-xs font-black text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white">
                        @error('current_password') <p class="text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">New Cipher</label>
                            <input wire:model="password" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-6 py-3.5 text-xs font-black text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white">
                            @error('password') <p class="text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Confirm New Cipher</label>
                            <input wire:model="password_confirmation" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-6 py-3.5 text-xs font-black text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white">
                            @error('password_confirmation') <p class="text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-white/5">
                        <button type="submit" class="rounded-2xl bg-indigo-600 px-8 py-3.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl transition-all hover:bg-violet-600 active:scale-95">
                             Update Cipher
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Section 3: Danger Zone --}}
        <div class="flex flex-col gap-2">
            <h3 class="text-xs font-black text-rose-600 dark:text-rose-400 uppercase tracking-tight">Danger Zone</h3>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">Irreversible node destruction. Proceed with maximal caution.</p>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-3xl border border-rose-100 bg-rose-50/30 p-10 shadow-sm dark:border-rose-500/10 dark:bg-rose-500/5">
                <div class="max-w-md space-y-6">
                    <p class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest leading-loose">Deleting your account will result in permanent loss of all associated identity records and institutional access.</p>
                    
                    <div x-data="{ confirmingDestruction: false }">
                        <button x-show="!confirmingDestruction" @click="confirmingDestruction = true" class="rounded-2xl bg-rose-500 px-8 py-3.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-rose-600 transition-all active:scale-95">
                             Initiate Node Destruction
                        </button>

                        <div x-show="confirmingDestruction" class="space-y-4" style="display:none;">
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-rose-600 dark:text-rose-400 ml-1">Confirm With Your Cipher</label>
                            <input wire:model="delete_confirmation_password" type="password" placeholder="Cipher Here" class="w-full rounded-2xl border border-rose-200 bg-white px-6 py-3.5 text-xs font-black text-slate-900 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white">
                            @error('delete_confirmation_password') <p class="text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</p> @enderror
                            
                            <div class="flex items-center gap-4">
                                <button wire:click="deleteUser" class="rounded-2xl bg-rose-600 px-8 py-3.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-rose-700 transition-all active:scale-95">
                                     Delete Permanently
                                </button>
                                <button @click="confirmingDestruction = false" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900">
                                     Aborted
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
