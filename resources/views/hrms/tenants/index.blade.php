@extends('hrms.layouts.app')

@section('title', 'Workspaces - Platform Administration')

@section('content')
<div
    x-data="tenantDirectory({
        dataUrl: '{{ route('tenants.data') }}',
        storeUrl: '{{ route('tenants.store') }}',
        updateUrlBase: '{{ url('/platform/tenants') }}',
        deleteUrlBase: '{{ url('/platform/tenants') }}',
        filters: {
            q: '{{ request('q') }}',
            status: '{{ request('status') }}',
            page: {{ request('page', 1) }}
        }
    })"
    x-init="init()"
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
                    Workspaces
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Manage platform organizations effectively.
                </p>
            </div>
            <button @click="openCreateModal()" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                <span>New Workspace</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="flex-1 space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Search</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-cyan-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input x-model.trim="filters.q" @input.debounce.250ms="fetchData(1)" type="text" placeholder="Search by name, key, or domain..." class="w-full rounded-xl border border-slate-200 bg-transparent pl-10 pr-4 py-2.5 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white">
                </div>
            </div>
            <div class="w-full lg:w-48 space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Status</label>
                <select x-model="filters.status" @change="fetchData(1)" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white appearance-none">
                    <option value="" class="dark:bg-slate-900">All</option>
                    <option value="active" class="dark:bg-slate-900">Active</option>
                    <option value="inactive" class="dark:bg-slate-900">Inactive</option>
                </select>
            </div>
            <div class="flex items-center gap-3">
                 <button @click="fetchData(1)" class="w-full lg:w-auto inline-flex justify-center items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                    Apply Filters
                 </button>
            </div>
        </div>
    </div>

    {{-- Grid --}}
    <div class="relative min-h-[400px]">
        {{-- Loading Sequence --}}
        <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="n in 6" :key="n">
                <div class="h-48 animate-pulse rounded-2xl bg-slate-100 dark:bg-slate-800/50"></div>
            </template>
        </div>

        {{-- Entity Stream --}}
        <div x-show="!loading && tenants.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" style="display: none;">
            <template x-for="tenant in tenants" :key="tenant.id">
                <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700 text-lg font-bold dark:bg-cyan-500/10 dark:text-cyan-400" x-text="tenant.code"></div>
                            <div class="min-w-0">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white truncate" x-text="tenant.name"></h3>
                                <p class="text-xs text-slate-500 truncate mt-0.5" x-text="tenant.email || 'No email'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 py-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                         <div class="flex items-center justify-between text-xs">
                             <span class="text-slate-500 font-semibold">Status</span>
                             <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 font-semibold" 
                                :class="tenant.is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400'">
                                <div class="h-1.5 w-1.5 rounded-full bg-current" :class="tenant.is_active ? 'animate-pulse' : ''"></div>
                                <span x-text="tenant.is_active ? 'Active' : 'Inactive'"></span>
                             </span>
                         </div>
                         <div class="flex items-center justify-between text-xs">
                             <span class="text-slate-500 font-semibold">Domain Pointer</span>
                             <span class="text-slate-700 dark:text-slate-300 font-medium font-mono" x-text="tenant.slug"></span>
                         </div>
                    </div>

                    <div class="mt-2 flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="text-xs font-semibold text-slate-500">
                             <span x-text="tenant.currency || 'USD'"></span> / <span x-text="tenant.timezone || 'UTC'"></span>
                        </div>
                        <button @click="openEditModal(tenant)" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                            Edit
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Pagination --}}
        <div x-show="!loading && meta.last_page > 1" class="mt-8 flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-800" style="display: none;">
            <p class="text-xs text-slate-500" x-text="`Page ${meta.current_page} of ${meta.last_page}`"></p>
            <div class="flex gap-2">
                <button @click="fetchData(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 transition-colors">Previous</button>
                <button @click="fetchData(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 transition-colors">Next</button>
            </div>
        </div>

        {{-- Empty State --}}
        <div x-show="!loading && !tenants.length" class="flex flex-col items-center justify-center py-20 bg-slate-50 rounded-2xl border border-slate-200 border-dashed dark:bg-slate-900/50 dark:border-slate-800" style="display: none;">
            <svg class="h-12 w-12 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">No Workspaces Found</h3>
        </div>
    </div>

    {{-- Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="closeModal()" class="flex w-full max-w-2xl flex-col max-h-[90vh] overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="mode === 'edit' ? 'Edit Workspace' : 'New Workspace'"></h3>
                </div>
                <button @click="closeModal()" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="overflow-y-auto p-6 space-y-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Workspace Name</label>
                        <input x-model.trim="form.name" type="text" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" placeholder="Workspace Name">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Unit Key</label>
                        <input x-model.trim="form.code" type="text" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" placeholder="CODE">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Domain Slug</label>
                        <input x-model.trim="form.slug" type="text" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" placeholder="my-workspace">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Admin Email</label>
                        <input x-model.trim="form.email" type="email" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" placeholder="admin@domain.com">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-400">Currency Protocol</label>
                        <input x-model.trim="form.currency" type="text" class="w-full rounded-xl border border-slate-200 bg-transparent px-4 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:text-white" placeholder="USD">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 cursor-pointer hover:bg-slate-100 transition-colors dark:border-slate-800 dark:bg-slate-900/50 dark:hover:bg-slate-900">
                            <input x-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-600/20 dark:border-slate-600 dark:bg-slate-800">
                            <div>
                                <span class="block text-sm font-bold text-slate-900 dark:text-white">Active Status</span>
                                <span class="block text-xs text-slate-500 mt-0.5">Toggle availability of this workspace</span>
                            </div>
                        </label>
                    </div>
                </div>

                <template x-if="formErrors.length">
                    <div class="rounded-lg bg-rose-50 p-4 border border-rose-200 dark:bg-rose-500/10 dark:border-rose-500/20">
                        <template x-for="error in formErrors" :key="error">
                            <p class="text-xs font-medium text-rose-600 dark:text-rose-400" x-text="error"></p>
                        </template>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-900/50">
                <button @click="closeModal()" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </button>
                <button @click="submitTenant()" :disabled="saving" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-6 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600 disabled:opacity-50">
                    <span x-show="saving" class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-r-white"></span>
                    <span x-text="mode === 'edit' ? 'Save Changes' : 'Create Workspace'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
