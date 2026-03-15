@extends('hrms.layouts.app')

@section('title', 'Users - Platform Administration')

@section('content')
<div
    x-data="tenantUserManager({
        dataUrl: '{{ route('tenant-users.data') }}',
        storeUrl: '{{ route('tenant-users.store') }}',
        inviteUrl: '{{ route('tenant-users.invite') }}'
    })"
    x-init="init()"
    class="space-y-8"
>
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Users</h1>
            <p class="mt-2 text-sm text-slate-500">Manage user access, roles, and invitations.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openInviteModal()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                <span>Invite User</span>
            </button>
            <button @click="openCreateModal()" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-cyan-600 dark:text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Add User</span>
            </button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- User List --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/30">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Active Users</h2>
                    <p class="text-xs text-slate-500" x-text="`${meta.total || 0} users total`"></p>
                </div>
                <button @click="fetchData()" class="rounded-lg p-2 text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300 transition-colors">
                    <svg :class="loading ? 'animate-spin text-cyan-500' : ''" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>

            {{-- Loading State --}}
            <div x-show="loading" class="space-y-4 p-6" style="display: none;">
                <template x-for="n in 5" :key="n">
                    <div class="h-16 animate-pulse rounded-xl bg-slate-100 dark:bg-slate-800/50"></div>
                </template>
            </div>

            <div x-show="!loading && users.length" class="divide-y divide-slate-100 dark:divide-slate-800">
                <template x-for="user in users" :key="user.id">
                    <div class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="relative shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300" x-text="user.name.substring(0, 1).toUpperCase()"></div>
                                <div class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-emerald-500 dark:border-slate-900"></div>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate" x-text="user.name"></h4>
                                <p class="mt-0.5 text-xs text-slate-500 truncate" x-text="user.email"></p>
                            </div>
                        </div>
                        <div class="flex flex-wrap justify-end gap-2">
                            <template x-for="role in user.roles" :key="role">
                                <span class="rounded-md bg-cyan-50 border border-cyan-100 px-2.5 py-1 text-[10px] font-semibold text-cyan-600 dark:bg-cyan-500/10 dark:border-cyan-500/20 dark:text-cyan-400 capitalize" x-text="role.replace(/_/g, ' ')"></span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Empty State --}}
            <div x-show="!loading && !users.length" class="flex flex-col items-center justify-center py-16 text-center" style="display: none;">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">No Users Found</h3>
                <p class="mt-1 text-xs text-slate-500">There are no active users in this workspace.</p>
            </div>

            {{-- Pagination --}}
            <div class="flex items-center justify-between border-t border-slate-100 px-6 py-4 bg-slate-50 dark:border-slate-800 dark:bg-slate-950/30">
                <p class="text-xs text-slate-500" x-text="`Page ${meta.current_page} of ${meta.last_page}`"></p>
                <div class="flex gap-2">
                    <button @click="fetchData(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 transition-colors">
                         Previous
                    </button>
                    <button @click="fetchData(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 transition-colors">
                         Next
                    </button>
                </div>
            </div>
        </div>

        {{-- Invitations --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden self-start">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/30">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Pending Invitations</h2>
                <p class="mt-0.5 text-xs text-slate-500">Awaiting user response</p>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                <template x-if="!invitations.length">
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <p class="text-xs text-slate-500">No pending invitations.</p>
                    </div>
                </template>
                <template x-for="invitation in invitations" :key="invitation.id">
                    <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="min-w-0 pr-2">
                                <p class="text-sm font-bold text-slate-900 dark:text-white truncate" x-text="invitation.email"></p>
                                <p class="mt-0.5 text-[10px] text-indigo-500 dark:text-indigo-400 capitalize" x-text="invitation.role_name.replace(/_/g, ' ')"></p>
                            </div>
                            <span class="shrink-0 rounded bg-slate-100 px-2 py-1 text-[10px] font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-400" x-text="`Exp: ${invitation.expires_at}`"></span>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" readonly :value="invitation.accept_url" class="flex-1 w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950/50">
                            <button @click="navigator.clipboard.writeText(invitation.accept_url); toast.show = true; toast.message = 'Link copied!'" class="shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition-colors">
                                Copy
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    
    {{-- Add User Modal --}}
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/50 backdrop-blur-sm" x-transition>
        <div @click.away="closeModal('create')" class="relative w-full max-w-sm overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
            
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Add User</h3>
                <button @click="closeModal('create')" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400 mb-1">Name</label>
                    <input x-model.trim="userForm.name" type="text" placeholder="John Doe" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400 mb-1">Email</label>
                    <input x-model.trim="userForm.email" type="email" placeholder="john@example.com" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400 mb-1">Password</label>
                    <input x-model="userForm.password" type="password" placeholder="Minimum 8 characters" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400 mb-1">Role</label>
                    <select x-model="userForm.role_name" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 appearance-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white cursor-pointer">
                        <template x-for="role in roles" :key="role.id">
                            <option :value="role.name" x-text="role.label" class="dark:bg-slate-900"></option>
                        </template>
                    </select>
                </div>
                
                <template x-if="userErrors.length">
                    <div class="rounded-lg bg-rose-50 p-4 border border-rose-200 dark:bg-rose-500/10 dark:border-rose-500/20">
                        <template x-for="error in userErrors" :key="error">
                            <div class="text-xs text-rose-600 dark:text-rose-400 font-medium" x-text="error"></div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-4 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50">
                <button @click="closeModal('create')" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </button>
                <button @click="submitUser()" :disabled="savingUser" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-5 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600 disabled:opacity-50">
                    <span x-text="savingUser ? 'Saving...' : 'Save User'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Invite Modal --}}
    <div x-show="showInviteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/50 backdrop-blur-sm" x-transition>
        <div @click.away="closeModal('invite')" class="relative w-full max-w-sm overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
            
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Invite User</h3>
                <button @click="closeModal('invite')" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400 mb-1">Name (Optional)</label>
                    <input x-model.trim="inviteForm.name" type="text" placeholder="John Doe" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400 mb-1">Email</label>
                    <input x-model.trim="inviteForm.email" type="email" placeholder="john@example.com" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400 mb-1">Role</label>
                    <select x-model="inviteForm.role_name" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 appearance-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white cursor-pointer">
                        <template x-for="role in roles" :key="role.id">
                            <option :value="role.name" x-text="role.label" class="dark:bg-slate-900"></option>
                        </template>
                    </select>
                </div>
                
                <template x-if="inviteErrors.length">
                     <div class="rounded-lg bg-rose-50 p-4 border border-rose-200 dark:bg-rose-500/10 dark:border-rose-500/20">
                        <template x-for="error in inviteErrors" :key="error">
                            <div class="text-xs text-rose-600 dark:text-rose-400 font-medium" x-text="error"></div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-4 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50">
                <button @click="closeModal('invite')" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </button>
                <button @click="submitInvite()" :disabled="savingInvite" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-5 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600 disabled:opacity-50">
                    <span x-text="savingInvite ? 'Creating...' : 'Create Invite'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
