<div class="space-y-8 pb-12">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-white px-10 py-10 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400">Institutional Governance</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Temporal Policy Hub</span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Holiday <span class="text-cyan-500">Sentinel</span>
                </h1>
                <p class="mt-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-loose max-w-2xl">
                    Mission control for jurisdictional calendars, holiday observance logic, and temporal corporate policies across global operational sectors.
                </p>
            </div>

            <div class="flex gap-4 p-1.5 rounded-2xl bg-slate-100 dark:bg-white/5 shadow-inner">
                <button wire:click="$set('activeTab', 'policies')" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'policies' ? 'bg-slate-900 text-white shadow-xl' : 'text-slate-400 hover:text-slate-600' }}">Policies</button>
                <button wire:click="$set('activeTab', 'calendar')" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'calendar' ? 'bg-slate-900 text-white shadow-xl' : 'text-slate-400 hover:text-slate-600' }}">Event Grid</button>
            </div>
        </div>
    </div>

    @if($activeTab === 'policies')
        {{-- Policy Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($policies as $policy)
                <div class="group relative flex flex-col rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:shadow-xl hover:border-cyan-400/30">
                    <div class="flex items-start justify-between mb-6">
                        <div class="h-14 w-14 flex items-center justify-center rounded-2xl bg-cyan-50 font-black text-[15px] text-cyan-600 shadow-inner dark:bg-cyan-500/10 dark:text-cyan-400 transition-transform group-hover:scale-110 uppercase">
                            {{ $policy->country_code }}
                        </div>
                        <div class="flex gap-2">
                             <button wire:click="openPolicyModal({{ $policy->id }})" class="p-2.5 rounded-xl bg-slate-50 text-slate-400 hover:bg-cyan-600 hover:text-white transition-all dark:bg-white/5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                             </button>
                             <button wire:confirm="Are you sure you want to purge this temporal policy node?" wire:click="deletePolicy({{ $policy->id }})" class="p-2.5 rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white transition-all dark:bg-white/5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                             </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-[9px] font-black uppercase text-cyan-600 tracking-widest">{{ $policy->code }}</span>
                        <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                        <span class="text-[8px] font-black uppercase text-slate-400 tracking-[0.2em]">{{ $policy->state_code ?: 'ALL REGIONS' }}</span>
                    </div>
                    <h4 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight truncate">{{ $policy->name }}</h4>
                    <p class="mt-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-loose line-clamp-2 h-12 uppercase">{{ $policy->description ?: 'Generic temporal observance policy for the specified navigational sector.' }}</p>

                    <div class="mt-8 pt-6 border-t border-slate-50 dark:border-white/5 flex items-center justify-between">
                         <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">{{ $policy->holiday_dates_count }} Observed Days</span>
                         </div>
                         <button wire:click="$set('selectedPolicyId', {{ $policy->id }}); $set('activeTab', 'calendar')" class="text-[9px] font-black uppercase text-cyan-600 hover:text-cyan-500 tracking-widest underline decoration-cyan-500/30 underline-offset-4 transition-all">Inspect temporal grid</button>
                    </div>
                </div>
            @endforeach

            <button wire:click="openPolicyModal()" class="group relative cursor-pointer flex flex-col items-center justify-center rounded-[2.5rem] border-4 border-dashed border-slate-100 p-12 text-center transition-all hover:bg-slate-50 hover:border-cyan-400 dark:border-white/5 dark:hover:bg-white/2">
                <div class="h-16 w-16 flex items-center justify-center rounded-2xl bg-white shadow-sm border border-slate-100 text-slate-300 dark:bg-slate-900 dark:border-white/10 group-hover:bg-cyan-600 group-hover:text-white group-hover:border-cyan-600 transition-all">
                    <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </div>
                <h4 class="mt-8 text-[12px] font-black text-slate-400 uppercase tracking-[0.25em] group-hover:text-cyan-600 transition-colors">Invoke Policy Node</h4>
            </button>
        </div>
    @else
        {{-- Calendar Tab --}}
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Left: Policy Selector --}}
            <aside class="w-full lg:w-96 shrink-0 space-y-4">
                <div class="rounded-[2rem] bg-slate-100 p-4 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                    <h5 class="px-4 py-2 text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Observance Vectors</h5>
                    @foreach(App\Models\HolidayPolicy::all() as $p)
                        <button wire:click="$set('selectedPolicyId', {{ $p->id }})" class="w-full px-5 py-4 rounded-2xl mb-2 flex items-center justify-between gap-4 transition-all {{ $selectedPolicyId == $p->id ? 'bg-white shadow-xl text-cyan-600 dark:bg-slate-900 dark:text-cyan-400' : 'text-slate-400 hover:bg-white/40 dark:hover:bg-white/2 hover:text-slate-600' }}">
                            <span class="text-[11px] font-black uppercase tracking-widest truncate">{{ $p->name }}</span>
                            @if($selectedPolicyId == $p->id)
                                <div class="h-1.5 w-1.5 rounded-full bg-cyan-500 animate-pulse shrink-0"></div>
                            @endif
                        </button>
                    @endforeach
                </div>
            </aside>

            {{-- Right: Event Grid --}}
            <main class="flex-1 space-y-6">
                @if($selectedPolicy)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                             <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $selectedPolicy->name }} <span class="text-cyan-500">Epochs</span></h3>
                             <span class="px-3 py-1 rounded-full bg-slate-100 text-[8px] font-black uppercase text-slate-400 dark:bg-white/5 shadow-inner">{{ $selectedPolicy->holidayDates->count() }} Signal Points</span>
                        </div>
                        <button wire:click="openDateModal()" class="px-8 py-3 rounded-xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-cyan-600 transition-all shadow-xl">Inject temporal signal</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($selectedPolicy->holidayDates as $date)
                            <div class="group flex items-center justify-between p-6 rounded-3xl border border-slate-100 bg-white dark:border-white/5 dark:bg-slate-900/50 hover:shadow-lg transition-all">
                                <div class="flex items-center gap-5">
                                    <div class="flex flex-col items-center justify-center h-14 w-14 rounded-2xl bg-slate-50 border border-slate-100 dark:bg-white/5 dark:border-white/10 shadow-inner shrink-0 group-hover:bg-cyan-50 group-hover:border-cyan-100 transition-colors">
                                        <span class="text-[14px] font-black text-slate-900 dark:text-white leading-none tracking-tighter">{{ $date->holiday_date->format('d') }}</span>
                                        <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">{{ $date->holiday_date->format('M') }}</span>
                                    </div>
                                    <div>
                                        <h5 class="text-[13px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $date->name }}</h5>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[8px] font-black uppercase tracking-widest {{ $date->is_optional ? 'text-amber-500' : 'text-cyan-600' }}">
                                                {{ $date->is_optional ? 'Discretionary' : 'Mandatory Observance' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-all scale-95 group-hover:scale-100">
                                    <button wire:click="openDateModal({{ $date->id }})" class="p-2 rounded-lg bg-slate-50 text-slate-400 hover:text-cyan-600 dark:bg-white/5">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                    </button>
                                    <button wire:confirm="Are you sure you want to clear this temporal marker?" wire:click="deleteDate({{ $date->id }})" class="p-2 rounded-lg bg-slate-50 text-slate-400 hover:text-rose-500 dark:bg-white/5">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-32 rounded-[3rem] bg-slate-50 border-4 border-dashed border-slate-100 flex flex-col items-center text-center dark:bg-white/2 dark:border-white/5">
                        <div class="h-20 w-20 rounded-[2rem] bg-white shadow-sm flex items-center justify-center text-slate-200 dark:bg-slate-900 mb-6">
                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        </div>
                        <h4 class="text-xl font-black text-slate-400 uppercase tracking-widest">Select a jurisdictional node to inspect temporal epochs.</h4>
                    </div>
                @endif
            </main>
        </div>
    @endif

    {{-- Policy Modal --}}
    @if($showPolicyModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div wire:click="$set('showPolicyModal', false)" class="absolute inset-0 bg-slate-900/60 backdrop-blur-xl transition-opacity"></div>
            <div class="relative w-full max-w-2xl rounded-[3rem] bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 flex flex-col animate-in fade-in zoom-in duration-300">
                <div class="flex items-center justify-between px-10 py-8 border-b border-slate-50 dark:border-white/5">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Temporal <span class="text-cyan-500">Node Provisioning</span></h2>
                    <button wire:click="$set('showPolicyModal', false)" class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-500 dark:bg-white/5 transition-all">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form wire:submit="savePolicy" class="p-10 space-y-8">
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1 text-xs">Node Descriptor</label>
                            <input wire:model="name" type="text" placeholder="Organizational Sector Policy..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest focus:ring-0 focus:border-cyan-400">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1 text-xs">Sector Slug</label>
                            <input wire:model="code" type="text" placeholder="HOLIDAY_DEFAULT..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest disabled:opacity-50" {{ $editingPolicyId ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1 text-xs">Country Sector</label>
                            <select wire:model="countryCode" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                                @foreach($countries as $code => $label)
                                    <option value="{{ $code }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1 text-xs">Administrative State</label>
                            <select wire:model="stateCode" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                                @foreach($states as $code => $label)
                                    <option value="{{ $code }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="pt-6 border-t border-slate-100 dark:border-white/5 flex justify-end gap-5">
                         <button type="button" wire:click="$set('showPolicyModal', false)" class="text-[11px] font-black uppercase text-slate-500 px-6 transition-all hover:text-slate-800">Abort</button>
                         <button type="submit" class="rounded-2xl bg-slate-900 px-12 py-4 text-[11px] font-black uppercase text-white shadow-2xl hover:bg-cyan-600 transition-all active:scale-95">Synchronize Policy Node</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Date Modal --}}
    @if($showDateModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div wire:click="$set('showDateModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity"></div>
            <div class="relative w-full max-w-lg rounded-[2.5rem] bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 flex flex-col animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between px-8 py-6 border-b border-slate-50 dark:border-white/5 shrink-0">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Inject <span class="text-cyan-500">Temporal Signal</span></h2>
                    <button wire:click="$set('showDateModal', false)" class="h-10 w-10 flex items-center justify-center text-slate-400 hover:text-rose-500 transition-all">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form wire:submit="saveDate" class="p-8 space-y-8">
                     <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Epoch Identifier</label>
                        <input wire:model="dateName" type="text" placeholder="National Celebration..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Temporal Marker (Date)</label>
                            <input wire:model="holidayDate" type="date" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Observance Type</label>
                            <div class="flex items-center gap-3 py-3">
                                <button type="button" @click="$wire.set('isOptional', false)" :class="!$wire.isOptional ? 'bg-cyan-600 text-white shadow-lg' : 'bg-slate-100 text-slate-400 dark:bg-white/5'" class="flex-1 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Public</button>
                                <button type="button" @click="$wire.set('isOptional', true)" :class="$wire.isOptional ? 'bg-amber-600 text-white shadow-lg' : 'bg-slate-100 text-slate-400 dark:bg-white/5'" class="flex-1 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Optional</button>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 flex justify-end gap-5">
                         <button type="button" wire:click="$set('showDateModal', false)" class="text-[11px] font-black uppercase text-slate-500 px-6 transition-all hover:text-slate-800">Abort</button>
                         <button type="submit" class="rounded-2xl bg-slate-900 px-10 py-3.5 text-[10px] font-black uppercase text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95">Lock Marker</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
