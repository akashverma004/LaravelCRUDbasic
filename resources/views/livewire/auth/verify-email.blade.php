<div>
    @include('hrms.components.public-navbar')

    <div class="relative min-h-[80vh] flex items-center justify-center px-4 py-8 mt-6">
        <div class="relative w-full max-w-6xl grid lg:grid-cols-2 gap-10 items-center">
            
            {{-- Left Side: Content --}}
            <div class="hidden lg:block space-y-10 pr-10">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50/50 px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600 shadow-sm">
                        Verification Required
                    </div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-900 leading-[1.05] uppercase">
                        Confirm your <br/>
                        <span class="bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent underline decoration-indigo-100 decoration-4 underline-offset-[8px]">Email.</span>
                    </h1>
                    <p class="text-[10px] font-bold text-slate-500 leading-relaxed max-w-xs uppercase tracking-widest">
                        Please check your inbox and verify your email address to access your account.
                    </p>
                </div>
            </div>

            {{-- Right Side: Verify Card --}}
            <div class="relative w-full max-w-md ml-auto">
                <div class="group relative overflow-hidden rounded-[2.5rem] border-2 border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-2xl transition-all">
                    
                    <div class="p-8 sm:p-10 space-y-5">
                        <div class="mb-6 text-center lg:text-left">
                            <h1 class="text-2xl font-black tracking-tight text-slate-900 uppercase">Verify <span class="text-indigo-600">Email</span></h1>
                            <p class="mt-1 text-[9px] font-black text-slate-400 uppercase tracking-widest">Activate your account link</p>
                        </div>

                        @if (session('status') == 'verification-link-sent')
                            <div class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 shadow-sm">
                                <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                <p class="text-[8px] font-black uppercase tracking-widest text-emerald-600 leading-tight">Verification link retransmitted.</p>
                            </div>
                        @else
                            <div class="flex items-start gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                                <svg class="h-4 w-4 shrink-0 text-indigo-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest leading-loose">Check your email for the link.</p>
                            </div>
                        @endif

                        <div class="space-y-3 pt-6 border-t border-slate-100">
                            <button wire:click="sendVerification" class="w-full rounded-full bg-slate-900 py-3.5 text-[9px] font-black uppercase tracking-[0.3em] text-white shadow-xl shadow-slate-900/10 transition-all hover:bg-indigo-600 hover:shadow-indigo-600/30 active:scale-95">
                                Resend Email
                            </button>

                            <button wire:click="logout" class="w-full rounded-full border border-slate-200 bg-white py-2 text-[8px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-950 transition-all active:scale-95">
                                Sign Out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
