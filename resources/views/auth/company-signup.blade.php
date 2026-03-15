<x-guest-layout title="Workspace Architecture - PeopleFlow HRMS">
    @include('hrms.components.public-navbar')

    <div class="relative min-h-screen flex items-center justify-center px-6 py-16">
        <div class="relative w-full max-w-7xl grid lg:grid-cols-[1fr,auto] gap-16 items-start">
            
            {{-- Left Side: Enterprise Content --}}
            <div class="hidden lg:block space-y-16 pt-20">
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-3 rounded-full border border-violet-100 bg-violet-50/50 px-5 py-2 text-xs font-black uppercase tracking-[0.2em] text-violet-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        Enterprise Infrastructure
                    </div>
                    <h1 class="text-6xl font-black tracking-tighter text-slate-900 leading-[1.05]">
                        Scale your <br/>
                        <span class="bg-gradient-to-r from-violet-600 via-indigo-600 to-blue-600 bg-clip-text text-transparent">workforce.</span>
                    </h1>
                    <p class="text-xl font-medium text-slate-500 leading-relaxed max-w-xl">
                        Deploy a complete people management ecosystem in minutes. Built for modern organizations that value speed, security, and precision.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 gap-12">
                    <div class="space-y-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-xl shadow-slate-200">
                            <svg class="h-6 w-6 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-3h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12" /></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight">Multi-Tenant Architecture</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">Isolate your company data with our secure, low-latency infrastructure design.</p>
                    </div>
                    <div class="space-y-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-xl shadow-slate-200">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3 6-6M2.25 12a9.75 9.75 0 1119.5 0 9.75 9.75 0 01-19.5 0z" /></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight">Regulatory Compliance</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">Built-in support for global labor laws, tax regimes, and data privacy standards.</p>
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-200 flex items-center gap-8">
                    <div>
                        <div class="text-2xl font-black text-slate-900 leading-none">99.9%</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-2">Uptime SLA</div>
                    </div>
                    <div class="h-8 w-px bg-slate-200"></div>
                    <div>
                        <div class="text-2xl font-black text-slate-900 leading-none">256-bit</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-2">AES Encryption</div>
                    </div>
                    <div class="h-8 w-px bg-slate-200"></div>
                    <div>
                        <div class="text-2xl font-black text-slate-900 leading-none">24/7</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-2">Priority Support</div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Signup Card --}}
            <div class="relative w-full max-w-2xl ml-auto">
                {{-- Flare Logo --}}
                <div class="mb-10 flex flex-col items-center lg:items-start">
                    <a href="/" class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-[14px] bg-gradient-to-br from-violet-500 to-indigo-600 shadow-xl shadow-violet-500/20">
                            <span class="text-2xl font-black text-white">PF</span>
                        </div>
                        <span class="text-2xl font-black tracking-tight text-slate-900 uppercase tracking-widest">PeopleFlow</span>
                    </a>
                </div>

                <form method="POST" action="{{ route('company-signup.store') }}"
                    class="overflow-hidden rounded-[48px] border border-white/40 bg-white/70 shadow-[0_40px_100px_rgba(0,0,0,0.05)] backdrop-blur-3xl transition-all">
                    @csrf

                    <div class="px-10 py-10 space-y-8">
                        <div class="text-center lg:text-left">
                            <h1 class="text-2xl font-black tracking-tight text-slate-900 uppercase tracking-widest">Register Workspace</h1>
                            <p class="mt-2 text-xs font-medium text-slate-400">Set up your organization account</p>
                        </div>

                        @if ($errors->any())
                            <div class="flex items-center gap-3 rounded-2xl border border-rose-100 bg-rose-50/50 px-5 py-3">
                                <div class="h-1.5 w-1.5 rounded-full bg-rose-500 animate-pulse"></div>
                                <p class="text-[10px] font-bold text-rose-600 uppercase tracking-widest">{{ $errors->first() }}</p>
                            </div>
                        @endif

                        {{-- Section: Company Details --}}
                        <div>
                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-px flex-1 bg-slate-200"></div>
                                <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-violet-500">Company Details</h2>
                                <div class="h-px flex-1 bg-slate-200"></div>
                            </div>

                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="space-y-1.5">
                                    <label class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Company Name</label>
                                    <input name="company_name" value="{{ old('company_name') }}"
                                        class="w-full rounded-xl border border-slate-200 bg-white/50 px-5 py-3 text-xs font-bold text-slate-900 placeholder:text-slate-300 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                        placeholder="e.g. Acme Corp" required>
                                    @error('company_name')<p class="mt-1.5 text-[8px] font-bold text-rose-500 ml-4 italic">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Short Code <span class="text-slate-300 font-normal">(Optional)</span></label>
                                    <input name="company_code" value="{{ old('company_code') }}"
                                        class="w-full rounded-xl border border-slate-200 bg-white/50 px-5 py-3 text-xs font-bold text-slate-900 placeholder:text-slate-300 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                        placeholder="e.g. ACME">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Email Address</label>
                                    <input type="email" name="company_email" value="{{ old('company_email') }}"
                                        class="w-full rounded-xl border border-slate-200 bg-white/50 px-5 py-3 text-xs font-bold text-slate-900 placeholder:text-slate-300 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                        placeholder="contact@acme.com" required>
                                    @error('company_email')<p class="mt-1.5 text-[8px] font-bold text-rose-500 ml-4 italic">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Phone Number</label>
                                    <input name="company_phone" value="{{ old('company_phone') }}"
                                        class="w-full rounded-xl border border-slate-200 bg-white/50 px-5 py-3 text-xs font-bold text-slate-900 placeholder:text-slate-300 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                        placeholder="+1 234 567 890">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Country</label>
                                    <select name="country"
                                        class="w-full rounded-xl border border-slate-200 bg-white/50 px-5 py-3 text-xs font-bold text-slate-900 appearance-none transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10">
                                        @foreach($countries as $code => $name)
                                            <option value="{{ $code }}" @selected(old('country', 'IN') === $code)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Timezone</label>
                                    <input name="timezone" value="{{ old('timezone', 'Asia/Kolkata') }}"
                                        class="w-full rounded-xl border border-slate-200 bg-white/50 px-5 py-3 text-xs font-bold text-slate-900 placeholder:text-slate-300 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                        placeholder="e.g. Asia/Kolkata" required>
                                    @error('timezone')<p class="mt-1.5 text-[8px] font-bold text-rose-500 ml-4 italic">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Section: Admin Details --}}
                        <div>
                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-px flex-1 bg-slate-200"></div>
                                <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-500">Administrator Account</h2>
                                <div class="h-px flex-1 bg-slate-200"></div>
                            </div>

                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="space-y-1.5">
                                    <label class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Full Name</label>
                                    <input name="admin_name" value="{{ old('admin_name') }}"
                                        class="w-full rounded-xl border border-slate-200 bg-white/50 px-5 py-3 text-xs font-bold text-slate-900 placeholder:text-slate-300 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                        placeholder="Full Name" required>
                                    @error('admin_name')<p class="mt-1.5 text-[8px] font-bold text-rose-500 ml-4 italic">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Email Address</label>
                                    <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                                        class="w-full rounded-xl border border-slate-200 bg-white/50 px-5 py-3 text-xs font-bold text-slate-900 placeholder:text-slate-300 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                        placeholder="admin@acme.com" required>
                                    @error('admin_email')<p class="mt-1.5 text-[8px] font-bold text-rose-500 ml-4 italic">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Password</label>
                                    <input type="password" name="password"
                                        class="w-full rounded-xl border border-slate-200 bg-white/50 px-5 py-3 text-xs font-bold text-slate-900 placeholder:text-slate-300 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                        placeholder="••••••••" required>
                                    @error('password')<p class="mt-1.5 text-[8px] font-bold text-rose-500 ml-4 italic">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="ml-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Confirm Password</label>
                                    <input type="password" name="password_confirmation"
                                        class="w-full rounded-xl border border-slate-200 bg-white/50 px-5 py-3 text-xs font-bold text-slate-900 placeholder:text-slate-300 transition-all focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10"
                                        placeholder="••••••••" required>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-col items-center gap-6 pt-6">
                            <button type="submit"
                                class="w-full rounded-full bg-slate-900 py-4 text-xs font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-slate-900/10 transition-all hover:bg-violet-600 hover:shadow-violet-600/30 active:scale-[0.98]">
                                Create Account
                            </button>
                            <a href="{{ route('login') }}" class="text-[9px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-colors">
                                Already have an account? Sign In
                            </a>
                        </div>
                    </div>
                </form>

                <div class="mt-16 text-center lg:text-right opacity-30">
                    <p class="text-[9px] font-black uppercase tracking-[0.5em] text-slate-900">PeopleFlow v4.0.0</p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
