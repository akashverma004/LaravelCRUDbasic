@extends('hrms.layouts.app')

@section('title', 'Governance Hub - PeopleFlow HRMS')

@section('content')
<div x-data="{ 
    activeModal: null,
    loading: false,
    toast: { show: false, message: '', type: 'success' },
    showToast(msg, type = 'success') {
        this.toast = { show: true, message: msg, type: type };
        setTimeout(() => this.toast.show = false, 3000);
    }
}" class="relative space-y-6 pb-8">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Administration</span>
                <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Regulatory</span>
            </div>
            <h1 class="text-lg font-black tracking-tight text-slate-900 dark:text-white uppercase">
                Governance <span class="text-cyan-500">Hub</span>
            </h1>
            <p class="mt-0.5 text-[9px] font-bold text-slate-400 uppercase tracking-wide leading-relaxed">
                Centralized directive management and organizational policy architecture.
            </p>
        </div>
        <div class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900">
             <svg class="h-4 w-4 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>
        </div>
    </div>

    {{-- Policy Lattice Grid --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        
        <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-all hover:border-cyan-500/30 hover:shadow-md dark:border-white/5 dark:bg-slate-900/50">
            <div class="mb-3 flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600 transition-colors group-hover:bg-cyan-500 group-hover:text-white dark:bg-cyan-500/10 dark:text-cyan-400">
                 <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" /></svg>
            </div>
            <h2 class="text-[11px] font-black tracking-tight text-slate-900 uppercase transition-colors group-hover:text-cyan-600 dark:text-white">Holiday Protocols</h2>
            <p class="mt-1.5 text-[8px] font-bold uppercase leading-relaxed tracking-wide text-slate-400">Architect regional schedules.</p>
            <div class="mt-3 flex items-center gap-3">
                <button @click="activeModal = 'holiday'" class="flex items-center gap-1.5 text-[8px] font-black uppercase tracking-widest text-cyan-500 hover:text-cyan-600">
                    <span>Manage</span>
                    <svg class="h-3 w-3 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </button>
                <a href="{{ route('policies.holiday-calendar.index') }}" class="flex items-center gap-1.5 text-[8px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600">
                    <span>Calendar</span>
                </a>
            </div>
        </div>

        @foreach ($types as $item)
            <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-all hover:border-cyan-500/30 hover:shadow-md dark:border-white/5 dark:bg-slate-900/50">
                <div class="mb-3 flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-950 transition-colors group-hover:bg-cyan-500 group-hover:text-white dark:bg-white/5 dark:text-slate-400 dark:group-hover:bg-cyan-500/20 dark:group-hover:text-cyan-400">
                    {!! $item['icon'] ?? '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>' !!}
                </div>
                <h2 class="text-[11px] font-black tracking-tight text-slate-900 uppercase transition-colors group-hover:text-cyan-600 dark:text-white">{{ $item['title'] ?? 'Policy' }}</h2>
                <p class="mt-1.5 text-[8px] font-bold uppercase leading-relaxed tracking-wide text-slate-400 line-clamp-1">{{ str_replace(' policy', '', $item['description']) }}</p>
                <div class="mt-3 flex items-center gap-2">
                    <button @click="activeModal = '{{ $item['type'] ?? '' }}'" class="flex items-center gap-1.5 text-[8px] font-black uppercase tracking-widest text-cyan-500 hover:text-cyan-600">
                        <span>Configure</span>
                        <svg class="h-2.5 w-2.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- MODALS --}}

    <div x-show="activeModal === 'holiday'" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-transition.opacity>
        <div @click.away="activeModal = null" class="w-full max-w-3xl rounded-[20px] bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/5 overflow-hidden max-h-[85vh] flex flex-col" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0">
            <div class="flex items-center justify-between border-b border-slate-50 px-5 py-3 dark:border-white/5 shrink-0">
                <div>
                    <h3 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.2em]">Holiday Protocols</h3>
                    <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Manage regional holiday schedules</p>
                </div>
                <button @click="activeModal = null" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-5 overflow-y-auto custom-scrollbar flex-1">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {{-- Form --}}
                    <div class="lg:col-span-4 space-y-4">
                        <div x-data="asyncForm({ reloadOnSuccess: true })">
                            <h4 class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3">Create New Protocol</h4>
                            <form @submit.prevent="submit()" method="POST" action="{{ route('policies.holiday-policies.store') }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Name</label>
                                    <input type="text" name="name" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-900 focus:border-cyan-500 dark:border-white/10 dark:bg-slate-800 dark:text-white" placeholder="e.g. India Standard">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Country</label>
                                        <input type="text" name="country_code" required maxlength="3" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-900 uppercase dark:border-white/10 dark:bg-slate-800 dark:text-white" placeholder="IND">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">State</label>
                                        <input type="text" name="state_code" required maxlength="3" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-900 uppercase dark:border-white/10 dark:bg-slate-800 dark:text-white" placeholder="MH">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Weekend (Select Days)</label>
                                    <div class="flex flex-wrap gap-2 pt-1">
                                        @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                            <label class="flex items-center gap-1.5 cursor-pointer group">
                                                <input type="checkbox" name="weekend_days[]" value="{{ $day }}" class="h-3.5 w-3.5 rounded border-slate-300 text-cyan-500 focus:ring-cyan-500 dark:border-slate-800 dark:bg-slate-950" @checked(in_array($day, ['saturday', 'sunday'], true))>
                                                <span class="text-[9px] font-bold text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white uppercase">{{ substr($day, 0, 3) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" :disabled="saving" class="w-full mt-2 rounded-xl bg-slate-900 border border-white/10 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 disabled:opacity-50 dark:bg-white/5">
                                    <span x-text="saving ? 'Syncing...' : 'Create Protocol'"></span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- List --}}
                    <div class="lg:col-span-8 space-y-4">
                        <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Existing Protocols</h4>
                        @forelse ($holidayPolicies as $policy)
                            <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-5 dark:border-white/5 dark:bg-white/[0.02]">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h5 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $policy->name }}</h5>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[9px] font-black text-cyan-600 dark:text-cyan-400 uppercase tracking-widest bg-cyan-100/50 dark:bg-cyan-500/10 px-2 py-0.5 rounded">{{ $policy->country_code }}-{{ $policy->state_code }}</span>
                                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">{{ $policy->holiday_dates_count }} Markers</span>
                                        </div>
                                    </div>
                                    <div x-data="asyncForm({ reloadOnSuccess: true })" class="flex items-center gap-2">
                                        <form @submit.prevent="submit()" method="POST" action="{{ route('policies.holiday-policies.update', $policy) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="is_active" :value="{{ $policy->is_active ? 0 : 1 }}">
                                            <button type="submit" class="p-2 rounded-lg transition-colors {{ $policy->is_active ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-white/5' }}" title="{{ $policy->is_active ? 'Active' : 'Deactivated' }}">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                            </button>
                                        </form>
                                        <a href="{{ route('policies.holiday-calendar.index', ['policy_id' => $policy->id]) }}" class="p-2 rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 hover:bg-slate-900 hover:text-white transition-all">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                        </a>
                                        <form x-data="asyncForm({ reloadOnSuccess: true })" @submit.prevent="if(confirm('Delete protocol?')) submit()" action="{{ route('policies.holiday-policies.destroy', $policy) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center rounded-2xl border border-dashed border-slate-200 dark:border-white/5">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Archived or empty protocols.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dynamic Policy Modals --}}
    @foreach ($types as $item)
        <div x-show="activeModal === 'policy-{{ $item['type'] }}'" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-transition.opacity>
            <div x-show="activeModal === '{{ $item['type'] }}'" x-transition x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
                <div @click.away="activeModal = null" class="w-full max-w-2xl rounded-[20px] bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/5 overflow-hidden max-h-[85vh] flex flex-col">
                    <div class="flex items-center justify-between border-b border-slate-50 px-5 py-3 dark:border-white/5 shrink-0">
                        <div>
                            <h3 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.2em]">{{ $item['title'] ?? 'Policy' }}</h3>
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ $item['description'] }} Settings</p>
                        </div>
                        <button @click="activeModal = null" class="text-slate-400 hover:text-slate-900 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
                    <div x-data="asyncForm({ reloadOnSuccess: true })">
                        <form @submit.prevent="submit()" method="POST" action="{{ $item['route'] }}" class="grid gap-4 md:grid-cols-2">
                            @csrf @method('PATCH')

                            @foreach ($item['definition']['fields'] as $field)
                                @php
                                    $name     = $field['name'];
                                    $label    = $field['label'];
                                    $type     = $field['type'];
                                    $options  = $field['options'] ?? [];
                                    $required = $field['required'] ?? false;
                                    $policy   = $item['policy'];
                                    $current  = $policy->{$name} ?? '';

                                    // Filter out technical fields not needed for regular HR
                                    if (in_array($name, ['rules', 'exceptions', 'metadata', 'code'])) {
                                        continue;
                                    }
                                @endphp

                                <div class="relative {{ in_array($field['type'], ['textarea', 'json'], true) ? 'md:col-span-2' : '' }}">
                                    @if ($type === 'boolean')
                                        <label class="inline-flex items-center gap-3 pt-1.5 cursor-pointer group">
                                            <input type="hidden" name="{{ $name }}" value="0">
                                            <input type="checkbox" name="{{ $name }}" value="1" class="peer h-3.5 w-3.5 rounded border-slate-300 text-cyan-500 focus:ring-cyan-500" @checked((bool) $current)>
                                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $label }}</span>
                                        </label>
                                    @elseif ($type === 'textarea')
                                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">{{ $label }}</label>
                                        <textarea name="{{ $name }}" rows="2" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-900 dark:border-white/10 dark:bg-slate-800 dark:text-white" @if($required) required @endif>{{ $current }}</textarea>
                                    @elseif ($type === 'json' && ($name === 'allowed_departments' || $name === 'allowed_roles'))
                                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">{{ $label }}</label>
                                        <div x-data="{ 
                                            options: {{ $name === 'allowed_departments' ? $departments->map(fn($d)=>(['id'=>$d->id,'name'=>$d->name])) : $roles->map(fn($r)=>(['id'=>$r->id,'name'=>$r->name])) }},
                                            selected: {{ is_array($current) ? json_encode($current) : ($current ? json_encode(json_decode($current, true) ?? []) : '[]') }},
                                            toggle(id) {
                                                id = id.toString();
                                                const idx = this.selected.indexOf(id);
                                                if(idx > -1) this.selected.splice(idx, 1);
                                                else this.selected.push(id);
                                            }
                                        }" class="space-y-1.5">
                                            <div class="relative">
                                                <select @change="if($event.target.value) { toggle($event.target.value); $event.target.value=''; }" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white appearance-none transition-all">
                                                    <option value="" class="dark:bg-slate-800 dark:text-white">Add {{ $label }}...</option>
                                                    <template x-for="opt in options" :key="opt.id">
                                                        <option :value="opt.id" x-text="opt.name" :disabled="selected.includes(opt.id.toString())" class="dark:bg-slate-800 dark:text-white"></option>
                                                    </template>
                                                </select>
                                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap gap-1 min-h-[24px]">
                                                <template x-for="id in selected" :key="id">
                                                    <div class="inline-flex items-center gap-1 bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 px-1.5 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-widest border border-cyan-200/50 dark:border-cyan-500/20 group">
                                                        <span x-text="options.find(o => o.id.toString() === id.toString())?.name || id"></span>
                                                        <button type="button" @click="toggle(id)" class="hover:text-rose-500 transition-colors">
                                                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                            {{-- Hidden inputs to submit array --}}
                                            <template x-for="id in selected" :key="'hidden-'+id">
                                                <input type="hidden" :name="'{{ $name }}[]'" :value="id">
                                            </template>
                                            <template x-if="selected.length === 0">
                                                <input type="hidden" name="{{ $name }}" value="">
                                            </template>
                                        </div>
                                    @elseif ($type === 'json' && !in_array($name, ['weekend_days', 'allowed_departments', 'allowed_roles']))
                                        {{-- Skipped --}}
                                    @elseif ($type === 'json')
                                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">{{ $label }}</label>
                                        <textarea name="{{ $name }}" rows="2" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 font-mono text-xs font-bold text-slate-900 dark:border-white/10 dark:bg-slate-800 dark:text-white" @if($required) required @endif>{{ is_array($current) ? json_encode($current, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : $current }}</textarea>
                                    @elseif ($type === 'select')
                                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">{{ $label }}</label>
                                        <div class="relative">
                                            <select name="{{ $name }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/10 dark:bg-slate-800 dark:text-white appearance-none transition-all" @if($required) required @endif>
                                                <option value="" class="dark:bg-slate-800 dark:text-white">Select Option</option>
                                                @foreach ($options as $opt)
                                                    <option value="{{ $opt }}" @selected((string)$current === (string)$opt) class="dark:bg-slate-800 dark:text-white">{{ ucfirst($opt) }}</option>
                                                @endforeach
                                            </select>
                                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                            </div>
                                        </div>
                                    @else
                                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">{{ $label }}</label>
                                        <input type="{{ in_array($type, ['integer', 'number']) ? 'number' : $type }}" name="{{ $name }}" value="{{ $current }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-900 dark:border-white/10 dark:bg-slate-800 dark:text-white" @if($required) required @endif>
                                    @endif
                                </div>
                            @endforeach

                            <div class="md:col-span-2 pt-6 border-t border-slate-50 dark:border-white/5 flex justify-end">
                                <button type="submit" :disabled="saving" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-8 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 disabled:opacity-50 dark:bg-white/5">
                                    <span x-text="saving ? 'Processing...' : 'Save Configuration'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Universal Notification --}}
    <div 
        x-show="toast.show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-4 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-4 opacity-0 scale-95"
        class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-xs font-bold text-white shadow-2xl backdrop-blur-xl"
        x-cloak
    >
        <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="h-2 w-2 rounded-full animate-pulse"></div>
        <span x-text="toast.message"></span>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.4); border-radius: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.05); }
    .dark .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); }
</style>
@endsection
