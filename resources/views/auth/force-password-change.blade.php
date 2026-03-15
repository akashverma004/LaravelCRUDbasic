<x-guest-layout title="Security Escalation - PeopleFlow HRMS">
    @include('hrms.components.public-navbar')

    <div class="relative min-h-[calc(100vh-80px)] overflow-hidden bg-slate-950 flex items-center justify-center px-6 py-12">
        {{-- High-Impact Decorations --}}
        <div class="absolute -left-40 -top-40 h-[40rem] w-[40rem] rounded-full bg-cyan-500/10 blur-[120px]"></div>
        <div class="absolute -right-40 -bottom-40 h-[40rem] w-[40rem] rounded-full bg-indigo-500/10 blur-[120px]"></div>
        
        <div class="relative w-full max-w-xl">
            {{-- Security Notice Terminal --}}
            <div class="overflow-hidden rounded-[3rem] border border-white/10 bg-slate-900/40 shadow-[0_32px_120px_-20px_rgba(0,0,0,0.8)] backdrop-blur-3xl transition-all">
                
                {{-- Terminal Header --}}
                <div class="relative border-b border-white/5 bg-slate-950/40 p-12 text-center">
                    <div class="mx-auto mb-8 flex h-20 w-20 items-center justify-center rounded-[1.8rem] bg-gradient-to-br from-cyan-400 to-rose-600 shadow-2xl shadow-rose-500/20">
                        <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-black tracking-tight text-white uppercase">Security <span class="text-rose-400">Escalation</span></h1>
                    <p class="mt-3 text-[10px] font-black uppercase tracking-[0.3em] text-slate-500">MANDATORY_CREDENTIAL_ROTATION</p>
                </div>

                <div class="p-12 space-y-8">
                    {{-- Warning Payload --}}
                    <div class="flex items-start gap-4 rounded-3xl border border-rose-500/10 bg-rose-500/5 p-6 shadow-xl shadow-rose-500/5">
                        <div class="h-2 w-2 rounded-full bg-rose-500 mt-1 shrink-0 animate-pulse"></div>
                        <p class="text-[10px] font-bold text-rose-400 uppercase tracking-widest leading-relaxed">
                            For security purposes, you must rotate the temporary access key provided by your organization before proceeding into the workspace.
                        </p>
                    </div>

                    @if (session('warning'))
                        <div class="flex items-center gap-4 rounded-2xl border border-amber-500/10 bg-amber-500/5 px-6 py-4">
                            <svg class="h-5 w-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12V12.75z" /></svg>
                            <p class="text-[9px] font-black uppercase tracking-widest text-amber-400">{{ session('warning') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.force-change.store') }}" class="space-y-8">
                        @csrf

                        {{-- Current Key --}}
                        <div>
                            <label for="current_password" class="block text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4 mb-3">Temporary Access Key</label>
                            <input id="current_password" name="current_password" type="password" required
                                class="w-full rounded-2xl border border-white/5 bg-slate-950/50 px-8 py-5 text-sm font-bold text-white placeholder:text-slate-700 transition-all focus:border-cyan-400 focus:bg-slate-950 focus:ring-4 focus:ring-cyan-400/10 @error('current_password') border-rose-500 @enderror"
                                placeholder="••••••••••••">
                            @error('current_password')<p class="mt-3 text-[8px] font-black uppercase tracking-widest text-rose-500 ml-4">{{ $message }}</p>@enderror
                        </div>

                        {{-- New Key --}}
                        <div>
                            <label for="password" class="block text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4 mb-3">Permanent Private Key</label>
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                class="w-full rounded-2xl border border-white/5 bg-slate-950/50 px-8 py-5 text-sm font-bold text-white placeholder:text-slate-700 transition-all focus:border-cyan-400 focus:bg-slate-950 focus:ring-4 focus:ring-cyan-400/10 @error('password') border-rose-500 @enderror"
                                placeholder="High entropy recommended">
                            @error('password')<p class="mt-3 text-[8px] font-black uppercase tracking-widest text-rose-500 ml-4">{{ $message }}</p>@enderror
                        </div>

                        {{-- Confirm Key --}}
                        <div>
                            <label for="password_confirmation" class="block text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4 mb-3">Verify New Key</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                class="w-full rounded-2xl border border-white/5 bg-slate-950/50 px-8 py-5 text-sm font-bold text-white placeholder:text-slate-700 transition-all focus:border-cyan-400 focus:bg-slate-950 focus:ring-4 focus:ring-cyan-400/10"
                                placeholder="Repeat private key">
                        </div>

                        {{-- Execution --}}
                        <div class="pt-4">
                            <button type="submit"
                                class="group relative w-full overflow-hidden rounded-2xl bg-cyan-400 py-6 text-[10px] font-black uppercase tracking-[0.3em] text-slate-950 shadow-2xl shadow-cyan-400/20 transition-all hover:bg-cyan-300 hover:-translate-y-1 active:translate-y-0">
                                Commit Rotation & Enter
                            </button>
                        </div>
                    </form>

                    <div class="pt-8 text-center border-t border-white/5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-[9px] font-black uppercase tracking-widest text-slate-500 hover:text-white transition-colors">
                                Terminate Current Session
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Terminal Footer Decoration --}}
            <div class="mt-12 text-center">
                <p class="text-[9px] font-black uppercase tracking-[0.4em] text-slate-700">Credential_Rotation_Interface_V4.0</p>
            </div>
        </div>
    </div>
</x-guest-layout>
