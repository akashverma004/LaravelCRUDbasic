<div>
    @include('hrms.components.public-navbar')

    <div class="relative pt-24 pb-12 px-6">
        <div class="max-w-7xl mx-auto space-y-16">
            {{-- Header --}}
            <div class="text-center max-w-2xl mx-auto">
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-emerald-600 mb-6">
                    Investment
                </div>
                <h1 class="text-5xl font-black tracking-tighter text-slate-900 mb-4 leading-tight uppercase">
                    Transparent <span class="bg-gradient-to-r from-emerald-600 top-blue-600 bg-clip-text text-transparent underline decoration-emerald-100 decoration-8 underline-offset-[12px]">Plan.</span>
                </h1>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-loose">
                    Unlock institutional excellence with our simple, all-inclusive pricing designed for every stage of growth.
                </p>
            </div>

            {{-- Plans Grid --}}
            <div class="grid md:grid-cols-2 gap-10 max-w-5xl mx-auto items-stretch">
                {{-- Growth --}}
                <div class="group relative overflow-hidden rounded-[3.5rem] border-2 border-white bg-white/40 shadow-xl shadow-slate-200/40 backdrop-blur-3xl transition-all p-12 hover:border-emerald-200 flex flex-col items-center text-center">
                    <h3 class="text-2xl font-black text-slate-900 uppercase mb-4 tracking-tight">Growth <span class="text-emerald-500">Tier</span></h3>
                    <div class="flex items-baseline gap-2 mb-10">
                        <span class="text-6xl font-black tracking-tighter text-slate-900">$49</span>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">/ Month</span>
                    </div>
                    <div class="space-y-4 mb-10 text-left w-full">
                        <div class="flex items-center gap-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 pb-3">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            Up to 50 Team Nodes
                        </div>
                        <div class="flex items-center gap-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 pb-3">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            Temporal Tracking Core
                        </div>
                        <div class="flex items-center gap-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 pb-3">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            Personnel Directory
                        </div>
                    </div>
                    <a href="{{ route('company-signup.create') }}" class="w-full rounded-3xl bg-slate-900 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-white shadow-2xl shadow-slate-900/10 transition-all hover:bg-emerald-600 hover:shadow-emerald-600/30 active:scale-95">
                        Initiate Node
                    </a>
                </div>

                {{-- Enterprise --}}
                <div class="group relative overflow-hidden rounded-[3.5rem] bg-slate-950 shadow-[0_40px_100px_rgba(0,0,0,0.5)] transition-all p-12 flex flex-col items-center text-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/10 to-blue-600/10"></div>
                    <h3 class="text-2xl font-black text-white uppercase mb-4 tracking-tight relative z-10">Lattice <span class="text-indigo-400">Tier</span></h3>
                    <div class="flex items-baseline gap-2 mb-10 relative z-10">
                        <span class="text-6xl font-black tracking-tighter text-white">$149</span>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">/ Month</span>
                    </div>
                    <div class="space-y-4 mb-10 text-left w-full relative z-10">
                        <div class="flex items-center gap-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-white/5 pb-3">
                            <svg class="h-4 w-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            Infinite Team Nodes
                        </div>
                        <div class="flex items-center gap-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-white/5 pb-3">
                            <svg class="h-4 w-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            Payroll Integration Engine
                        </div>
                        <div class="flex items-center gap-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-white/5 pb-3">
                            <svg class="h-4 w-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            Advanced Analytics Suite
                        </div>
                    </div>
                    <button class="w-full rounded-3xl bg-white py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-950 shadow-2xl shadow-indigo-500/20 transition-all hover:bg-indigo-400 hover:text-white active:scale-95 relative z-10">
                        Scale Institution
                    </button>
                    <p class="mt-8 text-[9px] font-black uppercase tracking-widest text-slate-600 relative z-10">Institutions with >1000 nodes require Custom SLA.</p>
                </div>
            </div>
        </div>
    </div>

    @include('hrms.components.public-footer')
</div>
