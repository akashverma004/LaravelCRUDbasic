@use('Illuminate\Support\Facades\Storage')
@extends('hrms.layouts.app')

@section('title', $employee->full_name . ' - PeopleFlow HRMS')

@section('content')
<div x-data="employeeProfile()" class="overflow-x-hidden">

    {{-- Header --}}
    <div class="mb-8 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-6">
            {{-- Photo --}}
            <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-3xl border-4 border-white shadow-xl dark:border-slate-800">
                @if($employee->profile_photo)
                    <img src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->full_name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-cyan-400 to-indigo-600">
                        <span class="text-3xl font-bold text-white">{{ substr($employee->full_name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $employee->full_name }}</h1>
                    <span class="rounded-full bg-cyan-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300">{{ $employee->status }}</span>
                </div>
                <p class="mt-1 text-lg font-medium text-slate-500 dark:text-slate-400">{{ $employee->job_title }} · <span class="text-slate-400">{{ $employee->department?->name }}</span></p>
                <div class="mt-3 flex items-center gap-4 text-xs font-semibold text-slate-400">
                    <div class="flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                        Member since {{ $employee->joined_on?->format('M Y') }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $employee->city ?? 'Remote' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-bold text-white hover:bg-cyan-500 transition-all shadow-sm">
                    <span>Quick Actions</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 z-50 w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-2xl dark:border-slate-700 dark:bg-slate-800" style="display: none;">
                    @if($isAdmin)
                        <a href="{{ route('assets.index') }}" class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700/50">
                            <svg class="h-4 w-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Register Asset
                        </a>
                        <a href="{{ route('payroll.index') }}" class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700/50">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Generate Payslip
                        </a>
                        <button class="w-full flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700/50">
                            <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                            Assign Shift
                        </button>
                    @endif
                    <button class="w-full flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700/50">
                        <svg class="h-4 w-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        Give Kudos
                    </button>
                    <div class="my-1 border-t border-slate-100 dark:border-slate-700"></div>
                    @if($isAdmin)
                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Archive this employee?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Archive Employee
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- ═══ Left Column ═══ --}}
        <div class="space-y-6">

            {{-- Work Info --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Work Info</h3>
                <div class="space-y-3">
                    @foreach([
                        ['Department', $employee->department?->name],
                        ['Role', $employee->role?->name ? ucwords(str_replace('_', ' ', $employee->role->name)) : null],
                        ['Manager', $employee->manager?->full_name],
                        ['Type', $employee->employment_type ? ucwords(str_replace('-', ' ', $employee->employment_type)) : null],
                        ['Salary', $employee->salary ? '₹ ' . number_format($employee->salary, 2) : null],
                        ['Joined', $employee->joined_on?->format('d M Y')],
                    ] as [$label, $value])
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $label }}</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $value ?? 'N/A' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Quick Stats</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl bg-blue-50 p-3 text-center dark:bg-blue-500/10">
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $employee->leaveRequests->count() }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Leave Requests</p>
                    </div>
                    <div class="rounded-xl bg-green-50 p-3 text-center dark:bg-green-500/10">
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $employee->attendanceRecords->count() }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Attendance</p>
                    </div>
                </div>
            </div>

            {{-- Skills --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Skills</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($employee->skills as $skill)
                        <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300">
                            {{ $skill->name }}
                            @if($skill->proficiency)
                                <span class="rounded-full px-1.5 py-0.5 text-[10px] {{ $skill->proficiency === 'expert' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' : ($skill->proficiency === 'intermediate' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400' : 'bg-slate-200 text-slate-600 dark:bg-slate-600 dark:text-slate-400') }}">
                                    {{ ucfirst($skill->proficiency) }}
                                </span>
                            @endif
                        </span>
                    @empty
                        <p class="text-xs text-slate-400 dark:text-slate-500">No skills added</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ═══ Right Column — Tabbed ═══ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Tab Navigation --}}
            <div class="flex overflow-x-auto rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800/50 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @foreach([
                    'work' => 'Work',
                    'personal' => 'Personal',
                    'emergency' => 'Emergency',
                    'identity' => 'Identity',
                    'bank' => 'Bank',
                    'preferences' => 'Preferences',
                    'education' => 'Education',
                    'experience' => 'Experience',
                    'leaves' => 'Leaves',
                ] as $tabId => $tabLabel)
                <button
                    @click="activeTab = '{{ $tabId }}'"
                    class="flex-shrink-0 border-b-2 px-5 py-3 text-sm font-medium transition-colors"
                    :class="activeTab === '{{ $tabId }}' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'"
                >{{ $tabLabel }}</button>
                @endforeach
            </div>

            {{-- Edit / Save Buttons --}}
            @if($isAdmin || $isSelf)
            <div class="flex items-center justify-between mt-2">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white" x-text="activeTab.charAt(0).toUpperCase() + activeTab.slice(1)"></h2>
                <div class="flex items-center gap-2" x-show="['work', 'personal', 'emergency', 'identity', 'bank', 'preferences'].includes(activeTab)">
                    <template x-if="!editing">
                        <button @click="editing = true" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500/10 px-3 py-1.5 text-xs font-semibold text-cyan-600 hover:bg-cyan-500/20 dark:text-cyan-400">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit
                        </button>
                    </template>
                    <template x-if="editing">
                        <div class="flex gap-2">
                            <button @click="editing = false" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">Cancel</button>
                            <button @click="submitForm()" :disabled="saving" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-cyan-600 disabled:opacity-50">
                                <svg x-show="saving" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Save Changes
                            </button>
                        </div>
                    </template>
                </div>
            </div>
            @endif

            {{-- ── TAB: Work Details ─────────────────────────────── --}}
            <div x-show="activeTab === 'work'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <div class="grid gap-5 sm:grid-cols-2">
                    @include('hrms.self-service.partials._field', ['field' => 'full_name', 'label' => 'Full Name', 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._field', ['field' => 'job_title', 'label' => 'Job Title', 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._select', ['field' => 'department_id', 'label' => 'Department', 'options' => $departments->pluck('name', 'id')->toArray(), 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._select', ['field' => 'manager_id', 'label' => 'Manager', 'options' => $managers->pluck('full_name', 'id')->toArray(), 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._select', ['field' => 'role_id', 'label' => 'Role', 'options' => $roles->pluck('name', 'id')->toArray(), 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._select', ['field' => 'employment_type', 'label' => 'Employment Type', 'options' => ['full-time' => 'Full-time', 'part-time' => 'Part-time', 'contract' => 'Contract', 'intern' => 'Intern'], 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._select', ['field' => 'status', 'label' => 'Status', 'options' => ['active' => 'Active', 'on-leave' => 'On Leave', 'resigned' => 'Resigned'], 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._field', ['field' => 'salary', 'label' => 'Salary (annual)', 'type' => 'number', 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._field', ['field' => 'joined_on', 'label' => 'Joined On', 'type' => 'date', 'readonly' => !$isAdmin])
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
                        'male' => 'Male', 'female' => 'Female', 'non_binary' => 'Non-binary',
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
                    @include('hrms.self-service.partials._field', ['field' => 'zip_code', 'label' => 'Zip / Postal Code'])
                    @include('hrms.self-service.partials._field', ['field' => 'address', 'label' => 'Address', 'span' => 2])
                    @include('hrms.self-service.partials._field', ['field' => 'country', 'label' => 'Country'])
                    @include('hrms.self-service.partials._field', ['field' => 'state', 'label' => 'State'])
                </div>
            </div>

            {{-- ── TAB: Emergency Contact ────────────────────────────── --}}
            <div x-show="activeTab === 'emergency'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">
                    <svg class="mr-1 inline h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                    Emergency contact information
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
            @if($isAdmin || $isSelf)
            <div x-show="activeTab === 'identity'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">
                    <svg class="mr-1 inline h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Identity information is encrypted.
                </p>
                <div class="grid gap-5 sm:grid-cols-2">
                    @include('hrms.self-service.partials._field', ['field' => 'pan_number', 'label' => 'PAN Number', 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._field', ['field' => 'aadhaar_number', 'label' => 'Aadhaar Number', 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._field', ['field' => 'passport_number', 'label' => 'Passport Number', 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._field', ['field' => 'passport_expiry', 'label' => 'Passport Expiry', 'type' => 'date', 'readonly' => !$isAdmin])
                </div>
            </div>

            {{-- ── TAB: Bank Details ─────────────────────────────────── --}}
            <div x-show="activeTab === 'bank'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <div class="grid gap-5 sm:grid-cols-2">
                    @include('hrms.self-service.partials._field', ['field' => 'bank_name', 'label' => 'Bank Name', 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._field', ['field' => 'bank_account_number', 'label' => 'Account Number', 'readonly' => !$isAdmin])
                    @include('hrms.self-service.partials._field', ['field' => 'bank_ifsc', 'label' => 'IFSC Code', 'readonly' => !$isAdmin])
                </div>
            </div>
            @endif

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

            {{-- TAB: Education --}}
            <div x-show="activeTab === 'education'" class="space-y-4">
                @forelse($employee->educations as $edu)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800/50">
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $edu->degree }}{{ $edu->field_of_study ? ' in ' . $edu->field_of_study : '' }}</h4>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $edu->institution }}</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">{{ $edu->year_from ?? '?' }} — {{ $edu->year_to ?? 'Present' }}</p>
                </div>
                @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center dark:border-slate-600">
                    <svg class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 7l-9-5 9-5 9 5-9 5z"></path></svg>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">No education records</p>
                </div>
                @endforelse
            </div>

            {{-- TAB: Experience --}}
            <div x-show="activeTab === 'experience'" class="space-y-4">
                @forelse($employee->experiences as $exp)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800/50">
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $exp->designation }}</h4>
                    <p class="mt-0.5 text-sm text-cyan-600 dark:text-cyan-400">{{ $exp->company }}</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                        {{ $exp->from_date?->format('M Y') ?? '?' }} — {{ $exp->to_date?->format('M Y') ?? 'Present' }}
                    </p>
                    @if($exp->description)
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $exp->description }}</p>
                    @endif
                </div>
                @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center dark:border-slate-600">
                    <svg class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m-3 0h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"></path></svg>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">No work experience</p>
                </div>
                @endforelse
            </div>

            {{-- TAB: Recent Leaves --}}
            <div x-show="activeTab === 'leaves'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                @forelse ($employee->leaveRequests->take(10) as $leave)
                    <div class="mb-3 flex items-center justify-between rounded-lg bg-slate-50 p-3 last:mb-0 dark:bg-slate-900/50">
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ ucfirst($leave->leave_type) }} — {{ $leave->start_date->format('d M') }} to {{ $leave->end_date->format('d M') }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $leave->reason }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-medium
                            {{ $leave->status === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400' :
                               ($leave->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' :
                               'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400') }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No leave requests.</p>
                @endforelse
            </div>
        </div>
    </div>
    </div>

    {{-- Edit Slide-over Panel removed in favour of inline profile editing --}}

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('employeeProfile', () => ({
            activeTab: 'personal',
            editing: false,
            saving: false,
            employee: @json($employee),
            form: {
                full_name: @json($employee->full_name),
                email: @json($employee->email),
                personal_email: @json($employee->personal_email),
                phone: @json($employee->phone),
                job_title: @json($employee->job_title),
                department_id: @json($employee->department_id),
                manager_id: @json($employee->manager_id),
                role_id: @json($employee->role_id),
                salary: @json($employee->salary),
                status: @json($employee->status),
                employment_type: @json($employee->employment_type),
                country: @json($employee->country),
                state: @json($employee->state),
                city: @json($employee->city),
                zip_code: @json($employee->zip_code),
                address: @json($employee->address),
                joined_on: @json($employee->joined_on?->format('Y-m-d')),
                date_of_birth: @json($employee->date_of_birth?->format('Y-m-d')),
                gender: @json($employee->gender),
                blood_group: @json($employee->blood_group),
                marital_status: @json($employee->marital_status),
                bio: @json($employee->bio),
                emergency_contact_name: @json($employee->emergency_contact_name),
                emergency_contact_phone: @json($employee->emergency_contact_phone),
                emergency_contact_relationship: @json($employee->emergency_contact_relationship),
                pan_number: @json($employee->pan_number),
                aadhaar_number: @json($employee->aadhaar_number),
                bank_name: @json($employee->bank_name),
                bank_account_number: @json($employee->bank_account_number),
                bank_ifsc: @json($employee->bank_ifsc),
            },
            async submitForm() {
                this.saving = true;
                try {
                    await axios.patch('{{ route('employees.update', $employee->id) }}', this.form, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    window.location.reload();
                } catch (error) {
                    let msg = 'There was an error updating the employee details. Check the console.';
                    if(error.response && error.response.status === 422) {
                        const errors = Object.values(error.response.data.errors).flat();
                        msg = errors.join('\n');
                    }
                    alert(msg);
                    console.error(error);
                    this.saving = false;
                }
            }
        }))
    });
</script>
@endsection
