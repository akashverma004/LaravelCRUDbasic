@extends('hrms.layouts.app')

@section('title', 'Performance - PeopleFlow HRMS')

@section('content')
<div x-data="performanceManager()" x-init="init()">
    
    {{-- Toast Notification --}}
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl px-5 py-3 shadow-2xl"
        :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
        style="display: none;"
    >
        <template x-if="toast.type === 'success'">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </template>
        <span x-text="toast.message" class="text-sm font-medium"></span>
    </div>

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Performance Management</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Track goals, feedback, and growth across the organization</p>
        </div>
        <div class="flex items-center gap-2">
            <template x-if="activeTab === 'goals'">
                <button @click="showGoalForm = true" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Goal
                </button>
            </template>
            <template x-if="activeTab === 'reviews' && isManager">
                <button @click="showReviewForm = true" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    New Review
                </button>
            </template>
            <template x-if="activeTab === 'notes'">
                <button @click="showNoteForm = true" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Log 1-on-1
                </button>
            </template>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="mb-6 flex overflow-x-auto rounded-xl border border-slate-200 bg-white p-1 dark:border-slate-700 dark:bg-slate-800/50 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <button @click="activeTab = 'goals'" class="flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all" :class="activeTab === 'goals' ? 'bg-cyan-500 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-700'">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Goals & OKRs
        </button>
        <button @click="activeTab = 'reviews'" class="flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all" :class="activeTab === 'reviews' ? 'bg-cyan-500 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-700'">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Performance Reviews
        </button>
        <button @click="activeTab = 'notes'" class="flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all" :class="activeTab === 'notes' ? 'bg-cyan-500 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-700'">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            1-on-1 Notes
        </button>
    </div>

    {{-- Content Areas --}}
    <div class="space-y-6">
        
        {{-- TAB: Goals --}}
        <div x-show="activeTab === 'goals'" x-transition>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <template x-for="goal in goals" :key="goal.id">
                    <div class="group rounded-2xl border border-slate-200 bg-white p-5 transition-shadow hover:shadow-lg dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="mb-4 flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white" x-text="goal.title"></h4>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400" x-text="goal.description"></p>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                :class="{
                                    'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400': goal.priority === 'high',
                                    'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-400': goal.priority === 'medium',
                                    'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400': goal.priority === 'low'
                                }" x-text="goal.priority"></span>
                        </div>
                        
                        <div class="mb-2 flex items-center justify-between text-xs font-medium">
                            <span class="text-slate-500 dark:text-slate-400">Progress</span>
                            <span class="text-cyan-600 dark:text-cyan-400" x-text="goal.progress + '%'"></span>
                        </div>
                        <div class="mb-4 h-2 rounded-full bg-slate-100 dark:bg-slate-700">
                            <div class="h-2 rounded-full bg-cyan-500 transition-all duration-500" :style="'width: ' + goal.progress + '%'"></div>
                        </div>

                        <div class="flex items-center justify-between border-t border-slate-100 pt-4 dark:border-slate-700">
                            <span class="text-[10px] text-slate-400 dark:text-slate-500" x-text="'Due: ' + goal.due_date"></span>
                            <div class="flex gap-2">
                                <template x-if="goal.status === 'active'">
                                    <button @click="updateGoalProgress(goal)" class="rounded-lg p-1.5 text-slate-400 hover:bg-cyan-50 hover:text-cyan-600 dark:hover:bg-cyan-500/10">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- TAB: Reviews --}}
        <div x-show="activeTab === 'reviews'" x-transition>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800/50">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Cycle</th>
                            <th class="px-6 py-4">Employee</th>
                            <th class="px-6 py-4">Reviewer</th>
                            <th class="px-6 py-4 text-center">Rating</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <template x-for="review in reviews" :key="review.id">
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white" x-text="review.review_cycle"></td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400" x-text="review.employee.full_name"></td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400" x-text="review.reviewer.name"></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-0.5 text-amber-400">
                                        <template x-for="i in 5">
                                            <svg class="h-4 w-4" :class="i <= review.rating ? 'fill-current' : 'text-slate-200 dark:text-slate-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider"
                                        :class="review.status === 'submitted' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' : 'bg-slate-100 text-slate-500 dark:border-slate-600'" x-text="review.status"></span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-500" x-text="review.created_at"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB: 1-on-1 Notes --}}
        <div x-show="activeTab === 'notes'" x-transition>
            <div class="space-y-4">
                <template x-for="note in notes" :key="note.id">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 dark:text-white" x-text="'1-on-1 with ' + note.employee.full_name"></h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400" x-text="'Manager: ' + note.manager.name + ' · ' + note.meeting_date"></p>
                                </div>
                            </div>
                        </div>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <h5 class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">Talking Points</h5>
                                <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-wrap" x-text="note.talking_points"></p>
                            </div>
                            <div>
                                <h5 class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">Action Items</h5>
                                <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-wrap" x-text="note.action_items || 'No action items logged'"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- FORMS / MODALS (Simplification for brevity, we'd add full forms here) --}}
</div>
@endsection
