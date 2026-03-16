@extends('hrms.layouts.app')

@section('title', 'Assets - PeopleFlow HRMS')

@section('content')
<div x-data="assetManager()" x-init="init()" class="space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase"><span class="text-cyan-500">Asset</span> Inventory</h1>
            <p class="mt-1 text-[11px] font-medium text-slate-500 uppercase tracking-wide">Manage company equipment and office tools across the grid.</p>
        </div>
        <template x-if="isAdmin">
            <button @click="openAddModal()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Add Asset</span>
            </button>
        </template>
    </div>

    {{-- Asset Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="asset in assets" :key="asset.id">
            <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-xl dark:border-white/5 dark:bg-slate-900/50">
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-cyan-500 group-hover:text-white transition-all dark:bg-white/5 shadow-sm border border-slate-100 dark:border-white/5">
                        <template x-if="asset.category === 'laptop'">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </template>
                        <template x-if="asset.category === 'keys'">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-.999.43-1.563A6 6 0 1121.75 8.25z"></path></svg>
                        </template>
                        <template x-if="!['laptop', 'keys'].includes(asset.category)">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path></svg>
                        </template>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-tight text-slate-900 dark:text-white truncate" x-text="asset.name"></h4>
                        <p class="text-[9px] text-slate-400 font-extrabold uppercase tracking-widest mt-1" x-text="categories[asset.category]"></p>
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-white/5">
                    <div class="flex items-center justify-between">
                         <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status</span>
                         <span class="rounded-lg px-2 py-0.5 text-[8px] font-black uppercase tracking-widest shadow-sm"
                            :class="{
                                'bg-emerald-500 text-white': asset.status === 'available',
                                'bg-cyan-500 text-white': asset.status === 'assigned',
                                'bg-rose-500 text-white': ['damaged', 'lost'].includes(asset.status),
                                'bg-slate-200 text-slate-600 dark:bg-white/10 dark:text-slate-400': ['retired'].includes(asset.status)
                            }" x-text="asset.status"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Registry ID</span>
                        <span class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight" x-text="asset.serial_number || 'UNKNOWN'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Custody</span>
                        <span class="text-[10px] font-black text-cyan-600 dark:text-cyan-400 uppercase tracking-tight" x-text="asset.employee ? asset.employee.full_name : 'RESTRICTED'"></span>
                    </div>
                </div>

                <template x-if="isAdmin">
                    <button @click="editAsset(asset)" class="mt-5 w-full rounded-xl bg-slate-50 border border-slate-100 py-2.5 text-[9px] font-black uppercase tracking-widest text-slate-500 hover:bg-cyan-500 hover:text-white transition-all dark:bg-white/5 dark:border-white/5 dark:text-slate-400 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                        Modify Registry
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

            <div class="flex justify-end gap-3 bg-slate-50 px-5 py-4 dark:bg-white/5">
                <button @click="showAddModal = false" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cancel</button>
                <button @click="saveAsset()" class="rounded-lg bg-slate-900 border border-white/10 px-6 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <span x-text="isEditing ? 'Save Changes' : 'Add Asset'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
