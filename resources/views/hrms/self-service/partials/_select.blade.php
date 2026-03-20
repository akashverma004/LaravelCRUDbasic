{{-- Reusable select field for self-service profile --}}
@php
    $readonly = $readonly ?? false;
    $optionsJson = \Illuminate\Support\Js::from(collect($options)->mapWithKeys(fn ($text, $value) => [(string) $value => $text]));
@endphp
<div>
    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 ml-2 mb-1">{{ $label }}</label>
    <template x-if="loading">
        <div class="h-8 w-full animate-pulse rounded-xl bg-slate-50 dark:bg-slate-950/40"></div>
    </template>
    
    <template x-if="!loading">
        <div class="w-full">
            @if($readonly)
                <div class="flex items-center rounded-xl border border-slate-100 bg-slate-50 px-3 py-1.5 dark:border-slate-800 dark:bg-slate-950/40 transition-all">
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-300 truncate"
                        x-text="(() => { const options = {{ $optionsJson }}; const value = employee.{{ $field }}; if (value === null || value === undefined || value === '') return '-'; return options[String(value)] ?? String(value).replace(/[_-]/g, ' ').replace(/\b\w/g, c => c.toUpperCase()); })()"></span>
                    <svg class="h-3 w-3 ml-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                </div>
            @else
                <template x-if="!editing">
                    <div class="flex items-center rounded-xl border border-slate-100 bg-slate-50 px-3 py-1.5 dark:border-slate-800 dark:bg-slate-950/40 transition-all hover:border-cyan-400/30">
                        <span class="text-xs font-semibold text-slate-900 dark:text-white truncate"
                            x-text="(() => { const options = {{ $optionsJson }}; const value = employee.{{ $field }}; if (value === null || value === undefined || value === '') return '-'; return options[String(value)] ?? String(value).replace(/[_-]/g, ' ').replace(/\b\w/g, c => c.toUpperCase()); })()"></span>
                    </div>
                </template>
                <template x-if="editing">
                    <div class="relative group">
                        <select x-model="form.{{ $field }}" 
                            :class="errors.{{ $field }} ? 'border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/50' : 'border-slate-200 focus:border-cyan-400 dark:border-slate-700 dark:focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-900'"
                            class="w-full rounded-xl border bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-900 dark:bg-slate-900 dark:text-white transition-all focus:ring-4 focus:ring-cyan-500/10 appearance-none"
                            style="color-scheme: dark;">
                            <option value="" class="dark:bg-slate-900 dark:text-white">Select Option...</option>
                            @foreach($options as $value => $text)
                                <option value="{{ $value }}" class="dark:bg-slate-900 dark:text-white">{{ $text }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-3 inset-y-0 flex items-center">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </div>
                        <template x-if="errors.{{ $field }}">
                            <div class="absolute -bottom-6 left-4 flex items-center gap-2">
                                <div class="h-1 w-1 rounded-full bg-rose-500"></div>
                                <p class="text-[8px] font-black text-rose-500 uppercase tracking-widest" x-text="errors.{{ $field }}[0]"></p>
                            </div>
                        </template>
                    </div>
                </template>
            @endif
        </div>
    </template>
</div>
