<x-guest-layout>
    <div class="min-h-screen transition-colors duration-300 dark:bg-gradient-to-br dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 bg-gradient-to-br from-slate-50 via-slate-100 to-slate-50 flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <!-- Theme Toggle Button -->
            <div class="flex justify-end mb-4">
                <button id="theme-toggle" type="button" class="p-2 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                </button>
            </div>

            <!-- Email Verification Container -->
            <div class="transition-colors duration-300 dark:bg-slate-800 bg-white dark:border-slate-700 border-slate-200 border rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header Section with Logo -->
                <div class="transition-colors duration-300 dark:bg-gradient-to-r dark:from-slate-750 dark:to-slate-800 bg-gradient-to-r from-slate-100 to-slate-200 dark:border-slate-700 px-8 py-8 text-center border-b border-slate-200">
                    <a href="{{ route('dashboard') }}" class="inline-block mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">P</span>
                        </div>
                    </a>
                    <h1 class="text-2xl font-bold dark:text-white text-slate-900">PeopleFlow HRMS</h1>
                    <p class="dark:text-slate-400 text-slate-500 text-sm mt-2">Verify your email</p>
                </div>

                <!-- Body Section -->
                <div class="px-8 py-8 space-y-6">
                    <!-- Info Message -->
                    <div class="p-4 rounded-lg dark:bg-blue-900/20 dark:border-blue-700/50 bg-blue-100 border border-blue-300">
                        <p class="text-sm dark:text-blue-300 text-blue-700">
                            {{ __('Thanks for signing up! Please verify your email address by clicking the link we sent to you. If you didn\'t receive it, we\'ll gladly send you another.') }}
                        </p>
                    </div>

                    <!-- Success Message -->
                    @if (session('status') == 'verification-link-sent')
                        <div class="p-4 rounded-lg dark:bg-emerald-900/20 dark:border-emerald-700/50 bg-emerald-100 border border-emerald-300">
                            <p class="text-sm dark:text-emerald-400 text-emerald-700">
                                {{ __('A new verification link has been sent to the email we have on file.') }}
                            </p>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="space-y-4 pt-4 dark:border-slate-700 border-t border-slate-200">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 dark:text-white dark:hover:shadow-lg dark:hover:shadow-cyan-500/50 bg-gradient-to-r from-cyan-500 to-blue-600 text-white hover:shadow-lg hover:shadow-cyan-500/30">
                                {{ __('Resend Verification Email') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-lg dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600 bg-slate-200 text-slate-700 hover:bg-slate-300 transition font-medium">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
