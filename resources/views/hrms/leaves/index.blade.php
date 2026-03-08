@extends('hrms.layouts.app')

@section('title', 'Leave Requests - PeopleFlow HRMS')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="space-y-2">
        <h1 class="text-4xl font-bold tracking-tight">Who's away</h1>
        <div class="flex items-center gap-6 text-sm">
            <div class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-orange-300"></span>
                <span>Annual</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-blue-300"></span>
                <span>Sick</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-emerald-300"></span>
                <span>Casual</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-rose-300"></span>
                <span>Unpaid</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-slate-300"></span>
                <span>Public holiday</span>
            </div>
        </div>
    </div>
    <a href="{{ route('leaves.create') }}" class="rounded-lg bg-cyan-500 px-4 py-2 font-semibold text-slate-900 hover:bg-cyan-400">+ New Request</a>
</div>

<div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
    <form method="GET" action="{{ route('leaves.index') }}" class="flex flex-wrap items-end gap-3">
        <input type="hidden" name="month" value="{{ $monthStart->format('Y-m') }}">
        <div>
            <label for="q" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Search</label>
            <input
                id="q"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                placeholder="Search by name, email, team"
                class="mt-1 w-72 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950"
            >
        </div>
        <div>
            <label for="department_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Filter</label>
            <select id="department_id" name="department_id" class="mt-1 w-52 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950">
                <option value="">All teams</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? '') == $department->id)>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="sort" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Sort</label>
            <select id="sort" name="sort" class="mt-1 w-44 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950">
                <option value="name_asc" @selected(($filters['sort'] ?? 'name_asc') === 'name_asc')>Name A-Z</option>
                <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>Name Z-A</option>
                <option value="department" @selected(($filters['sort'] ?? '') === 'department')>Department</option>
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-cyan-400">Apply</button>
        <a href="{{ route('leaves.index', ['month' => $monthStart->format('Y-m')]) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Reset</a>
        @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('leaves.pending', ['tab' => 'all']) }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('leaves.pending') && request('tab', 'pending') === 'all' ? 'bg-cyan-500 text-slate-900' : 'border border-slate-300 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800' }}">All Requests</a>
                <a href="{{ route('leaves.pending', ['tab' => 'pending']) }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('leaves.pending') && request('tab', 'pending') === 'pending' ? 'bg-cyan-500 text-slate-900' : 'border border-slate-300 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800' }}">Pending</a>
            </div>
        @endif
    </form>
</div>

<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-gradient-to-b from-white to-slate-50 dark:border-slate-800 dark:from-slate-900 dark:to-slate-950 shadow-sm">
    <div class="min-w-[1200px]">
        <div class="grid border-b border-slate-200 dark:border-slate-800" style="grid-template-columns: 280px repeat({{ $calendarDays->count() }}, minmax(26px, 1fr));">
            <div class="flex items-center justify-between border-r border-slate-200 px-5 py-4 dark:border-slate-800">
                <h2 class="text-3xl font-semibold">{{ $monthStart->format('F') }}</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('leaves.index', array_merge($filters, ['month' => $prevMonth])) }}" class="rounded-lg border border-slate-300 px-3 py-1 text-sm hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800" aria-label="Previous month">&larr;</a>
                    <a href="{{ route('leaves.index', array_merge($filters, ['month' => $nextMonth])) }}" class="rounded-lg border border-slate-300 px-3 py-1 text-sm hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800" aria-label="Next month">&rarr;</a>
                </div>
            </div>
            @foreach ($calendarDays as $day)
                <div class="py-3 text-center text-xs font-semibold {{ $day['is_weekend'] ? 'bg-slate-100 dark:bg-slate-800/50' : '' }}">
                    <div class="text-slate-400">{{ $day['dow'] }}</div>
                    <div class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $day['day'] }}</div>
                </div>
            @endforeach
        </div>

        @forelse ($employees as $employee)
            <div class="grid border-b border-slate-200 dark:border-slate-800" style="grid-template-columns: 280px repeat({{ $calendarDays->count() }}, minmax(26px, 1fr));">
                <div class="flex items-center gap-3 border-r border-slate-200 px-4 py-3 dark:border-slate-800">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-200 text-xs font-bold uppercase text-slate-600 dark:bg-slate-700 dark:text-slate-200">
                        {{ \Illuminate\Support\Str::of($employee->full_name)->explode(' ')->map(fn ($word) => \Illuminate\Support\Str::substr($word, 0, 1))->take(2)->implode('') }}
                    </div>
                    <div>
                        <p class="font-semibold">{{ $employee->full_name }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $employee->department->name ?? 'No department' }}</p>
                    </div>
                </div>

                <div class="relative" style="grid-column: 2 / -1;">
                    <div class="grid h-full" style="grid-template-columns: repeat({{ $calendarDays->count() }}, minmax(26px, 1fr));">
                        @foreach ($calendarDays as $day)
                            <div class="border-l border-slate-200 dark:border-slate-800 {{ $day['is_weekend'] ? 'bg-slate-100 dark:bg-slate-800/40' : '' }}"></div>
                        @endforeach
                    </div>

                    <div class="pointer-events-none absolute inset-0 grid" style="grid-template-columns: repeat({{ $calendarDays->count() }}, minmax(26px, 1fr));">
                        @foreach ($eventMap[$employee->id] ?? [] as $event)
                            @php
                                $eventClasses = match ($event['type']) {
                                    'annual' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-300',
                                    'sick' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                                    'casual' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
                                    'unpaid' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
                                    'holiday' => 'bg-slate-200 text-slate-700 dark:bg-slate-700/60 dark:text-slate-100',
                                    default => 'bg-slate-200 text-slate-700 dark:bg-slate-600/40 dark:text-slate-200',
                                };
                                $eventSizeClasses = $event['type'] === 'holiday'
                                    ? 'my-4 text-[10px] font-medium opacity-80 z-0'
                                    : 'my-3 text-xs font-semibold z-10';
                            @endphp
                            <div class="mx-1 flex items-center rounded-full px-3 {{ $eventClasses }} {{ $eventSizeClasses }}" style="grid-column: {{ $event['start_col'] }} / {{ $event['end_col'] }};">
                                {{ $event['label'] }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-600 dark:text-slate-400">No employees found for this month.</div>
        @endforelse
    </div>
</div>
@endsection
