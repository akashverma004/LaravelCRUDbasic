<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                {{ __('Name') }}
            </label>

            <input
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                class="w-full rounded-lg border border-gray-300 dark:border-slate-600
                bg-white dark:bg-slate-900
                px-4 py-2
                text-gray-900 dark:text-white
                placeholder-gray-400 dark:placeholder-slate-400
                focus:ring-2 focus:ring-cyan-500"
                required
            >
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                {{ __('Email') }}
            </label>

            <input
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                class="w-full rounded-lg border border-gray-300 dark:border-slate-600
                bg-white dark:bg-slate-900
                px-4 py-2
                text-gray-900 dark:text-white
                focus:ring-2 focus:ring-cyan-500"
                required
            >
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                {{ __('Phone') }}
            </label>

            <input
                name="phone"
                type="text"
                value="{{ old('phone', $user->phone ?? '') }}"
                class="w-full rounded-lg border border-gray-300 dark:border-slate-600
                bg-white dark:bg-slate-900 px-4 py-2
                text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500"
            >
        </div>

        <!-- Employee ID -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                {{ __('Employee ID') }}
            </label>

            <input
                name="employee_id"
                type="text"
                value="{{ old('employee_id', $user->employee_id ?? '') }}"
                class="w-full rounded-lg border border-gray-300 dark:border-slate-600
                bg-white dark:bg-slate-900 px-4 py-2
                text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500"
            >
        </div>

        <!-- Department -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                {{ __('Department') }}
            </label>

            <input
                name="department"
                type="text"
                value="{{ old('department', $user->department ?? '') }}"
                class="w-full rounded-lg border border-gray-300 dark:border-slate-600
                bg-white dark:bg-slate-900 px-4 py-2
                text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500"
            >
        </div>

        <!-- Designation -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                {{ __('Designation') }}
            </label>

            <input
                name="designation"
                type="text"
                value="{{ old('designation', $user->designation ?? '') }}"
                class="w-full rounded-lg border border-gray-300 dark:border-slate-600
                bg-white dark:bg-slate-900 px-4 py-2
                text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500"
            >
        </div>

        <!-- Joining Date -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                {{ __('Joining Date') }}
            </label>

            <input
                name="joining_date"
                type="date"
                value="{{ old('joining_date', $user->joining_date ?? '') }}"
                class="w-full rounded-lg border border-gray-300 dark:border-slate-600
                bg-white dark:bg-slate-900 px-4 py-2
                text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500"
            >
        </div>

        <!-- Address -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                {{ __('Address') }}
            </label>

            <textarea
                name="address"
                rows="3"
                class="w-full rounded-lg border border-gray-300 dark:border-slate-600
                bg-white dark:bg-slate-900 px-4 py-2
                text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500"
            >{{ old('address', $user->address ?? '') }}</textarea>
        </div>

        <!-- Save Button -->
        <div class="flex items-center gap-4 pt-4">
            <button
                type="submit"
                class="px-6 py-2.5 rounded-lg font-semibold text-sm
                bg-gradient-to-r from-cyan-500 to-cyan-600
                text-slate-900 hover:shadow-lg hover:shadow-cyan-500/30 transition"
            >
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
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
