<x-guest-layout>
    <div class="min-h-screen transition-colors duration-300 dark:bg-gradient-to-br dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 bg-gradient-to-br from-slate-50 via-slate-100 to-slate-50 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-3xl">
            <form method="POST" action="{{ route('company-signup.store') }}" class="transition-colors duration-300 dark:bg-slate-800 bg-white dark:border-slate-700 border-slate-200 border rounded-2xl shadow-2xl overflow-hidden">
                @csrf
                <div class="transition-colors duration-300 dark:bg-gradient-to-r dark:from-slate-800 dark:to-slate-900 bg-gradient-to-r from-slate-200 to-slate-400 dark:border-slate-700 px-8 py-8 text-center border-b border-slate-200">
                    <h1 class="text-2xl font-bold dark:text-white text-slate-900">Create Company Workspace</h1>
                    <p class="dark:text-slate-400 text-slate-600 text-sm mt-2">Set up your tenant and first admin in one step.</p>
                </div>

                <div class="px-8 py-8 grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <h2 class="text-lg font-semibold dark:text-white text-slate-900">Company Details</h2>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-slate-300 text-slate-700">Company Name</label>
                        <input name="company_name" value="{{ old('company_name') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-slate-300 text-slate-700">Company Code (Optional)</label>
                        <input name="company_code" value="{{ old('company_code') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="ACME">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-slate-300 text-slate-700">Company Email</label>
                        <input type="email" name="company_email" value="{{ old('company_email') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-slate-300 text-slate-700">Company Phone</label>
                        <input name="company_phone" value="{{ old('company_phone') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-slate-300 text-slate-700">Country</label>
                        <select name="country" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @foreach($countries as $code => $name)
                                <option value="{{ $code }}" @selected(old('country', 'IN') === $code)>{{ $name }} ({{ $code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-slate-300 text-slate-700">Timezone</label>
                        <input name="timezone" value="{{ old('timezone', 'Asia/Kolkata') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>

                    <div class="md:col-span-2 mt-2">
                        <h2 class="text-lg font-semibold dark:text-white text-slate-900">Primary Admin</h2>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-slate-300 text-slate-700">Admin Name</label>
                        <input name="admin_name" value="{{ old('admin_name') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-slate-300 text-slate-700">Admin Email</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-slate-300 text-slate-700">Password</label>
                        <input type="password" name="password" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-slate-300 text-slate-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>

                    <div class="md:col-span-2 flex items-center justify-between pt-2">
                        <a href="{{ route('login') }}" class="text-sm text-cyan-600 hover:text-cyan-700 dark:text-cyan-400">Back to Login</a>
                        <button type="submit" class="rounded-lg bg-cyan-500 px-5 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Create Workspace</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
