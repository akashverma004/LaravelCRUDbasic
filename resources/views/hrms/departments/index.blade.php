@extends('hrms.layouts.app')

@section('title', 'Departments - PeopleFlow HRMS')

@section('content')
<div
    x-data="{
        viewMode: 'list',
        ...departmentDirectory({
            dataUrl: '{{ route('departments.data') }}',
            storeUrl: '{{ route('departments.store') }}',
            deleteUrlBase: '{{ url('/departments') }}',
            canManage: @js(Auth::user()->hasAnyRole(['admin', 'hr_manager'])),
            employees: @js($employees->map(fn ($employee) => ['id' => $employee->id, 'name' => $employee->full_name])->values())
        })
    }"
    x-init="init()"
    class="relative space-y-6 pb-8"
>

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Organization</span>
                <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Departments</span>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                Department <span class="text-cyan-500">Directory</span>
            </h1>
            <p class="mt-1 text-[11px] font-medium text-slate-400 uppercase tracking-wide leading-relaxed">
                Manage your company's teams, structures, and leadership roles.
            </p>
        </div>
        <div class="flex items-center gap-3">
            {{-- View Toggle --}}
            <div class="flex items-center rounded-xl bg-slate-100 p-1 dark:bg-white/5">
                <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white shadow-sm text-cyan-500 dark:bg-slate-800' : 'text-slate-400 hover:text-slate-600 dark:hover:text-white'" class="flex h-8 w-8 items-center justify-center rounded-lg transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" /></svg>
                </button>
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white shadow-sm text-cyan-500 dark:bg-slate-800' : 'text-slate-400 hover:text-slate-600 dark:hover:text-white'" class="flex h-8 w-8 items-center justify-center rounded-lg transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z" /></svg>
                </button>
            </div>

            @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                <button @click="openCreateModal()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>New Org Unit</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Content --}}
    <div class="relative min-h-[400px]">
        {{-- Skeleton Loading --}}
        <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="n in 6" :key="n">
                <div class="h-44 animate-pulse rounded-2xl border border-slate-100 bg-white dark:border-white/5 dark:bg-slate-900/50"></div>
            </template>
        </div>

        {{-- Units View Mode Switcher --}}
        <div x-show="!loading && departments.length" style="display: none;">
            
            {{-- Grid View --}}
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5">
                <template x-for="department in departments" :key="department.id">
                    <div class="group relative rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm transition-all hover:border-cyan-500/30 hover:shadow-md dark:border-white/5 dark:bg-slate-900/50">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-[10px] font-black text-slate-500 dark:bg-white/5" x-text="department.code"></div>
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg text-cyan-500 group-hover:scale-110 transition-transform">
                                <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011-1v5m-4 0h4"></path></svg>
                            </div>
                        </div>

                        <a :href="department.show_url" class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight group-hover:text-cyan-500 transition-colors block truncate mb-0.5" x-text="department.name" title="department.name"></a>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400" x-text="department.employees_count + ' Personnel'"></p>
                        
                        <div class="mt-3.5 pt-3 border-t border-slate-50 dark:border-white/5">
                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Lead</p>
                            <p class="text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase truncate" x-text="department.lead_name || 'No Lead Assigned'"></p>
                        </div>

                        <div class="mt-3 flex items-center justify-end gap-1" x-show="canManage">
                            <a :href="department.show_url" class="p-1.5 text-slate-400 hover:text-cyan-500 transition-colors"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg></a>
                            <button @click="deleteDepartment(department)" class="p-1.5 text-slate-400 hover:text-rose-500 transition-colors"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- List View (Stream Layout) --}}
            <div x-show="viewMode === 'list'" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900 border-b-0">
                
                {{-- Stream Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-5 py-2.5 dark:border-white/5 dark:bg-white/5">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest" x-text="`${departments.length || 0} Unit Records`"></p>
                    <button @click="fetchData()" class="rounded-lg p-1.5 text-slate-400 hover:text-cyan-500 hover:bg-cyan-50 dark:hover:bg-white/5 transition-all">
                        <svg :class="loading ? 'animate-spin text-cyan-500' : ''" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                    </button>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-white/5">
                    <template x-for="department in departments" :key="department.id">
                        <div class="group flex items-center justify-between px-5 py-3.5 hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-all">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-50 text-[11px] font-black text-slate-400 dark:bg-white/5 dark:text-cyan-400 ring-2 ring-slate-100 dark:ring-white/5 transition-colors group-hover:text-cyan-500">
                                        <span x-text="department.code"></span>
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white dark:border-slate-900 shadow-sm bg-cyan-500 shadow-cyan-500/20 animate-pulse"></div>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xs font-black uppercase tracking-tight text-slate-900 dark:text-white leading-none">
                                            <a :href="department.show_url" class="hover:text-cyan-500 transition-colors" x-text="department.name"></a>
                                        </h3>
                                    </div>
                                    <div class="mt-1.5 flex items-center gap-2">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Lead:</span>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300" x-text="department.lead_name || 'Unassigned'"></span>
                                        <div class="h-1 w-1 rounded-full bg-slate-200 dark:bg-white/10 mx-1"></div>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400" x-text="department.employees_count + ' Personnel'"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a :href="department.show_url" class="rounded-lg px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/5 transition-all">
                                    Explore
                                </a>
                                @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                                    <button @click="deleteDepartment(department)" class="rounded-lg px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-all opacity-0 group-hover:opacity-100">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        {{-- Empty State --}}
        <div x-show="!loading && !departments.length" class="py-24 text-center border-2 border-dashed border-slate-200 rounded-2xl dark:border-white/5" style="display: none;">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300 dark:bg-white/5">
                 <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
            </div>
            <p class="mt-4 text-[11px] font-black uppercase tracking-widest text-slate-400">No departments cataloged.</p>
        </div>
    </div>

    {{-- Create Modal --}}
    @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.away="closeCreateModal()" class="w-full max-w-sm rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-white/5 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-white/5">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white">New Department</h3>
                <button @click="closeCreateModal()" class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Department Name</label>
                    <input x-model.trim="form.name" type="text" placeholder="e.g. Corporate Relations" class="w-full rounded-xl border border-slate-200 bg-transparent px-3.5 py-2.5 text-xs font-bold focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-white/10 dark:text-white">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Short Code</label>
                    <input x-model.trim="form.code" type="text" placeholder="CRT" class="w-full rounded-xl border border-slate-200 bg-transparent px-3.5 py-2.5 text-xs font-bold focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-white/10 dark:text-white uppercase uppercase-input">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Department Lead</label>
                    <select x-model="form.lead_employee_id" class="w-full rounded-xl border border-slate-200 bg-transparent px-3.5 py-2.5 text-xs font-bold focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-white/10 dark:text-white dark:bg-slate-900">
                        <option value="" class="dark:bg-slate-900 text-slate-400">Select an employee...</option>
                        <template x-for="employee in employees" :key="employee.id">
                            <option :value="String(employee.id)" x-text="employee.name" class="dark:bg-slate-900"></option>
                        </template>
                    </select>
                </div>

                <template x-if="formErrors.length">
                    <div class="rounded-xl bg-rose-50 p-3 border border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20">
                        <template x-for="error in formErrors" :key="error">
                             <div class="text-[9px] font-black text-rose-600 uppercase tracking-tight" x-text="error"></div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-end gap-3 bg-slate-50/50 px-5 py-4 dark:bg-white/[0.02]">
                <button @click="closeCreateModal()" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Discard</button>
                <button @click="submitCreate()" :disabled="saving" class="rounded-xl bg-slate-900 px-5 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 disabled:opacity-50 transition-all dark:bg-white/10 dark:hover:bg-cyan-500">
                    <span x-text="saving ? 'Syncing...' : 'Create Unit'"></span>
                </button>
            </div>
        </div>
    </div>
    @endif

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
</div>
@endsection
