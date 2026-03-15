@extends('hrms.layouts.app')

@section('title', 'Timeline Architect - PeopleFlow HRMS')

@section('content')
<div class="space-y-10">

    {{-- High Impact Header --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 px-8 py-10 shadow-2xl dark:bg-slate-950/40 dark:backdrop-blur-xl">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-[80px]"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-4xl font-black tracking-tight text-white lg:text-5xl">
                    Timeline <span class="text-cyan-400">Manager</span>
                </h1>
                <p class="mt-3 max-w-xl text-lg font-medium text-slate-400">
                    Coordinate holiday markers and chronological organizational pauses across regional protocols.
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('policies.holiday-policies.index') }}" class="group flex items-center gap-2 rounded-2xl bg-cyan-400 px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-950 shadow-xl shadow-cyan-400/20 transition-all hover:bg-cyan-300">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                    Protocol Architect
                </a>
                <a href="{{ route('policies.index') }}" class="rounded-2xl border border-slate-700 bg-slate-800 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-slate-700 transition-all">Governance Hub</a>
            </div>
        </div>
    </div>

    <div class="grid gap-10 xl:grid-cols-3">
        {{-- Protocol Selection --}}
        <div class="xl:col-span-1">
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-8">Active Protocol</h2>
                <form method="GET" action="{{ route('policies.holiday-calendar.index') }}">
                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4">Target Workspace</label>
                    <select name="policy_id" onchange="this.form.submit()" class="mt-2 w-full rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-900 focus:border-cyan-400 focus:bg-white focus:ring-0 dark:border-slate-800 dark:bg-slate-950 dark:text-white transition-all appearance-none">
                        @foreach ($policies as $policy)
                            <option value="{{ $policy->id }}" @selected(optional($selectedPolicy)->id === $policy->id)>
                                {{ $policy->country_code }}-{{ $policy->state_code }} | {{ $policy->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                @if ($selectedPolicy)
                    <div class="mt-8 space-y-4 rounded-[2rem] bg-slate-50 p-6 dark:bg-slate-950/40">
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Designation</span>
                            <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $selectedPolicy->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Telemetry Key</span>
                            <span class="text-xs font-mono text-cyan-500 font-black">{{ $selectedPolicy->code ?: 'GLOBAL_ROOT' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Pipeline Status</span>
                            <span class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full {{ $selectedPolicy->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                <span class="text-[9px] font-black uppercase tracking-widest {{ $selectedPolicy->is_active ? 'text-emerald-500' : 'text-rose-500' }}">{{ $selectedPolicy->is_active ? 'ONLINE' : 'OFFLINE' }}</span>
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Marker Management --}}
        <div class="xl:col-span-2 space-y-8">
            @if ($selectedPolicy)
                {{-- Add Marker --}}
                <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-500 mb-8">Initialize Chrono Marker</h2>
                    <div x-data="asyncForm({ reloadOnSuccess: true })">
                        <div x-show="toast.show" x-transition class="mb-6 rounded-2xl px-6 py-4 text-sm font-black" :class="toast.type === 'success' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white'" style="display: none;">
                            <span x-text="toast.message"></span>
                        </div>
                        <form x-ref="form" @submit.prevent="submit()" method="POST" action="{{ route('policies.holiday-calendar.dates.store', $selectedPolicy) }}" class="grid gap-6 md:grid-cols-4">
                            @csrf
                            <div class="md:col-span-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4">Marker Designation</label>
                                <input type="text" name="name" required placeholder="e.g. VERNAL_EQUINOX" class="mt-2 w-full rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-900 focus:border-cyan-400 focus:bg-white focus:ring-0 dark:border-slate-800 dark:bg-slate-950 dark:text-white transition-all">
                            </div>
                            <div>
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4">Chronos Pin</label>
                                <input type="date" name="holiday_date" required class="mt-2 w-full rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-900 focus:border-cyan-400 focus:bg-white focus:ring-0 dark:border-slate-800 dark:bg-slate-950 dark:text-white transition-all">
                            </div>
                            <div class="flex items-center mt-6 ml-4">
                                <label class="flex items-center gap-3 cursor-pointer group/opt">
                                    <div class="relative flex h-8 w-8 shrink-0 items-center justify-center">
                                        <input type="checkbox" name="is_optional" value="1" class="peer absolute inset-0 opacity-0 cursor-pointer">
                                        <div class="h-6 w-6 rounded-lg border-2 border-slate-200 transition-all peer-checked:bg-amber-400 peer-checked:border-amber-400 flex items-center justify-center dark:border-slate-800">
                                            <svg class="h-3 w-3 text-slate-950 opacity-0 peer-checked:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        </div>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 group-hover/opt:text-slate-900 dark:group-hover/opt:text-white">Optional Marker</span>
                                </label>
                            </div>
                            <div class="md:col-span-4">
                                <button type="submit" :disabled="saving" class="group relative flex w-full items-center justify-center gap-4 overflow-hidden rounded-2xl bg-slate-900 px-8 py-5 text-[10px] font-black uppercase tracking-widest text-white transition-all hover:bg-cyan-500 hover:text-slate-950 dark:bg-white dark:text-slate-950 dark:hover:bg-cyan-400 shadow-xl">
                                    <span x-text="saving ? 'COMMITTING...' : 'INIT_MARKER'"></span>
                                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Marker Ledger --}}
                <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-8">Established Markers</h2>
                    <div class="space-y-4">
                        @forelse ($selectedPolicy->holidayDates as $holidayDate)
                            <div x-data="asyncForm({ reloadOnSuccess: true })" class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-slate-50/50 p-6 transition-all hover:bg-white hover:shadow-xl dark:border-slate-800 dark:bg-slate-950/20 dark:hover:bg-slate-950">
                                <form x-ref="form" @submit.prevent="submit()" method="POST" action="{{ route('policies.holiday-calendar.dates.update', [$selectedPolicy, $holidayDate]) }}" class="grid gap-6 md:grid-cols-5 items-center">
                                    @csrf
                                    @method('PATCH')
                                    <div class="md:col-span-2">
                                        <label class="text-[8px] font-black uppercase tracking-widest text-slate-400 ml-4">Designation</label>
                                        <input type="text" name="name" value="{{ $holidayDate->name }}" class="mt-1 w-full rounded-xl border border-slate-100 bg-white px-5 py-3 text-[10px] font-black uppercase text-slate-900 focus:border-cyan-400 focus:ring-0 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="text-[8px] font-black uppercase tracking-widest text-slate-400 ml-4">Chronos Pin</label>
                                        <input type="date" name="holiday_date" value="{{ $holidayDate->holiday_date?->toDateString() }}" class="mt-1 w-full rounded-xl border border-slate-100 bg-white px-5 py-3 text-[10px] font-black text-slate-900 focus:border-cyan-400 focus:ring-0 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                                    </div>
                                    <div class="flex items-center mt-4">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <div class="relative flex h-8 w-8 shrink-0 items-center justify-center">
                                                <input type="checkbox" name="is_optional" value="1" class="peer absolute inset-0 opacity-0" @checked($holidayDate->is_optional)>
                                                <div class="h-5 w-5 rounded-lg border-2 border-slate-200 transition-all peer-checked:bg-amber-400 peer-checked:border-amber-400 flex items-center justify-center">
                                                    <svg class="h-3 w-3 text-slate-950 opacity-0 peer-checked:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                                </div>
                                            </div>
                                            <span class="text-[8px] font-black uppercase tracking-widest text-slate-400">Optional</span>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="submit" :disabled="saving" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-900 text-white hover:bg-cyan-500 hover:text-slate-950 transition-all dark:bg-white dark:text-slate-950 dark:hover:bg-cyan-400">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        </button>
                                        <button type="button" @click="if (confirm('Purge marker?')) { $refs.form = $refs.deleteForm; submit(); }" class="h-10 w-10 flex items-center justify-center rounded-xl bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </button>
                                    </div>
                                </form>
                                <form x-ref="deleteForm" method="POST" action="{{ route('policies.holiday-calendar.dates.destroy', [$selectedPolicy, $holidayDate]) }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-20 rounded-[2rem] border-2 border-dashed border-slate-100 dark:border-slate-800">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">No organizational pauses established.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-20 rounded-[3rem] border-2 border-dashed border-slate-100 dark:border-slate-800">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Select a protocol to initialize timeline management.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
