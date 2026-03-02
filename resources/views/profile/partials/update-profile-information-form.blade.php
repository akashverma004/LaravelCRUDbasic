<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-medium text-slate-300 mb-2">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-4 py-2 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @error('name')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-4 py-2 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 rounded-lg bg-amber-900/20 border border-amber-700/50">
                    <p class="text-sm text-amber-300">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="inline-block ml-2 text-amber-400 hover:text-amber-300 underline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-emerald-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" style="background: linear-gradient(to right, #06b6d4, #0891b2); color: #0f172a; padding: 10px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s;" onmouseover="this.style.boxShadow='0 0 20px rgba(6,182,212,0.4)'" onmouseout="this.style.boxShadow='none'">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-emerald-400" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">
                    {{ __('✓ Saved successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>
