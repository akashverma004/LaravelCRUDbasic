@extends('hrms.layouts.app')

@section('title', 'Edit Department - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Edit Department</h1>
    <p class="text-slate-400">Update department information</p>
</div>

<div class="max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-6">
    <form method="POST" action="{{ route('departments.update', $department->id) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label class="block text-sm font-medium text-slate-300">Department Name</label>
            <input type="text" name="name" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('name') border-red-500 @enderror" value="{{ old('name', $department->name) }}" required>
            @error('name')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300">Department Code</label>
            <input type="text" name="code" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('code') border-red-500 @enderror" value="{{ old('code', $department->code) }}" required>
            @error('code')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300">Department Lead</label>
            <input type="text" name="lead_name" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('lead_name') border-red-500 @enderror" value="{{ old('lead_name', $department->lead_name) }}" required>
            @error('lead_name')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Update Department</button>
            <a href="{{ route('departments.show', $department->id) }}" class="rounded-lg border border-slate-700 px-6 py-2 font-semibold text-slate-300 hover:bg-slate-800">Cancel</a>
        </div>
    </form>
</div>
@endsection
