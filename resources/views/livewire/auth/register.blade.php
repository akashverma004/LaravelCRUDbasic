<div>
    @include('hrms.components.public-navbar')

    <div class="relative min-h-[80vh] flex items-center justify-center px-4 py-8 mt-6 overflow-hidden">
        {{-- Deep Gradient Mesh --}}
        <div class="absolute -top-[10%] -left-[5%] w-[35%] h-[35%] rounded-full bg-indigo-600/5 blur-[100px] animate-pulse"></div>
        <div class="absolute -bottom-[10%] -right-[5%] w-[40%] h-[40%] rounded-full bg-violet-600/5 blur-[120px] animate-pulse" style="animation-delay: 2s"></div>

        <div class="relative w-full max-w-6xl grid lg:grid-cols-[1.1fr,1fr] gap-12 items-center">
            
            {{-- Left Side: Content --}}
            <div class="hidden lg:block space-y-12 pr-12">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-3 rounded-full border border-indigo-100 bg-indigo-50/50 px-4 py-1.5 text-[9px] font-black uppercase tracking-[0.3em] text-indigo-600 shadow-sm border-b-2">
                        Institutional Growth
                    </div>
                    <h1 class="text-6xl font-black tracking-tight text-slate-900 leading-[1] uppercase">
                        Start your <br/>
                        <span class="bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent italic">Journey.</span>
                    </h1>
                    <p class="text-[11px] font-bold text-slate-500 leading-relaxed max-w-sm uppercase tracking-widest text-justify opacity-80">
                        Join the most advanced platform for team management. Deploy a complete people management ecosystem in minutes.
                    </p>
                </div>
            </div>

            {{-- Right Side: Register Form --}}
            <div class="relative w-full max-w-md ml-auto">
                <div class="absolute -inset-[2px] rounded-[3rem] bg-gradient-to-br from-indigo-600/20 via-violet-600/10 to-blue-600/20 opacity-40 blur-sm group-hover:opacity-100 transition-opacity"></div>
                
                <div class="group relative overflow-hidden rounded-[3rem] border-2 border-white bg-white/60 shadow-[0_48px_100px_-24px_rgba(0,0,0,0.15)] backdrop-blur-3xl transition-all">
                    
                    <form wire:submit="register" class="p-8 sm:p-10">
                        <div class="space-y-4">
                            <div class="mb-6 text-center lg:text-left">
                                <h1 class="text-3xl font-black tracking-tighter text-slate-900 uppercase">Create <span class="text-indigo-600">Account.</span></h1>
                                <p class="mt-1 text-[9px] font-black text-slate-400 uppercase tracking-[0.4em]">Sign Up to Platform</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <a href="{{ route('social.redirect', 'google') }}" class="flex items-center justify-center gap-2 rounded-2xl border border-slate-100 bg-white px-3 py-2.5 text-[8px] font-black uppercase tracking-widest text-slate-700 transition-all hover:shadow-lg active:scale-95 shadow-sm">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                    <span>Google</span>
                                </a>
                                <a href="{{ route('social.redirect', 'azure') }}" class="flex items-center justify-center gap-2 rounded-2xl border border-slate-100 bg-white px-3 py-2.5 text-[8px] font-black uppercase tracking-widest text-slate-700 transition-all hover:shadow-lg active:scale-95 shadow-sm">
                                    <svg class="h-4 w-4" viewBox="0 0 23 23"><path fill="#f3f3f3" d="M0 0h23v23H0z"/><path fill="#f35325" d="M1 1h10v10H1z"/><path fill="#81bc06" d="M12 1h10v10H12z"/><path fill="#05a6f0" d="M1 12h10v10H1z"/><path fill="#ffba08" d="M12 12h10v10H12z"/></svg>
                                    <span>Microsoft</span>
                                </a>
                            </div>

                            <div class="relative mb-4">
                                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
                                <div class="relative flex justify-center text-[7px] uppercase tracking-[0.8em] font-black text-slate-300"><span class="bg-white/60 px-4 rounded-full"> New Account</span></div>
                            </div>

                            {{-- Name --}}
                            <div class="space-y-0.5">
                                <label class="ml-2 block text-[8px] font-black uppercase tracking-widest text-slate-400 opacity-60">Full Name</label>
                                <input wire:model="name" type="text"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-6 py-3 text-xs font-black text-slate-900 transition-all focus:border-indigo-600 focus:ring-8 focus:ring-indigo-600/5 placeholder-slate-200"
                                    placeholder="Your Name" required autofocus>
                                @error('name') <p class="text-[7px] font-black text-rose-500 uppercase tracking-widest ml-2 mt-0.5">{{ $message }}</p> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="space-y-0.5">
                                <label class="ml-2 block text-[8px] font-black uppercase tracking-widest text-slate-400 opacity-60">Email Address</label>
                                <input wire:model="email" type="email"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-6 py-3 text-xs font-black text-slate-900 transition-all focus:border-indigo-600 focus:ring-8 focus:ring-indigo-600/5 placeholder-slate-200"
                                    placeholder="your@email.com" required>
                                @error('email') <p class="text-[7px] font-black text-rose-500 uppercase tracking-widest ml-2 mt-0.5">{{ $message }}</p> @enderror
                            </div>

                            {{-- Password --}}
                            <div class="space-y-0.5">
                                <label class="ml-2 block text-[8px] font-black uppercase tracking-widest text-slate-400 opacity-60">Password</label>
                                <input wire:model="password" type="password"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-6 py-3 text-xs font-black text-slate-900 transition-all focus:border-indigo-600 focus:ring-8 focus:ring-indigo-600/5 placeholder-slate-200"
                                    placeholder="••••••••••••" required>
                                @error('password') <p class="text-[7px] font-black text-rose-500 uppercase tracking-widest ml-2 mt-0.5">{{ $message }}</p> @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="space-y-0.5">
                                <label class="ml-2 block text-[8px] font-black uppercase tracking-widest text-slate-400 opacity-60">Confirm Password</label>
                                <input wire:model="password_confirmation" type="password"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-6 py-3 text-xs font-black text-slate-900 transition-all focus:border-indigo-600 focus:ring-8 focus:ring-indigo-600/5 placeholder-slate-200"
                                    placeholder="••••••••••••" required>
                            </div>

                            <button type="submit"
                                class="group relative w-full overflow-hidden rounded-full bg-slate-950 py-4 text-[10px] font-black uppercase tracking-[0.4em] text-white shadow-2xl shadow-slate-950/20 transition-all active:scale-[0.98]">
                                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-violet-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <span class="relative z-10">Sign Up</span>
                            </button>

                            <div class="pt-6 text-center border-t border-slate-100">
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Already have an account?
                                    <a href="{{ route('login') }}" class="font-black text-indigo-600 hover:text-indigo-700 ml-1">Sign In</a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('hrms.components.public-footer')
</div>
