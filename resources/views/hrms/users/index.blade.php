@extends('hrms.layouts.app')

@section('title', 'User Management - PeopleFlow HRMS')

@php
    $userItems = $users->getCollection()->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->map(fn ($role) => [
                'id' => $role->id,
                'display_name' => $role->display_name,
                'name' => $role->name,
            ])->values(),
            'update_url' => route('users.update-roles', $user),
            'assign_url' => route('users.assign-role', $user),
            'remove_url' => route('users.remove-role', $user),
        ];
    })->values();

    $roleOptions = $roles->map(fn ($role) => [
        'id' => $role->id,
        'display_name' => $role->display_name,
        'name' => $role->name,
    ])->values();
@endphp

@section('content')
<div
    x-data="userRoleManager({
        users: @js($userItems),
        roles: @js($roleOptions)
    })"
    class="space-y-8"
>
    {{-- Universal Notification --}}
    <div 
        x-show="toast.show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-4 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-4 opacity-0 scale-95"
        class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-xs font-bold text-white shadow-2xl backdrop-blur-xl dark:bg-slate-800/90"
        x-cloak
    >
        <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="h-2 w-2 rounded-full animate-pulse"></div>
        <span x-text="toast.message"></span>
    </div>

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">User <span class="text-cyan-500">Management</span></h1>
            <p class="mt-1 text-[11px] font-medium text-slate-500">Manage system users and assign roles.</p>
        </div>
    </div>

    {{-- Users List --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50">
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">User</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Assigned Roles</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <template x-for="user in users" :key="user.id">
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        <span x-text="user.name.charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="truncate text-sm font-bold text-slate-900 dark:text-white" x-text="user.name"></h3>
                                        <p class="mt-0.5 text-xs text-slate-500 truncate" x-text="user.email"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="role in user.roles" :key="`${user.id}-${role.id}`">
                                        <span class="inline-flex rounded-md bg-cyan-50 border border-cyan-100 px-2.5 py-1 text-[10px] font-semibold text-cyan-700 capitalize dark:border-cyan-500/20 dark:bg-cyan-500/10 dark:text-cyan-400" x-text="role.display_name"></span>
                                    </template>
                                    <template x-if="!user.roles.length">
                                        <span class="text-xs italic text-slate-400">No roles assigned</span>
                                    </template>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="openModal(user)" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-slate-600 shadow-sm transition-all hover:bg-slate-50 hover:text-cyan-600 dark:border-white/5 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-cyan-400">
                                    Manage Roles
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="users.length === 0">
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-sm text-slate-500">No users found.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-100 px-6 py-4 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Assign Roles Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="closeModal()" class="w-full max-w-lg rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh]">
            
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Manage Roles</h3>
                    <p class="text-xs text-slate-500 mt-1" x-text="selectedUser ? `For ${selectedUser.name}` : ''"></p>
                </div>
                <button @click="closeModal()" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6">
                <div x-show="errorMessage" class="mb-6 rounded-xl bg-rose-50 p-4 border border-rose-200 dark:bg-rose-500/10 dark:border-rose-500/20" style="display: none;">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span class="text-sm font-semibold text-rose-800 dark:text-rose-300" x-text="errorMessage"></span>
                    </div>
                </div>
                
                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Available Roles</h4>
                
                <div class="space-y-3">
                    <template x-for="role in roles" :key="role.id">
                        <label class="flex items-center justify-between cursor-pointer rounded-xl border border-slate-200 bg-slate-50 p-4 transition-all hover:border-cyan-300 dark:border-slate-800 dark:bg-slate-900/50 dark:hover:border-cyan-700">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white capitalize" x-text="role.display_name"></p>
                                <p class="mt-0.5 text-xs text-slate-500 font-mono" x-text="role.name"></p>
                            </div>
                            <div class="relative flex h-5 w-5 shrink-0 items-center justify-center rounded border border-slate-300 bg-white transition-all dark:border-slate-600 dark:bg-slate-800">
                                <input type="checkbox" :value="role.id" x-model="selectedRoles" class="peer absolute inset-0 opacity-0 cursor-pointer">
                                <div class="absolute inset-0 rounded flex items-center justify-center peer-checked:bg-cyan-500 peer-checked:border-cyan-500 transition-colors">
                                    <svg class="h-3.5 w-3.5 text-white opacity-0 peer-checked:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                </div>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <div class="flex flex-shrink-0 items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 dark:border-white/5 dark:bg-white/5 items-center">
                <button @click="closeModal()" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Cancel
                </button>
                <button @click="saveRoles()" :disabled="saving" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-6 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 disabled:opacity-50 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <span x-show="!saving" class="flex items-center gap-2">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        Save Changes
                    </span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Processing
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
