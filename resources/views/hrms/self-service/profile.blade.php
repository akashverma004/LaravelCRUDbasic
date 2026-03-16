@extends('hrms.layouts.app')

@section('title', 'My Profile - PeopleFlow HRMS')

@section('content')
<div x-data="selfServiceProfile()" x-init="init()" class="space-y-6 relative">
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

    {{-- Profile Hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-6 py-8 shadow-sm border border-slate-200 dark:border-white/5 dark:bg-slate-900">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/5 blur-[80px] pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-indigo-500/5 blur-[80px] pointer-events-none"></div>
        
        <div class="relative flex flex-col items-center gap-8 lg:flex-row lg:items-center">
            {{-- Photo Container --}}
            <div class="group relative">
                <div class="h-32 w-32 overflow-hidden rounded-full border-4 border-slate-50 shadow-md transition-all group-hover:scale-[1.02] dark:border-slate-800">
                    <template x-if="photoUrl">
                        <img :src="photoUrl" alt="Profile" class="h-full w-full object-cover">
                    </template>
                    <template x-if="!photoUrl">
                        <div class="flex h-full w-full items-center justify-center bg-slate-100 dark:bg-slate-800">
                            <span class="text-4xl font-bold text-slate-400 dark:text-slate-500" x-text="employee.full_name ? employee.full_name.substring(0, 1).toUpperCase() : '?'"></span>
                        </div>
                    </template>
                </div>
                <label class="absolute inset-0 flex cursor-pointer items-center justify-center rounded-full bg-slate-900/50 opacity-0 transition-opacity group-hover:opacity-100 backdrop-blur-sm">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15a2.25 2.25 0 002.25-2.25V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                    <input type="file" @change="uploadPhoto($event)" class="hidden">
                </label>
                <div x-show="uploading" class="absolute -right-2 -top-2 flex h-8 w-8 items-center justify-center rounded-full bg-cyan-500 text-white shadow-lg animate-pulse">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
            </div>

            <div class="flex-1 text-center lg:text-left">
                <div class="flex flex-wrap items-center justify-center gap-4 lg:justify-start">
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white" x-text="employee.full_name"></h2>
                    <span class="rounded-full bg-cyan-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-400" x-text="employee.status"></span>
                </div>
                <p class="mt-2 text-sm font-medium text-slate-500 dark:text-slate-400" x-text="(employee.job_title || 'Employee') + ' • ' + (employee.department || 'Not Assigned')"></p>
                <div class="mt-6 flex flex-wrap items-center justify-center gap-6 lg:justify-start">
                    <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        <span class="text-xs font-medium" x-text="'Joined: ' + (employee.joined_on || 'Unknown')"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Side Information --}}
        <div class="space-y-8">
            {{-- Skills --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/40">
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Skills</h3>
                    <button @click="showSkillForm = !showSkillForm" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                         <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    </button>
                </div>

                {{-- Skill Input --}}
                <div x-show="showSkillForm" x-transition class="mb-6 rounded-xl bg-slate-50 p-4 border border-slate-200 space-y-4 dark:bg-slate-950 dark:border-white/5">
                    <div>
                         <label class="block mb-1 text-[10px] font-semibold uppercase tracking-wider text-slate-500">Skill Name</label>
                         <input type="text" x-model="skillForm.name" placeholder="E.g., Design" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:text-white">
                    </div>
                    <div>
                         <label class="block mb-1 text-[10px] font-semibold uppercase tracking-wider text-slate-500">Proficiency</label>
                         <select x-model="skillForm.proficiency" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 appearance-none dark:border-white/10 dark:bg-slate-900 dark:text-white">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button @click="addSkill()" class="flex-1 rounded-lg bg-cyan-500 py-2 text-xs font-bold text-white hover:bg-cyan-600 transition-colors">Add</button>
                        <button @click="showSkillForm = false" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5">Cancel</button>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <template x-for="skill in employee.skills || []" :key="skill.id">
                        <div class="group flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 dark:border-white/5 dark:bg-slate-950 transition-colors hover:border-cyan-300 dark:hover:border-cyan-700">
                            <div class="h-1.5 w-1.5 rounded-full" :class="{ 'bg-emerald-500': skill.proficiency === 'expert', 'bg-cyan-500': skill.proficiency === 'intermediate', 'bg-slate-400': skill.proficiency === 'beginner' }"></div>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300" x-text="skill.name"></span>
                            <button @click="removeSkill(skill.id)" class="opacity-0 group-hover:opacity-100 transition-opacity text-slate-400 hover:text-rose-500 text-xs ml-1">×</button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Job Details --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/40">
                <h3 class="mb-6 text-sm font-bold text-slate-900 dark:text-white">Job Details</h3>
                <div class="space-y-6">
                    <template x-for="item in [
                        { label: 'Manager', value: employee.manager, icon: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z' },
                        { label: 'Department', value: employee.department, icon: 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-3.75h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z' },
                        { label: 'Employment Type', value: employee.employment_type, icon: 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25' },
                    ]" :key="item.label">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" /></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500" x-text="item.label"></p>
                                <p class="mt-0.5 text-sm font-medium text-slate-900 truncate dark:text-white capitalize" x-text="item.value || 'Not Assigned'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Main Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Tabs --}}
            <div class="flex gap-1.5 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm dark:border-slate-800 dark:bg-slate-900/40 custom-scrollbar">
                <template x-for="t in tabs" :key="t.id">
                    <button @click="activeTab = t.id" 
                        class="flex shrink-0 items-center justify-center rounded-xl px-4 py-1.5 text-[10px] font-black uppercase tracking-widest transition-all"
                        :class="activeTab === t.id ? 'bg-slate-900 text-white dark:bg-white/10 dark:text-cyan-400 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white'"
                        x-text="t.label"></button>
                </template>
            </div>

            {{-- Content --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 dark:border-slate-800 dark:bg-slate-900/40">
                <div class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white" x-text="tabs.find(t => t.id === activeTab)?.label"></h2>
                    
                    <div x-show="['personal', 'emergency', 'identity', 'bank', 'preferences'].includes(activeTab)">
                        <button x-show="!editing" @click="startEditing()" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 border border-slate-200 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-700 shadow-sm transition-all hover:bg-slate-200 dark:border-white/5 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                            <span>Edit Detail</span>
                        </button>
                        <div x-show="editing" class="flex items-center gap-4" style="display: none;">
                            <span class="text-[9px] font-black tracking-widest text-cyan-500 animate-pulse">EDIT MODE</span>
                            <div class="flex gap-2">
                                <button @click="cancelEditing()" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cancel</button>
                                <button @click="saveProfile()" :disabled="saving" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-5 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-xl shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-95 disabled:opacity-50 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                                    <span x-show="!saving" class="flex items-center gap-2">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        Save Changes
                                    </span>
                                    <span x-show="saving" class="flex items-center gap-2">
                                        <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                        Processing
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6">
                    {{-- TAB: Personal --}}
                    <div x-show="activeTab === 'personal'" class="grid gap-6 sm:grid-cols-2 animate-fade-in">
                        @include('hrms.self-service.partials._field', ['field' => 'email', 'label' => 'Email Address', 'readonly' => true])
                        @include('hrms.self-service.partials._field', ['field' => 'personal_email', 'label' => 'Personal Email', 'type' => 'email'])
                        @include('hrms.self-service.partials._field', ['field' => 'phone', 'label' => 'Phone Number'])
                        @include('hrms.self-service.partials._field', ['field' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date'])
                        @include('hrms.self-service.partials._select', ['field' => 'gender', 'label' => 'Gender', 'options' => [
                            'male' => 'Male', 'female' => 'Female', 'non-binary' => 'Non Binary', 'other' => 'Other', 'prefer_not_to_say' => 'Prefer Not To Say'
                        ]])
                        @include('hrms.self-service.partials._select', ['field' => 'marital_status', 'label' => 'Marital Status', 'options' => [
                            'single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed'
                        ]])
                        @include('hrms.self-service.partials._field', ['field' => 'blood_group', 'label' => 'Blood Group'])
                        @include('hrms.self-service.partials._field', ['field' => 'pronouns', 'label' => 'Pronouns'])
                        @include('hrms.self-service.partials._textarea', ['field' => 'bio', 'label' => 'Short Bio', 'span' => 2])
                        @include('hrms.self-service.partials._field', ['field' => 'address', 'label' => 'Address', 'span' => 2])
                    </div>

                    {{-- TAB: Emergency --}}
                    <div x-show="activeTab === 'emergency'" class="grid gap-6 sm:grid-cols-2 animate-fade-in" style="display: none;">
                        @include('hrms.self-service.partials._field', ['field' => 'emergency_contact_name', 'label' => 'Contact Name', 'span' => 2])
                        @include('hrms.self-service.partials._field', ['field' => 'emergency_contact_phone', 'label' => 'Contact Phone'])
                        @include('hrms.self-service.partials._field', ['field' => 'emergency_contact_relationship', 'label' => 'Relationship'])
                    </div>

                    {{-- TAB: Identity --}}
                    <div x-show="activeTab === 'identity'" class="grid gap-6 sm:grid-cols-2 animate-fade-in" style="display: none;">
                        @include('hrms.self-service.partials._field', ['field' => 'pan_number', 'label' => 'PAN Number'])
                        @include('hrms.self-service.partials._field', ['field' => 'aadhaar_number', 'label' => 'Aadhaar Number'])
                        @include('hrms.self-service.partials._field', ['field' => 'passport_number', 'label' => 'Passport Number'])
                        @include('hrms.self-service.partials._field', ['field' => 'passport_expiry', 'label' => 'Passport Expiry', 'type' => 'date'])
                        @include('hrms.self-service.partials._field', ['field' => 'nationality', 'label' => 'Nationality'])
                    </div>

                    {{-- TAB: Bank --}}
                    <div x-show="activeTab === 'bank'" class="grid gap-6 sm:grid-cols-2 animate-fade-in" style="display: none;">
                        @include('hrms.self-service.partials._field', ['field' => 'bank_name', 'label' => 'Bank Name', 'span' => 2])
                        @include('hrms.self-service.partials._field', ['field' => 'bank_account_number', 'label' => 'Account Number'])
                        @include('hrms.self-service.partials._field', ['field' => 'bank_ifsc', 'label' => 'IFSC Code'])
                    </div>

                    {{-- TAB: Preferences --}}
                    <div x-show="activeTab === 'preferences'" class="grid gap-6 sm:grid-cols-2 animate-fade-in" style="display: none;">
                        @include('hrms.self-service.partials._field', ['field' => 'hobbies', 'label' => 'Hobbies'])
                        @include('hrms.self-service.partials._field', ['field' => 'likes', 'label' => 'Interests'])
                        @include('hrms.self-service.partials._field', ['field' => 'food_preference', 'label' => 'Food Preference'])
                        @include('hrms.self-service.partials._field', ['field' => 'linkedin_url', 'label' => 'LinkedIn Profile URL'])
                        @include('hrms.self-service.partials._textarea', ['field' => 'health_issues', 'label' => 'Health Considerations / Notes', 'span' => 2])
                    </div>

                    {{-- TAB: Education --}}
                    <div x-show="activeTab === 'education'" class="space-y-8 animate-fade-in" style="display: none;">
                        <button @click="showEduForm = !showEduForm" class="flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 py-8 text-sm font-black text-slate-400 hover:border-cyan-400 hover:bg-cyan-50/30 hover:text-cyan-600 transition-all dark:border-slate-800 dark:bg-slate-950/40 dark:hover:border-cyan-500/50">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Add Education Qualification
                        </button>

                        {{-- Add Education Form --}}
                        <div x-show="showEduForm" x-transition class="rounded-xl border border-cyan-100 bg-white p-6 shadow-lg dark:border-cyan-500/20 dark:bg-slate-900">
                            <h4 class="text-xs font-black uppercase tracking-widest text-cyan-600 mb-6">Qualification Details</h4>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Degree / Certificate</label>
                                    <input type="text" x-model="eduForm.degree" :class="errors.degree ? 'border-rose-400' : 'border-slate-200'" class="w-full rounded-xl border bg-slate-50 px-4 py-2.5 text-xs font-bold dark:bg-slate-950 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Institution</label>
                                    <input type="text" x-model="eduForm.institution" :class="errors.institution ? 'border-rose-400' : 'border-slate-200'" class="w-full rounded-xl border bg-slate-50 px-4 py-2.5 text-xs font-bold dark:bg-slate-950 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Field of Study</label>
                                    <input type="text" x-model="eduForm.field_of_study" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold dark:bg-slate-950 dark:text-white">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">From Year</label>
                                        <input type="number" x-model="eduForm.year_from" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold dark:bg-slate-950 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">To Year</label>
                                        <input type="number" x-model="eduForm.year_to" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold dark:bg-slate-950 dark:text-white">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button @click="showEduForm = false" class="text-xs font-bold text-slate-400 hover:text-slate-600">Cancel</button>
                                <button @click="addEducation()" class="rounded-xl bg-slate-900 px-6 py-2.5 text-xs font-black text-white hover:bg-cyan-600 transition-colors dark:bg-white dark:text-slate-900">Save qualification</button>
                            </div>
                        </div>
                        
                        <div class="grid gap-6">
                            <template x-for="edu in employee.educations || []" :key="edu.id">
                                <div class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-8 transition-all shadow-sm hover:shadow-xl dark:border-slate-800 dark:bg-slate-950/40">
                                    <div class="flex items-start justify-between">
                                        <div class="flex gap-6">
                                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500 dark:bg-indigo-500/10">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147L120 19l7.74-8.853m0 0a12.001 12.001 0 100 0zM6.88 4.847A12.003 12.003 0 005.67 6.187m8.228 1.148L12 7.777l-1.898-.442m0 0a12.001 12.001 0 011.898-.442m0 0l1.898.442" /></svg>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-black tracking-tight text-slate-900 dark:text-white" x-text="edu.degree"></h4>
                                                <p class="mt-1 text-sm font-bold text-slate-400 uppercase tracking-widest" x-text="edu.institution"></p>
                                                <div class="mt-4 flex items-center gap-2 text-slate-400">
                                                     <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                                     <p class="text-xs font-black" x-text="(edu.year_from || '----') + ' — ' + (edu.year_to || 'Present')"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button @click="editEducation(edu)" class="opacity-0 group-hover:opacity-100 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-100 transition-all dark:bg-white/5">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                                            </button>
                                            <button @click="removeEducation(edu.id)" class="opacity-0 group-hover:opacity-100 flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 transition-all dark:bg-rose-500/10 dark:text-rose-400">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- TAB: Experience --}}
                    <div x-show="activeTab === 'experience'" class="space-y-8 animate-fade-in" style="display: none;">
                        <button @click="showExpForm = !showExpForm" class="flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 py-8 text-sm font-black text-slate-400 hover:border-cyan-400 hover:bg-cyan-50/30 hover:text-cyan-600 transition-all dark:border-slate-800 dark:bg-slate-950/40">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Add Work Experience
                        </button>

                        {{-- Add Experience Form --}}
                        <div x-show="showExpForm" x-transition class="rounded-xl border border-cyan-100 bg-white p-6 shadow-lg dark:border-cyan-500/20 dark:bg-slate-900">
                            <h4 class="text-xs font-black uppercase tracking-widest text-cyan-600 mb-6">Experience Details</h4>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Company Name</label>
                                    <input type="text" x-model="expForm.company" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold dark:bg-slate-950 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Designation</label>
                                    <input type="text" x-model="expForm.designation" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold dark:bg-slate-950 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">From Date</label>
                                    <input type="date" x-model="expForm.from_date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold dark:bg-slate-950 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">To Date (Optional)</label>
                                    <input type="date" x-model="expForm.to_date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold dark:bg-slate-950 dark:text-white">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Responsibilities</label>
                                    <textarea x-model="expForm.description" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold dark:bg-slate-950 dark:text-white resize-none"></textarea>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button @click="showExpForm = false" class="text-xs font-bold text-slate-400 hover:text-slate-600">Cancel</button>
                                <button @click="addExperience()" class="rounded-xl bg-slate-900 px-6 py-2.5 text-xs font-black text-white hover:bg-cyan-600 transition-colors dark:bg-white dark:text-slate-900">Save experience</button>
                            </div>
                        </div>

                        <div class="grid gap-6">
                            <template x-for="exp in employee.experiences || []" :key="exp.id">
                                <div class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-8 transition-all shadow-sm hover:shadow-xl dark:border-slate-800 dark:bg-slate-950/40">
                                    <div class="flex items-start justify-between">
                                        <div class="flex gap-6">
                                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 dark:bg-amber-500/10">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 .28-.22.5-.5.5H4.25a.5.5 0 01-.5-.5v-4.25m16.5 0a2.25 2.25 0 00-1.883-2.212c-.49-.07-1.127-.163-1.867-.257a2.25 2.25 0 01-1.95-2.115V4.25c0-.28-.22-.5-.5-.5H8.25a.5.5 0 00-.5.5v5.828a2.25 2.25 0 01-1.95 2.115c-.74.094-1.377.186-1.867.257A2.25 2.25 0 003.75 14.15z" /></svg>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-black tracking-tight text-slate-900 dark:text-white" x-text="exp.designation"></h4>
                                                <p class="mt-1 text-sm font-bold text-slate-400 uppercase tracking-widest" x-text="exp.company"></p>
                                                <div class="mt-4 flex items-center gap-2 text-slate-400">
                                                     <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                                     <p class="text-xs font-black" x-text="(exp.from_date || '----') + ' — ' + (exp.to_date || 'Present')"></p>
                                                </div>
                                                <p x-show="exp.description" class="mt-4 text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-2xl" x-text="exp.description"></p>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button @click="editExperience(exp)" class="opacity-0 group-hover:opacity-100 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-100 transition-all dark:bg-white/5">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                                            </button>
                                            <button @click="removeExperience(exp.id)" class="opacity-0 group-hover:opacity-100 flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 transition-all dark:bg-rose-500/10 dark:text-rose-400">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- TAB: Account --}}
                    <div x-show="activeTab === 'account'" class="space-y-12 animate-fade-in" style="display: none;">
                        {{-- Profile Settings --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-8 dark:border-slate-800 dark:bg-slate-950/20">
                            <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-8">Login Account Details</h3>
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Display Name</label>
                                    <input type="text" x-model="accountForm.name" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-900 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Login Email</label>
                                    <input type="email" x-model="accountForm.email" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-900 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end">
                                <button @click="updateAccount()" :disabled="savingAccount" class="rounded-2xl bg-slate-900 px-8 py-3 text-sm font-black text-white hover:bg-cyan-600 transition-colors dark:bg-white dark:text-slate-900 disabled:opacity-50">
                                    <span x-text="savingAccount ? 'Saving...' : 'Update Login Profile'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Security --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-8 dark:border-slate-800 dark:bg-slate-950/20">
                            <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-8">Security & Password</h3>
                            <div class="grid gap-6 sm:grid-cols-3">
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Current Password</label>
                                    <input type="password" x-model="passwordForm.current_password" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-900 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">New Password</label>
                                    <input type="password" x-model="passwordForm.password" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-900 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Confirm New Password</label>
                                    <input type="password" x-model="passwordForm.password_confirmation" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-900 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end">
                                <button @click="updatePassword()" :disabled="savingPassword" class="rounded-2xl bg-cyan-500 px-8 py-3 text-sm font-black text-white hover:bg-cyan-600 transition-colors disabled:opacity-50 shadow-lg shadow-cyan-500/20">
                                    <span x-text="savingPassword ? 'Updating...' : 'Change Password'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.3s ease-out; }
</style>
@endsection
