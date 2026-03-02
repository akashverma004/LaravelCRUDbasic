@extends('hrms.layouts.app')

@section('title', 'Create Employee - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Add New Employee</h1>
    <p class="text-slate-400">Create a new employee record</p>
</div>

<div class="max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-6">
    <form method="POST" action="{{ route('employees.store') }}" class="space-y-4">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-300">Full Name</label>
                <input type="text" name="full_name" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('full_name') border-red-500 @enderror" value="{{ old('full_name') }}" required>
                @error('full_name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Email</label>
                <input type="email" name="email" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('email') border-red-500 @enderror" value="{{ old('email') }}" required>
                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Phone</label>
                <input type="tel" name="phone" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('phone') border-red-500 @enderror" value="{{ old('phone') }}" required>
                @error('phone')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Job Title</label>
                <input type="text" name="job_title" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('job_title') border-red-500 @enderror" value="{{ old('job_title') }}" required>
                @error('job_title')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Department</label>
                <select name="department_id" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('department_id') border-red-500 @enderror" required>
                    <option value="">Select Department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>
                    @endforeach
                </select>
                @error('department_id')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Employment Type</label>
                <select name="employment_type" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('employment_type') border-red-500 @enderror" required>
                    <option value="">Select Type</option>
                    <option value="full-time" @selected(old('employment_type') === 'full-time')>Full-time</option>
                    <option value="part-time" @selected(old('employment_type') === 'part-time')>Part-time</option>
                    <option value="contract" @selected(old('employment_type') === 'contract')>Contract</option>
                    <option value="intern" @selected(old('employment_type') === 'intern')>Intern</option>
                </select>
                @error('employment_type')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Salary</label>
                <input type="number" step="0.01" min="0" name="salary" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('salary') border-red-500 @enderror" value="{{ old('salary') }}" required>
                @error('salary')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Join Date</label>
                <input type="date" name="joined_on" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('joined_on') border-red-500 @enderror" value="{{ old('joined_on') }}" required>
                @error('joined_on')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Status</label>
                <select name="status" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 @error('status') border-red-500 @enderror" required>
                    <option value="">Select Status</option>
                    <option value="active" @selected(old('status') === 'active')>Active</option>
                    <option value="on-leave" @selected(old('status') === 'on-leave')>On Leave</option>
                    <option value="resigned" @selected(old('status') === 'resigned')>Resigned</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Create Employee</button>
            <a href="{{ route('employees.index') }}" class="rounded-lg border border-slate-700 px-6 py-2 font-semibold text-slate-300 hover:bg-slate-800">Cancel</a>
        </div>
    </form>
</div>
@endsection
