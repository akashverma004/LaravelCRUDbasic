<div class="space-y-4 pb-8">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-32 w-32 rounded-full bg-emerald-500/10 blur-[50px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400">Launch Protocol</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Onboarding Sequence</span>
                </div>
                <h1 class="text-lg font-black tracking-tight text-slate-900 dark:text-white uppercase mt-1">
                    Onboarding <span class="text-emerald-500">Hub</span>
                </h1>
                <p class="mt-1 text-[9px] font-bold text-slate-500 uppercase tracking-widest leading-loose">
                    Mission critical acclimation, documentation, and cultural integration tracking.
                </p>
            </div>

            @if($isAdmin)
                <div class="flex gap-2.5">
                    <button wire:click="openTemplateBuilder" class="group relative flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 px-4 py-1.5 text-[9px] font-black uppercase tracking-widest text-slate-700 shadow-sm hover:text-emerald-600 transition-all dark:bg-white/5 dark:border-white/10 dark:text-slate-300">
                        <span>New Blueprint</span>
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    </button>
                    <button wire:click="$set('showAssignModal', true)" class="group relative flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-1.5 text-[9px] font-black uppercase tracking-widest text-white shadow-md hover:bg-emerald-600 transition-all">
                        <span>Initiate Sequence</span>
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if(!$isAdmin)
        {{-- Employee View --}}
        @if($activeOnboarding)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Progress Sidebar --}}
                <div class="lg:col-span-1 space-y-4">
                    <div class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm dark:border-emerald-500/10 dark:bg-slate-900">
                        <div class="mb-5">
                            <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-600 mb-2">Completion Status</h4>
                            <div class="relative h-2 w-full rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                                <div class="absolute inset-y-0 left-0 bg-emerald-500 transition-all duration-700" style="width: {{ $activeOnboarding->progress }}%"></div>
                            </div>
                            <div class="mt-2.5 flex items-center justify-between">
                                <span class="text-xl font-black text-slate-900 dark:text-white">{{ $activeOnboarding->progress }}%</span>
                                <span class="text-[8px] font-black uppercase tracking-widest text-slate-400">{{ $activeOnboarding->tasks()->where('is_completed', true)->count() }} of {{ $activeOnboarding->tasks()->count() }} Phases</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-50 dark:border-white/5">
                            <p class="text-[9px] font-bold text-slate-400 leading-relaxed uppercase tracking-widest">Welcome to the organization. Please complete all checklist items below to finalize your integration sequence.</p>
                        </div>
                    </div>
                </div>

                {{-- Task List --}}
                <div class="lg:col-span-2 space-y-3">
                    @foreach($activeOnboarding->tasks as $task)
                        <div class="group relative flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:shadow-md">
                            <div wire:click="toggleTask({{ $task->id }})" class="cursor-pointer shrink-0 h-6 w-6 rounded-md border-2 {{ $task->is_completed ? 'bg-emerald-500 border-emerald-500' : 'bg-slate-50 border-slate-200 hover:border-emerald-400 dark:bg-white/5 dark:border-white/10' }} flex items-center justify-center transition-all">
                                @if($task->is_completed)
                                    <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="text-[10px] font-black uppercase tracking-tight {{ $task->is_completed ? 'text-slate-400 line-through' : 'text-slate-900 dark:text-white' }}">{{ $task->title }}</h4>
                                <p class="text-[8px] font-bold uppercase tracking-widest mt-0.5 {{ $task->is_completed ? 'text-slate-300' : 'text-slate-400' }}">{{ $task->description }}</p>
                            </div>

                            @if($task->is_completed)
                                <span class="text-[7px] font-black uppercase text-emerald-500 whitespace-nowrap">{{ $task->completed_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center rounded-[2rem] border-2 border-dashed border-slate-200 dark:border-white/10">
                <div class="h-14 w-14 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-400 mb-4 dark:bg-emerald-500/10">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.601a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-1.566-2.917A3.75 3.75 0 0012 18z" /></svg>
                </div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight text-center">No Active Sequences</h3>
                <p class="mt-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-widest">You have completed all assigned onboarding protocols.</p>
            </div>
        @endif
    @else
        {{-- Admin View --}}
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-4">
            {{-- Onboardings Grid --}}
            <div class="xl:col-span-3 space-y-4">
                <div class="flex items-center justify-between px-1">
                    <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Active Missions</h4>
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ count($onboardings) }} Deployment(s)</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($onboardings as $o)
                        <div class="group relative rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:shadow-md">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-9 w-9 rounded-lg bg-slate-50 flex items-center justify-center font-black dark:bg-white/5 uppercase tracking-tighter text-[10px] text-slate-400">
                                        {{ substr($o->employee->full_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $o->employee->full_name }}</h4>
                                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Joined {{ $o->started_at->format('M d, y') }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[7px] font-black uppercase tracking-widest {{ $o->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : 'bg-cyan-50 text-cyan-600' }}">
                                    {{ $o->status === 'completed' ? 'Synchronized' : 'Acclimating' }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-[7px] font-black uppercase tracking-widest">
                                        <span class="text-slate-400">Sequence Progress</span>
                                        <span class="text-slate-900 dark:text-white">{{ $o->progress }}%</span>
                                    </div>
                                    <div class="h-1 w-full rounded-full bg-slate-50 dark:bg-white/5 overflow-hidden">
                                        <div class="h-full bg-emerald-500 transition-all duration-700" style="width: {{ $o->progress }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap gap-1">
                                    @php $pendingCount = $o->tasks()->where('is_completed', false)->count(); @endphp
                                    @if($pendingCount > 0)
                                        <span class="px-1.5 py-0.5 rounded text-[6px] font-black uppercase tracking-widest bg-rose-50 text-rose-500">{{ $pendingCount }} Pending Gates</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Templates Sidebar --}}
            <div class="xl:col-span-1 space-y-4">
                <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 px-1 text-right">Blueprints</h4>
                
                @foreach($templates as $t)
                    <div class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $t->name }}</h4>
                            <div class="flex items-center gap-2">
                                <span class="bg-slate-100 dark:bg-white/5 px-1.5 py-0.5 rounded text-[7px] font-black uppercase tracking-widest text-slate-400">{{ $t->tasks_count }} Phases</span>
                                <button wire:click="openTemplateBuilder({{ $t->id }})" class="text-slate-400 hover:text-emerald-500 transition-colors">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                </button>
                            </div>
                        </div>
                        <p class="mt-1.5 text-[8px] font-bold text-slate-400 uppercase tracking-widest line-clamp-2">{{ $t->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Assign Modal --}}
    @if($showAssignModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div wire:click="$set('showAssignModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-sm rounded-[1.5rem] bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-4 dark:border-white/5">
                    <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Initiate <span class="text-emerald-500">Launch Sequence</span></h2>
                </div>
                
                <div class="p-4 space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-[8px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Target Employee</label>
                        <select wire:model="selectedEmployeeId" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[10px] font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase focus:ring-emerald-500/20 focus:border-emerald-500">
                            <option value="">Select Candidate</option>
                            @foreach($availableEmployees as $e)
                                <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                            @endforeach
                        </select>
                        @error('selectedEmployeeId') <span class="text-[7px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[8px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Sequence Blueprint</label>
                        <select wire:model="selectedTemplateId" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[10px] font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase focus:ring-emerald-500/20 focus:border-emerald-500">
                            <option value="">Select Blueprint</option>
                            @foreach($templates as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->tasks_count }} Gates)</option>
                            @endforeach
                        </select>
                        @error('selectedTemplateId') <span class="text-[7px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50 p-4 dark:border-white/5 dark:bg-white/5 flex justify-end gap-2.5">
                    <button wire:click="$set('showAssignModal', false)" class="text-[8px] font-black uppercase text-slate-500 px-3 py-1.5 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">Abort</button>
                    <button wire:click="assignOnboarding" class="rounded-lg bg-slate-900 px-4 py-1.5 text-[8px] font-black uppercase text-white shadow-md hover:bg-emerald-600 transition-all">Launch Deployment</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Template Builder Modal --}}
    @if($templateBuilderMode)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="closeTemplateBuilder" class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity"></div>
            <div class="relative w-full max-w-3xl rounded-[1.5rem] bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden flex flex-col max-h-[90vh]">
                
                <div class="border-b border-slate-100 p-5 dark:border-white/5 flex items-center justify-between shrink-0">
                    <div>
                        <h2 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tight">Onboarding <span class="text-emerald-500">Blueprint Builder</span></h2>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Design chronological checklist sequences</p>
                    </div>
                    <button wire:click="closeTemplateBuilder" class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="p-5 flex-1 overflow-y-auto custom-scrollbar space-y-6">
                    <div class="space-y-4">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100 dark:border-white/5 pb-2">Core Metadata</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5 md:col-span-1">
                                <label class="text-[8px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Blueprint Title</label>
                                <input wire:model="builderName" type="text" placeholder="e.g. Engineering Onboarding" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[10px] font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-tight focus:ring-emerald-500/20 focus:border-emerald-500">
                                @error('builderName') <span class="text-[7px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1.5 md:col-span-1">
                                <label class="text-[8px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Chronological Description</label>
                                <textarea wire:model="builderDescription" placeholder="Brief objective of this checklist..." rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[10px] font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-tight focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"></textarea>
                                @error('builderDescription') <span class="text-[7px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 pb-2">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Sequential Gateway Checklist</h3>
                            <button wire:click="addBuilderTask" class="text-[8px] font-black uppercase tracking-widest text-emerald-600 hover:text-emerald-500 flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                Add Gate
                            </button>
                        </div>
                        
                        <div class="space-y-3">
                            @foreach($builderTasks as $idx => $btask)
                                <div class="group relative rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-slate-900/50 flex gap-4 transition-all focus-within:ring-2 focus-within:ring-emerald-500/20">
                                    <div class="shrink-0 pt-2">
                                        <div class="h-6 w-6 rounded border-2 border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-slate-800 flex items-center justify-center text-[9px] font-black text-slate-400">
                                            {{ $idx + 1 }}
                                        </div>
                                    </div>
                                    <div class="flex-1 space-y-3">
                                        <div>
                                            <input wire:model="builderTasks.{{ $idx }}.title" type="text" placeholder="Gate Title (e.g. Slack Setup)" class="w-full bg-transparent border-0 border-b-2 border-slate-100 px-0 py-1.5 text-[10px] font-black text-slate-900 uppercase tracking-tight focus:ring-0 focus:border-emerald-500 dark:text-white dark:border-white/10 dark:focus:border-emerald-500 placeholder-slate-300">
                                            @error('builderTasks.'.$idx.'.title') <p class="text-[7px] font-black text-rose-500 uppercase mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <input wire:model="builderTasks.{{ $idx }}.description" type="text" placeholder="Actionable instruction..." class="w-full bg-transparent border-none px-0 py-1 text-[9px] font-bold text-slate-500 uppercase tracking-widest focus:ring-0 dark:text-slate-400 placeholder-slate-300">
                                        </div>
                                    </div>
                                    <div class="shrink-0 pt-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="removeBuilderTask({{ $idx }})" class="text-rose-400 hover:text-rose-600 p-1">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if(empty($builderTasks))
                                <div class="text-center py-6">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">No sequential gates formulated.</p>
                                    <button wire:click="addBuilderTask" class="mt-2 text-[8px] font-black uppercase text-emerald-600 hover:underline">Add First Gate</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-slate-100 bg-slate-50 p-5 dark:border-white/5 dark:bg-white/5 flex items-center justify-between shrink-0">
                    <div>
                        @if($builderId)
                            <button wire:click="deleteTemplate({{ $builderId }})" wire:confirm="Wipe this blueprint entirely? This will not affect active sequences already launched." class="text-[8px] font-black uppercase tracking-widest text-rose-500 hover:text-rose-600 hover:underline">Delete Blueprint</button>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <button wire:click="closeTemplateBuilder" class="text-[9px] font-black uppercase text-slate-500 px-4 py-2 hover:text-slate-900 dark:hover:text-white transition-colors tracking-widest">Discard</button>
                        <button wire:click="saveTemplate" class="rounded-xl bg-slate-900 px-6 py-2.5 text-[9px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-emerald-600 active:scale-95 transition-all">Engage & Save Blueprint</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
