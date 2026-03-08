@extends('hrms.layouts.app')

@section('title', 'Company Setup — Step 1 of 2')

@section('content')
<div class="mx-auto max-w-3xl">

    {{-- ── Stepper ─────────────────────────────────────────────── --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Welcome to PeopleFlow 👋</h1>
        <p class="mt-1 text-slate-500 dark:text-slate-400">Let's get your workspace set up in 2 quick steps.</p>

        <div class="mt-6 flex items-center gap-0">
            {{-- Step 1 --}}
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-500 text-sm font-bold text-slate-900">1</div>
                <span class="text-sm font-semibold text-cyan-500">Company Info</span>
            </div>
            <div class="mx-4 h-px flex-1 bg-slate-300 dark:bg-slate-700"></div>
            {{-- Step 2 --}}
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-slate-300 dark:border-slate-600 text-sm font-bold text-slate-400 dark:text-slate-500">2</div>
                <span class="text-sm font-medium text-slate-400 dark:text-slate-500">Departments</span>
            </div>
        </div>
    </div>

    {{-- ── Form ────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-200 dark:border-slate-800 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Company Details</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Basic information about your company workspace.</p>
        </div>

        <form method="POST" action="{{ route('onboarding.store') }}" class="p-6 space-y-6">
            @csrf

            {{-- Company basics --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Company Name <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name', $tenant->name) }}"
                        class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white @error('name') border-red-500 @enderror"
                        required placeholder="Acme Corp">
                    @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Company Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $tenant->email) }}"
                        class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white @error('email') border-red-500 @enderror"
                        required placeholder="hr@acmecorp.com">
                    @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone</label>
                    <input name="phone" value="{{ old('phone', $tenant->phone) }}"
                        class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        placeholder="+91 98765 43210">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Country</label>
                    <select name="country"
                        class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        @foreach($countries as $code => $label)
                            <option value="{{ $code }}" @selected(old('country', $tenant->country ?? 'IN') === $code)>{{ $label }} ({{ $code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Timezone <span class="text-red-500">*</span></label>
                    <input name="timezone" value="{{ old('timezone', $tenant->timezone ?? 'Asia/Kolkata') }}"
                        class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white @error('timezone') border-red-500 @enderror"
                        required placeholder="Asia/Kolkata">
                    @error('timezone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Currency <span class="text-red-500">*</span></label>
                    <input name="currency" value="{{ old('currency', $tenant->currency ?? 'INR') }}"
                        class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white @error('currency') border-red-500 @enderror"
                        required placeholder="INR">
                    @error('currency')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Office Address</label>
                    <textarea name="address" rows="2"
                        class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        placeholder="123 Business Park, Mumbai, MH">{{ old('address', $tenant->address) }}</textarea>
                </div>
            </div>

            {{-- Leave defaults --}}
            <div class="border-t border-slate-200 dark:border-slate-800 pt-5">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Default Leave Limits (days/year)</h3>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Annual</label>
                        <input type="number" min="0" name="annual_limit"
                            value="{{ old('annual_limit', $leavePolicy->annual_limit ?? 15) }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                        @error('annual_limit')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Sick</label>
                        <input type="number" min="0" name="sick_limit"
                            value="{{ old('sick_limit', $leavePolicy->sick_limit ?? 10) }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Casual</label>
                        <input type="number" min="0" name="casual_limit"
                            value="{{ old('casual_limit', $leavePolicy->casual_limit ?? 8) }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Unpaid</label>
                        <input type="number" min="0" name="unpaid_limit"
                            value="{{ old('unpaid_limit', $leavePolicy->unpaid_limit ?? 0) }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-800 pt-5">
                <p class="text-sm text-slate-400">Step 1 of 2</p>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-6 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    Save & Continue →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
