{{-- Reusable select field for self-service profile --}}
<div>
    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ $label }}</label>
    <template x-if="!editing">
        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-300"
            x-text="(() => { const v = employee.{{ $field }}; return v ? v.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : 'Not set'; })()"></p>
    </template>
    <template x-if="editing">
        <select x-model="form.{{ $field }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            <option value="">Select...</option>
            @foreach($options as $value => $text)
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        </select>
    </template>
</div>
