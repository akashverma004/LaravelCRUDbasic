<x-guest-layout title="Signal Validation - PeopleFlow HRMS">
    @include('hrms.components.public-navbar')

    <div class="relative min-h-[calc(100vh-80px)] overflow-hidden bg-slate-950 flex items-center justify-center px-6 py-12">
        {{-- High-Impact Decorations --}}
        <div class="absolute -left-40 -top-40 h-[40rem] w-[40rem] rounded-full bg-cyan-500/10 blur-[120px]"></div>
        <div class="absolute -right-40 -bottom-40 h-[40rem] w-[40rem] rounded-full bg-indigo-500/10 blur-[120px]"></div>
        
        <div class="relative w-full max-w-xl">
            {{-- Validation Terminal --}}
            <div class="overflow-hidden rounded-[3rem] border border-white/10 bg-slate-900/40 shadow-[0_32px_120px_-20px_rgba(0,0,0,0.8)] backdrop-blur-3xl transition-all">
                
                {{-- Terminal Header --}}
                <div class="relative border-b border-white/5 bg-slate-950/40 p-12 text-center">
                    <div class="mx-auto mb-8 flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-gradient-to-br from-cyan-400 to-indigo-600 shadow-2xl shadow-cyan-400/20">
                        <span class="text-3xl font-black text-white">V</span>
                    </div>
                    <h1 class="text-3xl font-black tracking-tight text-white uppercase">Signal <span class="text-cyan-400">Validation</span></h1>
                    <p class="mt-3 text-[10px] font-black uppercase tracking-[0.3em] text-slate-500">Awaiting External Authentication Handshake</p>
                </div>

                <div class="p-12 space-y-8">
                    {{-- Status Payload --}}
                    <div class="flex items-start gap-4 rounded-3xl border border-indigo-500/10 bg-indigo-500/5 p-6 shadow-xl shadow-indigo-500/5">
                        <svg class="h-6 w-6 shrink-0 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest leading-relaxed">
                            {{ __('Thanks for signing up! Please verify your email address by clicking the link we sent to you. If you didn\'t receive it, we\'ll gladly send you another.') }}
                        </p>
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="flex items-center gap-4 rounded-3xl border border-emerald-500/10 bg-emerald-500/5 px-8 py-5 shadow-xl shadow-emerald-500/5">
                            <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-emerald-500">
                                {{ __('A new verification link has been sent to the email we have on file.') }}
                            </p>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="space-y-4 pt-8 border-t border-white/5">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="group relative w-full overflow-hidden rounded-2xl bg-cyan-400 py-6 text-[10px] font-black uppercase tracking-[0.3em] text-slate-950 shadow-2xl shadow-cyan-400/20 transition-all hover:bg-cyan-300 hover:-translate-y-1 active:translate-y-0">
                                {{ __('Resend Validation Token') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-2xl border border-white/5 bg-white/5 py-4 text-[9px] font-black uppercase tracking-widest text-slate-500 hover:text-white hover:bg-white/10 transition-all">
                                {{ __('Terminate Connection') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Terminal Footer Decoration --}}
            <div class="mt-12 text-center">
                <p class="text-[9px] font-black uppercase tracking-[0.4em] text-slate-700">Handshake_Validation_Interface_V4.0</p>
            </div>
        </div>
    </div>
</x-guest-layout>
