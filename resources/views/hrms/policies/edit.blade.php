@extends('hrms.layouts.app')

@section('title', $definition['title'] . ' - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">{{ $definition['title'] }}</h1>
        <p class="text-slate-600 dark:text-slate-400">{{ $definition['description'] }}</p>
    </div>
    <a href="{{ route('policies.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Back to Policies</a>
</div>

<div x-data="asyncForm()" class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
    <div x-show="toast.show" x-transition class="mb-4 rounded-xl px-4 py-3 text-sm font-semibold" :class="toast.type === 'success' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300'" style="display: none;">
        <span x-text="toast.message"></span>
    </div>
    <div x-show="errorMessage" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-600 dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-300" style="display: none;">
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
                    <label class="inline-flex items-center gap-2 pt-7">
                        <input type="hidden" name="{{ $name }}" value="0">
                        <input type="checkbox" name="{{ $name }}" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked((bool) old($name, (bool) $policy->{$name}))>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }} @if($required) <span class="text-red-500">*</span> @endif</span>
                    </label>
                @elseif ($type === 'textarea')
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }} @if($required) <span class="text-red-500">*</span> @endif</label>
                    <textarea name="{{ $name }}" rows="5" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-950 @error($name) border-red-500 @enderror" @if($required) required @endif>{{ $current }}</textarea>
                @elseif ($type === 'json')
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }} @if($required) <span class="text-red-500">*</span> @endif</label>
                    <textarea name="{{ $name }}" rows="6" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 font-mono text-xs dark:border-slate-700 dark:bg-slate-950 @error($name) border-red-500 @enderror" placeholder="{&#10;  &quot;key&quot;: &quot;value&quot;&#10;}" @if($required) required @endif>{{ is_array($current) ? json_encode($current, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : $current }}</textarea>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Must be valid JSON.</p>
                @elseif ($type === 'select')
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }} @if($required) <span class="text-red-500">*</span> @endif</label>
                    <select name="{{ $name }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-950 @error($name) border-red-500 @enderror" @if($required) required @endif>
                        <option value="">Select {{ $label }}</option>
                        @foreach ($options as $option)
                            <option value="{{ $option }}" @selected((string) $current === (string) $option)>{{ ucfirst($option) }}</option>
                        @endforeach
                    </select>
                @else
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }} @if($required) <span class="text-red-500">*</span> @endif</label>
                    <input type="{{ $type === 'integer' ? 'number' : $type }}" name="{{ $name }}" value="{{ $current }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-950 @if($type === 'date') transition-colors duration-300 bg-white text-slate-900 dark:text-white appearance-auto @endif @error($name) border-red-500 @enderror" @if($required) required @endif @if($min !== null) min="{{ $min }}" @endif @if($max !== null) max="{{ $max }}" @endif @if($step !== null) step="{{ $step }}" @endif>
                @endif

                @error($name)
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <div class="md:col-span-2 pt-2">
            <button type="submit" :disabled="saving" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-slate-900 hover:bg-cyan-400 disabled:opacity-60">
                <span x-text="saving ? 'Saving...' : 'Save Policy'"></span>
            </button>
        </div>
    </form>
</div>
@endsection
