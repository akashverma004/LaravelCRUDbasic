{{-- Reusable text/date/email/url field for self-service profile --}}
@php $span = $span ?? 1; $type = $type ?? 'text'; $readonly = $readonly ?? false; @endphp
<div class="{{ $span === 2 ? 'sm:col-span-2' : '' }}">
    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ $label }}</label>
    @if($readonly)
        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300" x-text="employee.{{ $field }} || 'Not set'"></p>
    @else
        <template x-if="!editing">
            <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300" x-text="employee.{{ $field }} || 'Not set'"></p>
        </template>
        <template x-if="editing">
            <input type="{{ $type }}" x-model="form.{{ $field }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
        </template>
    @endif
</div>
