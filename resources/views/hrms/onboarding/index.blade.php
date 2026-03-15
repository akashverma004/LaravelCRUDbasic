@extends('hrms.layouts.app')

@section('title', 'Onboarding - PeopleFlow HRMS')

@section('content')
<div x-data="onboardingPortal()" x-init="init()" class="space-y-6">
    
    {{-- Toast Notification --}}
    <div
        x-show="toast.show"
        x-transition
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl px-4 py-3 shadow-xl border bg-white dark:bg-slate-900 border-emerald-200 text-emerald-800 dark:border-emerald-500/20 dark:text-emerald-400"
        style="display: none;"
    >
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
        </div>
        <span x-text="toast.message" class="text-sm font-bold"></span>
    </div>

    {{-- Employee View --}}
    <template x-if="!isAdmin && onboarding">
        <div class="max-w-4xl mx-auto space-y-6">
            {{-- Welcome --}}
            <div class="relative overflow-hidden rounded-2xl bg-slate-900 px-6 py-12 text-center dark:bg-slate-950/60 shadow-sm">
                <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-cyan-500/10 blur-[80px]"></div>
                <div class="relative z-10">
                    <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-3xl shadow-lg">👋</div>
                    <h1 class="text-4xl font-bold tracking-tight text-white mb-2">Welcome to the Team</h1>
                    <p class="text-slate-400 text-sm max-w-sm mx-auto">We're excited to have you here! Please complete the steps below to get started.</p>
                    
                    <div class="mt-10 mx-auto max-w-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400">Your Progress</span>
                            <span class="text-xs font-bold text-white" x-text="onboarding.progress + '%'"></span>
                        </div>
                        <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-cyan-500 transition-all duration-1000 shadow-[0_0_8px_rgba(34,211,238,0.4)]" :style="'width: ' + onboarding.progress + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Checklist --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 dark:bg-slate-950/50 dark:border-slate-800">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Your Checklist</h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    <template x-for="task in onboarding.tasks" :key="task.id">
                        <div class="flex items-start gap-4 p-6 transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                            <button @click="toggleTask(task)" :disabled="toggling" 
                                class="mt-1 h-6 w-6 flex-shrink-0 rounded-lg border flex items-center justify-center transition-all"
                                :class="task.is_completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-200 dark:border-slate-700 text-transparent hover:border-cyan-500'">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white" :class="task.is_completed ? 'line-through text-slate-400' : ''" x-text="task.title"></h4>
                                <p class="mt-1 text-xs text-slate-500" x-text="task.description"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    {{-- Empty State --}}
    <template x-if="!isAdmin && !onboarding && !loading">
        <div class="flex flex-col items-center justify-center py-20 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 dark:bg-slate-900/50 dark:border-slate-800">
            <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm text-emerald-500 dark:bg-slate-800">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">All Set!</h3>
            <p class="mt-2 text-sm text-slate-500 max-w-xs">You have completed all your onboarding tasks. Welcome aboard!</p>
        </div>
    </template>

    {{-- Admin View --}}
    <template x-if="isAdmin">
        <div class="space-y-6">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Onboarding Tracker</h1>
                    <p class="mt-2 text-sm text-slate-500">Manage new hires and track their progress.</p>
                </div>
                <button @click="showAssignModal = true" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Add New Hire</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="o in onboardings" :key="o.id">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-bold text-slate-500 uppercase" x-text="o.started_at"></span>
                            <span class="rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                :class="o.status === 'completed' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400'"
                                x-text="o.status"></span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate mb-4" x-text="o.employee_name"></h4>
                        
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 uppercase">
                                <span>Progress</span>
                                <span class="text-slate-900 dark:text-white" x-text="o.progress + '%'"></span>
                            </div>
                            <div class="h-1 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-cyan-500 transition-all duration-700" :style="'width: ' + o.progress + '%'"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <template x-if="!onboardings.length">
                <div class="py-20 text-center rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                    <p class="text-sm font-medium text-slate-500">No active onboardings found.</p>
                </div>
            </template>
        </div>
    </template>

    {{-- Setup Modal --}}
    <div x-show="showAssignModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.away="showAssignModal = false" class="w-full max-w-sm rounded-2xl bg-white shadow-xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">New Onboarding</h3>
                <button @click="showAssignModal = false" class="text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Select Employee</label>
                    <select x-model="assignForm.employee_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                        <option value="">Choose...</option>
                        <template x-for="emp in availableEmployees" :key="emp.id">
                            <option :value="String(emp.id)" x-text="emp.full_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Select Template</label>
                    <select x-model="assignForm.template_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                        <option value="">Choose...</option>
                        <template x-for="t in templates" :key="t.id">
                            <option :value="String(t.id)" x-text="t.name"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 bg-slate-50 px-5 py-3 dark:bg-slate-900/50">
                <button @click="showAssignModal = false" class="text-xs font-semibold text-slate-500">Cancel</button>
                <button @click="assignWorkflow()" :disabled="!assignForm.employee_id || !assignForm.template_id || toggling" class="rounded-lg bg-cyan-500 px-4 py-1.5 text-xs font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
                    <span x-text="toggling ? 'Starting...' : 'Start Onboarding'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
