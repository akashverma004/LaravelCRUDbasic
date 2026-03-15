@extends('hrms.layouts.app')

@section('title', 'Departments - PeopleFlow HRMS')

@section('content')
<div
    x-data="departmentDirectory({
        dataUrl: '{{ route('departments.data') }}',
        storeUrl: '{{ route('departments.store') }}',
        deleteUrlBase: '{{ url('/departments') }}',
        canManage: @js(Auth::user()->hasAnyRole(['admin', 'hr_manager'])),
        employees: @js($employees->map(fn ($employee) => ['id' => $employee->id, 'name' => $employee->full_name])->values())
    })"
    x-init="init()"
    class="space-y-6"
>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Departments</h1>
            <p class="mt-2 text-sm text-slate-500">Manage your company's teams and their structure.</p>
        </div>
        @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
            <button @click="openCreateModal()" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Add Department</span>
            </button>
        @endif
    </div>

    {{-- Grid --}}
    <div class="relative min-h-[300px]">
        {{-- Loading --}}
        <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="n in 6" :key="n">
                <div class="h-40 animate-pulse rounded-xl bg-slate-50 dark:bg-slate-800/50"></div>
            </template>
        </div>

        {{-- Units --}}
        <div x-show="!loading && departments.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" style="display: none;">
            <template x-for="department in departments" :key="department.id">
                <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400" x-text="department.code"></div>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                    </div>

                    <a :href="department.show_url" class="text-base font-bold text-slate-900 dark:text-white hover:text-cyan-500 transition-colors" x-text="department.name"></a>
                    
                    <div class="mt-4 flex gap-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Manager</p>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="department.lead_name || 'Unassigned'"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Total</p>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="department.employees_count"></p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-2" x-show="canManage" style="display: none;">
                        <a :href="department.show_url" class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 hover:text-cyan-600 transition-colors">Details</a>
                        <button @click="deleteDepartment(department)" :disabled="deletingId === department.id" class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-rose-500 hover:text-rose-700 transition-colors">Delete</button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty --}}
        <div x-show="!loading && !departments.length" class="py-20 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 dark:bg-slate-900/20 dark:border-slate-800" style="display: none;">
            <p class="text-sm font-medium text-slate-500">No departments found.</p>
        </div>
    </div>

    {{-- Create Modal --}}
    @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.away="closeCreateModal()" class="w-full max-w-sm rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">New Department</h3>
                <button @click="closeCreateModal()" class="text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Name</label>
                    <input x-model.trim="form.name" type="text" placeholder="e.g. Sales" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Code</label>
                    <input x-model.trim="form.code" type="text" placeholder="SLS" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white uppercase">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Manager</label>
                    <select x-model="form.lead_employee_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                        <option value="">Select...</option>
                        <template x-for="employee in employees" :key="employee.id">
                            <option :value="String(employee.id)" x-text="employee.name"></option>
                        </template>
                    </select>
                </div>

                <template x-if="formErrors.length">
                    <div class="rounded-lg bg-rose-50 p-3 border border-rose-100 dark:bg-rose-500/10">
                        <template x-for="error in formErrors" :key="error">
                             <div class="text-[10px] font-bold text-rose-600" x-text="error"></div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="flex justify-end gap-3 bg-slate-50 px-5 py-3 dark:bg-slate-900/50">
                <button @click="closeCreateModal()" class="text-xs font-semibold text-slate-400 hover:text-slate-900 transition-colors">Cancel</button>
                <button @click="submitCreate()" :disabled="saving" class="rounded-lg bg-cyan-500 px-5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-cyan-600 disabled:opacity-50">
                    <span x-text="saving ? 'Saving...' : 'Add Department'"></span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
