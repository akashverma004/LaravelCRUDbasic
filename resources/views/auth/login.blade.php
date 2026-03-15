<x-guest-layout title="Login - PeopleFlow HRMS">
    @include('hrms.components.public-navbar')

    <div class="relative min-h-screen flex items-center justify-center px-4 py-12">
        <div class="relative w-full max-w-6xl grid lg:grid-cols-2 gap-12 items-center">
            
            {{-- Left Side: Content --}}
            <div class="hidden lg:block space-y-12 pr-12">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 rounded-full border border-violet-100 bg-violet-50/50 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-violet-600">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-violet-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-violet-500"></span>
                        </span>
                        Enterprise HR Solutions
                    </div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-900 leading-[1.1]">
                        Architect your <br/>
                        <span class="bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent">people operations.</span>
                    </h1>
                    <p class="text-lg font-medium text-slate-500 leading-relaxed max-w-md">
                        PeopleFlow provides the infrastructure required to scale your organization from 1 to 10,000 employees with ease.
                    </p>
                </div>

                <div class="grid gap-8">
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-lg shadow-slate-200">
                            <svg class="h-5 w-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">High-Performance Core</h3>
                            <p class="text-xs text-slate-500 mt-1">Real-time sync across payroll, attendance, and performance tracking.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-lg shadow-slate-200">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.744c0 3.89 1.856 7.344 4.755 9.53a11.959 11.959 0 01-4.755-9.53v-.377L9 12.75z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Secure SSO Auth</h3>
                            <p class="text-xs text-slate-500 mt-1">Enterprise-grade security with Google and Microsoft integration.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Login Form --}}
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

                {{-- Login Card --}}
                <div class="group relative overflow-hidden rounded-[32px] border border-white/40 bg-white/70 shadow-[0_20px_50px_rgba(0,0,0,0.05)] backdrop-blur-2xl transition-all">
                    
                    {{-- Form --}}
                    <form method="POST" action="{{ route('login') }}" class="p-10">
                        @csrf

                        <div class="space-y-5">
                            <div class="mb-6 text-center">
                                <h1 class="text-xl font-bold tracking-tight text-slate-900">Sign in</h1>
                                <p class="mt-1 text-xs text-slate-500">Access your workspace infrastructure</p>
                            </div>

                            {{-- Social Login (SSO) --}}
                            <div class="grid grid-cols-2 gap-3 mb-6">
                                <a href="{{ route('social.redirect', 'google') }}" class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition-all hover:bg-slate-50 hover:border-slate-300">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                    <span>Google</span>
                                </a>
                                <a href="{{ route('social.redirect', 'azure') }}" class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition-all hover:bg-slate-50 hover:border-slate-300">
                                    <svg class="h-4 w-4" viewBox="0 0 23 23"><path fill="#f3f3f3" d="M0 0h23v23H0z"/><path fill="#f35325" d="M1 1h10v10H1z"/><path fill="#81bc06" d="M12 1h10v10H12z"/><path fill="#05a6f0" d="M1 12h10v10H1z"/><path fill="#ffba08" d="M12 12h10v10H12z"/></svg>
                                    <span>Microsoft</span>
                                </a>
                            </div>

                            <div class="relative mb-6">
                                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
                                <div class="relative flex justify-center text-[10px] uppercase tracking-widest text-slate-400"><span class="bg-white/70 px-2 rounded-full backdrop-blur-sm px-4">Or sign in with email</span></div>
                            </div>

                            @if ($errors->any())
                                <div class="flex items-center gap-3 rounded-2xl border border-rose-100 bg-rose-50/50 px-4 py-3">
                                    <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                                    <p class="text-xs font-medium text-rose-600">{{ $errors->first() }}</p>
                                </div>
                            @endif

                            {{-- Email --}}
                            <div class="space-y-1.5">
                                <label for="email" class="ml-1 block text-[10px] font-bold uppercase tracking-widest text-slate-400">Email Address</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-white/50 px-4 py-2.5 text-sm text-slate-900 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                    placeholder="name@company.com" required autofocus autocomplete="username">
                            </div>

                            {{-- Password --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between ml-1">
                                    <label for="password" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Password</label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-[10px] font-bold text-violet-600 hover:text-violet-700 uppercase tracking-widest">Forgot?</a>
                                    @endif
                                </div>
                                <input id="password" type="password" name="password"
                                    class="w-full rounded-xl border border-slate-200 bg-white/50 px-4 py-2.5 text-sm text-slate-900 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                    placeholder="••••••••" required autocomplete="current-password">
                            </div>

                            {{-- Remember Me --}}
                            <div class="flex items-center gap-2 ml-1">
                                <input type="checkbox" name="remember" id="remember" class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                <label for="remember" class="text-xs font-medium text-slate-500 select-none">Stay signed in</label>
                            </div>

                            <button type="submit"
                                class="w-full rounded-full bg-slate-900 py-3 text-sm font-bold text-white shadow-xl shadow-slate-900/10 transition-all hover:bg-violet-600 hover:shadow-violet-600/20 active:scale-[0.98]">
                                Sign In
                            </button>

                            <div class="pt-8 text-center border-t border-slate-100">
                                <p class="text-xs text-slate-500">
                                    Need a workspace?
                                    <a href="{{ route('company-signup.create') }}" class="font-bold text-violet-600 hover:text-violet-700 ml-1">Create an account</a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
