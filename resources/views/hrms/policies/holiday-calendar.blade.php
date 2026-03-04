@extends('hrms.layouts.app')

@section('title', 'Holiday Calendar - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-3xl font-bold">Holiday Calendar</h1>
        <p class="text-slate-600 dark:text-slate-400">Manage holiday dates for a selected state policy</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('policies.holiday-policies.index') }}" class="rounded-lg border border-cyan-300 px-4 py-2 text-sm font-semibold text-cyan-700 hover:bg-cyan-50 dark:border-cyan-700 dark:text-cyan-300 dark:hover:bg-cyan-900/20">Manage Policies</a>
        <a href="{{ route('policies.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Back to Policies</a>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-3">
    <div class="xl:col-span-1">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold">State Policies</h2>
            <form method="GET" action="{{ route('policies.holiday-calendar.index') }}" class="mt-3">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Select Policy</label>
                <select name="policy_id" onchange="this.form.submit()" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950">
                    @foreach ($policies as $policy)
                        <option value="{{ $policy->id }}" @selected(optional($selectedPolicy)->id === $policy->id)>
                            {{ $policy->country_code }}-{{ $policy->state_code }} | {{ $policy->name }} ({{ $policy->holiday_dates_count }})
                        </option>
                    @endforeach
                </select>
            </form>

            @if ($selectedPolicy)
                <div class="mt-4 rounded-xl border border-slate-200 p-3 text-sm dark:border-slate-700">
                    <p><span class="font-medium">Policy:</span> {{ $selectedPolicy->name }}</p>
                    <p><span class="font-medium">Region:</span> {{ $selectedPolicy->country_code }}-{{ $selectedPolicy->state_code }}</p>
                    <p><span class="font-medium">Code:</span> {{ $selectedPolicy->code ?: 'N/A' }}</p>
                    <p><span class="font-medium">Status:</span> {{ $selectedPolicy->is_active ? 'Active' : 'Inactive' }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="xl:col-span-2 space-y-6">
        @if ($selectedPolicy)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-lg font-semibold">Add Holiday Date</h2>
                <form method="POST" action="{{ route('policies.holiday-calendar.dates.store', $selectedPolicy) }}" class="mt-4 grid gap-3 md:grid-cols-4">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Holiday Name</label>
                        <input type="text" name="name" required class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950" placeholder="Gudi Padwa">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Date</label>
                    <input type="date" name="holiday_date" required class="transition-colors duration-300 mt-1 w-full rounded-lg border border-slate-300 bg-white text-slate-900 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white appearance-auto">
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="hidden" name="is_optional" value="0">
                            <input type="checkbox" name="is_optional" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                            <span>Optional</span>
                        </label>
                    </div>
                    <div class="md:col-span-4">
                        <button type="submit" class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-cyan-400">Add Holiday</button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-lg font-semibold">Holiday Dates</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($selectedPolicy->holidayDates as $holidayDate)
                        <div class="grid gap-3 rounded-xl border border-slate-200 p-3 md:grid-cols-5 dark:border-slate-700">
                            <form method="POST" action="{{ route('policies.holiday-calendar.dates.update', [$selectedPolicy, $holidayDate]) }}" class="contents">
                                @csrf
                                @method('PATCH')
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Name</label>
                                    <input type="text" name="name" value="{{ $holidayDate->name }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-950">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Date</label>
                                <input type="date" name="holiday_date" value="{{ $holidayDate->holiday_date?->toDateString() }}" class="transition-colors duration-300 mt-1 w-full rounded-lg border border-slate-300 bg-white text-slate-900 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white appearance-auto">
                                </div>
                                <div class="flex items-end">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="hidden" name="is_optional" value="0">
                                        <input type="checkbox" name="is_optional" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked($holidayDate->is_optional)>
                                        <span>Optional</span>
                                    </label>
                                </div>
                                <div class="flex items-end gap-2">
                                    <button type="submit" class="rounded-lg bg-slate-200 px-3 py-1 text-sm font-medium text-slate-900 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Save</button>
                                </div>
                            </form>
                            <div class="md:col-span-5">
                                <form method="POST" action="{{ route('policies.holiday-calendar.dates.destroy', [$selectedPolicy, $holidayDate]) }}" onsubmit="return confirm('Remove this holiday date?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1 text-sm font-medium text-white hover:bg-red-500">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">No holidays added for this state yet.</p>
                    @endforelse
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                Create a holiday policy first, then add holiday dates for that state.
            </div>
        @endif
    </div>
</div>
@endsection
