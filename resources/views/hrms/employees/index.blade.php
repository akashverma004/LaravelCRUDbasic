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
    class="space-y-6 relative"
>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase"><span class="text-cyan-500">Employee</span> Directory</h1>
            <p class="mt-1 text-[11px] font-medium text-slate-500 uppercase tracking-wide">Find and manage your team members across all protocols.</p>
        </div>
        @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
            <button @click="openCreateModal()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Add Person</span>
            </button>
        @endif
    </div>

    {{-- Filters --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <label class="block text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 mb-1.5 ml-1">Search Registry</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input x-model.trim="filters.q" @input.debounce.250ms="fetchData(1)" type="text" placeholder="Designation, rank or protocol..." class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white">
                </div>
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 mb-1.5 ml-1">Unit</label>
                <select x-model="filters.department_id" @change="fetchData(1)" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white appearance-none">
                    <option value="">All Units</option>
                    <template x-for="department in departments" :key="department.id">
                        <option :value="String(department.id)" x-text="department.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 mb-1.5 ml-1">Designation</label>
                <select x-model="filters.role_id" @change="fetchData(1)" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white appearance-none">
                    <option value="">All Ranks</option>
                    <template x-for="role in roles" :key="role.id">
                        <option :value="String(role.id)" x-text="role.name"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    {{-- Employee List --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900/50 overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-5 py-2.5 dark:border-white/5 dark:bg-white/5">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest" x-text="`${meta.total || 0} Registered Personnel`"></p>
            <button @click="fetchData()" class="rounded-lg p-1.5 text-slate-400 hover:text-cyan-500 hover:bg-cyan-50 dark:hover:bg-white/5 transition-all">
                <svg :class="loading ? 'animate-spin text-cyan-500' : ''" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
            </button>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="p-4 space-y-3" style="display: none;">
            <template x-for="index in 5" :key="index">
                <div class="h-14 animate-pulse rounded-lg bg-slate-50 dark:bg-slate-800/50"></div>
            </template>
        </div>

        {{-- Stream --}}
        <div x-show="!loading && employees.length" class="divide-y divide-slate-100 dark:divide-white/5">
            <template x-for="employee in employees" :key="employee.id">
                <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-white/5 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="relative group/avatar">
                            <template x-if="avatarUrl(employee)">
                                <img :src="avatarUrl(employee)" :alt="employee.full_name" class="h-10 w-10 rounded-xl object-cover bg-white ring-2 ring-slate-100 dark:ring-white/5">
                            </template>
                            <template x-if="!avatarUrl(employee)">
                                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-100 text-[10px] font-black text-slate-600 dark:bg-white/5 dark:text-cyan-400 ring-2 ring-slate-100 dark:ring-white/5">
                                    <span x-text="employee.full_name.charAt(0)"></span>
                                </div>
                            </template>
                            <div class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white dark:border-slate-900 shadow-sm" :class="employee.status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></div>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-black uppercase tracking-tight text-slate-900 dark:text-white leading-none" x-text="employee.full_name"></h3>
                            <div class="mt-1.5 flex items-center gap-2">
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400" x-text="employee.job_title || 'Personnel'"></span>
                                <div class="h-1 w-1 rounded-full bg-slate-200 dark:bg-white/10"></div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400" x-text="employee.department_name || 'Unassigned'"></span>
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
        <div @click.away="closeCreateModal()" class="w-full max-w-4xl rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0">
            
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">New Employee</h3>
                    <p class="text-xs text-slate-500">Fill in all details to create a new record.</p>
                </div>
                <button @click="closeCreateModal()" class="text-slate-400 hover:text-slate-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[80vh]">
                <div class="space-y-8">
                    {{-- Section 1: Core & Account --}}
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 mb-4 pb-2 border-b border-cyan-500/10 dark:text-cyan-400">Section 01 / Core & Account</h4>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Full Name *</label>
                                <input x-model.trim="form.full_name" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white" placeholder="John Smith">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Job Title *</label>
                                <input x-model.trim="form.job_title" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white" placeholder="Design Lead">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Work Email *</label>
                                <input x-model.trim="form.email" type="email" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Temporary Password *</label>
                                <input x-model="form.password" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Department *</label>
                                <select x-model="form.department_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                    <option value="">Choose...</option>
                                    <template x-for="department in departments" :key="department.id">
                                        <option :value="String(department.id)" x-text="department.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Manager</label>
                                <select x-model="form.manager_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                    <option value="">Choose...</option>
                                    <template x-for="manager in managers" :key="manager.id">
                                        <option :value="String(manager.id)" x-text="manager.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">System Role</label>
                                <select x-model="form.role_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                    <option value="">Choose...</option>
                                    <template x-for="role in roles" :key="role.id">
                                        <option :value="String(role.id)" x-text="role.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Employment Type *</label>
                                <select x-model="form.employment_type" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                    <option value="full-time">Full Time</option>
                                    <option value="part-time">Part Time</option>
                                    <option value="contract">Contract</option>
                                    <option value="intern">Intern</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Annual Salary *</label>
                                <input x-model="form.salary" type="number" step="0.01" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Joining Date *</label>
                                <input x-model="form.joined_on" type="date" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status *</label>
                                <select x-model="form.status" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                    <option value="active">Active</option>
                                    <option value="on-leave">On Leave</option>
                                    <option value="resigned">Resigned</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Personal Details --}}
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600 mb-4 pb-2 border-b border-indigo-500/10 dark:text-indigo-400">Section 02 / Personal Details</h4>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Phone *</label>
                                <input x-model.trim="form.phone" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Personal Email</label>
                                <input x-model.trim="form.personal_email" type="email" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Date of Birth</label>
                                <input x-model="form.date_of_birth" type="date" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Gender</label>
                                <select x-model="form.gender" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                    <option value="">Choose...</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="non-binary">Non-binary</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Marital Status</label>
                                <select x-model="form.marital_status" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                    <option value="">Choose...</option>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="widowed">Widowed</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Blood Group</label>
                                <select x-model="form.blood_group" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-1.5 text-[12px] focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                    <option value="">Choose...</option>
                                    <option value="A+">A+</option><option value="A-">A-</option>
                                    <option value="B+">B+</option><option value="B-">B-</option>
                                    <option value="O+">O+</option><option value="O-">O-</option>
                                    <option value="AB+">AB+</option><option value="AB-">AB-</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Location --}}
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 mb-4 pb-2 border-b border-emerald-500/10 dark:text-emerald-400">Section 03 / Location</h4>
                        <div class="grid gap-4 sm:grid-cols-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Country *</label>
                                <select x-model="form.country" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                    <template x-for="c in countries" :key="c.code">
                                        <option :value="c.code" x-text="c.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">State *</label>
                                <select x-model="form.state" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                    <option value="">Choose...</option>
                                    <template x-for="s in states" :key="s.code">
                                        <option :value="s.code" x-text="s.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">City *</label>
                                <input x-model.trim="form.city" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Zip Code</label>
                                <input x-model.trim="form.zip_code" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div class="sm:col-span-4">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Full Address *</label>
                                <textarea x-model.trim="form.address" rows="2" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Identity & Banking --}}
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-rose-600 mb-4 pb-2 border-b border-rose-500/10 dark:text-rose-400">Section 04 / Identity & Banking</h4>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">PAN Number</label>
                                <input x-model.trim="form.pan_number" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Aadhaar Number</label>
                                <input x-model.trim="form.aadhaar_number" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Bank Name</label>
                                <input x-model.trim="form.bank_name" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">A/C Number</label>
                                    <input x-model.trim="form.bank_account_number" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">IFSC Code</label>
                                    <input x-model.trim="form.bank_ifsc" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 5: Emergency Contact --}}
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-amber-600 mb-4 pb-2 border-b border-amber-500/10 dark:text-amber-400">Section 05 / Emergency Contact</h4>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Contact Name</label>
                                <input x-model.trim="form.emergency_contact_name" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Phone</label>
                                <input x-model.trim="form.emergency_contact_phone" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Relationship</label>
                                <input x-model.trim="form.emergency_contact_relationship" type="text" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                            </div>
                        </div>
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
