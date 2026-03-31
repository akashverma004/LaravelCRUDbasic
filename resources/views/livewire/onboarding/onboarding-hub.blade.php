<div class="space-y-8 pb-12">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-emerald-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400">Launch Protocol</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Onboarding Sequence</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Onboarding <span class="text-emerald-500">Hub</span>
                </h1>
                <p class="mt-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-loose">
                    Mission critical acclimation, documentation, and cultural integration tracking.
                </p>
            </div>

            @if($isAdmin)
                <div class="flex gap-3">
                    <button wire:click="$set('showAssignModal', true)" class="group relative flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-emerald-600 transition-all">
                        <span>Initiate Sequence</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if(!$isAdmin)
        {{-- Employee View --}}
        @if($activeOnboarding)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Progress Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm dark:border-emerald-500/10 dark:bg-slate-900">
                        <div class="mb-8">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 mb-2">Completion Status</h4>
                            <div class="relative h-4 w-full rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                                <div class="absolute inset-y-0 left-0 bg-emerald-500 transition-all duration-700" style="width: {{ $activeOnboarding->progress }}%"></div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $activeOnboarding->progress }}%</span>
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $activeOnboarding->tasks()->where('is_completed', true)->count() }} of {{ $activeOnboarding->tasks()->count() }} Phases</span>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-50 dark:border-white/5">
                            <p class="text-[10px] font-bold text-slate-400 leading-relaxed uppercase tracking-widest">Welcome to the organization. Please complete all checklist items below to finalize your integration sequence.</p>
                        </div>
                    </div>
                </div>

                {{-- Task List --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($activeOnboarding->tasks as $task)
                        <div class="group relative flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:shadow-md">
                            <div wire:click="toggleTask({{ $task->id }})" class="cursor-pointer shrink-0 h-8 w-8 rounded-xl border-2 {{ $task->is_completed ? 'bg-emerald-500 border-emerald-500' : 'bg-slate-50 border-slate-200 hover:border-emerald-400 dark:bg-white/5 dark:border-white/10' }} flex items-center justify-center transition-all">
                                @if($task->is_completed)
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="text-[12px] font-black uppercase tracking-tight {{ $task->is_completed ? 'text-slate-400 line-through' : 'text-slate-900 dark:text-white' }}">{{ $task->title }}</h4>
                                <p class="text-[9px] font-bold uppercase tracking-widest mt-1 {{ $task->is_completed ? 'text-slate-300' : 'text-slate-400' }}">{{ $task->description }}</p>
                            </div>

                            @if($task->is_completed)
                                <span class="text-[8px] font-black uppercase text-emerald-500 whitespace-nowrap">{{ $task->completed_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 text-center rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-white/10">
                <div class="h-20 w-20 rounded-[2rem] bg-emerald-50 flex items-center justify-center text-emerald-400 mb-6 dark:bg-emerald-500/10">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.601a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-1.566-2.917A3.75 3.75 0 0012 18z" /></svg>
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight text-center">No Active Sequences</h3>
                <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">You have completed all assigned onboarding protocols.</p>
            </div>
        @endif
    @else
        {{-- Admin View --}}
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            {{-- Onboardings Grid --}}
            <div class="xl:col-span-3 space-y-6">
                <div class="flex items-center justify-between px-2">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Active Missions</h4>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ count($onboardings) }} Deployment(s)</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($onboardings as $o)
                        <div class="group relative rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:shadow-lg">
                            <div class="flex items-start justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center font-black dark:bg-white/5 uppercase tracking-tighter text-slate-400">
                                        {{ substr($o->employee->full_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <h4 class="text-[12px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $o->employee->full_name }}</h4>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Joined {{ $o->started_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest {{ $o->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : 'bg-cyan-50 text-cyan-600' }}">
                                    {{ $o->status === 'completed' ? 'Synchronized' : 'Acclimating' }}
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-[8px] font-black uppercase tracking-widest">
                                        <span class="text-slate-400">Sequence Progress</span>
                                        <span class="text-slate-900 dark:text-white">{{ $o->progress }}%</span>
                                    </div>
                                    <div class="h-1.5 w-full rounded-full bg-slate-50 dark:bg-white/5 overflow-hidden">
                                        <div class="h-full bg-emerald-500 transition-all duration-700" style="width: {{ $o->progress }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap gap-1.5">
                                    @php $pendingCount = $o->tasks()->where('is_completed', false)->count(); @endphp
                                    @if($pendingCount > 0)
                                        <span class="px-2 py-0.5 rounded text-[7px] font-black uppercase tracking-widest bg-rose-50 text-rose-500">{{ $pendingCount }} Pending Gates</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Templates Sidebar --}}
            <div class="xl:col-span-1 space-y-6">
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 px-2 text-right">Blueprints</h4>
                
                @foreach($templates as $t)
                    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-white/5 dark:bg-slate-900/50">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $t->name }}</h4>
                            <span class="bg-slate-100 dark:bg-white/5 px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest text-slate-400">{{ $t->tasks_count }} Phases</span>
                        </div>
                        <p class="mt-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest line-clamp-2">{{ $t->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Assign Modal --}}
    @if($showAssignModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showAssignModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-6 dark:border-white/5">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Initiate <span class="text-emerald-500">Launch Sequence</span></h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Target Employee</label>
                        <select wire:model="selectedEmployeeId" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                            <option value="">Select Candidate</option>
                            @foreach($availableEmployees as $e)
                                <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                            @endforeach
                        </select>
                        @error('selectedEmployeeId') <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Sequence Blueprint</label>
                        <select wire:model="selectedTemplateId" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                            <option value="">Select Blueprint</option>
                            @foreach($templates as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->tasks_count }} Gates)</option>
                            @endforeach
                        </select>
                        @error('selectedTemplateId') <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50 p-6 dark:border-white/5 dark:bg-white/5 flex justify-end gap-3">
                    <button wire:click="$set('showAssignModal', false)" class="text-[10px] font-black uppercase text-slate-500 px-4">Abort</button>
                    <button wire:click="assignOnboarding" class="rounded-xl bg-slate-900 px-8 py-2.5 text-[10px] font-black uppercase text-white shadow-xl hover:bg-emerald-600 transition-all">Launch Deployment</button>
                </div>
            </div>
        </div>
    @endif
</div>
