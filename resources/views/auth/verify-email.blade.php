<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <!-- Email Verification Container -->
            <div class="bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header Section with Logo -->
                <div class="bg-gradient-to-r from-slate-750 to-slate-800 px-8 py-8 text-center border-b border-slate-700">
                    <a href="{{ route('dashboard') }}" class="inline-block mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">P</span>
                        </div>
                    </a>
                    <h1 class="text-2xl font-bold text-white">PeopleFlow HRMS</h1>
                    <p class="text-slate-400 text-sm mt-2">Verify your email</p>
                </div>

                <!-- Body Section -->
                <div class="px-8 py-8 space-y-6">
                    <!-- Info Message -->
                    <div class="p-4 rounded-lg bg-blue-900/20 border border-blue-700/50">
                        <p class="text-sm text-blue-300">
                            {{ __('Thanks for signing up! Please verify your email address by clicking the link we sent to you. If you didn\'t receive it, we\'ll gladly send you another.') }}
                        </p>
                    </div>

                    <!-- Success Message -->
                    @if (session('status') == 'verification-link-sent')
                        <div class="p-4 rounded-lg bg-emerald-900/20 border border-emerald-700/50">
                            <p class="text-sm text-emerald-400">
                                {{ __('A new verification link has been sent to the email we have on file.') }}
                            </p>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="space-y-4 pt-4 border-t border-slate-700">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" style="background: linear-gradient(to right, #06b6d4, #0891b2); color: #0f172a; padding: 12px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 14px; width: 100%; transition: all 0.2s;" onmouseover="this.style.boxShadow='0 0 20px rgba(6,182,212,0.4)'" onmouseout="this.style.boxShadow='none'">
                                {{ __('Resend Verification Email') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-lg bg-slate-700 text-slate-300 hover:bg-slate-600 transition font-medium">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
