@extends('hrms.layouts.app')

@section('title', 'My Profile - PeopleFlow HRMS')

@section('content')
<div x-data="selfServiceProfile()" x-init="init()" class="space-y-6">

    {{-- Toast Notification --}}
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl px-5 py-3 shadow-2xl"
        :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
        style="display: none;"
    >
        <template x-if="toast.type === 'success'">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </template>
        <template x-if="toast.type === 'error'">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </template>
        <span x-text="toast.message" class="text-sm font-medium"></span>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- ═══════════════ LEFT COLUMN ═══════════════ --}}
        <div class="space-y-6">
            {{-- Profile Photo Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <div class="flex flex-col items-center text-center">
                    <div class="group relative">
                        <div class="h-28 w-28 overflow-hidden rounded-full border-4 border-slate-200 dark:border-slate-600 transition-shadow group-hover:shadow-lg group-hover:shadow-cyan-500/20">
                            <template x-if="photoUrl">
                                <img :src="photoUrl" alt="Profile Photo" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!photoUrl">
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-cyan-400 to-blue-500">
                                    <span class="text-3xl font-bold text-white" x-text="employee.full_name ? employee.full_name.charAt(0).toUpperCase() : '?'"></span>
                                </div>
                            </template>
                        </div>
                        <label class="absolute inset-0 flex cursor-pointer items-center justify-center rounded-full bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <input type="file" accept="image/jpeg,image/png,image/jpg,image/webp" @change="uploadPhoto($event)" class="hidden">
                        </label>
                    </div>
                    <div x-show="uploading" class="mt-3">
                        <svg class="h-5 w-5 animate-spin text-cyan-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </div>
                    <button x-show="photoUrl && !uploading" @click="removePhoto()" class="mt-2 text-xs font-medium text-red-500 hover:text-red-400">Remove Photo</button>
                    <h2 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white" x-text="employee.full_name"></h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400" x-text="employee.job_title"></p>
                    <template x-if="employee.pronouns">
                        <p class="text-xs text-slate-400 dark:text-slate-500" x-text="employee.pronouns"></p>
                    </template>
                    <span class="mt-2 inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300" x-text="employee.status ? employee.status.charAt(0).toUpperCase() + employee.status.slice(1) : ''"></span>
                </div>
            </div>

            {{-- Work Info (Read-Only) --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Work Info</h3>
                <div class="space-y-3">
                    <template x-for="item in [
                        { label: 'Department', value: employee.department },
                        { label: 'Manager', value: employee.manager },
                        { label: 'Role', value: employee.role },
                        { label: 'Employment Type', value: employee.employment_type ? employee.employment_type.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : null },
                        { label: 'Joined', value: employee.joined_on },
                    ]" :key="item.label">
                        <div class="flex items-start gap-3">
                            <div class="min-w-0">
                                <p class="text-xs text-slate-500 dark:text-slate-400" x-text="item.label"></p>
                                <p class="text-sm font-medium text-slate-900 dark:text-white" x-text="item.value || 'N/A'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Skills --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Skills</h3>
                    <button @click="showSkillForm = !showSkillForm" class="text-xs font-medium text-cyan-600 hover:text-cyan-500 dark:text-cyan-400">+ Add</button>
                </div>
                {{-- Add Skill Form --}}
                <div x-show="showSkillForm" x-transition class="mb-4 space-y-2 rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-600 dark:bg-slate-700/50">
                    <input type="text" x-model="skillForm.name" placeholder="Skill name" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <select x-model="skillForm.proficiency" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="">Proficiency</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="expert">Expert</option>
                    </select>
                    <div class="flex gap-2">
                        <button @click="addSkill()" class="rounded-lg bg-cyan-500 px-3 py-1 text-xs font-semibold text-white hover:bg-cyan-600">Save</button>
                        <button @click="showSkillForm = false" class="rounded-lg border border-slate-300 px-3 py-1 text-xs text-slate-600 dark:border-slate-600 dark:text-slate-300">Cancel</button>
                    </div>
                </div>
                {{-- Skill Tags --}}
                <div class="flex flex-wrap gap-2">
                    <template x-for="skill in employee.skills || []" :key="skill.id">
                        <span class="group inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300">
                            <span x-text="skill.name"></span>
                            <template x-if="skill.proficiency">
                                <span class="rounded-full px-1.5 py-0.5 text-[10px]"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400': skill.proficiency === 'expert',
                                        'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400': skill.proficiency === 'intermediate',
                                        'bg-slate-200 text-slate-600 dark:bg-slate-600 dark:text-slate-400': skill.proficiency === 'beginner',
                                    }"
                                    x-text="skill.proficiency.charAt(0).toUpperCase() + skill.proficiency.slice(1)">
                                </span>
                            </template>
                            <button @click="removeSkill(skill.id)" class="ml-1 hidden text-red-400 hover:text-red-600 group-hover:inline">×</button>
                        </span>
                    </template>
                    <template x-if="!employee.skills || employee.skills.length === 0">
                        <p class="text-xs text-slate-400 dark:text-slate-500">No skills added yet</p>
                    </template>
                </div>
            </div>
        </div>

        {{-- ═══════════════ RIGHT COLUMN — TABBED ═══════════════ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Tab Navigation --}}
            <div class="flex overflow-x-auto rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800/50 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <template x-for="t in tabs" :key="t.id">
                    <button
                        @click="activeTab = t.id"
                        class="flex-shrink-0 border-b-2 px-5 py-3 text-sm font-medium transition-colors"
                        :class="activeTab === t.id
                            ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400'
                            : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                        x-text="t.label"
                    ></button>
                </template>
            </div>

            {{-- Edit / Save Buttons --}}
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white" x-text="tabs.find(t => t.id === activeTab)?.label"></h2>
                <div class="flex items-center gap-2" x-show="['personal', 'emergency', 'identity', 'bank', 'preferences'].includes(activeTab)">
                    <template x-if="!editing">
                        <button @click="startEditing()" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500/10 px-3 py-1.5 text-xs font-semibold text-cyan-600 hover:bg-cyan-500/20 dark:text-cyan-400">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit
                        </button>
                    </template>
                    <template x-if="editing">
                        <div class="flex gap-2">
                            <button @click="cancelEditing()" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">Cancel</button>
                            <button @click="saveProfile()" :disabled="saving" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-cyan-600 disabled:opacity-50">
                                <svg x-show="saving" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Save Changes
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ── TAB: Personal Details ─────────────────────────────── --}}
            <div x-show="activeTab === 'personal'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <div class="grid gap-5 sm:grid-cols-2">
                    @include('hrms.self-service.partials._field', ['field' => 'email', 'label' => 'Work Email', 'readonly' => true])
                    @include('hrms.self-service.partials._field', ['field' => 'personal_email', 'label' => 'Personal Email', 'type' => 'email'])
                    @include('hrms.self-service.partials._field', ['field' => 'phone', 'label' => 'Phone'])
                    @include('hrms.self-service.partials._field', ['field' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date'])
                    @include('hrms.self-service.partials._select', ['field' => 'gender', 'label' => 'Gender', 'options' => [
                        'male' => 'Male', 'female' => 'Female', 'non-binary' => 'Non-binary',
                        'other' => 'Other', 'prefer_not_to_say' => 'Prefer not to say'
                    ]])
                    @include('hrms.self-service.partials._select', ['field' => 'marital_status', 'label' => 'Marital Status', 'options' => [
                        'single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed'
                    ]])
                    @include('hrms.self-service.partials._field', ['field' => 'blood_group', 'label' => 'Blood Group'])
                    @include('hrms.self-service.partials._field', ['field' => 'nationality', 'label' => 'Nationality'])
                    @include('hrms.self-service.partials._field', ['field' => 'pronouns', 'label' => 'Pronouns'])
                    @include('hrms.self-service.partials._field', ['field' => 'linkedin_url', 'label' => 'LinkedIn URL', 'type' => 'url', 'span' => 2])
                    @include('hrms.self-service.partials._textarea', ['field' => 'bio', 'label' => 'Bio / About Me', 'span' => 2])
                    @include('hrms.self-service.partials._field', ['field' => 'city', 'label' => 'City'])
                    @include('hrms.self-service.partials._field', ['field' => 'address', 'label' => 'Address', 'span' => 2])
                </div>
            </div>

            {{-- ── TAB: Emergency Contact ────────────────────────────── --}}
            <div x-show="activeTab === 'emergency'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">
                    <svg class="mr-1 inline h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                    This information is used in case of emergency at the workplace.
                </p>
                <div class="grid gap-5 sm:grid-cols-2">
                    @include('hrms.self-service.partials._field', ['field' => 'emergency_contact_name', 'label' => 'Contact Name'])
                    @include('hrms.self-service.partials._field', ['field' => 'emergency_contact_phone', 'label' => 'Contact Phone'])
                    @include('hrms.self-service.partials._select', ['field' => 'emergency_contact_relationship', 'label' => 'Relationship', 'options' => [
                        'spouse' => 'Spouse', 'parent' => 'Parent', 'sibling' => 'Sibling',
                        'child' => 'Child', 'friend' => 'Friend', 'other' => 'Other'
                    ]])
                </div>
            </div>

            {{-- ── TAB: Identity Documents ───────────────────────────── --}}
            <div x-show="activeTab === 'identity'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">
                    <svg class="mr-1 inline h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Your identity information is encrypted and stored securely.
                </p>
                <div class="grid gap-5 sm:grid-cols-2">
                    @include('hrms.self-service.partials._field', ['field' => 'pan_number', 'label' => 'PAN Number'])
                    @include('hrms.self-service.partials._field', ['field' => 'aadhaar_number', 'label' => 'Aadhaar Number'])
                    @include('hrms.self-service.partials._field', ['field' => 'passport_number', 'label' => 'Passport Number'])
                    @include('hrms.self-service.partials._field', ['field' => 'passport_expiry', 'label' => 'Passport Expiry', 'type' => 'date'])
                </div>
            </div>

            {{-- ── TAB: Bank Details ─────────────────────────────────── --}}
            <div x-show="activeTab === 'bank'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <div class="grid gap-5 sm:grid-cols-2">
                    @include('hrms.self-service.partials._field', ['field' => 'bank_name', 'label' => 'Bank Name'])
                    @include('hrms.self-service.partials._field', ['field' => 'bank_account_number', 'label' => 'Account Number'])
                    @include('hrms.self-service.partials._field', ['field' => 'bank_ifsc', 'label' => 'IFSC Code'])
                </div>
            </div>

            {{-- ── TAB: Preferences ──────────────────────────────────── --}}
            <div x-show="activeTab === 'preferences'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <div class="grid gap-5 sm:grid-cols-2">
                    @include('hrms.self-service.partials._field', ['field' => 'hobbies', 'label' => 'Hobbies'])
                    @include('hrms.self-service.partials._field', ['field' => 'likes', 'label' => 'Likes'])
                    @include('hrms.self-service.partials._select', ['field' => 'food_preference', 'label' => 'Food Preference', 'options' => [
                        'vegetarian' => 'Vegetarian', 'non-vegetarian' => 'Non-Vegetarian',
                        'vegan' => 'Vegan', 'eggetarian' => 'Eggetarian', 'jain' => 'Jain'
                    ]])
                    @include('hrms.self-service.partials._field', ['field' => 'health_issues', 'label' => 'Health Issues'])
                </div>
            </div>

            {{-- ── TAB: Education ────────────────────────────────────── --}}
            <div x-show="activeTab === 'education'" class="space-y-4">
                <button @click="showEduForm = !showEduForm" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500/10 px-4 py-2 text-sm font-semibold text-cyan-600 hover:bg-cyan-500/20 dark:text-cyan-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Education
                </button>

                {{-- Add Education Form --}}
                <div x-show="showEduForm" x-transition class="rounded-2xl border border-cyan-200 bg-cyan-50/50 p-5 dark:border-slate-600 dark:bg-slate-800/50">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Degree *</label>
                            <input type="text" x-model="eduForm.degree" placeholder="e.g. B.Tech" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Institution *</label>
                            <input type="text" x-model="eduForm.institution" placeholder="e.g. IIT Delhi" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Field of Study</label>
                            <input type="text" x-model="eduForm.field_of_study" placeholder="e.g. Computer Science" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">From Year</label>
                                <input type="number" x-model="eduForm.year_from" placeholder="2018" min="1950" max="2099" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">To Year</label>
                                <input type="number" x-model="eduForm.year_to" placeholder="2022" min="1950" max="2099" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <button @click="addEducation()" class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600">Save</button>
                        <button @click="showEduForm = false" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 dark:border-slate-600 dark:text-slate-300">Cancel</button>
                    </div>
                </div>

                {{-- Education Cards --}}
                <template x-for="edu in employee.educations || []" :key="edu.id">
                    <div class="group rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white" x-text="edu.degree + (edu.field_of_study ? ' in ' + edu.field_of_study : '')"></h4>
                                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400" x-text="edu.institution"></p>
                                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500" x-text="(edu.year_from || '?') + ' — ' + (edu.year_to || 'Present')"></p>
                            </div>
                            <button @click="removeEducation(edu.id)" class="hidden rounded-lg p-1 text-red-400 hover:bg-red-50 hover:text-red-600 group-hover:block dark:hover:bg-red-900/20">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
                <template x-if="!employee.educations || employee.educations.length === 0">
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center dark:border-slate-600">
                        <svg class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 7l-9-5 9-5 9 5-9 5z"></path></svg>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">No education records added yet</p>
                    </div>
                </template>
            </div>

            {{-- ── TAB: Experience ───────────────────────────────────── --}}
            <div x-show="activeTab === 'experience'" class="space-y-4">
                <button @click="showExpForm = !showExpForm" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500/10 px-4 py-2 text-sm font-semibold text-cyan-600 hover:bg-cyan-500/20 dark:text-cyan-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Experience
                </button>

                {{-- Add Experience Form --}}
                <div x-show="showExpForm" x-transition class="rounded-2xl border border-cyan-200 bg-cyan-50/50 p-5 dark:border-slate-600 dark:bg-slate-800/50">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Company *</label>
                            <input type="text" x-model="expForm.company" placeholder="e.g. Infosys" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Designation *</label>
                            <input type="text" x-model="expForm.designation" placeholder="e.g. Software Engineer" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">From Date</label>
                            <input type="date" x-model="expForm.from_date" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">To Date</label>
                            <input type="date" x-model="expForm.to_date" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Description</label>
                            <textarea x-model="expForm.description" rows="2" placeholder="Brief role description..." class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <button @click="addExperience()" class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600">Save</button>
                        <button @click="showExpForm = false" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 dark:border-slate-600 dark:text-slate-300">Cancel</button>
                    </div>
                </div>

                {{-- Experience Cards --}}
                <template x-for="exp in employee.experiences || []" :key="exp.id">
                    <div class="group rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white" x-text="exp.designation"></h4>
                                <p class="mt-0.5 text-sm text-cyan-600 dark:text-cyan-400" x-text="exp.company"></p>
                                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500" x-text="(exp.from_date || '?') + ' — ' + (exp.to_date || 'Present')"></p>
                                <template x-if="exp.description">
                                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400" x-text="exp.description"></p>
                                </template>
                            </div>
                            <button @click="removeExperience(exp.id)" class="hidden rounded-lg p-1 text-red-400 hover:bg-red-50 hover:text-red-600 group-hover:block dark:hover:bg-red-900/20">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
                <template x-if="!employee.experiences || employee.experiences.length === 0">
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center dark:border-slate-600">
                        <svg class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m-3 0h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"></path></svg>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">No work experience added yet</p>
                    </div>
                </template>
            </div>

            {{-- ── TAB: Account ──────────────────────────────────────── --}}
            <div x-show="activeTab === 'account'" class="space-y-6">
                {{-- Update Name & Email --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Account Information</h3>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Update your login name and email address</p>
                        </div>
                        <button @click="updateAccount()" :disabled="savingAccount" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-cyan-600 disabled:opacity-50">
                            <svg x-show="savingAccount" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <svg x-show="!savingAccount" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Save Changes
                        </button>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Name</label>
                            <input type="text" x-model="accountForm.name" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm transition-colors focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Email</label>
                            <input type="email" x-model="accountForm.email" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm transition-colors focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                    </div>
                </div>

                {{-- Update Password --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Password Security</h3>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Use a strong, unique password to keep your account secure</p>
                        </div>
                        <button @click="updatePassword()" :disabled="savingPassword" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-cyan-600 disabled:opacity-50">
                            <svg x-show="savingPassword" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <svg x-show="!savingPassword" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Update Password
                        </button>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Current Password</label>
                            <input type="password" x-model="passwordForm.current_password" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm transition-colors focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">New Password</label>
                            <input type="password" x-model="passwordForm.password" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm transition-colors focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Confirm New Password</label>
                            <input type="password" x-model="passwordForm.password_confirmation" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm transition-colors focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        </div>
                    </div>
                </div>

                {{-- Danger Zone --}}
                <div class="rounded-2xl border border-red-200/80 bg-white p-6 dark:border-red-900/40 dark:bg-slate-800/50">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-red-600 dark:text-red-400">Danger Zone</h3>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-red-300/60">Permanently delete your account and all data</p>
                        </div>
                        <button @click="showDeleteConfirm = !showDeleteConfirm" class="inline-flex items-center gap-1.5 rounded-lg bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-500/20 dark:text-red-400">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete Account
                        </button>
                    </div>
                    <div x-show="showDeleteConfirm" x-transition class="rounded-xl border border-red-200 bg-red-50/50 p-4 dark:border-red-900/50 dark:bg-red-950/20">
                        <p class="mb-3 text-sm text-slate-700 dark:text-slate-300">Enter your password to confirm deletion:</p>
                        <input type="password" x-model="deletePassword" placeholder="Password" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm transition-colors focus:border-red-500 focus:ring-1 focus:ring-red-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <div class="mt-3 flex gap-2">
                            <button @click="deleteAccount()" class="inline-flex items-center gap-1.5 rounded-lg bg-red-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-600">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Confirm Delete
                            </button>
                            <button @click="showDeleteConfirm = false; deletePassword = ''" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
