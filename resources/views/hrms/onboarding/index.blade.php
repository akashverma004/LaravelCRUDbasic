@extends('hrms.layouts.app')

@section('title', 'Onboarding - PeopleFlow HRMS')

@section('content')
<div x-data="onboardingPortal()" x-init="init()" class="space-y-6 relative">
    {{-- Universal Notification --}}
    <div 
        x-show="toast.show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-4 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-4 opacity-0 scale-95"
        class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/90 px-5 py-3 text-xs font-bold text-white shadow-2xl backdrop-blur-xl dark:bg-slate-800/90"
        x-cloak
    >
        <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="h-2 w-2 rounded-full animate-pulse"></div>
        <span x-text="toast.message"></span>
    </div>

    {{-- Employee View --}}
    <template x-if="!isAdmin && onboarding">
        <div class="max-w-4xl mx-auto space-y-6">
            {{-- Welcome --}}
            <div class="relative overflow-hidden rounded-2xl bg-slate-900 px-8 py-14 text-center dark:bg-slate-950/60 shadow-2xl">
                <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-[100px]"></div>
                <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-[100px]"></div>
                
                <div class="relative z-10">
                    <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-3xl shadow-2xl backdrop-blur-xl">👋</div>
                    <h1 class="text-4xl font-black tracking-tighter text-white mb-3 uppercase">Welcome to the <span class="text-cyan-400 font-black">Team</span></h1>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest max-w-sm mx-auto leading-relaxed">We're excited to have you here! Please complete the steps below to get started.</p>
                    
                    <div class="mt-12 mx-auto max-w-xs">
                        <div class="flex items-center justify-between mb-3 text-[10px]">
                            <span class="font-black uppercase tracking-[0.15em] text-cyan-500">Your Progress</span>
                            <span class="font-black text-white" x-text="onboarding.progress + '%'"></span>
                        </div>
                        <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden border border-white/5 p-0.5">
                            <div class="h-full bg-cyan-500 transition-all duration-1000 shadow-[0_0_12px_rgba(34,211,238,0.5)] rounded-full" :style="'width: ' + onboarding.progress + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Checklist --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900/50 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 dark:bg-white/5 dark:border-white/5">
                    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Your Checklist</h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-white/5">
                    <template x-for="task in onboarding.tasks" :key="task.id">
                        <div class="flex items-start gap-5 p-6 transition-all hover:bg-slate-50 dark:hover:bg-white/5">
                            <button @click="toggleTask(task)" :disabled="toggling" 
                                class="mt-1 h-5 w-5 flex-shrink-0 rounded-md border-2 border-slate-200 flex items-center justify-center transition-all disabled:opacity-50"
                                :class="task.is_completed ? 'bg-emerald-500 border-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'dark:border-white/10 text-transparent hover:border-cyan-500'">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="4.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                            </button>
                            <div class="min-w-0">
                                <h4 class="text-xs font-black uppercase tracking-tight text-slate-900 dark:text-white transition-all" :class="task.is_completed ? 'line-through text-slate-400' : ''" x-text="task.title"></h4>
                                <p class="mt-1.5 text-[10px] font-bold text-slate-500 uppercase tracking-wide leading-relaxed" x-text="task.description"></p>
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
                    <h1 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-white uppercase"><span class="text-cyan-500">Deployment</span> Matrix</h1>
                    <p class="mt-1 text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-none">Tracking integration velocity and procedural assimilation.</p>
                </div>
                <button @click="showAssignModal = true" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-white/10 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Insert Personnel</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="o in onboardings" :key="o.id">
                    <div class="group relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-xl dark:border-white/5 dark:bg-slate-900/50">
                        <div class="flex items-start justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-[11px] font-black text-slate-400 dark:bg-white/5 group-hover:bg-slate-900 group-hover:text-white transition-all" x-text="o.employee_name.charAt(0)"></div>
                                <div>
                                    <h4 class="text-xs font-black uppercase tracking-tight text-slate-900 dark:text-white truncate max-w-[140px]" x-text="o.employee_name"></h4>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mt-1" x-text="'Started: ' + o.started_at"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black text-slate-900 dark:text-white leading-none tabular-nums" x-text="o.progress + '%'"></span>
                                <p class="mt-1 text-[7px] font-black uppercase tracking-widest text-slate-300">Phase</p>
                            </div>
                        </div>
                        
                        <div class="h-2 w-full bg-slate-50 rounded-full overflow-hidden border border-slate-100 dark:bg-white/5 dark:border-white/5 shadow-inner">
                            <div class="h-full bg-cyan-500 shadow-[0_0_12px_rgba(6,182,212,0.4)] transition-all duration-1000" :style="'width: ' + o.progress + '%'"></div>
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

            <div class="flex justify-end gap-3 bg-slate-50 px-5 py-4 dark:bg-white/5">
                <button @click="showAssignModal = false" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cancel</button>
                <button @click="assignWorkflow()" :disabled="!assignForm.employee_id || !assignForm.template_id || toggling" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 border border-white/10 px-5 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-lg transition-all hover:bg-cyan-600 active:scale-95 disabled:opacity-50 dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    <span x-show="!toggling" class="flex items-center gap-2">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Start Onboarding
                    </span>
                    <span x-show="toggling" class="flex items-center gap-2">
                        <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Processing
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
