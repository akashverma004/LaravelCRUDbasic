@extends('hrms.layouts.app')

@section('title', 'Tenants')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold dark:text-white text-slate-900">Tenants</h1>
        <p class="text-slate-600 dark:text-slate-400">Manage companies on this platform.</p>
    </div>
    <a href="{{ route('tenants.create') }}" class="rounded-lg bg-cyan-500 px-4 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Add Tenant</a>
</div>

<form method="GET" class="mb-4 grid gap-3 md:grid-cols-3">
    <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search name/code/email" class="rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
    <select name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        <option value="">All Status</option>
        <option value="1" @selected(($filters['status'] ?? '') === '1')>Active</option>
        <option value="0" @selected(($filters['status'] ?? '') === '0')>Inactive</option>
    </select>
    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold dark:border-slate-700">Filter</button>
</form>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
    <table class="w-full text-sm">
        <thead class="bg-slate-100 dark:bg-slate-800">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Code</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $tenant)
                <tr class="border-t border-slate-200 dark:border-slate-800">
                    <td class="px-4 py-3">{{ $tenant->name }}</td>
                    <td class="px-4 py-3">{{ $tenant->code }}</td>
                    <td class="px-4 py-3">{{ $tenant->email }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2 py-1 text-xs {{ $tenant->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('tenants.edit', $tenant->id) }}" class="mr-2 text-cyan-600 hover:text-cyan-700">Edit</a>
                        <form action="{{ route('tenants.destroy', $tenant->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-700" onclick="return confirm('Delete this tenant?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">No tenants found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $tenants->links() }}
</div>
@endsection
