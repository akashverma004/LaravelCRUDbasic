@extends('hrms.layouts.app')

@section('title', $definition['title'] . ' - PeopleFlow HRMS')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">{{ $definition['title'] }}</h1>
            <p class="mt-1 text-[11px] font-medium text-slate-500 uppercase tracking-wide">{{ $definition['description'] }}</p>
        </div>
        <a href="{{ route('policies.index') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center px-4 py-2">Back to Hub</a>
    </div>

<div x-data="asyncForm()" class="mt-8 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
    {{-- Universal Notification --}}
    <div 
        x-show="toast.show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-4 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-4 opacity-0 scale-95"
        class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-xs font-bold text-white shadow-2xl backdrop-blur-xl dark:bg-slate-800/90"
        x-cloak
    >
        <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="h-2 w-2 rounded-full animate-pulse"></div>
        <span x-text="toast.message"></span>
    </div>

    <div x-show="errorMessage" class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[11px] font-bold text-rose-600 dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-300" x-cloak>
        <span x-text="errorMessage"></span>
    </div>
    <form x-ref="form" @submit.prevent="submit()" method="POST" action="{{ route('policies.update', $type) }}" class="grid gap-4 md:grid-cols-2">
        @csrf
        @method('PATCH')

        @foreach ($definition['fields'] as $field)
            <div class="{{ in_array($field['type'], ['textarea', 'json'], true) ? 'md:col-span-2' : '' }}">
                @php
                    $name     = $field['name'];
                    $label    = $field['label'];
                    $type     = $field['type'];
                    $options  = $field['options'] ?? [];
                    $required = $field['required'] ?? false;
                    $min      = $field['min'] ?? ($type === 'integer' || $type === 'number' ? '0' : null);
                    $max      = $field['max'] ?? null;
                    $step     = $field['step'] ?? ($type === 'number' ? '0.01' : ($type === 'integer' ? '1' : null));
                    $current  = old($name, $policy->{$name});
                @endphp

                @if ($type === 'boolean')
                    <label class="inline-flex items-center gap-3 pt-6 cursor-pointer group">
                        <input type="hidden" name="{{ $name }}" value="0">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="{{ $name }}" value="1" class="peer h-4 w-4 rounded border-slate-300 text-cyan-500 focus:ring-cyan-500 dark:border-white/10 dark:bg-white/5" @checked((bool) old($name, (bool) $policy->{$name}))>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $label }} @if($required) <span class="text-rose-500">*</span> @endif</span>
                    </label>
                @elseif ($type === 'textarea')
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">{{ $label }} @if($required) <span class="text-rose-500">*</span> @endif</label>
                    <textarea name="{{ $name }}" rows="5" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white @error($name) border-red-500 @enderror" @if($required) required @endif>{{ $current }}</textarea>
                @elseif ($type === 'json')
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">{{ $label }} @if($required) <span class="text-rose-500">*</span> @endif</label>
                    <textarea name="{{ $name }}" rows="6" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-mono text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white @error($name) border-red-500 @enderror" placeholder="{&#10;  &quot;key&quot;: &quot;value&quot;&#10;}" @if($required) required @endif>{{ is_array($current) ? json_encode($current, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : $current }}</textarea>
                    <p class="mt-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Must be valid JSON.</p>
                @elseif ($type === 'select')
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">{{ $label }} @if($required) <span class="text-rose-500">*</span> @endif</label>
                    <select name="{{ $name }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white appearance-none @error($name) border-red-500 @enderror" @if($required) required @endif>
                        <option value="">Select {{ $label }}</option>
                        @foreach ($options as $option)
                            <option value="{{ $option }}" @selected((string) $current === (string) $option)>{{ ucfirst($option) }}</option>
                        @endforeach
                    </select>
                @else
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">{{ $label }} @if($required) <span class="text-rose-500">*</span> @endif</label>
                    <input type="{{ $type === 'integer' ? 'number' : $type }}" name="{{ $name }}" value="{{ $current }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white @if($type === 'date') appearance-auto @endif @error($name) border-red-500 @enderror" @if($required) required @endif @if($min !== null) min="{{ $min }}" @endif @if($max !== null) max="{{ $max }}" @endif @if($step !== null) step="{{ $step }}" @endif>
                @endif

                @error($name)
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <div class="md:col-span-2 pt-4 border-t border-slate-100 dark:border-white/5">
            <button type="submit" :disabled="saving" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-8 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-xl transition-all hover:bg-cyan-600 active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400 disabled:opacity-50">
                <span x-show="!saving" class="flex items-center gap-2">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    Save Policy
                </span>
                <span x-show="saving" class="flex items-center gap-2">
                    <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Processing
                </span>
            </button>
        </div>
    </form>
</div>
@endsection
