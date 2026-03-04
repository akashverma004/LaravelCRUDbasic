@extends('hrms.layouts.app')

@section('title', 'Policies - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Policies</h1>
    <p class="text-slate-600 dark:text-slate-400">Manage all HRMS policies from one place</p>
</div>

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    <a href="{{ route('policies.holiday-policies.index') }}" class="rounded-2xl border border-cyan-300/70 bg-cyan-50 p-5 transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-cyan-900 dark:bg-cyan-950/30">
        <h2 class="text-lg font-semibold">Holiday Policies</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Create/edit state-wise policies (country, state, weekend, active status).</p>
        <p class="mt-4 text-sm font-medium text-cyan-700 dark:text-cyan-300">Open holiday policies -></p>
    </a>

    <a href="{{ route('policies.holiday-calendar.index') }}" class="rounded-2xl border border-cyan-300/70 bg-cyan-50 p-5 transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-cyan-900 dark:bg-cyan-950/30">
        <h2 class="text-lg font-semibold">Manage Holiday Calendar</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Select a policy and only manage holiday dates (add/edit/delete).</p>
        <p class="mt-4 text-sm font-medium text-cyan-700 dark:text-cyan-300">Open calendar manager -></p>
    </a>

    @foreach ($types as $item)
        <a href="{{ $item['route'] }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold">{{ $item['title'] }}</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $item['description'] }}</p>
            <p class="mt-4 text-sm font-medium text-cyan-600 dark:text-cyan-400">Open policy -></p>
        </a>
    @endforeach
</div>
@endsection
