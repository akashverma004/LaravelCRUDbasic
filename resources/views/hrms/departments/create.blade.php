@extends('hrms.layouts.app')

@section('title', 'Create Department - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Create Department</h1>
    <p class="mt-2 text-slate-500 dark:text-slate-400">Add a new operational unit to your organization.</p>
</div>

@php
    $input = 'w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white transition';
    $label = 'block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1';
@endphp

<div class="rounded-3xl bg-white shadow-sm dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
    <form method="POST" action="{{ route('departments.store') }}" novalidate>
        @csrf

        <div class="p-8 sm:p-10 divide-y divide-slate-200 dark:divide-slate-800">
            
            {{-- ── Department Core Details ────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 gap-x-8 gap-y-10 md:grid-cols-3 pb-8">
                <div class="md:col-span-1">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Core Details</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">The primary identifiers for this department.</p>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="{{ $label }}">Department Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name"
                            class="{{ $input }} @error('name') border-red-400 @enderror"
                            value="{{ old('name') }}"
                            placeholder="e.g. Engineering">
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('name'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Department Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code"
                            class="{{ $input }} @error('code') border-red-400 @enderror"
                            value="{{ old('code') }}"
                            placeholder="e.g. ENG">
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('code'){{ $message }}@enderror</p>
                    </div>
                </div>
            </div>

            {{-- ── Leadership ─────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 gap-x-8 gap-y-10 md:grid-cols-3 pt-8 pb-4">
                <div class="md:col-span-1">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Leadership</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Assign a leader to head this department's operations.</p>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 gap-6">
                    <div>
                        <label class="{{ $label }}">Department Lead <span class="text-slate-400 font-normal">(optional)</span></label>

                        @if($employees->isNotEmpty())
                            <select name="lead_employee_id" class="{{ $input }} @error('lead_employee_id') border-red-400 @enderror">
                                <option value="">— No Lead —</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('lead_employee_id') == $employee->id)>{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('lead_employee_id'){{ $message }}@enderror</p>
                        @else
                            <input type="text" name="lead_name" class="{{ $input }} @error('lead_name') border-red-400 @enderror" value="{{ old('lead_name') }}" placeholder="Enter lead name, or leave blank">
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">No employees found. You can type a lead name or skip this field.</p>
                            <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('lead_name'){{ $message }}@enderror</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Footer ───────────────────────────────────────────────── --}}
        <div class="flex items-center justify-end gap-3 rounded-b-3xl bg-slate-50 p-6 px-8 dark:bg-slate-800/50">
            <a href="{{ route('departments.index') }}"
               class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700 transition-colors">
                Cancel
            </a>
            <button type="submit"
                class="rounded-xl bg-cyan-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-cyan-500 transition-all">
                Create Department
            </button>
        </div>
    </form>
</div>
@endsection

