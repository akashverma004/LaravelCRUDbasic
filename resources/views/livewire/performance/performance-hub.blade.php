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
                @endif
            </div>
        </div>

        {{-- Tabs --}}
        <div class="mt-8 flex gap-1 border-b border-slate-100 dark:border-white/5">
            <button wire:click="setTab('goals')" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'goals' ? 'text-violet-600 border-b-2 border-violet-500 bg-violet-50/30' : 'text-slate-400 hover:text-slate-600 dark:hover:text-white' }}">Goal Tracking</button>
            <button wire:click="setTab('reviews')" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'reviews' ? 'text-indigo-600 border-b-2 border-indigo-500 bg-indigo-50/30' : 'text-slate-400 hover:text-slate-600 dark:hover:text-white' }}">Performance Reviews</button>
            <button wire:click="setTab('meetings')" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'meetings' ? 'text-emerald-600 border-b-2 border-emerald-500 bg-emerald-50/30' : 'text-slate-400 hover:text-slate-600 dark:hover:text-white' }}">1-on-1 Meetings</button>
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
</div>
