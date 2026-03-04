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

<div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
    <form method="POST" action="{{ route('policies.update', $type) }}" class="grid gap-4 md:grid-cols-2">
        @csrf
        @method('PATCH')

        @foreach ($definition['fields'] as $field)
            <div class="{{ in_array($field['type'], ['textarea', 'json'], true) ? 'md:col-span-2' : '' }}">
                @php
                    $name = $field['name'];
                    $label = $field['label'];
                    $type = $field['type'];
                    $options = $field['options'] ?? [];
                    $current = old($name, $policy->{$name});
                @endphp

                @if ($type === 'boolean')
                    <label class="inline-flex items-center gap-2 pt-7">
                        <input type="hidden" name="{{ $name }}" value="0">
                        <input type="checkbox" name="{{ $name }}" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked((bool) old($name, (bool) $policy->{$name}))>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }}</span>
                    </label>
                @elseif ($type === 'textarea')
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }}</label>
                    <textarea name="{{ $name }}" rows="5" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-950 @error($name) border-red-500 @enderror">{{ $current }}</textarea>
                @elseif ($type === 'json')
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }}</label>
                    <textarea name="{{ $name }}" rows="6" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 font-mono text-xs dark:border-slate-700 dark:bg-slate-950 @error($name) border-red-500 @enderror">{{ is_array($current) ? json_encode($current, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : $current }}</textarea>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Enter valid JSON format</p>
                @elseif ($type === 'select')
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }}</label>
                    <select name="{{ $name }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-950 @error($name) border-red-500 @enderror">
                        <option value="">Select {{ $label }}</option>
                        @foreach ($options as $option)
                            <option value="{{ $option }}" @selected((string) $current === (string) $option)>{{ ucfirst($option) }}</option>
                        @endforeach
                    </select>
                @else
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }}</label>
                    <input type="{{ $type === 'integer' ? 'number' : $type }}" name="{{ $name }}" value="{{ $current }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-950 @if($type === 'date') transition-colors duration-300 bg-white text-slate-900 dark:text-white appearance-auto @endif @error($name) border-red-500 @enderror" @if(in_array($type, ['integer', 'number'], true)) min="0" step="{{ $type === 'number' ? '0.01' : '1' }}" @endif>
                @endif

                @error($name)
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <div class="md:col-span-2 pt-2">
            <button type="submit" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-slate-900 hover:bg-cyan-400">Save Policy</button>
        </div>
    </form>
</div>
@endsection
