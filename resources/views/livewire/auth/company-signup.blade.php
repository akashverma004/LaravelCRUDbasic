<div>
    @include('hrms.components.public-navbar')

    <div class="relative min-h-[80vh] flex items-center justify-center px-4 py-8 mt-6">
        <div class="relative w-full max-w-6xl grid lg:grid-cols-2 gap-10 items-center">
            
            {{-- Left Side: Content --}}
            <div class="hidden lg:block space-y-10 pr-10">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 rounded-full border border-violet-100 bg-violet-50/50 px-3 py-1 text-[9px] font-black uppercase tracking-[0.3em] text-violet-600 shadow-sm">
                        Enterprise Settings
                    </div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-900 leading-[1.05] uppercase">
                        Create your <br/>
                        <span class="bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent underline decoration-violet-100 decoration-4 underline-offset-[8px]">Account.</span>
                    </h1>
                    <p class="text-[10px] font-bold text-slate-500 leading-relaxed max-w-xs uppercase tracking-widest">
                        Set up your organization's workspace and start managing your people with precision and ease.
                    </p>
                </div>
            </div>

            {{-- Right Side: Company Signup Card --}}
            <div class="relative w-full max-w-2xl ml-auto">
                <div class="group relative overflow-hidden rounded-[2.5rem] border-2 border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-2xl transition-all">
                    
                    <form wire:submit="signup" class="p-8 space-y-5">
                        <div class="mb-2 text-center lg:text-left">
                            <h1 class="text-2xl font-black tracking-tight text-slate-900 uppercase">New <span class="text-violet-600">Account</span></h1>
                            <p class="mt-1 text-[9px] font-black text-slate-400 uppercase tracking-widest">Register your business</p>
                        </div>

                        {{-- Section: Company Details --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="h-px flex-1 bg-slate-200/50"></div>
                                <h2 class="text-[9px] font-black uppercase tracking-[0.3em] text-violet-500 whitespace-nowrap">Company Information</h2>
                                <div class="h-px flex-1 bg-slate-200/50"></div>
                            </div>

                            <div class="grid gap-3 md:grid-cols-2">
                                <div class="space-y-0.5">
                                    <label class="ml-1 block text-[8px] font-black uppercase tracking-[0.3em] text-slate-400">Company Name</label>
                                    <input wire:model="company_name" 
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-900 focus:border-violet-500 focus:ring-4 focus:ring-violet-500/5 placeholder-slate-200"
                                        placeholder="E.G. Acme Corp" required>
                                    @error('company_name')<p class="mt-0.5 text-[7px] font-black text-rose-500 ml-1 uppercase tracking-widest">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-0.5">
                                    <label class="ml-1 block text-[8px] font-black uppercase tracking-[0.3em] text-slate-400">Short Code</label>
                                    <input wire:model="company_code" 
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-900 focus:border-violet-500 focus:ring-4 focus:ring-violet-500/5 placeholder-slate-200"
                                        placeholder="E.G. ACME">
                                    @error('company_code')<p class="mt-0.5 text-[7px] font-black text-rose-500 ml-1 uppercase tracking-widest">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-0.5">
                                    <label class="ml-1 block text-[8px] font-black uppercase tracking-[0.3em] text-slate-400">Business Email</label>
                                    <input type="email" wire:model="company_email" 
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-900 focus:border-violet-500 focus:ring-4 focus:ring-violet-500/5 placeholder-slate-200"
                                        placeholder="contact@company.com" required>
                                    @error('company_email')<p class="mt-0.5 text-[7px] font-black text-rose-500 ml-1 uppercase tracking-widest">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-0.5">
                                    <label class="ml-1 block text-[8px] font-black uppercase tracking-[0.3em] text-slate-400">Country</label>
                                    <select wire:model="country"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-900 appearance-none focus:border-violet-500 focus:ring-4 focus:ring-violet-500/5 uppercase tracking-widest">
                                        @foreach($countries as $code => $name)
                                            <option value="{{ $code }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Section: Admin Details --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="h-px flex-1 bg-slate-200/50"></div>
                                <h2 class="text-[9px] font-black uppercase tracking-[0.4em] text-indigo-500 whitespace-nowrap">Administrator Details</h2>
                                <div class="h-px flex-1 bg-slate-200/50"></div>
                            </div>

                            <div class="grid gap-3 md:grid-cols-2">
                                <div class="space-y-0.5">
                                    <label class="ml-1 block text-[8px] font-black uppercase tracking-[0.3em] text-slate-400">Admin Name</label>
                                    <input wire:model="admin_name" 
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/5 placeholder-slate-200"
                                        placeholder="Full Name" required>
                                    @error('admin_name')<p class="mt-0.5 text-[7px] font-black text-rose-500 ml-1 uppercase tracking-widest">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-0.5">
                                    <label class="ml-1 block text-[8px] font-black uppercase tracking-[0.3em] text-slate-400">Admin Email</label>
                                    <input wire:model="admin_email" type="email"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/5 placeholder-slate-200"
                                        placeholder="admin@company.com" required>
                                    @error('admin_email')<p class="mt-0.5 text-[7px] font-black text-rose-500 ml-1 uppercase tracking-widest">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-0.5">
                                    <label class="ml-1 block text-[8px] font-black uppercase tracking-[0.3em] text-slate-400">Password</label>
                                    <input type="password" wire:model="password" 
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/5 placeholder-slate-200"
                                        placeholder="••••••••••••" required>
                                    @error('password')<p class="mt-0.5 text-[7px] font-black text-rose-500 ml-1 uppercase tracking-widest">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-0.5">
                                    <label class="ml-1 block text-[8px] font-black uppercase tracking-[0.3em] text-slate-400">Confirm Password</label>
                                    <input type="password" wire:model="password_confirmation" 
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/5 placeholder-slate-200"
                                        placeholder="••••••••••••" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full rounded-full bg-slate-900 py-3.5 text-[9px] font-black uppercase tracking-[0.3em] text-white shadow-xl shadow-slate-900/10 transition-all hover:bg-violet-600 hover:shadow-violet-600/30 active:scale-[0.98]">
                            Create Account
                        </button>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-[8px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-950 transition-colors">
                                Existing account? Sign In
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
