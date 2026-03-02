@extends('hrms.layouts.app')

@section('title', 'Employees - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Employees</h1>
        <p class="text-slate-400">Manage your workforce</p>
    </div>
    <a href="{{ route('employees.create') }}" class="rounded-lg bg-cyan-500 px-4 py-2 font-semibold text-slate-900 hover:bg-cyan-400">+ Add Employee</a>
</div>

<div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
    <table class="w-full">
        <thead>
            <tr class="border-b border-slate-800">
                <th class="px-4 py-3 text-left text-sm font-semibold">Name</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Email</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Department</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($employees as $employee)
                <tr class="border-b border-slate-800 hover:bg-slate-800/50">
                    <td class="px-4 py-3">{{ $employee->full_name }}</td>
                    <td class="px-4 py-3 text-sm text-slate-400">{{ $employee->email }}</td>
                    <td class="px-4 py-3 text-sm">{{ $employee->department->name }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full bg-cyan-500/10 px-3 py-1 text-xs text-cyan-300">{{ ucfirst($employee->status) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('employees.show', $employee->id) }}" class="text-cyan-400 hover:text-cyan-300">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">No employees found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $employees->links('pagination::tailwind') }}
</div>
@endsection
