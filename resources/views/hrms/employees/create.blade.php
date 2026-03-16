@extends('hrms.layouts.app')

@section('title', 'Add Employee - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-white uppercase"><span class="text-cyan-500">Add</span> Employee</h1>
    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-1">Initiating a new personnel registry for your organization.</p>
</div>

@php
    $input  = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white transition-all';
    $label  = 'block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1';
@endphp

<div class="rounded-2xl bg-white shadow-sm dark:bg-slate-900/50 border border-slate-200 dark:border-white/5 overflow-hidden">
    <form method="POST" action="{{ route('employees.store') }}" id="employee-form" novalidate>
        @csrf

        <div class="space-y-12 p-8 sm:p-10 divide-y divide-slate-100 dark:divide-white/5">
            
            {{-- ── Work Details ────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="md:col-span-1">
                    <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">Employment Credentials</h2>
                    <p class="mt-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">Core identity and logistical data for the new personnel.</p>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="{{ $label }}">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" id="full_name"
                            class="{{ $input }} @error('full_name') border-rose-500 @enderror"
                            value="{{ old('full_name') }}"
                            data-rules="required|min:2|max:255"
                            placeholder="e.g. Jane Smith">
                        <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1 min-h-[16px]">@error('full_name'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email"
                            class="{{ $input }} @error('email') border-red-400 @enderror"
                            value="{{ old('email') }}"
                            data-rules="required|email"
                            placeholder="jane@company.com">
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('email'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Temporary Password <span class="text-red-500">*</span></label>
                        <input type="text" name="password" id="password"
                            class="{{ $input }} @error('password') border-red-400 @enderror"
                            data-rules="required|min:8"
                            placeholder="Secure password">
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('password'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Phone <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" id="phone"
                            class="{{ $input }} @error('phone') border-red-400 @enderror"
                            value="{{ old('phone') }}"
                            data-rules="required|max:30"
                            placeholder="+91 98765 43210">
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('phone'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Job Title <span class="text-red-500">*</span></label>
                        <input type="text" name="job_title" id="job_title"
                            class="{{ $input }} @error('job_title') border-red-400 @enderror"
                            value="{{ old('job_title') }}"
                            data-rules="required|max:255"
                            placeholder="Product Designer">
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('job_title'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Department <span class="text-red-500">*</span></label>
                        @if($departments->isEmpty())
                            <div class="mt-1 rounded-lg border border-amber-300 bg-amber-50 dark:bg-amber-950/50 dark:border-amber-700 px-3 py-2 text-sm text-amber-700 dark:text-amber-300">
                                No departments. <a href="{{ route('departments.create') }}" class="font-semibold underline">Create one.</a>
                            </div>
                        @else
                            <select name="department_id" id="department_id"
                                class="{{ $input }} @error('department_id') border-red-400 @enderror"
                                data-rules="required">
                                <option value="">Select Department</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}" @selected(old('department_id') == $dept->id)>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('department_id'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Direct Manager</label>
                        <select name="manager_id" class="{{ $input }}">
                            <option value="">No Manager</option>
                            @foreach ($managers as $manager)
                                <option value="{{ $manager->id }}" @selected(old('manager_id') == $manager->id)>
                                    {{ $manager->full_name }}{{ $manager->department ? ' ('.$manager->department->name.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">System Role</label>
                        <select name="role_id" class="{{ $input }}">
                            <option value="">Select Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id') == $role->id || $role->name === 'employee')>
                                    {{ $role->display_name ?? ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">Employment Type <span class="text-red-500">*</span></label>
                        <select name="employment_type" id="employment_type"
                            class="{{ $input }} @error('employment_type') border-red-400 @enderror"
                            data-rules="required">
                            <option value="">Select Type</option>
                            <option value="full-time"  @selected(old('employment_type') === 'full-time')>Full-time</option>
                            <option value="part-time"  @selected(old('employment_type') === 'part-time')>Part-time</option>
                            <option value="contract"   @selected(old('employment_type') === 'contract')>Contract</option>
                            <option value="intern"     @selected(old('employment_type') === 'intern')>Intern</option>
                        </select>
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('employment_type'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Annual Salary <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-xs font-black pointer-events-none">₹</span>
                            <input type="number" step="0.01" min="0" max="9999999999999" name="salary" id="salary"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-8 pr-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white transition-all @error('salary') border-rose-500 @enderror"
                                value="{{ old('salary') }}"
                                data-rules="required|min_val:0|max_val:9999999999999"
                                placeholder="500000">
                        </div>
                        <p class="mt-1.5 text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-1 min-h-[16px]">@error('salary'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Join Date <span class="text-red-500">*</span></label>
                        <input type="date" name="joined_on" id="joined_on"
                            class="{{ $input }} @error('joined_on') border-red-400 @enderror"
                            value="{{ old('joined_on') }}"
                            max="{{ date('Y-m-d') }}"
                            data-rules="required|past_or_today">
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('joined_on'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Account Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status"
                            class="{{ $input }} @error('status') border-red-400 @enderror"
                            data-rules="required">
                            <option value="">Select Status</option>
                            <option value="active"   @selected(old('status', 'active') === 'active')>Active</option>
                            <option value="on-leave" @selected(old('status') === 'on-leave')>On Leave</option>
                        </select>
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('status'){{ $message }}@enderror</p>
                    </div>
                </div>
            </div>

            {{-- ── Location ─────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 gap-x-8 gap-y-10 md:grid-cols-3 pt-12">
                <div class="md:col-span-1">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Location details</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Where is this employee based? Essential for payroll and holidays.</p>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="{{ $label }}">Country <span class="text-red-500">*</span></label>
                        <input type="text" name="country" list="country-options" maxlength="3"
                            class="{{ $input }} @error('country') border-red-400 @enderror"
                            value="{{ old('country', 'IN') }}"
                            data-rules="required"
                            placeholder="e.g. IN">
                        <datalist id="country-options">
                            @foreach ($countries as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </datalist>
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('country'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">State / Province <span class="text-red-500">*</span></label>
                        <input type="text" name="state" list="state-options" maxlength="3"
                            class="{{ $input }} @error('state') border-red-400 @enderror"
                            value="{{ old('state') }}"
                            data-rules="required"
                            placeholder="e.g. MH">
                        <datalist id="state-options">
                            @foreach ($states as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </datalist>
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('state'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">City <span class="text-red-500">*</span></label>
                        <input type="text" name="city"
                            class="{{ $input }} @error('city') border-red-400 @enderror"
                            value="{{ old('city') }}"
                            data-rules="required|max:100"
                            placeholder="e.g. Mumbai">
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('city'){{ $message }}@enderror</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Zip / Postal Code</label>
                        <input type="text" name="zip_code"
                            class="{{ $input }} @error('zip_code') border-red-400 @enderror"
                            value="{{ old('zip_code') }}"
                            data-rules="max:20"
                            placeholder="e.g. 400001">
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('zip_code'){{ $message }}@enderror</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="{{ $label }}">Address Line <span class="text-red-500">*</span></label>
                        <textarea name="address" rows="2"
                            class="{{ $input }} @error('address') border-red-400 @enderror"
                            data-rules="required|max:500"
                            placeholder="Full residential address...">{{ old('address') }}</textarea>
                        <p class="mt-1 text-xs text-red-500 min-h-[16px]">@error('address'){{ $message }}@enderror</p>
                    </div>
                </div>
            </div>

            {{-- ── Personal Preferences ─────────────────────────────────── --}}
            <div class="grid grid-cols-1 gap-x-8 gap-y-10 md:grid-cols-3 pt-12">
                <div class="md:col-span-1">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Cultural Fit</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Optional details to help teams organize get-togethers and events.</p>
                </div>
                
                <div class="md:col-span-2 grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="{{ $label }}">Hobbies</label>
                        <textarea name="hobbies" rows="2" class="{{ $input }}" placeholder="Reading, hiking...">{{ old('hobbies') }}</textarea>
                    </div>
                    <div>
                        <label class="{{ $label }}">Food Preference</label>
                        <select name="food_preference" class="{{ $input }}">
                            <option value="">No preference</option>
                            <option value="veg"     @selected(old('food_preference') === 'veg')>Vegetarian</option>
                            <option value="non-veg" @selected(old('food_preference') === 'non-veg')>Non-Vegetarian</option>
                        </select>
                    </div>
                    <div>
                        <label class="{{ $label }}">Allergies / Health Note</label>
                        <input type="text" name="health_issues" class="{{ $input }}"
                            value="{{ old('health_issues') }}" placeholder="e.g. Peanut allergy...">
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 bg-slate-50/50 p-8 dark:bg-white/5">
            <a href="{{ route('employees.index') }}"
               class="rounded-xl px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-slate-900 dark:hover:text-white transition-all">
                Cancel
            </a>
            <button type="submit"
                class="rounded-xl bg-slate-900 border border-white/10 px-8 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                Deploy Registry
            </button>
        </div>

    </form>
</div>

{{-- ── Real-time Inline Validation ──────────────────────────────── --}}
<script>
(function () {
    const validators = {
        required:      (v)    => v.trim() !== ''                                  || 'This field is required.',
        email:         (v)    => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())      || 'Enter a valid email address.',
        min:           (v, n) => v.trim().length >= +n                             || 'Minimum ' + n + ' characters required.',
        max:           (v, n) => v.trim().length <= +n                             || 'Maximum ' + n + ' characters allowed.',
        min_val:       (v, n) => +v >= +n                                          || 'Value must be at least ' + n + '.',
        max_val:       (v, n) => +v <= +n                                          || 'Value must not exceed ' + Number(n).toLocaleString() + '.',
        past_or_today: (v)    => !v || v <= new Date().toISOString().slice(0,10)  || 'Date cannot be in the future.',
    };

    function getErrorEl(input) {
        // Error paragraph is always the next sibling <p> after the input (or its wrapper)
        return input.closest('div')?.querySelector('p.text-red-500, p.text-xs');
    }

    function validate(input) {
        const rules  = (input.dataset.rules || '').split('|').filter(Boolean);
        const value  = input.value;
        const errEl  = getErrorEl(input);

        for (const rule of rules) {
            const [name, ...args] = rule.split(':');
            const fn = validators[name];
            if (!fn) continue;
            const result = fn(value, ...args);
            if (result !== true) {
                input.classList.add('border-red-400', 'focus:ring-red-400');
                input.classList.remove('border-emerald-400', 'focus:ring-emerald-400', 'border-slate-300');
                if (errEl) errEl.textContent = result;
                return false;
            }
        }

        // Passed
        input.classList.remove('border-red-400', 'focus:ring-red-400');
        if (value.trim()) {
            input.classList.add('border-emerald-400');
            input.classList.remove('border-slate-300');
        }
        if (errEl) errEl.textContent = '';
        return true;
    }

    document.querySelectorAll('[data-rules]').forEach(function (input) {
        input.addEventListener('blur', function () { validate(input); });
        input.addEventListener('input', function () {
            if (input.classList.contains('border-red-400') || input.classList.contains('border-emerald-400')) {
                validate(input);
            }
        });
        input.addEventListener('change', function () { validate(input); }); // for selects
    });

    var form = document.getElementById('employee-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            var allValid = true;
            form.querySelectorAll('[data-rules]').forEach(function (input) {
                if (!validate(input)) allValid = false;
            });
            if (!allValid) {
                e.preventDefault();
                var first = form.querySelector('.border-red-400');
                if (first) {
                    first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    first.focus();
                }
            }
        });
    }
})();
</script>
@endsection
