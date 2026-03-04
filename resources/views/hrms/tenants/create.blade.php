@extends('hrms.layouts.app')

@section('title', 'Create Tenant')

@section('content')
<div class="mx-auto max-w-3xl">
    <h1 class="mb-6 text-3xl font-bold dark:text-white text-slate-900">Create Tenant</h1>

    <form method="POST" action="{{ route('tenants.store') }}" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 grid gap-4 md:grid-cols-2">
        @csrf
        <div>
            <label class="block text-sm font-medium">Name</label>
            <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
        </div>
        <div>
            <label class="block text-sm font-medium">Code</label>
            <input name="code" value="{{ old('code') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
        </div>
        <div>
            <label class="block text-sm font-medium">Slug</label>
            <input name="slug" value="{{ old('slug') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium">Phone</label>
            <input name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium">Country</label>
            <input name="country" value="{{ old('country', 'IN') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 uppercase dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium">Timezone</label>
            <input name="timezone" value="{{ old('timezone', 'Asia/Kolkata') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
        </div>
        <div>
            <label class="block text-sm font-medium">Currency</label>
            <input name="currency" value="{{ old('currency', 'INR') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 uppercase dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
        </div>
        <div class="md:col-span-2">
            <label class="inline-flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                <span class="text-sm">Active</span>
            </label>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium">Address</label>
            <textarea name="address" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('address') }}</textarea>
        </div>
        <div class="md:col-span-2">
            <button class="rounded-lg bg-cyan-500 px-5 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Create Tenant</button>
        </div>
    </form>
</div>
@endsection
