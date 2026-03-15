@extends('hrms.layouts.app')

@section('title', 'Directory - PeopleFlow HRMS')

@section('content')
<div
    x-data="employeeDirectory({
        dataUrl: '{{ route('employees.data') }}',
        storeUrl: '{{ route('employees.store') }}',
        deleteUrlBase: '{{ url('/employees') }}',
        storageBase: '{{ asset('storage') }}',
        filters: @js($filters),
        departments: @js($departments->map(fn ($department) => ['id' => $department->id, 'name' => $department->name])->values()),
        roles: @js($roles->map(fn ($role) => ['id' => $role->id, 'name' => $role->display_name ?? ucfirst($role->name)])->values()),
        managers: @js($managers->map(fn ($manager) => ['id' => $manager->id, 'name' => $manager->full_name, 'department' => $manager->department?->name])->values()),
        countries: @js(collect($countries)->map(fn ($name, $code) => ['code' => $code, 'name' => $name])->values()),
        states: @js(collect($states)->map(fn ($name, $code) => ['code' => $code, 'name' => $name])->values())
    })"
    x-init="init()"
    class="space-y-6"
>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Directory</h1>
            <p class="mt-2 text-sm text-slate-500">Find and manage your team members.</p>
        </div>
        @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
            <button @click="openCreateModal()" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Add Person</span>
            </button>
        @endif
    </div>

    {{-- Filters --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input x-model.trim="filters.q" @input.debounce.250ms="fetchData(1)" type="text" placeholder="Name or role..." class="w-full rounded-lg border border-slate-200 bg-transparent pl-9 pr-3 py-1.5 text-xs text-slate-900 focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Department</label>
                <select x-model="filters.department_id" @change="fetchData(1)" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-xs text-slate-900 focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                    <option value="">All</option>
                    <template x-for="department in departments" :key="department.id">
                        <option :value="String(department.id)" x-text="department.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Role</label>
                <select x-model="filters.role_id" @change="fetchData(1)" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-xs text-slate-900 focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                    <option value="">All</option>
                    <template x-for="role in roles" :key="role.id">
                        <option :value="String(role.id)" x-text="role.name"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    {{-- Employee List --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-3 dark:border-slate-800 dark:bg-slate-950/30">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider" x-text="`${meta.total || 0} People`"></p>
            <button @click="fetchData()" class="rounded p-1 text-slate-400 hover:text-cyan-500 transition-colors">
                <svg :class="loading ? 'animate-spin text-cyan-500' : ''" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="p-4 space-y-3" style="display: none;">
            <template x-for="index in 5" :key="index">
                <div class="h-14 animate-pulse rounded-lg bg-slate-50 dark:bg-slate-800/50"></div>
            </template>
        </div>

        {{-- Stream --}}
        <div x-show="!loading && employees.length" class="divide-y divide-slate-100 dark:divide-slate-800">
            <template x-for="employee in employees" :key="employee.id">
                <div class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <template x-if="avatarUrl(employee)">
                                <img :src="avatarUrl(employee)" :alt="employee.full_name" class="h-9 w-9 rounded-lg object-cover bg-white">
                            </template>
                            <template x-if="!avatarUrl(employee)">
                                <div class="h-9 w-9 flex items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                    <span x-text="employee.full_name.charAt(0)"></span>
                                </div>
                            </template>
                            <div class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border-2 border-white dark:border-slate-900" :class="employee.status === 'active' ? 'bg-emerald-500' : 'bg-slate-400'"></div>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="employee.full_name"></h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] text-slate-500 truncate" x-text="employee.job_title"></span>
                                <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                                <span class="text-[10px] font-bold text-cyan-500" x-text="employee.department_name"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a :href="employee.show_url" class="rounded px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors">
                            Manage
                        </a>
                        @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                            <button @click="deleteEmployee(employee)" :disabled="deletingId === employee.id" class="rounded px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-rose-500 hover:bg-rose-50 transition-colors">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty --}}
        <div x-show="!loading && !employees.length" class="py-12 text-center bg-slate-50 dark:bg-slate-900/20" style="display: none;">
            <p class="text-xs font-medium text-slate-500">No people found.</p>
        </div>

        {{-- Pagination --}}
        <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-5 py-3 dark:border-slate-800 dark:bg-slate-950/30">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter" x-text="`Page ${meta.current_page || 1} of ${meta.last_page || 1}`"></span>
            <div class="flex items-center gap-1">
                <button @click="fetchData(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="px-3 py-1 text-[10px] font-bold text-slate-600 disabled:opacity-30">Prev</button>
                <button @click="fetchData(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="px-3 py-1 text-[10px] font-bold text-slate-600 disabled:opacity-30">Next</button>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.away="closeCreateModal()" class="w-full max-w-lg rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0">
            
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">New Employee</h3>
                <button @click="closeCreateModal()" class="text-slate-400 hover:text-slate-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-5 overflow-y-auto max-h-[70vh] space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Full Name</label>
                        <input x-model.trim="form.full_name" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white" placeholder="John Smith">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Email</label>
                        <input x-model.trim="form.email" type="email" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Password</label>
                        <input x-model="form.password" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Phone</label>
                        <input x-model.trim="form.phone" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Job Title</label>
                        <input x-model.trim="form.job_title" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Department</label>
                        <select x-model="form.department_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            <option value="">Choose...</option>
                            <template x-for="department in departments" :key="department.id">
                                <option :value="String(department.id)" x-text="department.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Manager</label>
                        <select x-model="form.manager_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            <option value="">Choose...</option>
                            <template x-for="manager in managers" :key="manager.id">
                                <option :value="String(manager.id)" x-text="manager.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">App Role</label>
                        <select x-model="form.role_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            <option value="">Choose...</option>
                            <template x-for="role in roles" :key="role.id">
                                <option :value="String(role.id)" x-text="role.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Type</label>
                        <select x-model="form.employment_type" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            <option value="full-time">Full Time</option>
                            <option value="part-time">Part Time</option>
                            <option value="contract">Contract</option>
                            <option value="intern">Intern</option>
                        </select>
                    </div>
                </div>

                <template x-if="formErrors.length">
                    <div class="rounded-lg bg-rose-50 p-3 border border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20">
                        <template x-for="error in formErrors" :key="error">
                             <div class="text-[10px] text-rose-600 font-bold" x-text="error"></div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="flex justify-end gap-3 bg-slate-50 px-5 py-3 dark:bg-slate-900/50">
                <button @click="closeCreateModal()" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">Cancel</button>
                <button @click="submitCreate()" :disabled="saving" class="rounded-lg bg-cyan-500 px-5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-cyan-600 disabled:opacity-50">
                    <span x-text="saving ? 'Saving...' : 'Add Employee'"></span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
