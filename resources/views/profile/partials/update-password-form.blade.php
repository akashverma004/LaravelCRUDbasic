<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-slate-300 mb-2">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-4 py-2 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" autocomplete="current-password" />
            @error('current_password', 'updatePassword')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-slate-300 mb-2">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-4 py-2 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" autocomplete="new-password" />
            @error('password', 'updatePassword')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-4 py-2 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" autocomplete="new-password" />
            @error('password_confirmation', 'updatePassword')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" style="background: linear-gradient(to right, #06b6d4, #0891b2); color: #0f172a; padding: 10px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s;" onmouseover="this.style.boxShadow='0 0 20px rgba(6,182,212,0.4)'" onmouseout="this.style.boxShadow='none'">
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-emerald-400" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">
                    {{ __('✓ Saved successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>
