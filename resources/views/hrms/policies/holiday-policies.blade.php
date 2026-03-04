@extends('hrms.layouts.app')

@section('title', 'Holiday Policies - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-3xl font-bold">Holiday Policies</h1>
        <p class="text-slate-600 dark:text-slate-400">Create and maintain country/state holiday policy definitions</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('policies.holiday-calendar.index') }}" class="rounded-lg border border-cyan-300 px-4 py-2 text-sm font-semibold text-cyan-700 hover:bg-cyan-50 dark:border-cyan-700 dark:text-cyan-300 dark:hover:bg-cyan-900/20">Manage Calendar</a>
        <a href="{{ route('policies.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Back to Policies</a>
    </div>
</div>

@php
    $weekdays = $weekdays ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
@endphp

<div class="grid gap-6 xl:grid-cols-3">
    <div class="xl:col-span-1">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold">Create Policy</h2>
            <form method="POST" action="{{ route('policies.holiday-policies.store') }}" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Policy Name</label>
                    <input type="text" name="name" required class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950" placeholder="India - Karnataka Holidays">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Country</label>
                        <input type="text" name="country_code" list="country-options" required maxlength="3" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm uppercase dark:border-slate-700 dark:bg-slate-950" placeholder="IN">
                        <datalist id="country-options">
                            @foreach ($countries as $code => $name)
                                <option value="{{ $code }}" label="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">State</label>
                        <input type="text" name="state_code" list="state-options" required maxlength="3" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm uppercase dark:border-slate-700 dark:bg-slate-950" placeholder="KA">
                        <datalist id="state-options">
                            @foreach ($states as $code => $name)
                                <option value="{{ $code }}" label="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Code (Optional)</label>
                    <input type="text" name="code" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950" placeholder="HOLIDAY_IN_KA">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Weekend Days</label>
                    <div class="mt-2 flex flex-wrap gap-3">
                        @foreach ($weekdays as $day)
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" name="weekend_days[]" value="{{ $day }}" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked(in_array($day, ['saturday', 'sunday'], true))>
                                <span class="capitalize">{{ $day }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" checked>
                    <span>Active</span>
                </label>
                <button type="submit" class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-cyan-400">Create Policy</button>
            </form>
        </div>
    </div>

    <div class="xl:col-span-2 space-y-4">
        @forelse ($policies as $policy)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">{{ $policy->name }}</h2>
                    <a href="{{ route('policies.holiday-calendar.index', ['policy_id' => $policy->id]) }}" class="text-sm font-medium text-cyan-600 hover:text-cyan-500 dark:text-cyan-300">Open Calendar</a>
                </div>
                <form method="POST" action="{{ route('policies.holiday-policies.update', $policy) }}" class="grid gap-3 md:grid-cols-2">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Policy Name</label>
                        <input type="text" name="name" value="{{ $policy->name }}" required class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Code</label>
                        <input type="text" name="code" value="{{ $policy->code }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Country</label>
                        <input type="text" name="country_code" list="country-options" maxlength="3" value="{{ $policy->country_code }}" required class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm uppercase dark:border-slate-700 dark:bg-slate-950">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">State</label>
                        <input type="text" name="state_code" list="state-options" maxlength="3" value="{{ $policy->state_code }}" required class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm uppercase dark:border-slate-700 dark:bg-slate-950">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Weekend Days</label>
                        <div class="mt-2 flex flex-wrap gap-3">
                            @foreach ($weekdays as $day)
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="weekend_days[]" value="{{ $day }}" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked(in_array($day, $policy->weekend_days ?? [], true))>
                                    <span class="capitalize">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="md:col-span-2 flex items-center gap-3">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked((bool) $policy->is_active)>
                            <span>Active</span>
                        </label>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $policy->holiday_dates_count }} holiday dates</span>
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Save Policy</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('policies.holiday-policies.destroy', $policy) }}" class="mt-3" onsubmit="return confirm('Delete this holiday policy and all dates?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">Delete Policy</button>
                </form>
            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                No holiday policies yet. Create one from the left panel.
            </div>
        @endforelse
    </div>
</div>
@endsection
