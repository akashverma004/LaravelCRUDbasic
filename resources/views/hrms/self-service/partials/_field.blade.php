{{-- Reusable text/date/email/url field for self-service profile --}}
@php $span = $span ?? 1; $type = $type ?? 'text'; $readonly = $readonly ?? false; @endphp
<div class="{{ $span === 2 ? 'sm:col-span-2' : '' }}">
    <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">{{ $label }}</label>
    <template x-if="loading">
        <div class="h-10 w-full animate-pulse rounded-xl bg-slate-50 dark:bg-slate-950/40"></div>
    </template>
    
    <template x-if="!loading">
        <div class="w-full">
            @if($readonly)
                <div class="relative group">
                    <div class="flex items-center rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 dark:border-slate-800 dark:bg-slate-950/40 transition-all">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-tight" x-text="employee.{{ $field }} || 'NULL_SET'"></span>
                        <svg class="h-3 w-3 ml-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                    </div>
                </div>
            @else
                <template x-if="!editing">
                    <div class="flex items-center rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 dark:border-slate-800 dark:bg-slate-950/40 transition-all hover:border-cyan-400/30">
                        <span class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight" x-text="employee.{{ $field }} || 'UNSET_PROPERTY'"></span>
                    </div>
                </template>
                <template x-if="editing">
                    <div class="relative group">
                        <input type="{{ $type }}" x-model="form.{{ $field }}" 
                            :class="errors.{{ $field }} ? 'border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/50' : 'border-slate-200 focus:border-cyan-400 dark:border-slate-700 dark:focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-950'"
                            class="w-full rounded-xl border bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-900 dark:bg-slate-950 dark:text-white transition-all focus:ring-4 focus:ring-cyan-500/10 placeholder:text-slate-300">
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
