<div class="space-y-8 pb-12">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-violet-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-violet-600 dark:text-violet-400">Growth Console</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Career Advancement</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Performance <span class="text-violet-500">Hub</span>
                </h1>
                <p class="mt-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-loose">
                    {{ $isManager ? 'Oversee and evaluate team achievements and strategic objectives.' : 'Track your career goals, reviews, and 1-on-1 meeting records.' }}
                </p>
            </div>

            <div class="flex gap-4">
                @if($activeTab === 'goals')
                    <button wire:click="$set('showGoalModal', true)" class="h-12 inline-flex items-center justify-center gap-3 rounded-xl bg-slate-900 px-8 text-[11px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-violet-600 transition-all active:scale-95 dark:bg-white/5 dark:text-violet-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Define Goal</span>
                    </button>
                @elseif($activeTab === 'reviews' && $isManager)
                    <button wire:click="$set('showReviewModal', true)" class="h-12 inline-flex items-center justify-center gap-3 rounded-xl bg-slate-900 px-8 text-[11px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-indigo-600 transition-all active:scale-95 dark:bg-white/5 dark:text-indigo-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .466.187.912.518 1.243l3.5 3.5c.331.331.777.518 1.243.518.231 0 .454-.035.664-.1l.1-.035M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        <span>Write Review</span>
                    </button>
                @elseif($activeTab === 'meetings' && $isManager)
                    <button wire:click="$set('showMeetingModal', true)" class="h-12 inline-flex items-center justify-center gap-3 rounded-xl bg-slate-900 px-8 text-[11px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-emerald-600 transition-all active:scale-95 dark:bg-white/5 dark:text-emerald-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Log 1-on-1</span>
                    </button>
                @elseif($activeTab === 'feedback')
                    <button wire:click="$set('showFeedbackModal', true)" class="h-12 inline-flex items-center justify-center gap-3 rounded-xl bg-slate-900 px-8 text-[11px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:text-cyan-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                        <span>Request Feedback</span>
                    </button>
                @elseif($activeTab === 'praise')
                    <button wire:click="$set('showPraiseModal', true)" class="h-12 inline-flex items-center justify-center gap-3 rounded-xl bg-slate-900 px-8 text-[11px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-amber-500 transition-all active:scale-95 dark:bg-white/5 dark:text-amber-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                        <span>Publish Praise</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Tabs --}}
        <div class="mt-8 flex gap-1 border-b border-slate-100 dark:border-white/5">
            <button wire:click="setTab('goals')" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'goals' ? 'text-violet-600 border-b-2 border-violet-500 bg-violet-50/30' : 'text-slate-400 hover:text-slate-600 dark:hover:text-white' }}">Goal Tracking</button>
            <button wire:click="setTab('reviews')" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'reviews' ? 'text-indigo-600 border-b-2 border-indigo-500 bg-indigo-50/30' : 'text-slate-400 hover:text-slate-600 dark:hover:text-white' }}">Performance Reviews</button>
            <button wire:click="setTab('meetings')" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'meetings' ? 'text-emerald-600 border-b-2 border-emerald-500 bg-emerald-50/30' : 'text-slate-400 hover:text-slate-600 dark:hover:text-white' }}">1-on-1 Meetings</button>
            <button wire:click="setTab('feedback')" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'feedback' ? 'text-cyan-600 border-b-2 border-cyan-500 bg-cyan-50/30' : 'text-slate-400 hover:text-slate-600 dark:hover:text-white' }}">360º Feedback</button>
            <button wire:click="setTab('praise')" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'praise' ? 'text-amber-500 border-b-2 border-amber-400 bg-amber-50/30' : 'text-slate-400 hover:text-slate-600 dark:hover:text-white' }}">Public Praise</button>
        </div>
    </div>

    <div wire:loading.class="opacity-50" class="transition-opacity">
        @if($activeTab === 'goals')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($goals as $goal)
                    <div class="group relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:shadow-md">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-2">
                                @if($goal->priority === 'high')
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]"></span>
                                @elseif($goal->priority === 'medium')
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                @else
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                @endif
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $goal->priority }} Priority</span>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $goal->status }}</span>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-[12px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $goal->title }}</h4>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1 line-clamp-2">{{ $goal->description ?: 'No briefing provided.' }}</p>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-widest">
                                <span class="text-slate-400">Resolution Status</span>
                                <span class="text-slate-900 dark:text-white tabular-nums">{{ $goal->progress }}%</span>
                            </div>
                            <div class="h-1.5 w-full bg-slate-100 rounded-full dark:bg-white/5 overflow-hidden">
                                <div class="h-full bg-violet-500 rounded-full transition-all duration-700" style="width:{{ $goal->progress }}%"></div>
                            </div>
                        </div>

                        @if($goal->employee)
                            <div class="mt-6 pt-6 border-t border-slate-50 dark:border-white/5 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-lg bg-slate-100 flex items-center justify-center font-black text-[9px] text-slate-500 dark:bg-white/5">
                                        {{ substr($goal->employee->full_name, 0, 1) }}
                                    </div>
                                    <p class="text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase">{{ $goal->employee->full_name }}</p>
                                </div>
                                @if(!$isManager)
                                    <input type="range" wire:change="updateGoalProgress({{ $goal->id }}, $event.target.value)" value="{{ $goal->progress }}" min="0" max="100" class="w-24 h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer dark:bg-slate-700">
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center uppercase">
                        <p class="text-[10px] font-black text-slate-400 tracking-widest">Strategic objectives not yet defined.</p>
                    </div>
                @endforelse
            </div>
        @elseif($activeTab === 'reviews')
            <div class="space-y-4">
                @forelse($reviews as $review)
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-white/5 dark:bg-slate-900 overflow-hidden relative group">
                        <div class="absolute right-0 top-0 h-full w-2 bg-indigo-500/10 group-hover:bg-indigo-500 transition-all"></div>
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                            <div class="flex-1 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 flex-shrink-0 rounded-xl bg-indigo-50 flex items-center justify-center font-black text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                        {{ substr($review->employee->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $review->employee->full_name }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $review->review_cycle }} • {{ $review->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <p class="text-[11px] font-bold text-slate-600 dark:text-slate-400 leading-relaxed max-w-2xl">{{ $review->feedback }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-center lg:items-end gap-2">
                                <div class="flex gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200 dark:text-white/5' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    @endfor
                                </div>
                                <p class="text-[9px] font-black uppercase text-indigo-500 tracking-widest mt-1">Evaluated by {{ $review->reviewer->name }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center uppercase bg-white rounded-2xl border border-slate-200 dark:bg-slate-900 dark:border-white/5">
                        <p class="text-[10px] font-black text-slate-400 tracking-widest">Performance assessments will appear here.</p>
                    </div>
                @endforelse
            </div>
        @elseif($activeTab === 'meetings')
            <div class="space-y-6">
                @forelse($meetings as $meeting)
                    <div class="relative pl-12">
                        <div class="absolute left-4 top-0 bottom-0 w-[2px] bg-slate-100 dark:bg-white/5"></div>
                        <div class="absolute left-0 top-6 h-8 w-8 rounded-full border-4 border-white bg-emerald-500 shadow-sm dark:border-slate-800"></div>
                        
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-[11px] font-black uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('F d, Y') }}</span>
                                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Facilitated by {{ $meeting->manager->name }}</p>
                                </div>
                                <span class="bg-emerald-50 text-[9px] font-black uppercase px-2 py-0.5 rounded text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Doc #{{ $meeting->id }}</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
                                <div class="space-y-2">
                                    <h5 class="text-[9px] font-black uppercase tracking-widest text-slate-400">Critical Talking Points</h5>
                                    <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">{{ $meeting->talking_points }}</p>
                                </div>
                                @if($meeting->action_items)
                                    <div class="space-y-2">
                                        <h5 class="text-[9px] font-black uppercase tracking-widest text-emerald-500">Scheduled Actions</h5>
                                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">{{ $meeting->action_items }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center uppercase bg-white rounded-2xl border border-slate-200 dark:bg-slate-900 dark:border-white/5">
                        <p class="text-[10px] font-black text-slate-400 tracking-widest">Meeting documentation history is empty.</p>
                    </div>
                @endforelse
            </div>
        @elseif($activeTab === 'feedback')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($feedbackRequests as $fb)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900 relative">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100 dark:border-white/5">
                            <div class="flex items-center gap-2">
                                <span class="h-6 w-6 flex items-center justify-center rounded bg-cyan-50 text-[8px] font-black text-cyan-600 uppercase">{{ substr($fb->requester->full_name, 0, 1) }}</span>
                                <p class="text-[10px] font-black uppercase text-slate-900 dark:text-white">{{ $fb->requester->full_name }}</p>
                            </div>
                            <span class="text-[8px] font-black uppercase tracking-widest text-slate-400">Asked</span>
                            <div class="flex items-center gap-2">
                                <p class="text-[10px] font-black uppercase text-slate-900 dark:text-white">{{ $fb->reviewer->full_name }}</p>
                                <span class="h-6 w-6 flex items-center justify-center rounded bg-slate-50 text-[8px] font-black text-slate-500 uppercase">{{ substr($fb->reviewer->full_name, 0, 1) }}</span>
                            </div>
                        </div>
                        <p class="text-[11px] font-bold text-slate-600 dark:text-slate-400 italic mb-4">"{{ $fb->request_note }}"</p>
                        
                        @if($fb->status === 'pending')
                            <div class="flex justify-end mt-4">
                                @if($employee && $fb->reviewer_id === $employee->id)
                                    <button class="text-[9px] font-black uppercase tracking-widest text-white bg-slate-900 shadow-lg px-4 py-2 rounded-lg hover:bg-cyan-600 transition-all">Submit Feedback</button>
                                @else
                                    <span class="text-[9px] font-black uppercase tracking-widest text-amber-500 bg-amber-50 px-3 py-1 rounded-md">Pending Response</span>
                                @endif
                            </div>
                        @else
                            <div class="bg-slate-50 dark:bg-white/5 p-4 rounded-xl mt-4">
                                <p class="text-[10px] font-bold text-slate-700 dark:text-slate-300">{{ $fb->feedback }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center uppercase bg-white rounded-2xl border border-slate-200 dark:bg-slate-900 dark:border-white/5">
                        <p class="text-[10px] font-black text-slate-400 tracking-widest">No continuous feedback requests found.</p>
                    </div>
                @endforelse
            </div>
        @elseif($activeTab === 'praise')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @forelse($praises as $praise)
                    <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-amber-50/30 p-6 shadow-sm dark:border-white/5 dark:from-slate-900 dark:to-amber-900/10 group overflow-hidden relative">
                        <div class="absolute -right-10 -top-10 opacity-20 group-hover:scale-110 transition-transform">
                            @if($praise->badge === 'kudos')
                                <span class="text-8xl">👍</span>
                            @elseif($praise->badge === 'team_player')
                                <span class="text-8xl">🤝</span>
                            @else
                                <span class="text-8xl">💡</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 mb-6 relative">
                            <div class="h-12 w-12 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center font-black dark:bg-slate-800 dark:border-white/5">
                                {{ substr($praise->receiver->full_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase text-slate-900 dark:text-white">{{ $praise->receiver->full_name }}</p>
                                <p class="text-[8px] font-black uppercase text-amber-500 tracking-widest mt-0.5">{{ str_replace('_', ' ', $praise->badge) }}</p>
                            </div>
                        </div>
                        <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 leading-relaxed relative">{{ $praise->message }}</p>
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between relative">
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">From {{ $praise->sender->full_name }}</span>
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $praise->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center uppercase bg-white rounded-2xl border border-slate-200 dark:bg-slate-900 dark:border-white/5">
                        <p class="text-[10px] font-black text-slate-400 tracking-widest">Be the first to recognize a peer's hard work!</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    {{-- Modals --}}
    @if($showGoalModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showGoalModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-6 dark:border-white/5 flex items-center justify-between">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Set <span class="text-violet-500">Objective</span></h2>
                </div>
                <div class="p-6 space-y-4">
                    @if($isManager)
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500">Assign to Employee</label>
                            <select wire:model="goalEmployeeId" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                                <option value="">Select Staff</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500">Goal Title</label>
                        <input wire:model="goalTitle" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500">Priority Level</label>
                            <select wire:model="goalPriority" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500">Deadline</label>
                            <input wire:model="goalDueDate" type="date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500">Detailed Briefing</label>
                        <textarea wire:model="goalDescription" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-tighter"></textarea>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 p-6 dark:border-white/5 dark:bg-white/5 flex justify-end gap-3">
                    <button wire:click="$set('showGoalModal', false)" class="text-[10px] font-black uppercase text-slate-500 px-4">Cancel</button>
                    <button wire:click="saveGoal" class="rounded-xl bg-slate-900 px-8 py-2.5 text-[10px] font-black uppercase text-white shadow-xl hover:bg-violet-600 transition-all">Establish Goal</button>
                </div>
            </div>
        </div>
    @endif

    @if($showReviewModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showReviewModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-6 dark:border-white/5 flex items-center justify-between">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Staff <span class="text-indigo-500">Evaluation</span></h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500">Reviewee</label>
                        <select wire:model="reviewEmployeeId" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                            <option value="">Choose Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500">Review Cycle</label>
                            <input wire:model="reviewCycle" type="text" placeholder="e.g. Q1 2026" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500">Performance Rating</label>
                            <select wire:model="reviewRating" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Very Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500">Comprehensive Feedback</label>
                        <textarea wire:model="reviewFeedback" rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-tighter"></textarea>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 p-6 dark:border-white/5 dark:bg-white/5 flex justify-end gap-3">
                    <button wire:click="$set('showReviewModal', false)" class="text-[10px] font-black uppercase text-slate-500 px-4">Cancel</button>
                    <button wire:click="saveReview" class="rounded-xl bg-slate-900 px-8 py-2.5 text-[10px] font-black uppercase text-white shadow-xl hover:bg-indigo-600 transition-all">Submit Evaluation</button>
                </div>
            </div>
        </div>
    @endif

    @if($showMeetingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showMeetingModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-6 dark:border-white/5 flex items-center justify-between">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Document <span class="text-emerald-500">1-on-1</span></h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500">Participating Colleague</label>
                            <select wire:model="meetingEmployeeId" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                                <option value="">Select Staff</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500">Session Date</label>
                            <input wire:model="meetingDate" type="date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500">Core Talking Points</label>
                        <textarea wire:model="meetingTalkingPoints" rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-tighter"></textarea>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 p-6 dark:border-white/5 dark:bg-white/5 flex justify-end gap-3">
                    <button wire:click="$set('showMeetingModal', false)" class="text-[10px] font-black uppercase text-slate-500 px-4">Cancel</button>
                    <button wire:click="saveMeeting" class="rounded-xl bg-slate-900 px-8 py-2.5 text-[10px] font-black uppercase text-white shadow-xl hover:bg-emerald-600 transition-all">Log Session</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Feedback Request Modal --}}
    @if($showFeedbackModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showFeedbackModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-6 dark:border-white/5 flex items-center justify-between">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Request <span class="text-cyan-500">Feedback</span></h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500">Ask Peer</label>
                        <select wire:model="feedbackReviewerId" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                            <option value="">Select Coworker</option>
                            @foreach($allEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500">Context / What to focus on</label>
                        <textarea wire:model="feedbackNote" rows="4" placeholder="I'd love your thoughts on my recent presentation..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-tighter"></textarea>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 p-6 dark:border-white/5 dark:bg-white/5 flex justify-end gap-3">
                    <button wire:click="$set('showFeedbackModal', false)" class="text-[10px] font-black uppercase text-slate-500 px-4">Cancel</button>
                    <button wire:click="requestFeedback" class="rounded-xl bg-slate-900 px-8 py-2.5 text-[10px] font-black uppercase text-white shadow-xl hover:bg-cyan-600 transition-all">Send Request</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Public Praise Modal --}}
    @if($showPraiseModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showPraiseModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-6 dark:border-white/5 flex items-center justify-between">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Recognize <span class="text-amber-500">Peer</span></h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500">Who to praise</label>
                            <select wire:model="praiseReceiverId" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                                <option value="">Select Coworker</option>
                                @foreach($allEmployees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500">Badge Type</label>
                            <select wire:model="praiseBadge" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                                <option value="kudos">👍 Kudos</option>
                                <option value="team_player">🤝 Team Player</option>
                                <option value="innovator">💡 Innovator</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500">Message</label>
                        <textarea wire:model="praiseMessage" rows="4" placeholder="Amazing job delivering the project on time!" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-tighter"></textarea>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 p-6 dark:border-white/5 dark:bg-white/5 flex justify-end gap-3">
                    <button wire:click="$set('showPraiseModal', false)" class="text-[10px] font-black uppercase text-slate-500 px-4">Cancel</button>
                    <button wire:click="publishPraise" class="rounded-xl bg-slate-900 px-8 py-2.5 text-[10px] font-black uppercase text-white shadow-xl hover:bg-amber-500 transition-all">Publish</button>
                </div>
            </div>
        </div>
    @endif
</div>
