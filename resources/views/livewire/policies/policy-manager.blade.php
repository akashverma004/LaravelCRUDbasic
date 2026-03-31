<div class="space-y-5 pb-8 relative">
    {{-- High-Impact Glass Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white/80 px-6 py-5 shadow-sm border border-slate-200 backdrop-blur-xl dark:bg-slate-900/60 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-blue-500/5 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center text-center lg:text-left">
            <div>
                <div class="flex items-center justify-center lg:justify-start gap-2 mb-0.5">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-blue-600 dark:text-blue-400">Institutional</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Governance</span>
                </div>
                <h1 class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all">
                    Policy <span class="text-blue-500">Frameworks</span>
                </h1>
                <p class="mt-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest opacity-80 leading-none">
                    Manage organizational standards and statutory compliance logic.
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-2.5">
                <a href="{{ route('policies.holiday-policies.index') }}" wire:navigate class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 text-[9px] font-black uppercase tracking-widest text-slate-600 shadow-sm transition-all hover:bg-slate-50 dark:border-white/5 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    <span>Holiday Vector</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($this->policies as $p)
            <div class="group relative rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:border-blue-500/30 hover:shadow-md">
                <div class="flex items-start justify-between mb-3">
                    <div class="h-9 w-9 rounded-lg bg-slate-50 flex items-center justify-center font-black dark:bg-white/5 border border-slate-100 dark:border-white/5 shadow-inner">
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </div>
                    <span class="px-2 py-0.5 rounded-lg text-[7px] font-black uppercase {{ $p['is_active'] ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10' : 'bg-rose-50 text-rose-600 dark:bg-rose-500/10' }}">
                        {{ $p['is_active'] ? 'Active' : 'Draft' }}
                    </span>
                </div>

                <div class="mb-4 min-h-[50px]">
                    <h4 class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $p['title'] }}</h4>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 line-clamp-2 opacity-80 leading-relaxed">{{ $p['description'] }}</p>
                </div>

                <div class="pt-3 border-t border-slate-50 dark:border-white/5 flex justify-end">
                    <button wire:click="openEditModal('{{ $p['type'] }}')" class="rounded-lg bg-slate-50 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest text-slate-500 hover:bg-blue-500 hover:text-white transition-all dark:bg-white/5 dark:text-slate-400 dark:hover:bg-blue-500/20 dark:hover:text-blue-400">
                        Configure
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Universal Modal (Standardized) --}}
    @if($showEditModal && $activeDefinition)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div wire:click="$set('showEditModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-lg rounded-xl bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200 max-h-[85vh] flex flex-col">
                <div class="border-b border-slate-100 p-5 dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-white/5">
                    <h2 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.2em] shadow-sm">Sync <span class="text-blue-500">{{ $activeDefinition['title'] }}</span></h2>
                    <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-900 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="p-5 space-y-5 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($activeDefinition['fields'] as $field)
                            @if($field['name'] === 'code') @continue @endif

                            <div class="space-y-1.5 {{ ($field['type'] === 'textarea' || $field['type'] === 'json') ? 'md:col-span-2' : '' }}">
                                <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest ml-1">{{ $field['label'] }}</label>
                                
                                <div class="relative">
                                    @if($field['type'] === 'text')
                                        <input wire:model="formData.{{ $field['name'] }}" type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white transition-all uppercase">
                                    @elseif($field['type'] === 'textarea')
                                        <textarea wire:model="formData.{{ $field['name'] }}" rows="2" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[10px] font-bold text-slate-900 focus:border-blue-500 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-tighter"></textarea>
                                    @elseif($field['type'] === 'number' || $field['type'] === 'integer')
                                        <input wire:model="formData.{{ $field['name'] }}" type="number" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-blue-500 dark:border-white/5 dark:bg-white/5 dark:text-white">
                                    @elseif($field['type'] === 'date')
                                        <input wire:model="formData.{{ $field['name'] }}" type="date" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-blue-500 dark:border-white/5 dark:bg-white/5 dark:text-white">
                                    @elseif($field['type'] === 'boolean')
                                        <div class="flex items-center gap-3 rounded-lg border border-slate-100 bg-slate-50 px-4 py-2 dark:border-white/5 dark:bg-white/5">
                                            <input wire:model="formData.{{ $field['name'] }}" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Active State</span>
                                        </div>
                                    @elseif($field['type'] === 'select')
                                        <select wire:model="formData.{{ $field['name'] }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-blue-500 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase appearance-none">
                                            @foreach($field['options'] as $opt)
                                                <option value="{{ $opt }}">{{ strtoupper($opt) }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($field['type'] === 'json')
                                        <div class="relative group">
                                            <textarea wire:model="formData.{{ $field['name'] }}" rows="4" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[9px] font-mono font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-emerald-400"></textarea>
                                            <div class="absolute right-2 top-2 px-1.5 py-0.5 rounded bg-slate-900/10 text-[6px] font-black uppercase text-slate-500">SCHEMA_VECTOR</div>
                                        </div>
                                    @endif
                                </div>
                                
                                @error('formData.'.$field['name'])
                                    <span class="text-[7px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 dark:border-white/5 dark:bg-white/5 flex justify-end gap-3 shrink-0">
                    <button wire:click="$set('showEditModal', false)" class="text-[9px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-all">Abort</button>
                    <button wire:click="savePolicy" class="rounded-lg bg-slate-900 px-6 py-2 text-[9px] font-black uppercase text-white shadow-xl hover:bg-blue-600 transition-all active:scale-95">Deploy Framework</button>
                </div>
            </div>
        </div>
    @endif
</div>
