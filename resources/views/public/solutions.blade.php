<x-guest-layout title="Solutions - PeopleFlow HRMS">
    @include('hrms.components.public-navbar')

    <div class="relative pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-indigo-600 mb-8">
                Industry Specific
            </div>
            <h1 class="text-6xl font-black tracking-tighter text-slate-900 mb-6 leading-tight">
                Tailored for every <br/>
                <span class="bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">sector.</span>
            </h1>
            <p class="text-xl font-medium text-slate-500 max-w-2xl mx-auto mb-20 leading-relaxed">
                Whether you're a fast-growing startup or an established enterprise, PeopleFlow scales with your specific organizational needs.
            </p>

            <div class="grid md:grid-cols-2 gap-12 text-left">
                <!-- Solution 1 -->
                <div class="flex gap-8 items-start p-10 rounded-[40px] bg-white border border-slate-100 shadow-2xl shadow-slate-200/40">
                    <div class="h-16 w-16 shrink-0 rounded-3xl bg-slate-900 text-white flex items-center justify-center text-2xl font-black">
                        01
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 mb-4 uppercase tracking-widest">Tech & Startups</h3>
                        <p class="text-base font-medium text-slate-500 leading-relaxed">Agile workforce management with high-speed onboarding and global remote-team support.</p>
                        <ul class="mt-6 space-y-3">
                            <li class="flex items-center gap-3 text-xs font-bold text-slate-700">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                Global Compliance Engines
                            </li>
                            <li class="flex items-center gap-3 text-xs font-bold text-slate-700">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                Slack & Teams Integration
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Solution 2 -->
                <div class="flex gap-8 items-start p-10 rounded-[40px] bg-white border border-slate-100 shadow-2xl shadow-slate-200/40">
                    <div class="h-16 w-16 shrink-0 rounded-3xl bg-violet-600 text-white flex items-center justify-center text-2xl font-black">
                        02
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 mb-4 uppercase tracking-widest">Manufacturing</h3>
                        <p class="text-base font-medium text-slate-500 leading-relaxed">Robust shift management and workforce safety tracking for large-scale operations.</p>
                        <ul class="mt-6 space-y-3">
                            <li class="flex items-center gap-3 text-xs font-bold text-slate-700">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                Shift Differential Logic
                            </li>
                            <li class="flex items-center gap-3 text-xs font-bold text-slate-700">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                Certification Tracking
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
