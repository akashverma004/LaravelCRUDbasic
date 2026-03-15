<x-guest-layout title="Pricing - PeopleFlow HRMS">
    @include('hrms.components.public-navbar')

    <div class="relative pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-blue-600 mb-8">
                Investment
            </div>
            <h1 class="text-6xl font-black tracking-tighter text-slate-900 mb-6 leading-tight">
                Simple, transparent <br/>
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">pricing.</span>
            </h1>
            <p class="text-xl font-medium text-slate-500 max-w-2xl mx-auto mb-20 leading-relaxed">
                Choose the plan that fits your organization's stage. No hidden fees, no complex contracts.
            </p>

            <div class="grid md:grid-cols-3 gap-8 text-left">
                <!-- Plan 1 -->
                <div class="p-10 rounded-[32px] border border-slate-200 bg-white shadow-xl shadow-slate-200/40">
                    <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-2">Growth</h3>
                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-5xl font-black text-slate-900">$2</span>
                        <span class="text-sm font-bold text-slate-400">/employee</span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 mb-8">Perfect for startups scaling their first teams.</p>
                    <button class="w-full py-4 rounded-full border-2 border-slate-900 text-xs font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all mb-8">Get Started</button>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-xs font-bold text-slate-800">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            Up to 50 employees
                        </li>
                        <li class="flex items-center gap-3 text-xs font-bold text-slate-800">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            Core HR & Documents
                        </li>
                    </ul>
                </div>

                <!-- Plan 2 (Pro) -->
                <div class="relative p-10 rounded-[32px] border-2 border-violet-500 bg-white shadow-2xl shadow-violet-200/40">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-violet-500 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Most Popular</div>
                    <h3 class="text-sm font-black text-violet-500 uppercase tracking-widest mb-2">Enterprise</h3>
                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-5xl font-black text-slate-900">$5</span>
                        <span class="text-sm font-bold text-slate-400">/employee</span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 mb-8">Complete control for distributed organizations.</p>
                    <button class="w-full py-4 rounded-full bg-violet-600 text-white text-xs font-black uppercase tracking-widest shadow-xl shadow-violet-500/30 hover:bg-violet-700 transition-all mb-8">Scale Now</button>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-xs font-bold text-slate-800">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            Unlimited employees
                        </li>
                        <li class="flex items-center gap-3 text-xs font-bold text-slate-800">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            Custom Workflows
                        </li>
                        <li class="flex items-center gap-3 text-xs font-bold text-slate-800">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            SSO & Advanced Security
                        </li>
                    </ul>
                </div>

                <!-- Plan 3 (Custom) -->
                <div class="p-10 rounded-[32px] border border-slate-200 bg-white shadow-xl shadow-slate-200/40">
                    <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-2">Custom</h3>
                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-4xl font-black text-slate-900">Custom</span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 mb-8">Tailored solutions for complex global requirements.</p>
                    <button class="w-full py-4 rounded-full border-2 border-slate-900 text-xs font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all mb-8">Contact Sales</button>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-xs font-bold text-slate-800">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            Dedicated Account Manager
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
