@extends('hrms.layouts.app')

@section('title', 'Edit Employee - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Edit Employee</h1>
    <p class="text-slate-600 dark:text-slate-400">Update employee information</p>
</div>

<div class="max-w-4xl rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6">
    <form method="POST" action="{{ route('employees.update', $employee->id) }}" class="space-y-6">
        @csrf
        @method('PATCH')

        <div>
            <h2 class="text-lg font-semibold">Work Details</h2>
            <div class="mt-3 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Full Name</label>
                    <input type="text" name="full_name" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('full_name') border-red-500 @enderror" value="{{ old('full_name', $employee->full_name) }}" required>
                    @error('full_name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                    <input type="email" name="email" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('email') border-red-500 @enderror" value="{{ old('email', $employee->email) }}" required>
                    @error('email')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Reset Password 
                        <span class="font-normal text-slate-400 text-xs ml-1">(Leave blank to keep current)</span>
                    </label>
                    <input type="text" name="password" minlength="8" placeholder="New temporary password..." class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('password') border-red-500 @enderror" autocomplete="off" autocorrect="off">
                    @error('password')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone</label>
                    <input type="tel" name="phone" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('phone') border-red-500 @enderror" value="{{ old('phone', $employee->phone) }}" required>
                    @error('phone')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Job Title</label>
                    <input type="text" name="job_title" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('job_title') border-red-500 @enderror" value="{{ old('job_title', $employee->job_title) }}" required>
                    @error('job_title')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Department</label>
                    <select name="department_id" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('department_id') border-red-500 @enderror" required>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $employee->department_id) == $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Manager</label>
                    <select name="manager_id" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('manager_id') border-red-500 @enderror">
                        <option value="">No Manager</option>
                        @foreach ($managers as $manager)
                            <option value="{{ $manager->id }}" @selected(old('manager_id', $employee->manager_id) == $manager->id)>
                                {{ $manager->full_name }}{{ $manager->department ? ' (' . $manager->department->name . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('manager_id')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Role</label>
                    <select name="role_id" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2">
                        <option value="">-- Select Role --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected(old('role_id', $employee->role_id) == $role->id)>{{ $role->display_name ?? ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('role_id')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Employment Type</label>
                    <select name="employment_type" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('employment_type') border-red-500 @enderror" required>
                        <option value="full-time" @selected(old('employment_type', $employee->employment_type) === 'full-time')>Full-time</option>
                        <option value="part-time" @selected(old('employment_type', $employee->employment_type) === 'part-time')>Part-time</option>
                        <option value="contract" @selected(old('employment_type', $employee->employment_type) === 'contract')>Contract</option>
                        <option value="intern" @selected(old('employment_type', $employee->employment_type) === 'intern')>Intern</option>
                    </select>
                    @error('employment_type')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Salary</label>
                    <input type="number" step="0.01" min="0" name="salary" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('salary') border-red-500 @enderror" value="{{ old('salary', $employee->salary) }}" required>
                    @error('salary')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Join Date</label>
                                <input type="date" name="joined_on" class="transition-colors duration-300 mt-1 w-full rounded-lg border border-slate-300 bg-white text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-white px-3 py-2 appearance-auto @error('joined_on') border-red-500 @enderror" value="{{ old('joined_on', optional($employee->joined_on)->format('Y-m-d')) }}" required>
                    @error('joined_on')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                    <select name="status" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('status') border-red-500 @enderror" required>
                        <option value="active" @selected(old('status', $employee->status) === 'active')>Active</option>
                        <option value="on-leave" @selected(old('status', $employee->status) === 'on-leave')>On Leave</option>
                        <option value="resigned" @selected(old('status', $employee->status) === 'resigned')>Resigned</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold">Location Details</h2>
            <div class="mt-3 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Country</label>
                    <input type="text" name="country" list="country-options" maxlength="3" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('country') border-red-500 @enderror" value="{{ old('country', $employee->country) }}" required>
                    <datalist id="country-options">
                        @foreach ($countries as $code => $name)
                            <option value="{{ $code }}" label="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </datalist>
                    @error('country')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">State</label>
                    <input type="text" name="state" list="state-options" maxlength="3" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('state') border-red-500 @enderror" value="{{ old('state', $employee->state) }}" required>
                    <datalist id="state-options">
                        @foreach ($states as $code => $name)
                            <option value="{{ $code }}" label="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </datalist>
                    @error('state')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">City</label>
                    <input type="text" name="city" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('city') border-red-500 @enderror" value="{{ old('city', $employee->city) }}" required>
                    @error('city')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Address</label>
                    <textarea name="address" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('address') border-red-500 @enderror" required>{{ old('address', $employee->address) }}</textarea>
                    @error('address')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold">Personal Preferences (Optional)</h2>
            <div class="mt-3 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Hobbies</label>
                    <textarea name="hobbies" rows="2" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('hobbies') border-red-500 @enderror">{{ old('hobbies', $employee->hobbies) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Likes</label>
                    <textarea name="likes" rows="2" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('likes') border-red-500 @enderror">{{ old('likes', $employee->likes) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Food Preference</label>
                    <select name="food_preference" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('food_preference') border-red-500 @enderror">
                        <option value="">Select</option>
                        <option value="veg" @selected(old('food_preference', $employee->food_preference) === 'veg')>Veg</option>
                        <option value="non-veg" @selected(old('food_preference', $employee->food_preference) === 'non-veg')>Non-Veg</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Health Issues</label>
                    <input type="text" name="health_issues" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 @error('health_issues') border-red-500 @enderror" value="{{ old('health_issues', $employee->health_issues) }}">
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Update Employee</button>
            <a href="{{ route('employees.show', $employee->id) }}" class="rounded-lg border border-slate-300 px-6 py-2 font-semibold text-slate-700 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 hover:bg-slate-100">Cancel</a>
        </div>
    </form>
</div>
@endsection
