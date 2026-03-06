<section class="space-y-6">
    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 text-white px-6 py-2.5 rounded-lg border-none cursor-pointer font-semibold text-sm transition hover:bg-red-700"
    >
        {{ __('Delete Account') }}
    </button>

    <div x-data="{ isOpen: false }" :class="isOpen ? 'block' : 'hidden'" @keydown.escape.window="isOpen = false">
        <div class="fixed inset-0 z-50 flex items-center justify-center" x-show="isOpen">

            <div class="fixed inset-0 bg-black/60" @click="isOpen = false"></div>

            <div class="relative bg-slate-800 rounded-xl border border-slate-700 w-full max-w-md mx-auto p-6 shadow-xl">
                <h2 class="text-lg font-medium text-red-300 mb-4">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>

                <p class="text-sm text-slate-400 mb-4">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}
                </p>

                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('Password') }}
                        </label>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="w-full rounded-lg border border-slate-600 bg-slate-900 px-4 py-2 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500 transition"
                            placeholder="{{ __('Enter your password') }}"
                        />

                        @error('password', 'userDeletion')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-4 justify-end">
                        <button
                            type="button"
                            @click="isOpen = false"
                            class="px-6 py-2 rounded-lg bg-slate-700 text-slate-300 hover:bg-slate-600 transition font-medium"
                        >
                            {{ __('Cancel') }}
                        </button>

                        <button
                            type="submit"
                            class="bg-red-600 text-white px-6 py-2.5 rounded-lg border-none cursor-pointer font-semibold transition hover:bg-red-700"
                        >
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine.js modal trigger -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('open-modal', function(e) {
                if (e.detail === 'confirm-user-deletion') {
                    document.querySelector('[x-data*="isOpen"]').dispatchEvent(new CustomEvent('click', { detail: 'open' }));
                }
            });
        });
    </script>
</section>
