<div>
    @include('hrms.components.public-navbar')

    <div class="relative pt-24 pb-12 px-6">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-[280px,1fr] gap-12">
            {{-- Navigation --}}
            <div class="hidden lg:block space-y-10">
                <div class="space-y-4">
                    <h5 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 ml-4">Architecture</h5>
                    <div class="flex flex-col gap-2">
                        <a href="#" class="px-6 py-3 rounded-2xl bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-600/20">Lattice Protocol</a>
                        <a href="#" class="px-6 py-3 rounded-2xl bg-white/50 text-slate-500 text-[10px] font-black uppercase tracking-widest hover:bg-indigo-50 hover:text-indigo-600 transition-all">Node Provisioning</a>
                        <a href="#" class="px-6 py-3 rounded-2xl bg-white/50 text-slate-500 text-[10px] font-black uppercase tracking-widest hover:bg-indigo-50 hover:text-indigo-600 transition-all">Identity Services</a>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="space-y-12">
                <div class="space-y-6 max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-indigo-600">
                        Operational Docs
                    </div>
                    <h1 class="text-5xl font-black tracking-tighter text-slate-900 leading-tight uppercase">
                        Master the <span class="bg-gradient-to-r from-indigo-600 top-violet-600 bg-clip-text text-transparent underline decoration-indigo-200 decoration-8 underline-offset-[12px]">Lattice.</span>
                    </h1>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-loose">
                        Comprehensive infrastructure documentation for deploying and scaling your institutional human resources management system.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 gap-8">
                    {{-- Doc Card 1 --}}
                    <div class="group relative overflow-hidden rounded-[2.5rem] border border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-2xl transition-all p-10 hover:border-indigo-200">
                        <div class="flex flex-col gap-6">
                            <div class="h-12 w-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30 transition-transform group-hover:-rotate-6">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.754 18 18.168 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 uppercase">Core <span class="text-indigo-500">Manual</span></h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-loose">Learn basic organizational hierarchy and nodal distribution patterns.</p>
                        </div>
                    </div>

                    {{-- Doc Card 2 --}}
                    <div class="group relative overflow-hidden rounded-[2.5rem] border border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-2xl transition-all p-10 hover:border-violet-200 text-left">
                        <div class="flex flex-col gap-6">
                            <div class="h-12 w-12 rounded-xl bg-violet-600 text-white flex items-center justify-center shadow-lg shadow-violet-500/30 transition-transform group-hover:rotate-6">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 uppercase">Registry <span class="text-violet-500">API</span></h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-loose">Automate your workforce with our high-throughput REST protocol interface.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('hrms.components.public-footer')
</div>
