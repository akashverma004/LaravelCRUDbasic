@extends('hrms.layouts.app')

@section('title', 'Assets - PeopleFlow HRMS')

@section('content')
<div x-data="assetManager()" x-init="init()" class="space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Assets</h1>
            <p class="mt-2 text-sm text-slate-500">Manage company equipment and office tools.</p>
        </div>
        <template x-if="isAdmin">
            <button @click="openAddModal()" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Add Asset</span>
            </button>
        </template>
    </div>

    {{-- Asset Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="asset in assets" :key="asset.id">
            <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-slate-800 dark:bg-slate-900/50">
                
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                        <template x-if="asset.category === 'laptop'">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </template>
                        <template x-if="asset.category === 'keys'">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-.999.43-1.563A6 6 0 1121.75 8.25z"></path></svg>
                        </template>
                        <template x-if="!['laptop', 'keys'].includes(asset.category)">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path></svg>
                        </template>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate" x-text="asset.name"></h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider" x-text="categories[asset.category]"></p>
                    </div>
                </div>

                <div class="space-y-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                         <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</span>
                         <span class="rounded px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider"
                            :class="{
                                'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400': asset.status === 'available',
                                'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400': asset.status === 'assigned',
                                'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400': ['damaged', 'lost'].includes(asset.status),
                                'bg-slate-50 text-slate-500 dark:bg-white/5': ['retired'].includes(asset.status)
                            }" x-text="asset.status"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Serial</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="asset.serial_number || 'N/A'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Assigned to</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="asset.employee ? asset.employee.full_name : 'No one'"></span>
                    </div>
                </div>

                <template x-if="isAdmin">
                    <button @click="editAsset(asset)" class="mt-5 w-full rounded-lg bg-slate-50 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:bg-white/5 dark:text-slate-400 dark:hover:bg-white/10 transition-colors">
                        Edit Detail
                    </button>
                </template>
            </div>
        </template>
    </div>

    {{-- Asset Modal --}}
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.away="showAddModal = false" class="w-full max-w-sm rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="isEditing ? 'Edit Asset' : 'New Asset'"></h3>
                <button @click="showAddModal = false" class="text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Asset Name</label>
                    <input type="text" x-model.trim="addForm.name" :disabled="isEditing" placeholder="e.g. Dell Monitor" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 dark:border-slate-700 dark:text-white disabled:bg-slate-50">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <template x-if="!isEditing">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Category</label>
                            <select x-model="addForm.category" class="w-full rounded-lg border border-slate-200 bg-transparent px-2 py-2 text-xs text-slate-900 focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                <template x-for="[key, label] in Object.entries(categories)" :key="key">
                                    <option :value="key" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <div :class="isEditing ? 'col-span-2' : ''">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                        <select x-model="addForm.status" class="w-full rounded-lg border border-slate-200 bg-transparent px-2 py-2 text-xs text-slate-900 focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            <option value="available">Available</option>
                            <option value="assigned">Assigned</option>
                            <option value="damaged">Damaged</option>
                            <option value="lost">Lost</option>
                            <option value="retired">Retired</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Assignee</label>
                    <select x-model="addForm.employee_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                        <option value="">None</option>
                        <template x-for="emp in employees" :key="emp.id">
                            <option :value="emp.id" x-text="emp.full_name"></option>
                        </template>
                    </select>
                </div>

                <template x-if="!isEditing">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Serial Number</label>
                        <input type="text" x-model.trim="addForm.serial_number" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                    </div>
                </template>
            </div>

            <div class="flex justify-end gap-3 bg-slate-50 px-5 py-3 dark:bg-slate-900/50">
                <button @click="showAddModal = false" class="text-xs font-semibold text-slate-400 hover:text-slate-900 transition-colors">Cancel</button>
                <button @click="saveAsset()" class="rounded-lg bg-cyan-500 px-5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-cyan-600">
                    <span x-text="isEditing ? 'Save Changes' : 'Add Asset'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
