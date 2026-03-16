@use('Illuminate\Support\Facades\Storage')
@extends('hrms.layouts.app')

@section('title', $employee->full_name . ' - PeopleFlow HRMS')

@section('content')
<div x-data="employeeProfile()" class="space-y-6 relative">
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
    {{-- Profile Hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-10 shadow-sm border border-slate-200 dark:border-white/5 dark:bg-slate-900/50">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-[100px]"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-[100px]"></div>
        
        <div class="relative z-10 flex flex-col items-center gap-10 lg:flex-row lg:items-start text-center lg:text-left">
            {{-- Photo Container --}}
            <div class="relative group/photo">
                <div class="h-32 w-32 overflow-hidden rounded-2xl border-4 border-white dark:border-white/5 shadow-2xl transition-transform group-hover/photo:scale-105">
                    @if($employee->profile_photo)
                        <img src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->full_name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-slate-100 dark:bg-white/5">
                            <span class="text-4xl font-black text-slate-300 dark:text-slate-600 uppercase">{{ substr($employee->full_name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="absolute -bottom-1.5 -right-1.5 flex h-7 w-7 items-center justify-center rounded-lg bg-white dark:bg-slate-900 shadow-xl border border-slate-100 dark:border-white/10 ring-4 ring-white dark:ring-slate-900/50">
                    <div class="h-2.5 w-2.5 rounded-full {{ $employee->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></div>
                </div>
            </div>

            <div class="flex-1">
                <div class="flex flex-wrap items-center justify-center gap-4 lg:justify-start">
                    <h1 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-white uppercase">{{ $employee->full_name }}</h1>
                    <span class="rounded-lg bg-slate-900 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-white dark:bg-white/10 shadow-lg">ID: {{ $employee->id }}</span>
                </div>
                <p class="mt-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-none">
                    <span class="text-cyan-600 dark:text-cyan-400">{{ $employee->job_title ?? 'Employee' }}</span>
                    <span class="mx-2 text-slate-300 dark:text-white/10">/</span>
                    <span class="text-slate-900 dark:text-white">{{ $employee->department?->name ?? 'No Department' }}</span>
                </p>
                
                <div class="mt-6 flex flex-wrap items-center justify-center gap-8 lg:justify-start">
                    <div class="flex items-center gap-2.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Joined: {{ $employee->joined_on?->format('M Y') ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400">{{ $employee->city ?? 'Remote' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isAdmin || $isSelf)
                <button @click="editing = !editing" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <span x-text="editing ? 'Abort Edit' : 'Modify Record'"></span>
                </button>
                @endif
                
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-slate-400 hover:bg-cyan-50 hover:text-cyan-600 transition-all dark:bg-white/5 dark:border-white/5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" /></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-3 z-50 w-56 origin-top-right rounded-xl border border-slate-100 bg-white p-1.5 shadow-2xl dark:border-white/10 dark:bg-slate-900" x-cloak>
                        @if($isAdmin)
                            <a href="{{ route('assets.index') }}" class="block rounded-lg px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 hover:text-cyan-600 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-cyan-400">Asset Registry</a>
                            <a href="{{ route('payroll.index') }}" class="block rounded-lg px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 hover:text-cyan-600 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-cyan-400">Payroll Tiling</a>
                            <div class="my-1.5 border-t border-slate-50 dark:border-white/5"></div>
                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Purge this personnel record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full text-left rounded-lg px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10">Purge Record</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>v>

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
            <button @click="editing = false" class="text-[10px] font-black uppercase tracking-widest text-indigo-700 hover:text-indigo-900 dark:text-indigo-300 dark:hover:text-white transition-colors">Discard</button>
            <button @click="submitForm()" :disabled="saving" class="group relative flex items-center justify-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-6 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-xl shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-95 disabled:opacity-50 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                <span x-show="!saving" class="flex items-center gap-2">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    Save Changes
                </span>
                <span x-show="saving" class="flex items-center gap-2">
                    <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Processing
                </span>
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
