<div>
    @include('hrms.components.public-navbar')

    <div class="relative pt-24 pb-12 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-violet-100 bg-violet-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-violet-600 mb-6">
                Capabilities
            </div>
            <h1 class="text-5xl font-black tracking-tighter text-slate-900 mb-4 leading-tight uppercase">
                Modern tools for <br/>
                <span class="bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent">modern teams.</span>
            </h1>
            <p class="text-xl font-medium text-slate-500 max-w-2xl mx-auto mb-12 sm:mb-16 leading-relaxed uppercase tracking-widest text-[10px] font-bold">
                Experience the most advanced HRMS features designed to streamline operations and empower your workforce.
            </p>

            <div class="grid md:grid-cols-3 gap-8 text-left">
                <!-- Feature 1 -->
                <div class="group p-8 rounded-[2.5rem] border border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-xl transition-all hover:scale-[1.02] hover:border-violet-200">
                    <div class="h-14 w-14 rounded-2xl bg-violet-600 text-white flex items-center justify-center mb-8 shadow-2xl shadow-violet-500/40 transition-transform group-hover:rotate-6">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-3 uppercase tracking-tight">Core HR <span class="text-violet-500">Suite</span></h3>
                    <p class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-widest">Centralized records, digital onboarding, and automated document management for the modern enterprise.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-8 rounded-[2.5rem] border border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-xl transition-all hover:scale-[1.02] hover:border-indigo-200">
                    <div class="h-14 w-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center mb-8 shadow-2xl shadow-indigo-500/40 transition-transform group-hover:-rotate-6">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-3 uppercase tracking-tight">Time <span class="text-indigo-500">Tracking</span></h3>
                    <p class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-widest">Real-time clock-in/out, automated shift scheduling, and geofencing capabilities with absolute precision.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-8 rounded-[2.5rem] border border-white bg-white/40 shadow-xl shadow-slate-200/50 backdrop-blur-xl transition-all hover:scale-[1.02] hover:border-blue-200">
                    <div class="h-14 w-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center mb-8 shadow-2xl shadow-blue-500/40 transition-transform group-hover:scale-110">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-3 uppercase tracking-tight">Analytics <span class="text-blue-500">Engine</span></h3>
                    <p class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-widest">Continuous feedback loops, OKR tracking, and comprehensive review systems powered by deep intelligence.</p>
                </div>
            </div>
        </div>
    </div>

    @include('hrms.components.public-footer')
</div>
