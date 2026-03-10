@extends('hrms.layouts.app')

@section('title', 'Onboarding - PeopleFlow HRMS')

@section('content')
<div x-data="onboardingPortal()" x-init="init()">
    
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

    {{-- Employee View (Initial Load / Non-Admin) --}}
    <template x-if="!isAdmin && onboarding">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8 p-8 rounded-3xl bg-gradient-to-br from-cyan-600 to-indigo-700 text-white shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="h-32 w-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.15 14.1H3.85L12 5.45zM11 16h2v2h-2v-2zm0-7h2v5h-2V9z"></path></svg>
                </div>
                <div class="relative z-10 text-center">
                    <h1 class="text-3xl font-extrabold mb-2">Welcome to the Team! 👋</h1>
                    <p class="text-cyan-100 text-lg mb-6">We're so excited to have you here. Let's get you set up.</p>
                    
                    <div class="max-w-md mx-auto">
                        <div class="flex items-center justify-between mb-2 text-sm font-semibold">
                            <span>Your Progress</span>
                            <span x-text="onboarding.progress + '%'"></span>
                        </div>
                        <div class="h-3 w-full bg-white/20 rounded-full overflow-hidden">
                            <div class="h-full bg-white transition-all duration-1000" :style="'width: ' + onboarding.progress + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Your Checklist</h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    <template x-for="task in onboarding.tasks" :key="task.id">
                        <div class="flex items-center gap-4 p-6 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <button @click="toggleTask(task)" :disabled="toggling" 
                                class="flex-shrink-0 h-7 w-7 rounded-full border-2 flex items-center justify-center transition-all"
                                :class="task.is_completed ? 'bg-green-500 border-green-500 text-white' : 'border-slate-300 dark:border-slate-600 text-transparent'">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white" :class="task.is_completed ? 'line-through text-slate-400' : ''" x-text="task.title"></h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400" x-text="task.description"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    {{-- Empty State (No active onboarding) --}}
    <template x-if="!isAdmin && !onboarding && !loading">
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="h-20 w-20 rounded-3xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-4">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">All Set!</h3>
            <p class="text-slate-500 dark:text-slate-400 max-w-sm mt-1">You don't have any active onboarding tasks at the moment.</p>
        </div>
    </template>

    {{-- Admin View --}}
    <template x-if="isAdmin">
        <div>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Onboarding Pipeline</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Manage new hire workflows and track their arrival progress</p>
                </div>
                <button @click="showAssignModal = true" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600 transition-colors">
                    Assign Workflow
                </button>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <template x-for="o in onboardings" :key="o.id">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800/50 group hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400" x-text="'Started ' + o.started_at"></span>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
                                :class="o.status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-500/20' : 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20'"
                                x-text="o.status"></span>
                        </div>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-4" x-text="o.employee_name"></h4>
                        
                        <div class="mb-2 flex items-center justify-between text-xs font-medium">
                            <span class="text-slate-500 dark:text-slate-400">Progress</span>
                            <span class="text-cyan-600 dark:text-cyan-400" x-text="o.progress + '%'"></span>
                        </div>
                        <div class="h-2 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-cyan-500 transition-all duration-1000" :style="'width: ' + o.progress + '%'"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- Assign Modal --}}
    <div x-show="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="showAssignModal = false" class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-md p-8 shadow-2xl">
            <h3 class="text-xl font-bold mb-6">Assign Onboarding Workflow</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">New Hire</label>
                    <select x-model="assignForm.employee_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Select Employee</option>
                        <template x-for="emp in availableEmployees" :key="emp.id">
                            <option :value="emp.id" x-text="emp.full_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Workflow Template</label>
                    <select x-model="assignForm.template_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Select Template</option>
                        <template x-for="t in templates" :key="t.id">
                            <option :value="t.id" x-text="t.name"></option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button @click="assignWorkflow()" :disabled="!assignForm.employee_id || !assignForm.template_id || toggling" class="flex-1 rounded-xl bg-cyan-500 py-3 text-sm font-bold text-white hover:bg-cyan-600 disabled:opacity-50">Assign Workflow</button>
                <button @click="showAssignModal = false" class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection
