@extends('hrms.layouts.app')

@section('title', 'Department Details - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">{{ $department->name }}</h1>
        <p class="text-slate-600 dark:text-slate-400">Code: {{ $department->code }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('departments.edit', $department->id) }}" class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-cyan-400">Edit</a>
        <form method="POST" action="{{ route('departments.destroy', $department->id) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600" onclick="return confirm('Are you sure?')">Delete</button>
        </form>
    </div>
</div>

<div class="grid gap-6 md:grid-cols-3">
    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6">
        <h2 class="mb-4 text-lg font-semibold">Department Info</h2>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">Name</p>
                <p class="mt-1 text-slate-900 dark:text-white">{{ $department->name }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">Code</p>
                <p class="mt-1 text-slate-900 dark:text-white">{{ $department->code }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">Lead</p>
                <p class="mt-1 text-slate-900 dark:text-white">{{ $department->lead_name }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Total Employees</p>
                <p class="mt-1 text-2xl font-semibold text-cyan-300">{{ $department->employees->count() }}</p>
            </div>
        </div>
    </div>

    <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6">
        <h2 class="mb-4 text-lg font-semibold">Employees</h2>
        <div class="space-y-3">
            @forelse ($department->employees as $employee)
                <div class="flex items-center justify-between rounded-lg bg-slate-100 p-3 dark:bg-slate-950">
                    <div>
                        <a href="{{ route('employees.show', $employee->id) }}" class="font-medium hover:text-cyan-300">{{ $employee->full_name }}</a>
                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $employee->job_title }}</p>
                    </div>
                    <span class="rounded-full bg-cyan-500/10 px-3 py-1 text-xs text-cyan-300">{{ ucfirst($employee->status) }}</span>
                </div>
            @empty
                <p class="text-slate-600 dark:text-slate-400">No employees in this department.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
