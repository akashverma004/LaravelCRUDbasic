@extends('hrms.layouts.app')

@section('title', 'Departments - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Departments</h1>
        <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Manage organizational structure and teams</p>
    </div>
    @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
        <a href="{{ route('departments.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-cyan-500 transition-all focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Department
        </a>
    @endif
</div>

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($departments as $department)
        <a href="{{ route('departments.show', $department->id) }}" class="group relative flex flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:border-cyan-500 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-cyan-500/50 transition-all overflow-hidden">
            <div class="absolute right-0 top-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-cyan-50 opacity-50 dark:bg-cyan-900/10 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <span class="inline-block rounded-lg bg-slate-100 px-3 py-1 text-xs font-extrabold tracking-wider text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        {{ $department->code }}
                    </span>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </span>
                </div>

                <div class="mt-6">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">{{ $department->name }}</h3>
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Department Lead</p>
                            <p class="mt-0.5 text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $department->lead_name ?? '—' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">People</p>
                            <p class="mt-0.5 text-sm font-bold text-cyan-600 dark:text-cyan-400">{{ $department->employees_count }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 py-16 text-center dark:border-slate-800">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500 mb-4">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m-5 0v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">No departments</h3>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400 max-w-sm">Get started by creating your first team structure.</p>
            @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                <a href="{{ route('departments.create') }}" class="mt-6 inline-flex items-center gap-1.5 rounded-xl bg-cyan-600 px-5 py-2 text-sm font-bold text-white hover:bg-cyan-500 transition-colors">
                    Add Department
                </a>
            @endif
        </div>
    @endforelse
</div>
@endsection
