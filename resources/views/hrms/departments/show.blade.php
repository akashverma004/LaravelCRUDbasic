@extends('hrms.layouts.app')

@section('title', 'Department Details - PeopleFlow HRMS')

@section('content')
<div x-data="departmentProfile()" class="overflow-x-hidden">

    {{-- Header --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $department->name }}</h1>
            <p class="mt-1 flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $department->code }}</span>
            </p>
        </div>
        
        @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
            <div class="flex items-center gap-2">
                <button @click="isEditing = true" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    Edit Info
                </button>
                
                <form method="POST" action="{{ route('departments.destroy', $department->id) }}" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-orange-100/50 px-4 py-2 text-sm font-bold text-orange-700 hover:bg-orange-100 dark:bg-orange-500/10 dark:text-orange-400 dark:hover:bg-orange-500/20 transition-all" onclick="return confirm('Delete this department? All associated data might be affected.')">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        {{-- Left Col: Overview --}}
        <div class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-sm">
                <h2 class="mb-5 text-sm font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-800 pb-3">Overview</h2>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="font-semibold text-slate-500 dark:text-slate-400">Lead</p>
                        <div class="mt-1 flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 text-xs font-bold text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300">
                                {{ substr($department->lead_name ?? '?', 0, 1) }}
                            </div>
                            <span class="font-medium text-slate-900 dark:text-white">{{ $department->lead_name ?? 'No Lead Assigned' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-sm text-center">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Team Size</p>
                <div class="inline-flex items-baseline gap-2">
                    <span class="text-4xl font-extrabold text-cyan-600 dark:text-cyan-400">{{ $department->employees->count() }}</span>
                    <span class="text-sm font-medium text-slate-500">members</span>
                </div>
            </div>
        </div>

        {{-- Right Col: Roster --}}
        <div class="md:col-span-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-sm h-full">
                <h2 class="mb-5 text-sm font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-800 pb-3">Team Members</h2>
                <div class="space-y-2">
                    @forelse ($department->employees as $employee)
                        <div class="group flex items-center justify-between rounded-xl px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 overflow-hidden rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                                    @if($employee->profile_photo)
                                        <img src="{{ Storage::url($employee->profile_photo) }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-cyan-100 text-sm font-bold text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300">
                                            {{ substr($employee->full_name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('employees.show', $employee->id) }}" class="font-semibold text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition">{{ $employee->full_name }}</a>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $employee->job_title }}</p>
                                </div>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider 
                                {{ $employee->status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400' }}">
                                {{ $employee->status }}
                            </span>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 p-8 dark:border-slate-700">
                            <svg class="mb-2 h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No employees assigned yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Slide-over Panel --}}
    @if(Auth::user()->hasAnyRole(['admin', 'hr_manager']))
    <div x-show="isEditing" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="isEditing" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                    <div x-show="isEditing" @click.away="isEditing = false"
                         x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
                         x-transition:enter-start="translate-x-full" 
                         x-transition:enter-end="translate-x-0" 
                         x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
                         x-transition:leave-start="translate-x-0" 
                         x-transition:leave-end="translate-x-full" 
                         class="pointer-events-auto w-screen max-w-md bg-white shadow-2xl dark:bg-slate-900 flex flex-col">
                        
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white" id="slide-over-title">Edit Department</h2>
                            <button type="button" @click="isEditing = false" class="rounded-md text-slate-400 hover:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <span class="sr-only">Close panel</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto px-6 py-6 [scrollbar-width:thin]">
                            <form id="edit-form" @submit.prevent="submitForm">
                                @php
                                    $input = 'w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white transition';
                                    $label = 'block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1';
                                @endphp

                                <div class="space-y-5">
                                    <div>
                                        <label class="{{ $label }}">Department Name *</label>
                                        <input type="text" x-model="form.name" class="{{ $input }}" required>
                                    </div>
                                    <div>
                                        <label class="{{ $label }}">Department Code *</label>
                                        <input type="text" x-model="form.code" class="{{ $input }}" required>
                                    </div>
                                    <div>
                                        <label class="{{ $label }}">Department Lead (optional)</label>
                                        @if($employees->isNotEmpty())
                                            <select x-model="form.lead_employee_id" class="{{ $input }}">
                                                <option value="">— No Lead —</option>
                                                @foreach ($employees as $emp)
                                                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" x-model="form.lead_name" class="{{ $input }}" placeholder="Manual lead name...">
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="border-t border-slate-200 dark:border-slate-800 px-6 py-4 flex flex-shrink-0 justify-end gap-3 bg-slate-50 dark:bg-slate-900">
                            <button type="button" @click="isEditing = false" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</button>
                            <button form="edit-form" type="submit" :disabled="isSaving" class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-600 px-6 py-2 font-bold text-white transition hover:bg-cyan-500 disabled:opacity-50">
                                <span x-show="!isSaving">Save Changes</span>
                                <span x-show="isSaving">Saving...</span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('departmentProfile', () => ({
            isEditing: false,
            isSaving: false,
            form: {
                name: @json($department->name),
                code: @json($department->code),
                lead_name: @json($department->lead_name),
                // Since lead_employee_id is not stored in DB conventionally, we'll try to find an employee id
                lead_employee_id: @json($employees->where('full_name', $department->lead_name)->first()?->id ?? ''),
            },
            async submitForm() {
                this.isSaving = true;
                try {
                    await axios.patch('{{ route('departments.update', $department->id) }}', this.form, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    window.location.reload();
                } catch (error) {
                    let msg = 'Error updating department.';
                    if(error.response && error.response.status === 422) {
                        const errors = Object.values(error.response.data.errors).flat();
                        msg = errors.join('\n');
                    }
                    alert(msg);
                    console.error(error);
                    this.isSaving = false;
                }
            }
        }))
    });
</script>
@endsection
