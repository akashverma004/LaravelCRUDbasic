<x-guest-layout title="Features - PeopleFlow HRMS">
    @include('hrms.components.public-navbar')

    <div class="relative pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-violet-100 bg-violet-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-violet-600 mb-8">
                Capabilities
            </div>
            <h1 class="text-6xl font-black tracking-tighter text-slate-900 mb-6 leading-tight">
                Modern tools for <br/>
                <span class="bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent">modern teams.</span>
            </h1>
            <p class="text-xl font-medium text-slate-500 max-w-2xl mx-auto mb-20 leading-relaxed">
                Experience the most advanced HRMS features designed to streamline operations and empower your workforce.
            </p>

            <div class="grid md:grid-cols-3 gap-8 text-left">
                <!-- Feature 1 -->
                <div class="group p-8 rounded-[32px] border border-white/40 bg-white/70 shadow-xl shadow-slate-200/50 backdrop-blur-xl transition-all hover:scale-[1.02]">
                    <div class="h-12 w-12 rounded-2xl bg-violet-500 text-white flex items-center justify-center mb-6 shadow-lg shadow-violet-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-3 uppercase tracking-tight">Core HR</h3>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed">Centralized employee records, digital onboarding, and automated document management.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-8 rounded-[32px] border border-white/40 bg-white/70 shadow-xl shadow-slate-200/50 backdrop-blur-xl transition-all hover:scale-[1.02]">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-500 text-white flex items-center justify-center mb-6 shadow-lg shadow-indigo-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-3 uppercase tracking-tight">Time & Attendance</h3>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed">Real-time clock-in/out, automated shift scheduling, and geofencing capabilities.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-8 rounded-[32px] border border-white/40 bg-white/70 shadow-xl shadow-slate-200/50 backdrop-blur-xl transition-all hover:scale-[1.02]">
                    <div class="h-12 w-12 rounded-2xl bg-blue-500 text-white flex items-center justify-center mb-6 shadow-lg shadow-blue-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-3 uppercase tracking-tight">Performance</h3>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed">Continuous feedback loops, OKR tracking, and comprehensive 360 review systems.</p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
