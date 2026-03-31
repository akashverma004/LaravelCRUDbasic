<div>
    @include('hrms.components.public-navbar')

    <div class="relative min-h-[80vh] flex items-center justify-center px-4 py-8 mt-6">
        <div class="relative w-full max-w-6xl grid lg:grid-cols-2 gap-10 items-center">
            
            {{-- Left Side: Content --}}
            <div class="hidden lg:block space-y-10 pr-10">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-100 bg-cyan-50/50 px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 shadow-sm">
                        Account Restoration
                    </div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-900 leading-[1.05] uppercase">
                        Update your <br/>
                        <span class="bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent underline decoration-cyan-100 decoration-4 underline-offset-[8px]">Password.</span>
                    </h1>
                    <p class="text-[10px] font-bold text-slate-500 leading-relaxed max-w-xs uppercase tracking-widest">
                        Finalize your password update to regain access to your workspace and team tools.
                    </p>
                </div>
            </div>

            {{-- Right Side: Reset Card --}}
            <div class="relative w-full max-w-md ml-auto">
                <div class="group relative overflow-hidden rounded-[2.5rem] border-2 border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-2xl transition-all">
                    
                    <form wire:submit="resetPassword" class="p-8 sm:p-10">
                        <div class="space-y-4">
                            <div class="mb-6 text-center lg:text-left">
                                <h1 class="text-2xl font-black tracking-tight text-slate-900 uppercase">Reset <span class="text-cyan-600">Password</span></h1>
                                <p class="mt-1 text-[9px] font-black text-slate-400 uppercase tracking-widest">Update your credentials</p>
                            </div>

                            {{-- Email --}}
                            <div class="space-y-0.5">
                                <label class="ml-1 block text-[8px] font-black uppercase tracking-widest text-slate-400">Email Address</label>
                                <input wire:model="email" type="email" 
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-black text-slate-900 transition-all focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/5 placeholder-slate-200"
                                    placeholder="your@email.com" required autofocus>
                                @error('email') <p class="text-[7px] font-black text-rose-500 uppercase tracking-widest ml-1 mt-0.5">{{ $message }}</p> @enderror
                            </div>

                            {{-- Password --}}
                            <div class="space-y-0.5">
                                <label class="ml-1 block text-[8px] font-black uppercase tracking-widest text-slate-400">New Password</label>
                                <input wire:model="password" type="password" 
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-black text-slate-900 transition-all focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/5 placeholder-slate-200"
                                    placeholder="••••••••••••" required>
                                @error('password') <p class="text-[7px] font-black text-rose-500 uppercase tracking-widest ml-1 mt-0.5">{{ $message }}</p> @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="space-y-0.5">
                                <label class="ml-1 block text-[8px] font-black uppercase tracking-widest text-slate-400">Verify Password</label>
                                <input wire:model="password_confirmation" type="password" 
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-black text-slate-900 transition-all focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/5 placeholder-slate-200"
                                    placeholder="••••••••••••" required>
                            </div>

                            <button type="submit"
                                class="w-full rounded-full bg-slate-900 py-3.5 text-[9px] font-black uppercase tracking-[0.2em] text-white shadow-lg shadow-slate-900/10 transition-all hover:bg-cyan-600 hover:shadow-cyan-600/30 active:scale-95">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
