<x-guest-layout title="Join Workspace - PeopleFlow HRMS">
    @include('hrms.components.public-navbar')

    <div class="relative min-h-[calc(100vh-80px)] overflow-hidden bg-slate-950 flex items-center justify-center px-6 py-12">
        {{-- High-Impact Decorations --}}
        <div class="absolute -left-40 -top-40 h-[40rem] w-[40rem] rounded-full bg-cyan-500/10 blur-[120px]"></div>
        <div class="absolute -right-40 -bottom-40 h-[40rem] w-[40rem] rounded-full bg-indigo-500/10 blur-[120px]"></div>
        
        <div class="relative w-full max-w-xl">
            {{-- Inbound Invitation Terminal --}}
            <div class="overflow-hidden rounded-[3rem] border border-white/10 bg-slate-900/40 shadow-[0_32px_120px_-20px_rgba(0,0,0,0.8)] backdrop-blur-3xl transition-all">
                
                {{-- Terminal Header --}}
                <div class="relative border-b border-white/5 bg-slate-950/40 p-12 text-center">
                    <div class="mx-auto mb-8 flex h-20 w-20 items-center justify-center rounded-[1.8rem] bg-gradient-to-br from-cyan-400 to-indigo-600 shadow-2xl shadow-cyan-400/20">
                        <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-black tracking-tight text-white uppercase">Join <span class="text-cyan-400">Workspace</span></h1>
                    <p class="mt-3 text-[10px] font-black uppercase tracking-[0.3em] text-slate-500">Inbound Invitation Detected</p>
                </div>

                <div class="p-12 space-y-8">
                    {{-- Invitation Payload --}}
                    <div class="flex flex-col gap-2 rounded-3xl border border-white/5 bg-white/5 p-8 text-center">
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-500">Target Assignment</p>
                        <p class="text-lg font-black text-white uppercase tracking-tight">
                            {{ $invitation->role_name }} @ <span class="text-cyan-400">{{ $invitation->tenant->name }}</span>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('tenant-invitations.store-acceptance', $invitation->token) }}" class="space-y-8">
                        @csrf
                        
                        <div class="space-y-6">
                            {{-- Email Alias --}}
                            <div>
                                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4 mb-3">Operator Identifier</label>
                                <input value="{{ $invitation->email }}" class="w-full rounded-2xl border border-white/5 bg-slate-950/20 px-8 py-5 text-sm font-bold text-slate-500" disabled>
                            </div>

                            {{-- Name Input --}}
                            <div>
                                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4 mb-3">Public Persona</label>
                                <input name="name" value="{{ old('name', $invitation->name) }}" 
                                    class="w-full rounded-2xl border border-white/5 bg-slate-950/50 px-8 py-5 text-sm font-bold text-white placeholder:text-slate-700 transition-all focus:border-cyan-400 focus:bg-slate-950 focus:ring-4 focus:ring-cyan-400/10"
                                    placeholder="Enter your name" required>
                            </div>

                            {{-- Key Input --}}
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4 mb-3">Access Key</label>
                                    <input type="password" name="password" 
                                        class="w-full rounded-2xl border border-white/5 bg-slate-950/50 px-8 py-5 text-sm font-bold text-white placeholder:text-slate-700 transition-all focus:border-cyan-400 focus:bg-slate-950 focus:ring-4 focus:ring-cyan-400/10"
                                        placeholder="••••••••" required>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4 mb-3">Verify Key</label>
                                    <input type="password" name="password_confirmation" 
                                        class="w-full rounded-2xl border border-white/5 bg-slate-950/50 px-8 py-5 text-sm font-bold text-white placeholder:text-slate-700 transition-all focus:border-cyan-400 focus:bg-slate-950 focus:ring-4 focus:ring-cyan-400/10"
                                        placeholder="••••••••" required>
                                </div>
                            </div>
                        </div>

                        {{-- Execution --}}
                        <div class="pt-4">
                            <button type="submit"
                                class="group relative w-full overflow-hidden rounded-[2rem] bg-cyan-400 py-8 text-[11px] font-black uppercase tracking-[0.4em] text-slate-950 shadow-2xl shadow-cyan-400/20 transition-all hover:bg-cyan-300 hover:-translate-y-1 active:translate-y-0">
                                Synchronize with Workspace
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Terminal Footer Decoration --}}
            <div class="mt-12 text-center">
                <p class="text-[9px] font-black uppercase tracking-[0.4em] text-slate-700">Inbound_Connection_Finalize_V4.0</p>
            </div>
        </div>
    </div>
</x-guest-layout>
