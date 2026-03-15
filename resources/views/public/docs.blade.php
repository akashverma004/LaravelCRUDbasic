<x-guest-layout title="Documentation - PeopleFlow HRMS">
    @include('hrms.components.public-navbar')

    <div class="relative pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-[250px,1fr] gap-16">
                <!-- Sidebar -->
                <aside class="hidden lg:block space-y-12">
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Introduction</h4>
                        <ul class="space-y-4">
                            <li><a href="#" class="text-xs font-bold text-violet-600">Quick Start Guide</a></li>
                            <li><a href="#" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">Core Concepts</a></li>
                            <li><a href="#" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">Security Architecture</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">User Guides</h4>
                        <ul class="space-y-4">
                            <li><a href="#" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">Admin Dashboard</a></li>
                            <li><a href="#" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">Employee Self-Service</a></li>
                            <li><a href="#" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">Payroll Integration</a></li>
                        </ul>
                    </div>
                </aside>

                <!-- Content -->
                <article class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-emerald-600 mb-8">
                        Knowledge Base
                    </div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-900 mb-8 leading-tight">Quick Start Guide</h1>
                    
                    <div class="prose prose-slate prose-sm max-w-none">
                        <p class="text-lg text-slate-600 leading-relaxed mb-8">
                            Welcome to the PeopleFlow documentation. This guide will help you set up your organization and onboard your first employees in under 10 minutes.
                        </p>

                        <div class="p-8 rounded-[32px] bg-slate-900 text-white mb-12">
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-4">Baseline Installation</h3>
                            <div class="font-mono text-xs text-emerald-400">
                                $ composer create-project peopleflow/hrms my-org
                            </div>
                        </div>

                        <h2 class="text-2xl font-black text-slate-900 mb-4 uppercase tracking-tight">1. Initialize Workspace</h2>
                        <p class="text-slate-500 leading-relaxed mb-8">
                            Head to the <a href="{{ route('company-signup.create') }}" class="text-violet-600 font-bold underline decoration-violet-200 underline-offset-4">registration page</a> to create your company instance. You'll need to provide your corporate email and basic organizational details.
                        </p>

                        <h2 class="text-2xl font-black text-slate-900 mb-4 uppercase tracking-tight">2. Configure Departments</h2>
                        <p class="text-slate-500 leading-relaxed mb-8">
                            Set up your organizational hierarchy by defining departments and reporting lines. This ensures correct access control and routing for leave requests.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </div>
</x-guest-layout>
