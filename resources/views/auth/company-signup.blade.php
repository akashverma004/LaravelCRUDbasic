<x-guest-layout>
    @include('hrms.components.public-navbar')

    <div class="min-h-[calc(100vh-57px)] transition-colors duration-300 dark:bg-gradient-to-br dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 bg-gradient-to-br from-slate-50 via-slate-100 to-slate-50 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-3xl">
            <form method="POST" action="{{ route('company-signup.store') }}"
                  class="transition-colors duration-300 dark:bg-slate-800 bg-white dark:border-slate-700 border-slate-200 border rounded-2xl shadow-2xl overflow-hidden">
                @csrf

                {{-- Header --}}
                <div class="transition-colors duration-300 dark:bg-gradient-to-r dark:from-slate-800 dark:to-slate-900 bg-gradient-to-r from-slate-100 to-slate-200 dark:border-slate-700 px-8 py-7 text-center border-b border-slate-200">
                    <div class="mx-auto mb-3 w-12 h-12 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">P</span>
                    </div>
                    <h1 class="text-2xl font-bold dark:text-white text-slate-900">Create Company Workspace</h1>
                    <p class="dark:text-slate-400 text-slate-500 text-sm mt-1">Set up your HRMS and first admin account in one step.</p>
                </div>

                <div class="px-8 py-8 grid gap-5 md:grid-cols-2">

                    @if ($errors->any())
                        <div class="md:col-span-2 rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-700/50 px-4 py-3 text-sm text-red-600 dark:text-red-400">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Company Details --}}
                    <div class="md:col-span-2">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Company Details</h2>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5 dark:text-slate-300 text-slate-700">Company Name <span class="text-red-500">*</span></label>
                        <input name="company_name" value="{{ old('company_name') }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
                            placeholder="Acme Corporation" required>
                        @error('company_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5 dark:text-slate-300 text-slate-700">Company Code <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input name="company_code" value="{{ old('company_code') }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm font-mono uppercase dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
                            placeholder="ACME">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5 dark:text-slate-300 text-slate-700">Company Email <span class="text-red-500">*</span></label>
                        <input type="email" name="company_email" value="{{ old('company_email') }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
                            placeholder="hr@acme.com" required>
                        @error('company_email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5 dark:text-slate-300 text-slate-700">Company Phone</label>
                        <input name="company_phone" value="{{ old('company_phone') }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
                            placeholder="+91 98765 43210">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5 dark:text-slate-300 text-slate-700">Country</label>
                        <select name="country"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 transition">
                            @foreach($countries as $code => $name)
                                <option value="{{ $code }}" @selected(old('country', 'IN') === $code)>{{ $name }} ({{ $code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5 dark:text-slate-300 text-slate-700">Timezone <span class="text-red-500">*</span></label>
                        <input name="timezone" value="{{ old('timezone', 'Asia/Kolkata') }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
                            placeholder="Asia/Kolkata" required>
                        @error('timezone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Admin --}}
                    <div class="md:col-span-2 border-t border-slate-200 dark:border-slate-700 pt-4">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Primary Admin Account</h2>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5 dark:text-slate-300 text-slate-700">Admin Name <span class="text-red-500">*</span></label>
                        <input name="admin_name" value="{{ old('admin_name') }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
                            placeholder="John Doe" required>
                        @error('admin_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5 dark:text-slate-300 text-slate-700">Admin Email <span class="text-red-500">*</span></label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
                            placeholder="john@acme.com" required>
                        @error('admin_email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5 dark:text-slate-300 text-slate-700">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
                            placeholder="Minimum 8 characters" required>
                        @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5 dark:text-slate-300 text-slate-700">Confirm Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
                            placeholder="Re-enter password" required>
                    </div>

                    {{-- Submit --}}
                    <div class="md:col-span-2 flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('login') }}" class="text-sm text-slate-500 dark:text-slate-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                            ← Back to Login
                        </a>
                        <button type="submit"
                            class="rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow hover:shadow-cyan-500/30 hover:from-cyan-400 hover:to-blue-500 transition-all duration-200">
                            Create Workspace →
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
