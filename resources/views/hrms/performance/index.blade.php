@extends('hrms.layouts.app')

@section('title', 'Performance - PeopleFlow HRMS')

@section('content')
<div x-data="performanceManager()" x-init="init()" class="space-y-6">
    
    {{-- Toast --}}
    <div
        x-show="toast.show"
        x-transition
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl px-4 py-2.5 shadow-xl border bg-white dark:bg-slate-900"
        :class="toast.type === 'success' ? 'border-emerald-200 text-emerald-800 dark:border-emerald-500/20 dark:text-emerald-400' : 'border-rose-200 text-rose-800 dark:border-rose-500/20 dark:text-rose-400'"
        style="display: none;"
    >
        <div class="flex h-7 w-7 items-center justify-center rounded-lg" :class="toast.type === 'success' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10' : 'bg-rose-50 text-rose-600 dark:bg-rose-500/10'">
            <svg x-show="toast.type === 'success'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            <svg x-show="toast.type === 'error'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </div>
        <span x-text="toast.message" class="text-xs font-bold"></span>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-5 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Performance</h1>
            <p class="mt-1 text-sm text-slate-500">Track goals, check progress, and share feedback.</p>
        </div>
        <div class="flex items-center gap-2">
            <template x-if="activeTab === 'goals'">
                <button @click="showGoalForm = true" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>New Goal</span>
                </button>
            </template>
            <template x-if="activeTab === 'reviews' && isManager">
                <button @click="showReviewForm = true" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>New Review</span>
                </button>
            </template>
            <template x-if="activeTab === 'notes'">
                <button @click="showNoteForm = true" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>New Note</span>
                </button>
            </template>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 p-1 bg-slate-100 rounded-lg dark:bg-slate-900 w-fit">
        <button @click="activeTab = 'goals'" class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-md transition-colors" :class="activeTab === 'goals' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'">Goals</button>
        <button @click="activeTab = 'reviews'" class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-md transition-colors" :class="activeTab === 'reviews' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'">Reviews</button>
        <button @click="activeTab = 'notes'" class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-md transition-colors" :class="activeTab === 'notes' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'">Notes</button>
    </div>

    {{-- Content Area --}}
    <div>
        
        {{-- Goals Grid --}}
        <div x-show="activeTab === 'goals'" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <template x-for="goal in goals" :key="goal.id">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex justify-between items-start mb-4">
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="goal.title"></h4>
                            <p class="mt-0.5 text-[10px] text-slate-500 line-clamp-1" x-text="goal.description"></p>
                        </div>
                        <span class="rounded px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-tighter"
                            :class="{
                                'bg-rose-50 text-rose-600 dark:bg-rose-500/10': goal.priority === 'high',
                                'bg-amber-50 text-amber-600 dark:bg-amber-500/10': goal.priority === 'medium',
                                'bg-slate-50 text-slate-500 dark:bg-white/5': goal.priority === 'low'
                            }" x-text="goal.priority"></span>
                    </div>
                    
                    <div class="mt-4">
                        <div class="flex justify-between text-[9px] font-bold mb-1.5 text-slate-400">
                            <span class="uppercase">Progress</span>
                            <span class="text-cyan-500" x-text="goal.progress + '%'"></span>
                        </div>
                        <div class="h-1 w-full rounded-full bg-slate-50 dark:bg-slate-800">
                            <div class="h-full bg-cyan-500 transition-all duration-700" :style="'width: ' + goal.progress + '%'"></div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider">Due</span>
                            <span class="text-[10px] font-bold text-slate-700 dark:text-white" x-text="goal.due_date"></span>
                        </div>
                        <template x-if="goal.status === 'active'">
                            <button @click="updateGoalProgress(goal)" class="h-7 w-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-50 hover:text-cyan-600 dark:hover:bg-white/5 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <template x-if="goals.length === 0">
                <div class="col-span-full py-8 text-center rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                    <p class="text-xs font-medium text-slate-500">No active goals.</p>
                </div>
            </template>
        </div>

        {{-- Reviews Table --}}
        <div x-show="activeTab === 'reviews'" class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden" style="display: none;">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 dark:bg-slate-950/50 dark:border-slate-800">
                            <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Period</th>
                            <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Employee</th>
                            <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Reviewer</th>
                            <th class="px-5 py-3 text-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">Rating</th>
                            <th class="px-5 py-3 text-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <template x-for="review in reviews" :key="review.id">
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                <td class="px-5 py-3 text-xs font-bold text-slate-900 dark:text-white" x-text="review.review_cycle"></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 flex items-center justify-center rounded bg-slate-100 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400" x-text="review.employee.full_name.charAt(0)"></div>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="review.employee.full_name"></span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-500 truncate" x-text="review.reviewer.name"></td>
                                <td class="px-5 py-3 text-center">
                                    <div class="flex items-center justify-center gap-0.5 text-amber-400">
                                        <template x-for="i in 5">
                                            <svg class="h-2.5 w-2.5" :class="i <= review.rating ? 'fill-current' : 'text-slate-100 dark:text-slate-800'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="rounded px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider"
                                        :class="review.status === 'submitted' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'" x-text="review.status"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Notes Grid --}}
        <div x-show="activeTab === 'notes'" class="grid gap-4" style="display: none;">
            <template x-for="note in notes" :key="note.id">
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-9 w-9 flex items-center justify-center rounded-lg bg-slate-100 text-base font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400" x-text="note.employee.full_name.charAt(0)"></div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white" x-text="note.employee.full_name"></h4>
                            <p class="text-[10px] font-bold text-slate-400" x-text="note.meeting_date"></p>
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-950/30">
                            <h5 class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Observations</h5>
                            <p class="text-[11px] leading-relaxed text-slate-700 dark:text-slate-300 whitespace-pre-wrap" x-text="note.talking_points"></p>
                        </div>
                        <div class="rounded-lg bg-cyan-50/30 p-3 dark:bg-cyan-500/5">
                            <h5 class="text-[9px] font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-1.5">Actions</h5>
                            <p class="text-[11px] leading-relaxed text-slate-700 dark:text-slate-300 whitespace-pre-wrap" x-text="note.action_items || 'None planned.'"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Goal Form --}}
    <div x-show="showGoalForm" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.away="showGoalForm = false" class="w-full max-w-sm rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-widest">New Goal</h3>
                <button @click="showGoalForm = false" class="text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Goal Title</label>
                    <input type="text" x-model="goalForm.title" placeholder="e.g. Sales Target Q3" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] font-bold focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white">
                </div>
                <div>
                    <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Description</label>
                    <textarea x-model="goalForm.description" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] font-medium focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 dark:border-white/5 dark:bg-white/5 dark:text-white"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Due Date</label>
                        <input type="date" x-model="goalForm.due_date" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] font-bold focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Priority</label>
                        <select x-model="goalForm.priority" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] font-bold focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                <template x-if="isManager">
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Assign To</label>
                        <select x-model="goalForm.employee_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] font-bold focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white">
                            <option value="">Myself</option>
                            <template x-for="emp in employees" :key="emp.id">
                                <option :value="emp.id" x-text="emp.full_name"></option>
                            </template>
                        </select>
                    </div>
                </template>
            </div>
            <div class="flex justify-end gap-3 bg-slate-50 px-5 py-4 dark:bg-white/5">
                <button @click="showGoalForm = false" class="text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cancel</button>
                <button @click="saveGoal()" :disabled="!goalForm.title || saving" class="rounded-xl bg-slate-900 px-5 py-2 text-[10px] font-bold uppercase tracking-widest text-white shadow-xl shadow-slate-900/20 hover:bg-cyan-600 hover:shadow-cyan-600/20 disabled:opacity-50 transition-all active:scale-95">Create Goal</button>
            </div>
        </div>
    </div>

    {{-- Review Form --}}
    <div x-show="showReviewForm" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.away="showReviewForm = false" class="w-full max-w-xl rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-widest">Performance Review</h3>
                <button @click="showReviewForm = false" class="text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                {{-- Employee Select --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Review Cycle</label>
                        <input type="text" x-model="reviewForm.review_cycle" placeholder="e.g. Annual 2024" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] font-bold focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Employee</label>
                        <select x-model="reviewForm.employee_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] font-bold focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white">
                            <option value="">Select Employee</option>
                            <template x-for="emp in employees" :key="emp.id">
                                <option :value="emp.id" x-text="emp.full_name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                {{-- Rating & Feedback --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Rating (1-5)</label>
                        <div class="flex gap-2">
                            <template x-for="r in [1,2,3,4,5]">
                                <button @click="reviewForm.rating = r" 
                                    class="h-10 w-10 flex items-center justify-center rounded-xl font-bold transition-all border"
                                    :class="reviewForm.rating === r ? 'bg-cyan-500 border-cyan-500 text-white shadow-lg shadow-cyan-500/20' : 'bg-slate-50 border-slate-100 text-slate-400 hover:border-cyan-200 dark:bg-white/5 dark:border-white/5'">
                                    <span x-text="r"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">General Feedback</label>
                        <textarea x-model="reviewForm.feedback" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white"></textarea>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 text-emerald-600">Strengths</label>
                            <textarea x-model="reviewForm.strengths" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] focus:border-emerald-500 dark:border-white/5 dark:bg-white/5 dark:text-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 text-rose-600">Areas for Improvement</label>
                            <textarea x-model="reviewForm.areas_for_improvement" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] focus:border-rose-500 dark:border-white/5 dark:bg-white/5 dark:text-white"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 bg-slate-50 px-6 py-4 dark:bg-white/5">
                <button @click="showReviewForm = false" class="text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cancel</button>
                <button @click="saveReview()" :disabled="!reviewForm.employee_id || saving" class="rounded-xl bg-slate-900 px-6 py-2 text-[10px] font-bold uppercase tracking-widest text-white shadow-xl shadow-slate-900/20 hover:bg-cyan-600 hover:shadow-cyan-600/20 disabled:opacity-50 transition-all active:scale-95">Submit Review</button>
            </div>
        </div>
    </div>

    {{-- 1-on-1 Note Form --}}
    <div x-show="showNoteForm" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.away="showNoteForm = false" class="w-full max-w-xl rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-widest">1-on-1 Meeting Note</h3>
                <button @click="showNoteForm = false" class="text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Meeting Date</label>
                        <input type="date" x-model="noteForm.meeting_date" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] font-bold focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Employee</label>
                        <select x-model="noteForm.employee_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] font-bold focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white">
                            <option value="">Select Employee</option>
                            <template x-for="emp in employees" :key="emp.id">
                                <option :value="emp.id" x-text="emp.full_name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Observations & Talking Points</label>
                        <textarea x-model="noteForm.talking_points" rows="3" placeholder="What did we discuss?" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 text-cyan-600">Action Items</label>
                        <textarea x-model="noteForm.action_items" rows="2" placeholder="Tasks for next time..." class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] focus:border-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 text-rose-500">Private Manager Notes (Hidden from Employee)</label>
                        <textarea x-model="noteForm.private_notes" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50/30 px-3 py-2 text-[11px] focus:border-rose-500 dark:border-white/5 dark:bg-white/5 dark:text-white"></textarea>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 bg-slate-50 px-6 py-4 dark:bg-white/5">
                <button @click="showNoteForm = false" class="text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cancel</button>
                <button @click="saveNote()" :disabled="!noteForm.employee_id || saving" class="rounded-xl bg-slate-900 px-6 py-2 text-[10px] font-bold uppercase tracking-widest text-white shadow-xl shadow-slate-900/20 hover:bg-cyan-600 hover:shadow-cyan-600/20 disabled:opacity-50 transition-all active:scale-95">Save Note</button>
            </div>
        </div>
    </div>
</div>
@endsection
