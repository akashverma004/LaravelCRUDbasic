<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password"
                class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                {{ __('Current Password') }}
            </label>

            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                class="w-full rounded-lg border border-gray-300 dark:border-slate-600
                bg-white dark:bg-slate-900
                px-4 py-2
                text-gray-900 dark:text-white
                placeholder-gray-400 dark:placeholder-slate-400
                focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
            />

            @error('current_password', 'updatePassword')
                <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password"
                class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                {{ __('New Password') }}
            </label>

            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                class="w-full rounded-lg border border-gray-300 dark:border-slate-600
                bg-white dark:bg-slate-900
                px-4 py-2
                text-gray-900 dark:text-white
                placeholder-gray-400 dark:placeholder-slate-400
                focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
            />

            @error('password', 'updatePassword')
                <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation"
                class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                {{ __('Confirm Password') }}
            </label>

            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                class="w-full rounded-lg border border-gray-300 dark:border-slate-600
                bg-white dark:bg-slate-900
                px-4 py-2
                text-gray-900 dark:text-white
                placeholder-gray-400 dark:placeholder-slate-400
                focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
            />

            @error('password_confirmation', 'updatePassword')
                <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4 pt-4">

            <button
                type="submit"
                class="px-6 py-2.5 rounded-lg font-semibold text-sm
                bg-gradient-to-r from-cyan-500 to-cyan-600
                text-slate-900
                hover:shadow-lg hover:shadow-cyan-500/30
                transition"
            >
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    class="text-sm text-emerald-600 dark:text-emerald-400"
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                >
                    {{ __('✓ Saved successfully.') }}
                </p>
            @endif

        </div>
    </form>
</section>
