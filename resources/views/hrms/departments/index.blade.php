@extends('hrms.layouts.app')

@section('title', 'Departments - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Departments</h1>
        <p class="text-slate-600 dark:text-slate-400">Manage organizational structure</p>
    </div>
    <a href="{{ route('departments.create') }}" class="rounded-lg bg-cyan-500 px-4 py-2 font-semibold text-slate-900 hover:bg-cyan-400">+ Add Department</a>
</div>

<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @forelse ($departments as $department)
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6 hover:border-cyan-500/30">
            <h3 class="text-lg font-semibold">{{ $department->name }}</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Code: {{ $department->code }}</p>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Lead: {{ $department->lead_name }}</p>
            <p class="mt-2 text-xs text-cyan-300">{{ $department->employees_count }} employees</p>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('departments.show', $department->id) }}" class="text-sm text-cyan-400 hover:text-cyan-300">View</a>
                <a href="{{ route('departments.edit', $department->id) }}" class="text-sm text-cyan-400 hover:text-cyan-300">Edit</a>
            </div>
        </div>
    @empty
        <p class="text-slate-600 dark:text-slate-400">No departments yet.</p>
    @endforelse
</div>
@endsection
