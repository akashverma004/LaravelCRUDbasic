@extends('hrms.layouts.app')

@section('title', 'Create Department - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Create Department</h1>
    <p class="text-slate-600 dark:text-slate-400">Add a new department to your organization</p>
</div>

<div class="max-w-2xl rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6">
    <form method="POST" action="{{ route('departments.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Department Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
            @error('name')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Department Code <span class="text-red-500">*</span></label>
            <input type="text" name="code" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('code') border-red-500 @enderror" value="{{ old('code') }}" placeholder="e.g., ENG" required>
            @error('code')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Department Lead <span class="text-slate-400 font-normal">(optional)</span></label>

            @if($employees->isNotEmpty())
                {{-- Select from existing employees --}}
                <select name="lead_employee_id" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('lead_employee_id') border-red-500 @enderror">
                    <option value="">— No Lead —</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @selected(old('lead_employee_id') == $employee->id)>{{ $employee->full_name }}</option>
                    @endforeach
                </select>
                @error('lead_employee_id')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            @else
                {{-- No employees yet — allow entering a name manually --}}
                <input type="text" name="lead_name" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('lead_name') border-red-500 @enderror" value="{{ old('lead_name') }}" placeholder="Enter lead name, or leave blank">
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">No employees found. You can type a lead name or skip this field.</p>
                @error('lead_name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            @endif
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Create Department</button>
            <a href="{{ route('departments.index') }}" class="rounded-lg border border-slate-300 px-6 py-2 font-semibold text-slate-700 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 hover:bg-slate-100">Cancel</a>
        </div>
    </form>
</div>
@endsection

