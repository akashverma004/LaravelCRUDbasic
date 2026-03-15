@extends('hrms.layouts.app')

@section('title', $role->display_name . ' Users - PeopleFlow HRMS')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white capitalize">{{ $role->display_name }}</h1>
            <p class="text-sm text-slate-500 mt-1">Users assigned to the <span class="font-medium text-slate-700 dark:text-slate-300">{{ $role->name }}</span> role.</p>
        </div>
        <a href="{{ route('roles.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50 dark:text-slate-300 dark:hover:bg-slate-800 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Back to Roles
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($users as $user)
                <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/50 transition-all hover:border-cyan-200 dark:hover:border-cyan-900/50">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-700 font-bold dark:bg-cyan-500/10 dark:text-cyan-400">
                             {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500 truncate mt-0.5">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center rounded-xl border border-dashed border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/20">
                    <p class="text-sm font-medium text-slate-500">No users assigned to this role.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
