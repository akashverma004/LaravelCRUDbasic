@use('Illuminate\Support\Facades\Storage')
@extends('hrms.layouts.app')

@section('title', $employee->full_name . ' - PeopleFlow HRMS')

@section('content')
<div x-data="employeeProfile()" class="space-y-8">

    {{-- Profile Hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-10 shadow-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900/50">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-[80px]"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-center gap-8 lg:flex-row lg:items-start">
            {{-- Photo Container --}}
            <div class="relative shrink-0">
                <div class="h-32 w-32 overflow-hidden rounded-2xl border-4 border-slate-50 dark:border-slate-800 shadow-md">
                    @if($employee->profile_photo)
                        <img src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->full_name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-slate-100 dark:bg-slate-800">
                            <span class="text-3xl font-bold text-slate-500 dark:text-slate-400">{{ substr($employee->full_name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-white border-2 border-white dark:bg-slate-900 dark:border-slate-900 shadow-sm">
                    <span class="h-3 w-3 rounded-full {{ $employee->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                </div>
            </div>

            <div class="flex-1 text-center lg:text-left">
                <div class="flex flex-wrap items-center justify-center gap-4 lg:justify-start">
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $employee->full_name }}</h1>
                    <span class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">ID: {{ $employee->id }}</span>
                </div>
                <p class="mt-2 text-sm font-medium text-slate-500">{{ $employee->job_title ?? 'Employee' }} • {{ $employee->department?->name ?? 'No Department' }}</p>
                
                <div class="mt-4 flex flex-wrap items-center justify-center gap-6 lg:justify-start">
                    <div class="flex items-center gap-2 text-slate-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        <span class="text-xs font-medium">Joined: {{ $employee->joined_on?->format('M Y') ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        <span class="text-xs font-medium">{{ $employee->city ?? 'Remote' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isAdmin || $isSelf)
                <button @click="editing = !editing" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                    <span x-text="editing ? 'Cancel Edit' : 'Edit Profile'"></span>
                </button>
                @endif
                
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" /></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 z-50 w-48 origin-top-right rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-800 dark:bg-slate-900" style="display: none;">
                        @if($isAdmin)
                            <div class="p-1">
                                <a href="{{ route('assets.index') }}" class="block rounded-lg px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Assets</a>
                                <a href="{{ route('payroll.index') }}" class="block rounded-lg px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Payroll</a>
                            </div>
                            <div class="border-t border-slate-100 dark:border-slate-800 p-1">
                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full text-left rounded-lg px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:text-rose-500 dark:hover:bg-rose-500/10">Delete Employee</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Mode Commit Bar --}}
    <div x-show="editing" x-transition class="sticky top-6 z-40 flex items-center justify-between rounded-xl border border-indigo-200 bg-indigo-50 px-6 py-4 shadow-sm dark:border-indigo-500/20 dark:bg-indigo-500/10" style="display: none;">
        <div class="flex items-center gap-3">
             <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
             </div>
             <div>
                  <p class="text-sm font-semibold text-indigo-900 dark:text-indigo-200">You are editing this profile</p>
                  <p class="text-xs text-indigo-700 dark:text-indigo-400">Click save when you are finished making changes.</p>
             </div>
        </div>
        <div class="flex gap-3">
            <button @click="editing = false" class="rounded-lg px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 dark:text-indigo-300 dark:hover:bg-indigo-500/20 transition-colors">Discard</button>
            <button @click="submitForm()" :disabled="saving" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 transition-colors">
                <span x-show="saving" class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-r-white"></span>
                <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
            </button>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Quick Info --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-4">Quick Info</h3>
                <div class="space-y-4">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-slate-500">Manager</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $employee->manager?->full_name ?? 'None' }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-slate-500">Employment Type</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-white capitalize">{{ str_replace('-', ' ', $employee->employment_type) }}</span>
                    </div>
                    @if($isAdmin)
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-slate-500">Salary</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $employee->salary ? '₹' . number_format($employee->salary) : 'Not Specified' }}</span>
                    </div>
                    @endif
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-slate-500">Location</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $employee->city ?? 'Remote' }}</span>
                    </div>
                </div>
            </div>

            {{-- Activity Overview --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-4">Activity</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center dark:border-slate-800 dark:bg-slate-900/50">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $employee->leaveRequests->count() }}</p>
                        <p class="mt-1 text-xs text-slate-500">Leaves</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center dark:border-slate-800 dark:bg-slate-900/50">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $employee->attendanceRecords->count() }}</p>
                        <p class="mt-1 text-xs text-slate-500">Attendance</p>
                    </div>
                </div>
            </div>

            {{-- Skills --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-4">Skills</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($employee->skills as $skill)
                        <span class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            {{ $skill->name }}
                        </span>
                    @empty
                        <p class="text-xs text-slate-500">No skills listed.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Main Details Tabs --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Tabs --}}
            <div class="flex gap-2 overflow-x-auto rounded-xl border border-slate-200 bg-white p-2 shadow-sm dark:border-slate-800 dark:bg-slate-900/50 hide-scrollbar">
                @foreach([
                    'work' => 'Work Info',
                    'personal' => 'Personal Info',
                    'identity' => 'Identity & Banking',
                    'experience' => 'Experience',
                    'leaves' => 'Leave History',
                ] as $tabId => $tabLabel)
                <button
                    @click="activeTab = '{{ $tabId }}'"
                    class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-semibold transition-colors"
                    :class="activeTab === '{{ $tabId }}' ? 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white'"
                >{{ $tabLabel }}</button>
                @endforeach
            </div>

            {{-- Content Module --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50 min-h-[400px]">
                
                {{-- TAB: Work Info --}}
                <div x-show="activeTab === 'work'" class="space-y-6">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 pb-4 dark:border-slate-800">Work Information</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        @include('hrms.self-service.partials._field', ['field' => 'full_name', 'label' => 'Full Name', 'readonly' => !$isAdmin])
                        @include('hrms.self-service.partials._field', ['field' => 'job_title', 'label' => 'Job Title', 'readonly' => !$isAdmin])
                        @include('hrms.self-service.partials._select', ['field' => 'department_id', 'label' => 'Department', 'options' => $departments->pluck('name', 'id')->toArray(), 'readonly' => !$isAdmin])
                        @include('hrms.self-service.partials._select', ['field' => 'manager_id', 'label' => 'Manager', 'options' => $managers->pluck('full_name', 'id')->toArray(), 'readonly' => !$isAdmin])
                        @include('hrms.self-service.partials._select', ['field' => 'status', 'label' => 'Status', 'options' => ['active' => 'Active', 'on-leave' => 'On Leave', 'resigned' => 'Resigned'], 'readonly' => !$isAdmin])
                        @include('hrms.self-service.partials._field', ['field' => 'joined_on', 'label' => 'Date of Joining', 'type' => 'date', 'readonly' => !$isAdmin])
                    </div>
                </div>

                {{-- TAB: Personal Info --}}
                <div x-show="activeTab === 'personal'" class="space-y-6" style="display: none;">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 pb-4 dark:border-slate-800">Personal Data</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        @include('hrms.self-service.partials._field', ['field' => 'email', 'label' => 'Work Email', 'readonly' => true])
                        @include('hrms.self-service.partials._field', ['field' => 'personal_email', 'label' => 'Personal Email', 'type' => 'email'])
                        @include('hrms.self-service.partials._field', ['field' => 'phone', 'label' => 'Phone Number'])
                        @include('hrms.self-service.partials._field', ['field' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date'])
                        <div class="sm:col-span-2">
                            @include('hrms.self-service.partials._textarea', ['field' => 'bio', 'label' => 'Bio', 'span' => 2])
                        </div>
                    </div>
                </div>

                {{-- TAB: Identity & Banking --}}
                <div x-show="activeTab === 'identity'" class="space-y-6" style="display: none;">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 pb-4 dark:border-slate-800">Identity & Banking</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        @include('hrms.self-service.partials._field', ['field' => 'pan_number', 'label' => 'PAN Number', 'readonly' => !$isAdmin])
                        @include('hrms.self-service.partials._field', ['field' => 'aadhaar_number', 'label' => 'Aadhaar Number', 'readonly' => !$isAdmin])
                        @include('hrms.self-service.partials._field', ['field' => 'bank_name', 'label' => 'Bank Name', 'readonly' => !$isAdmin])
                        @include('hrms.self-service.partials._field', ['field' => 'bank_account_number', 'label' => 'Account Number', 'readonly' => !$isAdmin])
                    </div>
                </div>

                {{-- TAB: Experience --}}
                <div x-show="activeTab === 'experience'" class="space-y-6" style="display: none;">
                     <h2 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 pb-4 dark:border-slate-800">Past Experience</h2>
                     <div class="space-y-4">
                         @forelse($employee->experiences as $exp)
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/50">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $exp->designation }}</h4>
                                <p class="text-xs text-slate-500 mt-1">{{ $exp->company }}</p>
                                <p class="text-xs text-slate-400 mt-2">{{ $exp->from_date?->format('M Y') }} — {{ $exp->to_date?->format('M Y') ?? 'Present' }}</p>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                 <p class="text-sm text-slate-500">No past experience specified.</p>
                            </div>
                        @endforelse
                     </div>
                </div>

                {{-- TAB: Leave History --}}
                <div x-show="activeTab === 'leaves'" class="space-y-6" style="display: none;">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 pb-4 dark:border-slate-800">Leave History</h2>
                    <div class="space-y-3">
                        @forelse ($employee->leaveRequests->take(10) as $leave)
                            <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-900/50">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white capitalize">
                                        {{ $leave->leave_type }} Leave
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $leave->start_date->format('M d, Y') }} — {{ $leave->end_date->format('M d, Y') }}</p>
                                </div>
                                <span class="rounded-md px-2 py-1 text-xs font-semibold uppercase tracking-wider
                                    {{ $leave->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : '' }}
                                    {{ $leave->status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400' : '' }}
                                    {{ $leave->status === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400' : '' }}
                                ">{{ $leave->status }}</span>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <p class="text-sm text-slate-500">No leave history found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('employeeProfile', () => ({
            activeTab: 'work',
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
                    let msg = 'Failed to update profile.';
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

<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection
