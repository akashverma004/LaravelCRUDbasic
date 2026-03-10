@extends('hrms.layouts.app')

@section('title', 'Employees - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Employees</h1>
        <p class="text-slate-600 dark:text-slate-400">Manage your workforce</p>
    </div>
    @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
        <a href="{{ route('employees.create') }}" class="rounded-lg bg-cyan-500 px-4 py-2 font-semibold text-slate-900 hover:bg-cyan-400">+ Add Employee</a>
    @endif
</div>

<div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
    <form method="GET" action="{{ route('employees.index') }}" class="flex flex-wrap items-end gap-3">
        <div>
            <label for="q" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Search</label>
            <input
                id="q"
                type="text"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                placeholder="Name or email"
                class="mt-1 w-64 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950"
            >
        </div>
        <div>
            <label for="department_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Filter by Department</label>
            <select id="department_id" name="department_id" class="mt-1 w-56 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950">
                <option value="">All Departments</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? '') == $department->id)>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="role_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Filter by Role</label>
            <select id="role_id" name="role_id" class="mt-1 w-56 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950">
                <option value="">All Roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected(($filters['role_id'] ?? '') == $role->id)>
                        {{ $role->display_name ?? ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-cyan-400">Apply</button>
        <a href="{{ route('employees.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Reset</a>
    </form>
</div>

<div class="rounded-3xl border border-slate-200 bg-white overflow-hidden shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:bg-slate-950/50 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800">
                    <th class="px-6 py-4">Employee</th>
                    <th class="px-6 py-4">Contact</th>
                    <th class="px-6 py-4">Job Details</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                @forelse ($employees as $employee)
                    <tr class="group hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-xl">
                                    @if($employee->profile_photo)
                                        <img src="{{ Storage::url($employee->profile_photo) }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-xs font-bold text-slate-400 dark:from-slate-800 dark:to-slate-700">
                                            {{ substr($employee->full_name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $employee->full_name }}</p>
                                    <p class="truncate text-[10px] font-medium text-slate-400 uppercase tracking-tighter">{{ $employee->job_title }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $employee->email }}</p>
                            <p class="text-[10px] text-slate-400">{{ $employee->phone ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex w-fit items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400">{{ $employee->department->name }}</span>
                                <p class="text-[11px] text-slate-400">{{ $employee->city }}, {{ $employee->state }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider
                                {{ $employee->status === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 
                                   ($employee->status === 'on-leave' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400') }}">
                                <span class="h-1 w-1 rounded-full {{ $employee->status === 'active' ? 'bg-emerald-500' : ($employee->status === 'on-leave' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                                {{ $employee->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('employees.show', $employee->id) }}" class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-white hover:text-cyan-600 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-cyan-400" title="View Profile">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                                    <a href="{{ route('employees.edit', $employee->id) }}" class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-white hover:text-amber-600 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-amber-400" title="Edit">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-12 w-12 text-slate-200 dark:text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <p class="mt-4 text-sm font-medium text-slate-500">No employees found matching your criteria.</p>
                            </div>
                        </td>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $employees->links('pagination::tailwind') }}
</div>
@endsection
