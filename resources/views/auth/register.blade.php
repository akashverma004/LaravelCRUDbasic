    <div class="relative min-h-screen flex items-center justify-center px-4 py-12">
        <div class="relative w-full max-w-6xl grid lg:grid-cols-2 gap-12 items-center">
            
            {{-- Left Side: Content --}}
            <div class="hidden lg:block space-y-12 pr-12">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50/50 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-indigo-600">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" /></svg>
                        Join 2,000+ Companies
                    </div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-900 leading-[1.1]">
                        The future of <br/>
                        <span class="bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">work begins here.</span>
                    </h1>
                    <p class="text-lg font-medium text-slate-500 leading-relaxed max-w-md">
                        Get started with the most advanced people management platform. Unified data, effortless scaling.
                    </p>
                </div>

                <div class="grid gap-8">
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-lg shadow-slate-200">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Unified Team Directory</h3>
                            <p class="text-xs text-slate-500 mt-1">One source of truth for all your employee data and documentation.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-lg shadow-slate-200">
                            <svg class="h-5 w-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Automated Onboarding</h3>
                            <p class="text-xs text-slate-500 mt-1">Take new hires from offer letter to first day in under 5 minutes.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Register Form --}}
            <div class="relative w-full max-w-md ml-auto">
                {{-- Flare Logo --}}
                <div class="mb-10 flex flex-col items-center lg:items-start">
                    <a href="/" class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-[14px] bg-gradient-to-br from-violet-500 to-indigo-600 shadow-xl shadow-violet-500/20">
                            <span class="text-2xl font-black text-white">PF</span>
                        </div>
                        <span class="text-2xl font-black tracking-tight text-slate-900">PeopleFlow</span>
                    </a>
                </div>

                {{-- Register Card --}}
                <div class="group relative overflow-hidden rounded-[40px] border border-white/40 bg-white/70 shadow-[0_20px_50px_rgba(0,0,0,0.05)] backdrop-blur-2xl transition-all">
                    
                    {{-- Form --}}
                    <form method="POST" action="{{ route('register') }}" class="p-10">
                        @csrf

                        <div class="space-y-4">
                            <div class="mb-5 text-center">
                                <h1 class="text-xl font-bold tracking-tight text-slate-900">Create account</h1>
                                <p class="mt-1 text-xs text-slate-500">Join the people management network</p>
                            </div>

                            {{-- Social Login (SSO) --}}
                            <div class="grid grid-cols-2 gap-3 mb-5">
                                <a href="{{ route('social.redirect', 'google') }}" class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition-all hover:bg-slate-50 hover:border-slate-300">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                    <span>Google</span>
                                </a>
                                <a href="{{ route('social.redirect', 'azure') }}" class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition-all hover:bg-slate-50 hover:border-slate-300">
                                    <svg class="h-4 w-4" viewBox="0 0 23 23"><path fill="#f3f3f3" d="M0 0h23v23H0z"/><path fill="#f35325" d="M1 1h10v10H1z"/><path fill="#81bc06" d="M12 1h10v10H12z"/><path fill="#05a6f0" d="M1 12h10v10H1z"/><path fill="#ffba08" d="M12 12h10v10H12z"/></svg>
                                    <span>Microsoft</span>
                                </a>
                            </div>

                            <div class="relative mb-5">
                                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
                                <div class="relative flex justify-center text-[10px] uppercase tracking-widest text-slate-400"><span class="bg-white/70 px-2 rounded-full backdrop-blur-sm px-4">Or sign up with email</span></div>
                            </div>

                            @if ($errors->any())
                                <div class="flex items-center gap-3 rounded-2xl border border-rose-100 bg-rose-50/50 px-4 py-3">
                                    <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                                    <p class="text-xs font-medium text-rose-600">{{ $errors->first() }}</p>
                                </div>
                            @endif

                            {{-- Name --}}
                            <div class="space-y-1">
                                <label for="name" class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Full Name</label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-white/50 px-4 py-2.5 text-xs font-bold text-slate-900 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                    placeholder="John Doe" required autofocus autocomplete="name">
                            </div>

                            {{-- Email --}}
                            <div class="space-y-1">
                                <label for="email" class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Email Address</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-white/50 px-4 py-2.5 text-xs font-bold text-slate-900 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                    placeholder="name@company.com" required autocomplete="username">
                            </div>

                            {{-- Password --}}
                            <div class="space-y-1">
                                <label for="password" class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Password</label>
                                <input id="password" type="password" name="password"
                                    class="w-full rounded-xl border border-slate-200 bg-white/50 px-4 py-2.5 text-xs font-bold text-slate-900 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                    placeholder="••••••••" required autocomplete="new-password">
                            </div>

                            {{-- Confirm Password --}}
                            <div class="space-y-1">
                                <label for="password_confirmation" class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Confirm Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                    class="w-full rounded-xl border border-slate-200 bg-white/50 px-4 py-2.5 text-xs font-bold text-slate-900 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                    placeholder="••••••••" required autocomplete="new-password">
                            </div>

                            <button type="submit"
                                class="w-full rounded-full bg-slate-900 py-3 text-sm font-bold text-white shadow-xl shadow-slate-900/10 transition-all hover:bg-violet-600 hover:shadow-violet-600/20 active:scale-[0.98]">
                                Create Account
                            </button>

                            <div class="pt-8 text-center border-t border-slate-100">
                                <p class="text-xs text-slate-500">
                                    Already have an account?
                                    <a href="{{ route('login') }}" class="font-bold text-violet-600 hover:text-violet-700 ml-1">Sign in</a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
