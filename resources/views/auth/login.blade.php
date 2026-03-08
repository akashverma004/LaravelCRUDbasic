<x-guest-layout>
    @include('hrms.components.public-navbar')

    <div class="min-h-[calc(100vh-57px)] transition-colors duration-300 dark:bg-gradient-to-br dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 bg-gradient-to-br from-slate-50 via-slate-100 to-slate-50 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <form method="POST" action="{{ route('login') }}"
                  class="transition-colors duration-300 dark:bg-slate-800 bg-white dark:border-slate-700 border-slate-200 border rounded-2xl shadow-2xl overflow-hidden">

                {{-- Card Header --}}
                <div class="transition-colors duration-300 dark:bg-gradient-to-r dark:from-slate-800 dark:to-slate-900 bg-gradient-to-r from-slate-100 to-slate-200 dark:border-slate-700 px-8 py-8 text-center border-b border-slate-200">
                    <div class="mx-auto mb-4 w-14 h-14 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-2xl">P</span>
                    </div>
                    <h1 class="text-2xl font-bold dark:text-white text-slate-900">Welcome back</h1>
                    <p class="dark:text-slate-400 text-slate-500 text-sm mt-1">Sign in to your PeopleFlow workspace</p>
                </div>

                {{-- Form Body --}}
                <div class="px-8 py-8 space-y-5">
                    @csrf

                    @if ($errors->any())
                        <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/50">
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $errors->first() }}</p>
                        </div>
                    @endif

                    {{-- Company Code --}}
                    <div>
                        <label for="company" class="block text-sm font-medium dark:text-slate-300 text-slate-700 mb-1.5">Company Code <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input id="company" type="text" name="company" value="{{ old('company') }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder-slate-400 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition"
                            placeholder="e.g. ACME" autocomplete="organization">
                        @error('company')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium dark:text-slate-300 text-slate-700 mb-1.5">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder-slate-400 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition"
                            placeholder="you@company.com" required autofocus autocomplete="username">
                        @error('email')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium dark:text-slate-300 text-slate-700 mb-1.5">Password</label>
                        <input id="password" type="password" name="password"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder-slate-400 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition"
                            placeholder="••••••••" required autocomplete="current-password">
                        @error('password')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="rounded border-slate-300 dark:border-slate-600 text-cyan-600 dark:text-cyan-500 focus:ring-cyan-500 shadow-sm">
                            Remember me
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-cyan-600 dark:text-cyan-400 hover:underline">Forgot password?</a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 px-4 py-3 text-sm font-semibold text-white shadow hover:shadow-cyan-500/30 hover:from-cyan-400 hover:to-blue-500 transition-all duration-200">
                        Sign In →
                    </button>

                    {{-- Footer --}}
                    <p class="text-center text-sm text-slate-500 dark:text-slate-400 border-t border-slate-200 dark:border-slate-700 pt-4">
                        New company?
                        <a href="{{ route('company-signup.create') }}" class="text-cyan-600 dark:text-cyan-400 font-medium hover:underline">Create a workspace</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
