@extends('hrms.layouts.app')

@section('title', 'Leaves Overview - PeopleFlow HRMS')

@section('content')
<div class="space-y-8">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-cyan-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Leaves <span class="text-cyan-500">Overview</span>
                </h1>
                <div class="mt-4 flex flex-wrap items-center gap-4 text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-orange-400"></span>
                        <span>Annual</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-400"></span>
                        <span>Sick</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                        <span>Casual</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                        <span>Unpaid</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                        <span>Holiday</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('leaves.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                <span>Add Leave</span>
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
        <form method="GET" action="{{ route('leaves.index') }}" class="flex flex-wrap items-end gap-6">
            <input type="hidden" name="month" value="{{ $monthStart->format('Y-m') }}">
            <div class="flex flex-1 min-w-[280px] gap-4">
                <div class="flex-1 space-y-1.5">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input
                            id="q" name="q" value="{{ $filters['q'] ?? '' }}"
                            placeholder="Find employee..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-10 py-2 text-[11px] font-bold text-slate-900 placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white"
                        >
                    </div>
                </div>
                <div class="w-64 space-y-1.5">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500">Department</label>
                    <div class="relative">
                         <select id="department_id" name="department_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 appearance-none focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white">
                            <option value="" class="dark:bg-slate-900">All Teams</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? '') == $department->id) class="dark:bg-slate-900">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="rounded-xl bg-slate-900 border border-white/10 px-6 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">Filter</button>
                <a href="{{ route('leaves.index', ['month' => $monthStart->format('Y-m')]) }}" class="rounded-xl border border-slate-200 bg-slate-100 px-6 py-2 text-[10px] font-black uppercase tracking-widest text-slate-700 transition-all hover:bg-slate-200 dark:border-white/5 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">Clear</a>
            </div>
            
            @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                <div class="ml-auto block">
                    <div class="inline-flex rounded-lg border border-slate-200 bg-slate-50 p-1 dark:border-slate-800 dark:bg-slate-900/50">
                        <a href="{{ route('leaves.pending', ['tab' => 'all']) }}" class="rounded-md px-4 py-1.5 text-xs font-semibold transition-colors {{ request()->routeIs('leaves.pending') && request('tab', 'pending') === 'all' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">All</a>
                        <a href="{{ route('leaves.pending', ['tab' => 'pending']) }}" class="rounded-md px-4 py-1.5 text-xs font-semibold transition-colors {{ request()->routeIs('leaves.pending') && request('tab', 'pending') === 'pending' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">Pending</a>
                    </div>
                </div>
            @endif
        </form>
    </div>

    {{-- Calendar Matrix --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
        <div class="overflow-x-auto custom-scrollbar border-t border-slate-100 dark:border-slate-800">
            <div class="min-w-[1200px]">
                {{-- Matrix Header --}}
                <div class="grid border-b border-slate-200 dark:border-slate-800" style="grid-template-columns: 320px repeat({{ $calendarDays->count() }}, minmax(40px, 1fr));">
                    <div class="flex items-center justify-between border-r border-slate-200 bg-slate-50 px-6 py-3 dark:border-slate-800 dark:bg-slate-950/30 sticky left-0 z-20">
                        <div>
                            <h2 class="text-base font-black uppercase tracking-tight text-slate-900 dark:text-white">{{ $monthStart->format('F Y') }}</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('leaves.index', array_merge($filters, ['month' => $prevMonth])) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                            </a>
                            <a href="{{ route('leaves.index', array_merge($filters, ['month' => $nextMonth])) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                            </a>
                        </div>
                    </div>
                    @foreach ($calendarDays as $day)
                        <div class="flex flex-col items-center justify-center border-r border-slate-100 py-3 text-center dark:border-slate-800/50 {{ $day['is_weekend'] ? 'bg-slate-50 dark:bg-slate-800/20' : '' }}">
                            <div class="text-[10px] font-semibold uppercase text-slate-500">{{ substr($day['dow'], 0, 1) }}</div>
                            <div class="mt-1 text-sm font-bold {{ $day['is_weekend'] ? 'text-slate-400' : 'text-slate-900 dark:text-white' }}">{{ $day['day'] }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Rows --}}
                <div class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-transparent">
                    @forelse ($employees as $employee)
                        <div class="grid hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors" style="grid-template-columns: 320px repeat({{ $calendarDays->count() }}, minmax(40px, 1fr));">
                            <div class="flex items-center gap-4 border-r border-slate-200 bg-white px-6 py-3 dark:border-slate-800 dark:bg-slate-900 sticky left-0 z-10">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    {{ \Illuminate\Support\Str::of($employee->full_name)->explode(' ')->map(fn ($word) => \Illuminate\Support\Str::substr($word, 0, 1))->take(2)->implode('') }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $employee->full_name }}</p>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $employee->department->name ?? 'No Department' }}</p>
                                </div>
                            </div>

                            <div class="relative" style="grid-column: 2 / -1;">
                                {{-- Background Tracks --}}
                                <div class="grid h-full" style="grid-template-columns: repeat({{ $calendarDays->count() }}, minmax(40px, 1fr));">
                                    @foreach ($calendarDays as $day)
                                        <div class="border-r border-slate-100 dark:border-slate-800/50 {{ $day['is_weekend'] ? 'bg-slate-50/50 dark:bg-slate-800/20' : '' }}"></div>
                                    @endforeach
                                </div>

                                {{-- Events --}}
                                <div class="pointer-events-none absolute inset-0 grid py-2" style="grid-template-columns: repeat({{ $calendarDays->count() }}, minmax(40px, 1fr));">
                                    @foreach ($eventMap[$employee->id] ?? [] as $event)
                                        @php
                                            $eventClasses = match ($event['type']) {
                                                'annual' => 'bg-orange-500 text-white',
                                                'sick' => 'bg-cyan-500 text-white',
                                                'casual' => 'bg-indigo-500 text-white',
                                                'unpaid' => 'bg-rose-500 text-white',
                                                'holiday' => 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                                                default => 'bg-slate-500 text-white',
                                            };
                                        @endphp
                                        <div class="mx-0.5 my-0.5 flex items-center rounded-md px-2 text-[10px] font-semibold truncate pointer-events-auto {{ $eventClasses }}" style="grid-column: {{ $event['start_col'] }} / {{ $event['end_col'] }};" title="{{ $event['label'] }}">
                                            {{ $event['label'] }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center flex flex-col items-center justify-center col-span-full">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-4 dark:bg-slate-800">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">No Results Found</h3>
                            <p class="mt-1 text-sm text-slate-500">Try adjusting your filters.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.4); border-radius: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.05); }
    .dark .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); }
</style>
@endsection
