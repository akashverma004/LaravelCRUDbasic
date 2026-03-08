<x-guest-layout>
    @include('hrms.components.public-navbar')

    <div class="min-h-[calc(100vh-57px)] transition-colors duration-300 dark:bg-gradient-to-br dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 bg-gradient-to-br from-slate-50 via-slate-100 to-slate-50 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            
            <div class="transition-colors duration-300 dark:bg-slate-800 bg-white dark:border-slate-700 border-slate-200 border rounded-2xl shadow-2xl overflow-hidden p-8">
                
                <div class="text-center mb-6">
                    <div class="mx-auto mb-4 w-14 h-14 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Security Notice
                    </h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        For security reasons, you must change the temporary password provided by your company before continuing.
                    </p>
                </div>

                @if (session('warning'))
                    <div class="mb-6 rounded-lg border border-amber-500/50 bg-amber-50 dark:bg-amber-950/30 p-4">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-amber-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm font-medium text-amber-800 dark:text-amber-200">
                                {{ session('warning') }}
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.force-change.store') }}" class="space-y-5">
                    @csrf

                    {{-- Current Password --}}
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Current Temporary Password
                        </label>
                        <div class="mt-1">
                            <input id="current_password" name="current_password" type="password" required
                                class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-900 px-3 py-2 text-sm text-slate-900 dark:text-white placeholder-slate-400 transition focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 @error('current_password') border-red-500 ring-1 ring-red-500 @enderror">
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                            New Private Password
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-900 px-3 py-2 text-sm text-slate-900 dark:text-white placeholder-slate-400 transition focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 @error('password') border-red-500 ring-1 ring-red-500 @enderror">
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Confirm New Password
                        </label>
                        <div class="mt-1">
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-900 px-3 py-2 text-sm text-slate-900 dark:text-white placeholder-slate-400 transition focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        </div>
                    </div>

                    <button type="submit" class="mt-6 flex w-full justify-center rounded-lg bg-cyan-500 px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm hover:bg-cyan-400 transition-colors">
                        Update Password & Continue →
                    </button>
                    
                </form>

                <div class="mt-6 border-t border-slate-200 dark:border-slate-700 pt-6">
                    <form method="POST" action="{{ route('logout') }}" class="flex justify-center">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-800 dark:hover:text-white transition">
                            Sign out instead
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>
