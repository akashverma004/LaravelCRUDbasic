@extends('hrms.layouts.app')

@section('title', 'Setup Workspace - Step 1')

@section('content')
<div class="max-w-4xl mx-auto py-4 px-4">
    <div class="mb-4 p-4 rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-900 shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4xKSIvPjwvc3ZnPg==')] [mask-image:linear-gradient(to_bottom,white,transparent)]"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-black text-white uppercase tracking-tight">Workspace <span class="text-indigo-400">Setup</span></h1>
                <p class="text-[9px] font-black uppercase tracking-widest text-indigo-200/80 mt-0.5">Configure core organization settings (Step 1/2)</p>
            </div>
            <div class="hidden sm:flex items-center gap-2 bg-white/10 p-1.5 rounded-xl border border-white/10 backdrop-blur-md">
                <div class="px-3 py-1.5 bg-indigo-500 rounded-lg shadow-sm border border-indigo-400 line-clamp-1 whitespace-nowrap">
                    <span class="text-[8px] font-black text-white uppercase tracking-widest">1. General</span>
                </div>
                <div class="px-3 py-1.5 opacity-50 line-clamp-1 whitespace-nowrap">
                    <span class="text-[8px] font-black text-white uppercase tracking-widest">2. Structure</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-white/10 overflow-hidden">
        <form action="{{ route('onboarding.store') }}" method="POST" class="p-5">
            @csrf
            
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50/50 border border-rose-100 dark:bg-rose-950/20 dark:border-rose-900/50">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 shrink-0 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <h3 class="text-[10px] font-black uppercase text-rose-600 tracking-widest">Multiple Discrepancies Found</h3>
                            <ul class="mt-2 list-inside list-disc text-xs font-bold text-rose-500 space-y-1">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-6">
                <!-- Org Details -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100 dark:border-white/5">
                        <div class="p-1 rounded bg-indigo-50 dark:bg-indigo-500/10">
                            <svg class="w-3 h-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Organization Profile</h2>
                    </div>

                    <div class="grid md:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="ml-1 text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Entity Name</label>
                            <input name="name" type="text" value="{{ old('name', $tenant->name) }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-black text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white transition-all uppercase" placeholder="ACME INC." required autofocus>
                        </div>
                        <div class="space-y-1">
                            <label class="ml-1 text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Contact Email</label>
                            <input name="email" type="email" value="{{ old('email', $tenant->email) }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-black text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white transition-all lowercase" placeholder="hello@company.com" required>
                        </div>
                        <div class="space-y-1 md:col-span-2">
                            <label class="ml-1 text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Corporate Address</label>
                            <input name="address" type="text" value="{{ old('address', $tenant->address) }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-black text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white transition-all uppercase" placeholder="Street, City, Zip">
                        </div>
                        <div class="space-y-1">
                            <label class="ml-1 text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Phone Number</label>
                            <input name="phone" type="text" value="{{ old('phone', $tenant->phone) }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-black text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white transition-all" placeholder="+1 234 567 8900">
                        </div>
                    </div>
                </div>

                <!-- Locale -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100 dark:border-white/5">
                        <div class="p-1 rounded bg-emerald-50 dark:bg-emerald-500/10">
                            <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Localization</h2>
                    </div>

                    <div class="grid md:grid-cols-3 gap-3">
                        <div class="space-y-1">
                            <label class="ml-1 text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Headquarters</label>
                            <select name="country" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-black text-slate-900 shadow-sm appearance-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white uppercase">
                                @foreach($countries as $code => $countryName)
                                    <option value="{{ $code }}" @selected(old('country', $tenant->country) == $code)>{{ $countryName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="ml-1 text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Base Timezone</label>
                            <input name="timezone" type="text" value="{{ old('timezone', $tenant->timezone) }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-black text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white uppercase" placeholder="America/New_York" required>
                        </div>
                        <div class="space-y-1">
                            <label class="ml-1 text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Primary Currency</label>
                            <input name="currency" type="text" value="{{ old('currency', $tenant->currency) }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-black text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white uppercase" placeholder="USD" required>
                        </div>
                    </div>
                </div>

                <!-- Global Quotas -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100 dark:border-white/5">
                        <div class="p-1 rounded bg-amber-50 dark:bg-amber-500/10">
                            <svg class="w-3 h-3 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Base Leave Quotas</h2>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="space-y-1 border border-slate-100 dark:border-white/5 p-3 rounded-xl bg-slate-50 dark:bg-white/5">
                            <label class="block text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Annual</label>
                            <input name="annual_limit" type="number" min="0" value="{{ old('annual_limit', optional($leavePolicy)->annual_limit ?? 15) }}" class="w-full rounded border-0 bg-white px-2 py-1.5 text-xs font-black text-slate-900 shadow-sm focus:ring-2 focus:ring-indigo-500 dark:bg-slate-900 dark:text-white text-center" required>
                        </div>
                        <div class="space-y-1 border border-slate-100 dark:border-white/5 p-3 rounded-xl bg-slate-50 dark:bg-white/5">
                            <label class="block text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Sick</label>
                            <input name="sick_limit" type="number" min="0" value="{{ old('sick_limit', optional($leavePolicy)->sick_limit ?? 10) }}" class="w-full rounded border-0 bg-white px-2 py-1.5 text-xs font-black text-slate-900 shadow-sm focus:ring-2 focus:ring-indigo-500 dark:bg-slate-900 dark:text-white text-center" required>
                        </div>
                        <div class="space-y-1 border border-slate-100 dark:border-white/5 p-3 rounded-xl bg-slate-50 dark:bg-white/5">
                            <label class="block text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Casual</label>
                            <input name="casual_limit" type="number" min="0" value="{{ old('casual_limit', optional($leavePolicy)->casual_limit ?? 8) }}" class="w-full rounded border-0 bg-white px-2 py-1.5 text-xs font-black text-slate-900 shadow-sm focus:ring-2 focus:ring-indigo-500 dark:bg-slate-900 dark:text-white text-center" required>
                        </div>
                        <div class="space-y-1 border border-slate-100 dark:border-white/5 p-3 rounded-xl bg-slate-50 dark:bg-white/5">
                            <label class="block text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Unpaid</label>
                            <input name="unpaid_limit" type="number" min="0" value="{{ old('unpaid_limit', optional($leavePolicy)->unpaid_limit ?? 0) }}" class="w-full rounded border-0 bg-white px-2 py-1.5 text-xs font-black text-slate-900 shadow-sm focus:ring-2 focus:ring-indigo-500 dark:bg-slate-900 dark:text-white text-center" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-white/5 flex justify-end">
                <button type="submit" class="relative group overflow-hidden rounded-lg bg-slate-900 dark:bg-white px-6 py-2.5 text-[9px] font-black uppercase tracking-[0.3em] text-white dark:text-slate-900 shadow-xl shadow-slate-300 dark:shadow-none hover:shadow-2xl hover:-translate-y-[1px] transition-all active:scale-95">
                    <span class="relative z-10 flex items-center gap-1.5">
                        Next Phase: Structure
                        <svg class="w-3 h-3 translate-x-0 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </span>
                    <div class="absolute inset-0 bg-indigo-600 dark:bg-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
