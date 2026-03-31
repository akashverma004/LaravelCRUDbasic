<div>
    @include('hrms.components.public-navbar')

    <div class="relative min-h-[80vh] flex items-center justify-center px-4 py-8 mt-6">
        <div class="relative w-full max-w-6xl grid lg:grid-cols-2 gap-10 items-center">
            
            {{-- Left Side: Content --}}
            <div class="hidden lg:block space-y-10 pr-10">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/50 px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] text-blue-600 shadow-sm">
                        Incoming Invitation
                    </div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-900 leading-[1.05] uppercase">
                        Join your <br/>
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent underline decoration-blue-100 decoration-4 underline-offset-[8px]">Company.</span>
                    </h1>
                    <p class="text-[10px] font-bold text-slate-500 leading-relaxed max-w-xs uppercase tracking-widest">
                        You've been invited to join a workspace. Complete your profile to start collaborating with your team.
                    </p>
                </div>
            </div>

            {{-- Right Side: Invitation Card --}}
            <div class="relative w-full max-w-md ml-auto">
                <div class="group relative overflow-hidden rounded-[2.5rem] border-2 border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-2xl transition-all">
                    
                    <form wire:submit="accept" class="p-8 sm:p-10 space-y-5">
                        <div class="mb-4 text-center lg:text-left">
                            <h1 class="text-2xl font-black tracking-tight text-slate-900 uppercase">Join <span class="text-blue-500">Workspace</span></h1>
                            <p class="mt-1 text-[9px] font-black text-slate-400 uppercase tracking-widest">Complete your account setup</p>
                        </div>

                        {{-- Details --}}
                        <div class="flex flex-col gap-1 rounded-xl border border-blue-100 bg-blue-50/10 p-5 text-center shadow-inner">
                            <p class="text-[8px] font-black uppercase tracking-[0.4em] text-blue-400">Position</p>
                            <p class="text-lg font-black text-slate-900 uppercase tracking-tight">
                                {{ $invitation->role_name }} @ <span class="text-blue-600">{{ $invitation->tenant->name }}</span>
                            </p>
                        </div>

                        <div class="space-y-3">
                            {{-- Email --}}
                            <div class="space-y-0.5">
                                <label class="ml-1 block text-[8px] font-black uppercase tracking-widest text-slate-400">Email Address</label>
                                <div class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs font-black text-slate-400 uppercase tracking-widest cursor-not-allowed">
                                    {{ $invitation->email }}
                                </div>
                            </div>

                            {{-- Name --}}
                            <div class="space-y-0.5">
                                <label class="ml-1 block text-[8px] font-black uppercase tracking-widest text-slate-400">Full Name</label>
                                <input wire:model="name"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-black text-slate-900 placeholder:text-slate-200 transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 uppercase tracking-widest"
                                    placeholder="Your Name" required>
                                @error('name') <p class="text-[7px] font-black tracking-widest text-rose-500 ml-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            {{-- Password --}}
                            <div class="space-y-0.5">
                                <label class="ml-1 block text-[8px] font-black uppercase tracking-widest text-slate-400">New Password</label>
                                <input wire:model="password" type="password" 
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-black text-slate-900 placeholder:text-slate-200 transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                    placeholder="••••••••••••" required>
                                @error('password') <p class="text-[7px] font-black tracking-widest text-rose-500 ml-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <button type="submit"
                                class="w-full rounded-full bg-slate-900 py-3.5 text-[9px] font-black uppercase tracking-[0.4em] text-white shadow-xl shadow-slate-900/20 transition-all hover:bg-blue-600 hover:shadow-blue-600/30 active:scale-95">
                                Join Team
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
