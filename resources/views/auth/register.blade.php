<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <!-- Register Form Card -->
            <form method="POST" action="{{ route('register') }}" class="bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">
                <!-- Card Header with Logo -->
                <div class="bg-gradient-to-r from-slate-750 to-slate-800 px-8 py-8 text-center border-b border-slate-700">
                    <a href="{{ route('dashboard') }}" class="inline-block mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">P</span>
                        </div>
                    </a>
                    <h1 class="text-2xl font-bold text-white">PeopleFlow HRMS</h1>
                    <p class="text-slate-400 text-sm mt-2">Create your account</p>
                </div>

                <!-- Form Body -->
                <div class="px-8 py-8 space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-300 mb-2">{{ __('Full Name') }}</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" placeholder="John Doe" required autofocus autocomplete="name" />
                        @error('name')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">{{ __('Email Address') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" placeholder="your@email.com" required autocomplete="username" />
                        @error('email')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" placeholder="••••••••" required autocomplete="new-password" />
                        @error('password')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">{{ __('Confirm Password') }}</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" placeholder="••••••••" required autocomplete="new-password" />
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Register Button -->
                    <button type="submit" style="background: linear-gradient(to right, #06b6d4, #0891b2); color: #0f172a; padding: 12px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 14px; width: 100%; transition: all 0.2s;" onmouseover="this.style.boxShadow='0 0 20px rgba(6,182,212,0.4)'" onmouseout="this.style.boxShadow='none'">
                        {{ __('Create Account') }}
                    </button>

                    <!-- Login Link -->
                    <div class="text-center text-sm pt-2 border-t border-slate-700">
                        <a href="{{ route('login') }}" class="text-cyan-400 hover:text-cyan-300 transition">
                            {{ __('Already have an account? Sign in') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
