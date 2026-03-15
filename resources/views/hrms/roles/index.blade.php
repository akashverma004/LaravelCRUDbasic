@extends('hrms.layouts.app')

@section('title', 'Role Management - PeopleFlow HRMS')

@php
    $roleItems = $roles->map(function ($role) {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name,
            'description' => $role->description,
            'permissions' => $role->permissions->map(fn ($permission) => [
                'id' => $permission->id,
                'display_name' => $permission->display_name,
                'module' => $permission->module,
            ])->values(),
            'users_count' => $role->users()->count(),
            'users_url' => route('roles.users', $role),
            'update_url' => route('roles.update', $role),
            'delete_url' => route('roles.destroy', $role),
        ];
    })->values();

    $permissionGroups = $permissionsByModule->map(function ($items, $module) {
        return [
            'module' => $module ?: 'general',
            'permissions' => $items->map(fn ($permission) => [
                'id' => $permission->id,
                'display_name' => $permission->display_name,
            ])->values(),
        ];
    })->values();
@endphp

@section('content')
<div x-data="roleManager({
        roles: @js($roleItems),
        permissionGroups: @js($permissionGroups),
        storeUrl: '{{ route('roles.store') }}'
    })"
    class="space-y-8"
>
    {{-- Toast Notification --}}
    <div
        x-show="toast.show"
        x-transition
        class="fixed bottom-6 right-6 z-50 rounded-xl bg-slate-900 p-4 shadow-xl dark:bg-white"
        style="display: none;"
    >
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg" :class="toast.type === 'success' ? 'bg-emerald-500/20 text-emerald-300 dark:text-emerald-600' : 'bg-rose-500/20 text-rose-300 dark:text-rose-600'">
                <template x-if="toast.type === 'success'">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </template>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-100 dark:text-slate-900" x-text="toast.message"></p>
            </div>
        </div>
    </div>

    {{-- Header Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900/50">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-cyan-500/10 blur-[60px]"></div>
        <div class="absolute -bottom-20 -left-20 h-48 w-48 rounded-full bg-indigo-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white lg:text-4xl">
                    Role <span class="text-cyan-600 dark:text-cyan-400">Management</span>
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Define roles and configure system permissions for users.
                </p>
            </div>
            <button @click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                <span>Create Role</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            </button>
        </div>
    </div>

    {{-- Roles Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <template x-for="role in roles" :key="role.id">
            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-slate-800 dark:bg-slate-900/50">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white capitalize truncate" x-text="role.display_name"></h2>
                        <span class="inline-flex items-center gap-1.5 mt-1 rounded-md bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                            <span x-text="role.name"></span>
                        </span>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 flex-col items-center justify-center rounded-xl bg-slate-50 border border-slate-100 dark:border-slate-800 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <span class="text-sm font-bold tabular-nums" x-text="role.users_count"></span>
                        <span class="text-[8px] font-semibold uppercase text-slate-500">Users</span>
                    </div>
                </div>
                
                <div class="flex-1 py-4 border-t border-slate-100 dark:border-slate-800">
                    <p class="text-xs font-semibold text-slate-500 mb-3">Permissions</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="permission in role.permissions.slice(0, 5)" :key="`${role.id}-${permission.id}`">
                            <span class="rounded-md bg-slate-50 border border-slate-100 px-2.5 py-1 text-[10px] font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-400" x-text="permission.display_name"></span>
                        </template>
                        <template x-if="role.permissions.length > 5">
                            <span class="rounded-md bg-cyan-50 border border-cyan-100 px-2.5 py-1 text-[10px] font-semibold text-cyan-600 dark:border-cyan-500/20 dark:bg-cyan-500/10 dark:text-cyan-400" x-text="`+${role.permissions.length - 5} more`"></span>
                        </template>
                        <template x-if="role.permissions.length === 0">
                            <span class="text-xs text-slate-400 italic">No permissions assigned</span>
                        </template>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a :href="role.users_url" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300 transition-colors">
                        View Users
                    </a>
                    <div class="flex gap-2">
                        <button @click="openEdit(role)" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">Edit</button>
                        <button @click="removeRole(role)" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 transition-colors hover:bg-rose-50 dark:border-rose-500/20 dark:text-rose-500 dark:hover:bg-rose-500/10">Delete</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Role Form Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="closeModal()" class="flex w-full max-w-3xl flex-col max-h-[90vh] overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="form.id ? 'Edit Role' : 'Create Role'"></h3>
                    <p class="text-xs text-slate-500 mt-1">Configure role details and permissions.</p>
                </div>
                <button @click="closeModal()" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                <div x-show="errorMessage" class="rounded-xl bg-rose-50 p-4 border border-rose-200 dark:bg-rose-500/10 dark:border-rose-500/20" style="display: none;">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span class="text-sm font-semibold text-rose-800 dark:text-rose-300" x-text="errorMessage"></span>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400 mb-1">Role Name</label>
                            <input x-model="form.display_name" type="text" placeholder="e.g. HR Manager" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2.5 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400 mb-1">Key (Internal)</label>
                            <input x-model="form.name" type="text" placeholder="e.g. hr_manager" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2.5 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400 mb-1">Description</label>
                        <textarea x-model="form.description" rows="5" placeholder="Describe the purpose and access level of this role..." class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white"></textarea>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 pb-2 mb-4 dark:border-slate-800">Permissions</h4>
                    
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <template x-for="group in permissionGroups" :key="group.module">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/50">
                                <h5 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3 capitalize" x-text="group.module"></h5>
                                <div class="space-y-2.5">
                                    <template x-for="permission in group.permissions" :key="permission.id">
                                        <label class="flex items-start gap-3 cursor-pointer group/perm">
                                            <div class="relative flex h-5 w-5 shrink-0 items-center justify-center rounded border border-slate-300 bg-white transition-all dark:border-slate-600 dark:bg-slate-800">
                                                <input type="checkbox" :value="permission.id" x-model="form.permissions" class="peer absolute inset-0 opacity-0 cursor-pointer">
                                                <div class="absolute inset-0 rounded flex items-center justify-center peer-checked:bg-cyan-500 peer-checked:border-cyan-500 transition-colors">
                                                    <svg class="h-3.5 w-3.5 text-white opacity-0 peer-checked:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                                </div>
                                            </div>
                                            <span class="text-xs font-medium text-slate-600 dark:text-slate-400" x-text="permission.display_name"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-900/50">
                <button @click="closeModal()" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </button>
                <button @click="saveRole()" :disabled="saving" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-6 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600 disabled:opacity-50">
                    <span x-show="saving" class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-r-white"></span>
                    <span x-text="saving ? 'Saving...' : 'Save Role'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
