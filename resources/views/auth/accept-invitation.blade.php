<x-guest-layout>
    <div class="min-h-screen transition-colors duration-300 dark:bg-gradient-to-br dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 bg-gradient-to-br from-slate-50 via-slate-100 to-slate-50 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <form method="POST" action="{{ route('tenant-invitations.store-acceptance', $invitation->token) }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                @csrf
                <h1 class="text-2xl font-bold dark:text-white text-slate-900">Accept Invitation</h1>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">You are invited as <strong>{{ $invitation->role_name }}</strong> for <strong>{{ $invitation->tenant->name }}</strong>.</p>

                <div class="mt-5 space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input value="{{ $invitation->email }}" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 dark:border-slate-700 dark:bg-slate-800 dark:text-white" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input name="name" value="{{ old('name', $invitation->name) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password</label>
                        <input type="password" name="password" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    </div>
                </div>

                <button class="mt-5 w-full rounded-lg bg-cyan-500 px-4 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Join Workspace</button>
            </form>
        </div>
    </div>
</x-guest-layout>
