@extends('hrms.layouts.app')

@section('title', 'Tenant Users')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold dark:text-white text-slate-900">Users & Invitations</h1>
    <p class="text-slate-600 dark:text-slate-400">Create users directly or invite teammates to join this workspace.</p>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h2 class="mb-4 text-lg font-semibold dark:text-white text-slate-900">Create User</h2>
        <form method="POST" action="{{ route('tenant-users.store') }}" class="space-y-3">
            @csrf
            <input name="name" placeholder="Name" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            <input type="email" name="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            <input type="password" name="password" placeholder="Password" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            <select name="role_name" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-cyan-500 px-4 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Create User</button>
        </form>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h2 class="mb-4 text-lg font-semibold dark:text-white text-slate-900">Invite User</h2>
        <form method="POST" action="{{ route('tenant-users.invite') }}" class="space-y-3">
            @csrf
            <input name="name" placeholder="Name (optional)" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            <input type="email" name="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            <select name="role_name" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-cyan-500 px-4 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Generate Invite Link</button>
        </form>
    </div>
</div>

<div class="mt-8 rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
    <div class="border-b border-slate-200 px-4 py-3 dark:border-slate-800">
        <h3 class="font-semibold">Workspace Users</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-100 dark:bg-slate-800">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Roles</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr class="border-t border-slate-200 dark:border-slate-800">
                    <td class="px-4 py-3">{{ $user->name }}</td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $users->links() }}</div>
</div>

<div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
    <h3 class="mb-3 font-semibold">Pending Invitations</h3>
    <div class="space-y-3">
        @forelse($invitations as $inv)
            <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-800">
                <p class="text-sm font-medium">{{ $inv->email }} ({{ $inv->role_name }})</p>
                <p class="mt-1 text-xs text-slate-500">Invite URL: {{ route('tenant-invitations.accept', $inv->token) }}</p>
                <p class="text-xs text-slate-500">Expires: {{ optional($inv->expires_at)->format('d M Y H:i') }}</p>
            </div>
        @empty
            <p class="text-sm text-slate-500">No pending invitations.</p>
        @endforelse
    </div>
</div>
@endsection
