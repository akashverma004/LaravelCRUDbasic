@extends('hrms.layouts.app')

@section('title', 'Employees - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Employees</h1>
        <p class="text-slate-600 dark:text-slate-400">Manage your workforce</p>
    </div>
    <a href="{{ route('employees.create') }}" class="rounded-lg bg-cyan-500 px-4 py-2 font-semibold text-slate-900 hover:bg-cyan-400">+ Add Employee</a>
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

<div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6">
    <table class="w-full">
        <thead>
            <tr class="border-b border-slate-200 dark:border-slate-800">
                <th class="px-4 py-3 text-left text-sm font-semibold">Name</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Email</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Department</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Role</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($employees as $employee)
                <tr class="border-b border-slate-200 hover:bg-slate-100 dark:border-slate-800 dark:hover:bg-slate-800/50">
                    <td class="px-4 py-3">{{ $employee->full_name }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $employee->email }}</td>
                    <td class="px-4 py-3 text-sm">{{ $employee->department->name }}</td>
                    <td class="px-4 py-3 text-sm">{{ $employee->role?->display_name ?? ($employee->role ? ucfirst($employee->role->name) : '-') }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-3 py-1 text-xs {{
                            $employee->status === 'active'
                                ? 'bg-emerald-500/10 text-emerald-300'
                                : ($employee->status === 'on-leave'
                                    ? 'bg-amber-500/10 text-amber-300'
                                    : 'bg-rose-500/10 text-rose-300')
                        }}">{{ ucfirst($employee->status) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <a
                                href="{{ route('employees.show', $employee->id) }}"
                                class="text-cyan-400 hover:text-cyan-300"
                                title="View employee"
                                aria-label="View employee"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 3C5 3 1.73 7.11.46 9.12a1.62 1.62 0 0 0 0 1.76C1.73 12.89 5 17 10 17s8.27-4.11 9.54-6.12a1.62 1.62 0 0 0 0-1.76C18.27 7.11 15 3 10 3Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z" />
                                </svg>
                            </a>
                            <a
                                href="{{ route('employees.edit', $employee->id) }}"
                                class="text-amber-400 hover:text-amber-300"
                                title="Edit employee"
                                aria-label="Edit employee"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="m13.586 3.586 2.828 2.828a2 2 0 0 1 0 2.829l-7.6 7.6a2 2 0 0 1-.878.514l-3.11.889a.75.75 0 0 1-.928-.928l.889-3.11a2 2 0 0 1 .514-.878l7.6-7.6a2 2 0 0 1 2.829 0ZM12.525 6.06 6.12 12.464a.5.5 0 0 0-.129.22l-.59 2.066 2.066-.59a.5.5 0 0 0 .22-.129l6.404-6.404-1.566-1.566Z" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-slate-600 dark:text-slate-400">No employees found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $employees->links('pagination::tailwind') }}
</div>
@endsection
