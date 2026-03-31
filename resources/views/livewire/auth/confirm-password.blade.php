<div>
    @include('hrms.components.public-navbar')

    <div class="relative min-h-[80vh] flex items-center justify-center px-4 py-8 mt-6">
        <div class="relative w-full max-w-6xl grid lg:grid-cols-2 gap-10 items-center">
            
            {{-- Left Side: Content --}}
            <div class="hidden lg:block space-y-10 pr-10">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 rounded-full border border-rose-100 bg-rose-50/50 px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] text-rose-600 shadow-sm">
                        Security Verification
                    </div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-900 leading-[1.05] uppercase">
                        Confirm your <br/>
                        <span class="bg-gradient-to-r from-rose-600 to-indigo-600 bg-clip-text text-transparent underline decoration-rose-100 decoration-4 underline-offset-[8px]">Identity.</span>
                    </h1>
                    <p class="text-[10px] font-bold text-slate-500 leading-relaxed max-w-xs uppercase tracking-widest">
                        This is a secure area. Please confirm your password to proceed into the admin settings.
                    </p>
                </div>
            </div>

            {{-- Right Side: Confirm Card --}}
            <div class="relative w-full max-w-md ml-auto">
                <div class="group relative overflow-hidden rounded-[2.5rem] border-2 border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-2xl transition-all">
                    
                    <form wire:submit="confirmPassword" class="p-8 sm:p-10 space-y-5">
                        <div class="mb-6 text-center lg:text-left">
                            <h1 class="text-2xl font-black tracking-tight text-slate-900 uppercase">Confirm <span class="text-rose-600">Password</span></h1>
                            <p class="mt-1 text-[9px] font-black text-slate-400 uppercase tracking-widest">Verify your credentials</p>
                        </div>

                        <div class="flex items-start gap-3 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 shadow-sm">
                            <svg class="h-4 w-4 shrink-0 text-rose-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                            <p class="text-[8px] font-bold text-rose-600 uppercase tracking-widest leading-loose">Verify your password to access sensitive areas.</p>
                        </div>

                        {{-- Password --}}
                        <div class="space-y-1">
                            <label class="ml-1 block text-[8px] font-black uppercase tracking-widest text-slate-400">Password</label>
                            <input wire:model="password" type="password" 
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-black text-slate-900 transition-all focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 placeholder-slate-200"
                                placeholder="••••••••••••" required autofocus>
                            @error('password') <p class="text-[7px] font-black text-rose-500 uppercase tracking-widest ml-1 mt-0.5">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                            class="w-full rounded-full bg-slate-900 py-3.5 text-[9px] font-black uppercase tracking-[0.2em] text-white shadow-lg shadow-slate-900/10 transition-all hover:bg-rose-600 hover:shadow-rose-600/30 active:scale-95">
                            Confirm Identity
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
