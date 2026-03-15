<x-guest-layout title="Key Reset - PeopleFlow HRMS">
    @include('hrms.components.public-navbar')

    <div class="relative min-h-[calc(100vh-80px)] overflow-hidden bg-slate-950 flex items-center justify-center px-6 py-12">
        {{-- High-Impact Decorations --}}
        <div class="absolute -left-40 -top-40 h-[40rem] w-[40rem] rounded-full bg-cyan-500/10 blur-[120px]"></div>
        <div class="absolute -right-40 -bottom-40 h-[40rem] w-[40rem] rounded-full bg-indigo-500/10 blur-[120px]"></div>
        
        <div class="relative w-full max-w-xl">
            {{-- Reset Terminal --}}
            <div class="overflow-hidden rounded-[3rem] border border-white/10 bg-slate-900/40 shadow-[0_32px_120px_-20px_rgba(0,0,0,0.8)] backdrop-blur-3xl transition-all">
                
                {{-- Terminal Header --}}
                <div class="relative border-b border-white/5 bg-slate-950/40 p-12 text-center">
                    <div class="mx-auto mb-8 flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-gradient-to-br from-cyan-400 to-indigo-600 shadow-2xl shadow-cyan-400/20">
                        <span class="text-3xl font-black text-white">P</span>
                    </div>
                    <h1 class="text-3xl font-black tracking-tight text-white uppercase">Key <span class="text-cyan-400">Reset</span></h1>
                    <p class="mt-3 text-[10px] font-black uppercase tracking-[0.3em] text-slate-500">Executing Access Credential Update</p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="p-12 space-y-8">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    @if ($errors->any())
                        <div class="flex items-center gap-4 rounded-2xl border border-rose-500/10 bg-rose-500/5 px-6 py-4 shadow-xl shadow-rose-500/5">
                            <div class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-rose-500">{{ $errors->first() }}</p>
                        </div>
                    @endif

                    {{-- Email Input --}}
                    <div>
                        <label for="email" class="block text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4 mb-3">Operator Alias</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                            class="w-full rounded-2xl border border-white/5 bg-slate-950/50 px-8 py-5 text-sm font-bold text-white placeholder:text-slate-700 transition-all focus:border-cyan-400 focus:bg-slate-950 focus:ring-4 focus:ring-cyan-400/10"
                            placeholder="IDENT_TOKEN@HQ" required autofocus autocomplete="username">
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label for="password" class="block text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4 mb-3">New Access Key</label>
                        <input id="password" type="password" name="password"
                            class="w-full rounded-2xl border border-white/5 bg-slate-950/50 px-8 py-5 text-sm font-bold text-white placeholder:text-slate-700 transition-all focus:border-cyan-400 focus:bg-slate-950 focus:ring-4 focus:ring-cyan-400/10"
                            placeholder="Min 8 Characters" required autocomplete="new-password">
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4 mb-3">Re-verify Key</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            class="w-full rounded-2xl border border-white/5 bg-slate-950/50 px-8 py-5 text-sm font-bold text-white placeholder:text-slate-700 transition-all focus:border-cyan-400 focus:bg-slate-950 focus:ring-4 focus:ring-cyan-400/10"
                            placeholder="Re-enter Key" required autocomplete="new-password">
                    </div>

                    {{-- Execution --}}
                    <div class="pt-4">
                        <button type="submit"
                            class="group relative w-full overflow-hidden rounded-2xl bg-cyan-400 py-6 text-[10px] font-black uppercase tracking-[0.3em] text-slate-950 shadow-2xl shadow-cyan-400/20 transition-all hover:bg-cyan-300 hover:-translate-y-1 active:translate-y-0">
                            Commit New Key
                        </button>
                    </div>

                    <div class="pt-8 text-center border-t border-white/5">
                        <a href="{{ route('login') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-cyan-400 transition-colors">
                            Return to Access Point
                        </a>
                    </div>
                </form>
            </div>

            {{-- Terminal Footer Decoration --}}
            <div class="mt-12 text-center">
                <p class="text-[9px] font-black uppercase tracking-[0.4em] text-slate-700">Credential_Update_Interface_V4.0</p>
            </div>
        </div>
    </div>
</x-guest-layout>
