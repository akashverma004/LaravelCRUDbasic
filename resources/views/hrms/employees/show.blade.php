@use('Illuminate\Support\Facades\Storage')
@extends('hrms.layouts.app')

@section('title', $employee->full_name . ' - PeopleFlow HRMS')

@section('content')
<div x-data="{ activeTab: 'personal' }" class="overflow-x-hidden">

    {{-- Header --}}
    <div class="mb-6 flex items-center gap-4">
        <div class="flex items-center gap-4">
            {{-- Photo --}}
            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-full border-2 border-slate-200 dark:border-slate-600">
                @if($employee->profile_photo)
                    <img src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->full_name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-cyan-400 to-blue-500">
                        <span class="text-xl font-bold text-white">{{ substr($employee->full_name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $employee->full_name }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $employee->job_title }}
                    @if($employee->pronouns) <span class="text-slate-400 dark:text-slate-500">· {{ $employee->pronouns }}</span> @endif
                </p>
                <span class="mt-1 inline-flex items-center rounded-full bg-cyan-100 px-2.5 py-0.5 text-xs font-medium text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300">{{ ucfirst($employee->status) }}</span>
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

            {{-- TAB: Personal --}}
            <div x-show="activeTab === 'personal'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach([
                        ['Work Email', $employee->email],
                        ['Personal Email', $employee->personal_email],
                        ['Phone', $employee->phone],
                        ['Date of Birth', $employee->date_of_birth?->format('d M Y')],
                        ['Gender', $employee->gender ? ucfirst(str_replace('_', ' ', $employee->gender)) : null],
                        ['Marital Status', $employee->marital_status ? ucfirst($employee->marital_status) : null],
                        ['Blood Group', $employee->blood_group],
                        ['Nationality', $employee->nationality],
                        ['Pronouns', $employee->pronouns],
                    ] as [$label, $value])
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300">{{ $value ?? 'Not set' }}</p>
                    </div>
                    @endforeach
                    <div class="sm:col-span-2">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">LinkedIn</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300">
                            @if($employee->linkedin_url)
                                <a href="{{ $employee->linkedin_url }}" target="_blank" class="text-cyan-600 hover:underline dark:text-cyan-400">{{ $employee->linkedin_url }}</a>
                            @else Not set @endif
                        </p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Bio</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300">{{ $employee->bio ?? 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">City</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300">{{ $employee->city ?? 'Not set' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Address</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300">{{ $employee->address ?? 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Country</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300">{{ $employee->country ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">State</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300">{{ $employee->state ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- TAB: Emergency --}}
            <div x-show="activeTab === 'emergency'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">
                    <svg class="mr-1 inline h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                    Emergency contact information
                </p>
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach([
                        ['Contact Name', $employee->emergency_contact_name],
                        ['Contact Phone', $employee->emergency_contact_phone],
                        ['Relationship', $employee->emergency_contact_relationship ? ucfirst($employee->emergency_contact_relationship) : null],
                    ] as [$label, $value])
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300">{{ $value ?? 'Not set' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- TAB: Identity --}}
            <div x-show="activeTab === 'identity'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach([
                        ['PAN Number', $employee->pan_number],
                        ['Aadhaar Number', $employee->aadhaar_number],
                        ['Passport Number', $employee->passport_number],
                        ['Passport Expiry', $employee->passport_expiry?->format('d M Y')],
                    ] as [$label, $value])
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300">{{ $value ?? 'Not set' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- TAB: Bank --}}
            <div x-show="activeTab === 'bank'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach([
                        ['Bank Name', $employee->bank_name],
                        ['Account Number', $employee->bank_account_number],
                        ['IFSC Code', $employee->bank_ifsc],
                    ] as [$label, $value])
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300">{{ $value ?? 'Not set' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- TAB: Preferences --}}
            <div x-show="activeTab === 'preferences'" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach([
                        ['Hobbies', $employee->hobbies],
                        ['Likes', $employee->likes],
                        ['Food Preference', $employee->food_preference ? ucfirst($employee->food_preference) : null],
                        ['Health Issues', $employee->health_issues],
                    ] as [$label, $value])
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300">{{ $value ?? 'Not set' }}</p>
                    </div>
                    @endforeach
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
@endsection
