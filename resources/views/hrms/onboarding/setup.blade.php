@extends('hrms.layouts.app')

@section('title', 'Company Setup')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold dark:text-white text-slate-900">Welcome to {{ $tenant->name }}</h1>
        <p class="mt-2 text-slate-600 dark:text-slate-400">Complete this setup to start using your company workspace.</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form method="POST" action="{{ route('onboarding.store') }}" class="grid gap-4 md:grid-cols-2">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Company Name</label>
                <input name="name" value="{{ old('name', $tenant->name) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Company Email</label>
                <input type="email" name="email" value="{{ old('email', $tenant->email) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone</label>
                <input name="phone" value="{{ old('phone', $tenant->phone) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Country</label>
                <select name="country" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    @foreach($countries as $code => $label)
                        <option value="{{ $code }}" @selected(old('country', $tenant->country ?? 'IN') === $code)>{{ $label }} ({{ $code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Timezone</label>
                <input name="timezone" value="{{ old('timezone', $tenant->timezone ?? 'Asia/Kolkata') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Currency</label>
                <input name="currency" value="{{ old('currency', $tenant->currency ?? 'INR') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Address</label>
                <textarea name="address" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('address', $tenant->address) }}</textarea>
            </div>

            <div class="md:col-span-2 mt-2">
                <h2 class="text-lg font-semibold dark:text-white text-slate-900">Default Leave Limits</h2>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Annual</label>
                <input type="number" min="0" name="annual_limit" value="{{ old('annual_limit', $leavePolicy->annual_limit ?? 15) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Sick</label>
                <input type="number" min="0" name="sick_limit" value="{{ old('sick_limit', $leavePolicy->sick_limit ?? 10) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Casual</label>
                <input type="number" min="0" name="casual_limit" value="{{ old('casual_limit', $leavePolicy->casual_limit ?? 8) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Unpaid</label>
                <input type="number" min="0" name="unpaid_limit" value="{{ old('unpaid_limit', $leavePolicy->unpaid_limit ?? 0) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            </div>

            <div class="md:col-span-2 pt-3">
                <button type="submit" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Complete Setup</button>
            </div>
        </form>
    </div>
</div>
@endsection
