@extends('hrms.layouts.app')

@section('title', 'Company Setup — Step 1 of 2')

@section('content')
<div class="mx-auto max-w-3xl">

    {{-- Header Section --}}
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-black tracking-tighter text-slate-900 dark:text-white uppercase">Welcome to <span class="text-cyan-500">PeopleFlow</span> 👋</h1>
        <p class="mt-2 text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">Let's get your workspace set up in 2 quick steps.</p>

        <div class="mt-10 mx-auto max-w-sm">
            <div class="flex items-center justify-between gap-0">
                {{-- Step 1 --}}
                <div class="flex items-center gap-3">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-900 border border-white/10 text-xs font-black text-white shadow-lg shadow-cyan-500/10 dark:bg-white/5 dark:text-cyan-400">1</div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Workspace</span>
                </div>
                <div class="flex-1 mx-4 h-0.5 bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full w-1/2 bg-cyan-500 rounded-full"></div>
                </div>
                {{-- Step 2 --}}
                <div class="flex items-center gap-3">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-xs font-black text-slate-400 dark:border-white/5 dark:bg-white/5">2</div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Structure</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Section --}}
    <div x-data="asyncForm({ followRedirect: true })" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900/50 overflow-hidden">
        <div class="border-b border-slate-100 dark:border-white/5 px-8 py-6 bg-slate-50/50 dark:bg-white/5">
            <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">Workspace Designation</h2>
            <p class="mt-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Basic architectural telemetry for your organization.</p>
        </div>

        <div x-show="toast.show" x-transition class="mx-8 mt-6 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-[10px] font-bold text-white shadow-2xl backdrop-blur-xl dark:bg-slate-800/90" x-cloak>
            <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="h-1.5 w-1.5 rounded-full animate-pulse mr-2 inline-block"></div>
            <span x-text="toast.message"></span>
        </div>
        <div x-show="errorMessage" class="mx-8 mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[10px] font-bold text-rose-600 dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-300" x-cloak>
            <span x-text="errorMessage"></span>
        </div>

        <form x-ref="form" @submit.prevent="submit()" method="POST" action="{{ route('onboarding.store') }}" class="p-6 space-y-6">
            @csrf

            {{-- Company basics --}}
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Company Name <span class="text-rose-500">*</span></label>
                    <input name="name" value="{{ old('name', $tenant->name) }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white @error('name') border-red-500 @enderror"
                        required placeholder="Acme Corp">
                    @error('name')<p class="mt-1.5 text-[9px] font-bold text-rose-400 uppercase tracking-widest ml-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Company Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $tenant->email) }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white @error('email') border-red-500 @enderror"
                        required placeholder="hr@acmecorp.com">
                    @error('email')<p class="mt-1.5 text-[9px] font-bold text-rose-400 uppercase tracking-widest ml-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Phone</label>
                    <input name="phone" value="{{ old('phone', $tenant->phone) }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white"
                        placeholder="+91 98765 43210">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Country</label>
                    <select name="country"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white appearance-none">
                        @foreach($countries as $code => $label)
                            <option value="{{ $code }}" @selected(old('country', $tenant->country ?? 'IN') === $code)>{{ $label }} ({{ $code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Timezone <span class="text-rose-500">*</span></label>
                    <input name="timezone" value="{{ old('timezone', $tenant->timezone ?? 'Asia/Kolkata') }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white @error('timezone') border-red-500 @enderror"
                        required placeholder="Asia/Kolkata">
                    @error('timezone')<p class="mt-1.5 text-[9px] font-bold text-rose-400 uppercase tracking-widest ml-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Currency <span class="text-rose-500">*</span></label>
                    <input name="currency" value="{{ old('currency', $tenant->currency ?? 'INR') }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white @error('currency') border-red-500 @enderror"
                        required placeholder="INR">
                    @error('currency')<p class="mt-1.5 text-[9px] font-bold text-rose-400 uppercase tracking-widest ml-1">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Office Address</label>
                    <textarea name="address" rows="2"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white"
                        placeholder="123 Business Park, Mumbai, MH">{{ old('address', $tenant->address) }}</textarea>
                </div>
            </div>

            {{-- Leave defaults --}}
            <div class="border-t border-slate-100 dark:border-white/5 pt-6">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 ml-1">Default Leave Protocols</h3>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Annual</label>
                        <input type="number" min="0" name="annual_limit"
                            value="{{ old('annual_limit', $leavePolicy->annual_limit ?? 15) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white" required>
                        @error('annual_limit')<p class="mt-1.5 text-[9px] font-bold text-rose-400 uppercase tracking-widest ml-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Sick</label>
                        <input type="number" min="0" name="sick_limit"
                            value="{{ old('sick_limit', $leavePolicy->sick_limit ?? 10) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Casual</label>
                        <input type="number" min="0" name="casual_limit"
                            value="{{ old('casual_limit', $leavePolicy->casual_limit ?? 8) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1">Unpaid</label>
                        <input type="number" min="0" name="unpaid_limit"
                            value="{{ old('unpaid_limit', $leavePolicy->unpaid_limit ?? 0) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white" required>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between border-t border-slate-100 dark:border-white/5 pt-6 mt-4">
                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 leading-none">Step 01 <span class="mx-2">/</span> 02</p>
                <button type="submit" :disabled="saving"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-8 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400 disabled:opacity-50">
                    <span x-text="saving ? 'Processing...' : 'Save & Continue →'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
