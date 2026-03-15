<x-guest-layout title="Secure Access - PeopleFlow HRMS">
    @include('hrms.components.public-navbar')

    <div class="relative min-h-[calc(100vh-80px)] overflow-hidden bg-slate-950 flex items-center justify-center px-6 py-12">
        {{-- High-Impact Decorations --}}
        <div class="absolute -left-40 -top-40 h-[40rem] w-[40rem] rounded-full bg-cyan-500/10 blur-[120px]"></div>
        <div class="absolute -right-40 -bottom-40 h-[40rem] w-[40rem] rounded-full bg-indigo-500/10 blur-[120px]"></div>
        
        <div class="relative w-full max-w-xl">
            {{-- Secure Confirmation Terminal --}}
            <div class="overflow-hidden rounded-[3rem] border border-white/10 bg-slate-900/40 shadow-[0_32px_120px_-20px_rgba(0,0,0,0.8)] backdrop-blur-3xl transition-all">
                
                {{-- Terminal Header --}}
                <div class="relative border-b border-white/5 bg-slate-950/40 p-12 text-center">
                    <div class="mx-auto mb-8 flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-gradient-to-br from-cyan-400 to-indigo-600 shadow-2xl shadow-cyan-400/20">
                        <span class="text-3xl font-black text-white">S</span>
                    </div>
                    <h1 class="text-3xl font-black tracking-tight text-white uppercase">Secure <span class="text-cyan-400">Confirmation</span></h1>
                    <p class="mt-3 text-[10px] font-black uppercase tracking-[0.3em] text-slate-500">Elevated Authorization Locus</p>
                </div>

                <form method="POST" action="{{ route('password.confirm') }}" class="p-12 space-y-8">
                    @csrf

                    {{-- Security Payload --}}
                    <div class="flex items-start gap-4 rounded-3xl border border-rose-500/10 bg-rose-500/5 p-6 shadow-xl shadow-rose-500/5">
                        <svg class="h-6 w-6 shrink-0 text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        <p class="text-[10px] font-bold text-rose-400 uppercase tracking-widest leading-relaxed">
                            {{ __('This is a secure area. Please confirm your access key to proceed into the kernel.') }}
                        </p>
                    </div>

                    {{-- Access Key Input --}}
                    <div>
                        <label for="password" class="block text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4 mb-3">Master Access Key</label>
                        <input id="password" type="password" name="password"
                            class="w-full rounded-2xl border border-white/5 bg-slate-950/50 px-8 py-5 text-sm font-bold text-white placeholder:text-slate-700 transition-all focus:border-cyan-400 focus:bg-slate-950 focus:ring-4 focus:ring-cyan-400/10"
                            placeholder="••••••••••••" required autocomplete="current-password">
                        @error('password')<p class="mt-3 text-[8px] font-black uppercase tracking-widest text-rose-500 ml-4">{{ $message }}</p>@enderror
                    </div>

                    {{-- Execution --}}
                    <div class="pt-4">
                        <button type="submit"
                            class="group relative w-full overflow-hidden rounded-2xl bg-cyan-400 py-6 text-[10px] font-black uppercase tracking-[0.3em] text-slate-950 shadow-2xl shadow-cyan-400/20 transition-all hover:bg-cyan-300 hover:-translate-y-1 active:translate-y-0">
                            Confirm Authorization
                        </button>
                    </div>
                </form>
            </div>

            {{-- Terminal Footer Decoration --}}
            <div class="mt-12 text-center">
                <p class="text-[9px] font-black uppercase tracking-[0.4em] text-slate-700">Privileged_Access_Interface_V4.0</p>
            </div>
        </div>
    </div>
</x-guest-layout>
