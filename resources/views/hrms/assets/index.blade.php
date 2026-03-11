@extends('hrms.layouts.app')

@section('title', 'Asset Management - PeopleFlow HRMS')

@section('content')
<div x-data="assetManager()" x-init="init()">
    
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Asset Inventory</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Track company equipment, serial numbers, and employee assignments.</p>
        </div>
        <template x-if="isAdmin">
            <button @click="openAddModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Register Asset
            </button>
        </template>
    </div>

    {{-- Grid View --}}
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <template x-for="asset in assets" :key="asset.id">
            <div class="group rounded-2xl border border-slate-200 bg-white p-5 transition-shadow hover:shadow-lg dark:border-slate-700 dark:bg-slate-800/50">
                <div class="mb-4 flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-300">
                            <template x-if="asset.category === 'laptop'">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </template>
                            <template x-if="asset.category === 'keys'">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                            </template>
                            <template x-if="!['laptop', 'keys'].includes(asset.category)">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </template>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white" x-text="asset.name"></h4>
                            <p class="text-[10px] uppercase font-bold text-slate-400" x-text="categories[asset.category]"></p>
                        </div>
                    </div>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                        :class="{
                            'bg-green-100 text-green-700 dark:bg-green-500/20': asset.status === 'available',
                            'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20': asset.status === 'assigned',
                            'bg-rose-100 text-rose-700 dark:bg-rose-500/20': ['damaged', 'lost'].includes(asset.status)
                        }" x-text="asset.status"></span>
                </div>

                <div class="space-y-3 mb-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Serial Number</span>
                        <span class="font-mono text-slate-900 dark:text-white" x-text="asset.serial_number || 'N/A'"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Assigned To</span>
                        <span class="font-semibold text-cyan-600 dark:text-cyan-400" x-text="asset.employee ? asset.employee.full_name : 'No one'"></span>
                    </div>
                </div>

                <template x-if="isAdmin">
                    <div class="flex gap-2 border-t border-slate-100 pt-4 dark:border-slate-700">
                        <button @click="editAsset(asset)" class="flex-1 rounded-lg bg-slate-50 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600">Update Status</button>
                    </div>
                </template>
            </div>
        </template>
    </div>

    {{-- Add/Edit Modal (Admin Only) --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="showAddModal = false" class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-md p-8 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 text-slate-900 dark:text-white" x-text="isEditing ? 'Update Asset' : 'Register New Asset'"></h3>
            <div class="space-y-4">
                <template x-if="!isEditing">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Asset Name</label>
                        <input type="text" x-model="addForm.name" placeholder="e.g. MacBook Pro M3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                    </div>
                </template>
                <template x-if="isEditing">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Asset Name</label>
                        <input type="text" x-model="addForm.name" disabled class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                    </div>
                </template>
                <div class="grid grid-cols-2 gap-4">
                    <template x-if="!isEditing">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Category</label>
                            <select x-model="addForm.category" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                <template x-for="[key, label] in Object.entries(categories)" :key="key">
                                    <option :value="key" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Status</label>
                        <select x-model="addForm.status" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            <option value="available">Available</option>
                            <option value="assigned">Assigned</option>
                            <option value="damaged">Damaged</option>
                            <option value="lost">Lost</option>
                            <option value="retired">Retired</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Assign To Employee</label>
                    <select x-model="addForm.employee_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                        <option value="">-- Unassigned --</option>
                        <template x-for="emp in employees" :key="emp.id">
                            <option :value="emp.id" x-text="emp.full_name"></option>
                        </template>
                    </select>
                </div>
                <template x-if="!isEditing">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Serial Number</label>
                        <input type="text" x-model="addForm.serial_number" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                    </div>
                </template>
            </div>
            <div class="mt-8 flex gap-3">
                <button @click="saveAsset()" class="flex-1 rounded-xl bg-cyan-500 py-3 text-sm font-bold text-white hover:bg-cyan-600" x-text="isEditing ? 'Update Status' : 'Save Asset'"></button>
                <button @click="showAddModal = false" class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection
