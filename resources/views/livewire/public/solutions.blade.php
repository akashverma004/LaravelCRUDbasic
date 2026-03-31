<div>
    @include('hrms.components.public-navbar')

    <div class="relative pt-24 pb-12 px-6">
        <div class="max-w-7xl mx-auto space-y-16">
            {{-- Header --}}
            <div class="text-center max-w-2xl mx-auto">
                <div class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-indigo-600 mb-6">
                    Solutions
                </div>
                <h1 class="text-5xl font-black tracking-tighter text-slate-900 mb-4 leading-tight uppercase">
                    Built for <span class="bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent underline decoration-indigo-100 decoration-8 underline-offset-[12px]">scale.</span>
                </h1>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-loose">
                    From agile startups to global enterprises, PeopleFlow scales with your organizational complexity without friction.
                </p>
            </div>

            {{-- Solution Cards --}}
            <div class="grid md:grid-cols-2 gap-10">
                {{-- Startup --}}
                <div class="group relative overflow-hidden rounded-[2.5rem] border-2 border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-2xl transition-all p-10 hover:border-indigo-200">
                    <div class="flex items-start gap-8">
                        <div class="h-14 w-14 shrink-0 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-xl shadow-indigo-500/30 transition-transform group-hover:-rotate-6">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <div class="space-y-4">
                            <h3 class="text-2xl font-black text-slate-900 uppercase">Growth <span class="text-indigo-500">Node</span></h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-loose">Automated onboarding and core HR tools designed to get your startup operational in under 48 hours.</p>
                            <div class="pt-4 flex items-center gap-2 text-indigo-600 group-hover:gap-4 transition-all">
                                <span class="text-[10px] font-black uppercase tracking-widest italic">Explore Growth</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Enterprise --}}
                <div class="group relative overflow-hidden rounded-[2.5rem] border-2 border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-2xl transition-all p-10 hover:border-violet-200">
                    <div class="flex items-start gap-8">
                        <div class="h-14 w-14 shrink-0 rounded-2xl bg-violet-600 text-white flex items-center justify-center shadow-xl shadow-violet-500/30 transition-transform group-hover:rotate-6">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <div class="space-y-4">
                            <h3 class="text-2xl font-black text-slate-900 uppercase">Global <span class="text-violet-500">Lattice</span></h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-loose">Multi-country compliance, complex payroll, and deep analytics for large-scale institutional management.</p>
                            <div class="pt-4 flex items-center gap-2 text-violet-600 group-hover:gap-4 transition-all">
                                <span class="text-[10px] font-black uppercase tracking-widest italic">Request Architect</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('hrms.components.public-footer')
</div>
