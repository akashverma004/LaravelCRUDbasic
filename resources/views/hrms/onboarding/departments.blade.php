@extends('hrms.layouts.app')

@section('title', 'Setup Workspace - Step 2')

@section('content')
<div class="max-w-4xl mx-auto py-4 px-4" x-data="{
    departments: [
        { code: 'HR', name: 'Human Resources' },
        { code: 'ENG', name: 'Engineering' },
        { code: 'SALES', name: 'Sales & Marketing' }
    ],
    addDepartment() {
        this.departments.push({ code: '', name: '' });
    },
    removeDepartment(index) {
        if (this.departments.length > 1) {
            this.departments.splice(index, 1);
        }
    }
}">
    <div class="mb-4 p-4 rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-900 shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4xKSIvPjwvc3ZnPg==')] [mask-image:linear-gradient(to_bottom,white,transparent)]"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-black text-white uppercase tracking-tight">Organization <span class="text-indigo-400">Structure</span></h1>
                <p class="text-[9px] font-black uppercase tracking-widest text-indigo-200/80 mt-0.5">Configure foundational departments (Step 2/2)</p>
            </div>
            <div class="hidden sm:flex items-center gap-2 bg-white/10 p-1.5 rounded-xl border border-white/10 backdrop-blur-md">
                <div class="px-3 py-1.5 bg-indigo-500 rounded-lg shadow-sm border border-indigo-400 line-clamp-1 whitespace-nowrap">
                    <span class="text-[8px] font-black text-white uppercase tracking-widest gap-1 flex items-center">
                        <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        1. General
                    </span>
                </div>
                <div class="px-3 py-1.5 opacity-50 bg-white/5 rounded-lg border border-white/5 line-clamp-1 whitespace-nowrap">
                    <span class="text-[8px] font-black text-white uppercase tracking-widest">2. Structure</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-white/10 overflow-hidden">
        <form action="{{ route('onboarding.departments.store') }}" method="POST" class="p-5">
            @csrf
            
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50/50 border border-rose-100 dark:bg-rose-950/20 dark:border-rose-900/50">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 shrink-0 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <h3 class="text-[10px] font-black uppercase text-rose-600 tracking-widest">Verification Failed</h3>
                            <ul class="mt-2 list-inside list-disc text-xs font-bold text-rose-500 space-y-1">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-white/5">
                    <div>
                        <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Department Matrix</h2>
                        <p class="text-[8px] font-bold text-slate-400 mt-0.5 uppercase tracking-widest">Pre-populate your organization's hierarchy.</p>
                    </div>
                    <button type="button" @click="addDepartment" class="group relative flex items-center gap-1.5 rounded-lg bg-indigo-50 border border-indigo-100 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 transition-all dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:hover:bg-indigo-500/20 dark:text-indigo-400 shrink-0">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Add Node
                    </button>
                </div>

                <div class="space-y-2 pt-1">
                    <template x-for="(dept, index) in departments" :key="index">
                        <div class="group relative grid grid-cols-[1fr,2fr,auto] gap-2 items-start bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl p-3 transition-all focus-within:ring-1 focus-within:ring-indigo-500/20">
                            
                            <div class="space-y-1">
                                <label class="ml-1 block text-[7px] font-black uppercase text-slate-400 tracking-[0.2em]">Dept Code</label>
                                <input type="text" x-model="dept.code" :name="`departments[${index}][code]`" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[10px] font-black text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white uppercase transition-all" placeholder="ENG" required>
                            </div>
                            
                            <div class="space-y-1 flex-1 w-full">
                                <label class="ml-1 block text-[7px] font-black uppercase text-slate-400 tracking-[0.2em]">Department Name</label>
                                <input type="text" x-model="dept.name" :name="`departments[${index}][name]`" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[10px] font-black text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-900 dark:text-white transition-all uppercase" placeholder="Engineering" required>
                            </div>

                            <div class="pt-4 mt-1 opacity-0 group-hover:opacity-100 focus-within:opacity-100 transition-opacity flex items-center justify-center">
                                <button type="button" @click="removeDepartment(index)" class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded bg-white dark:bg-rose-500/10 transition-colors shadow-sm border border-slate-200 dark:border-white/5" x-show="departments.length > 1">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
                <a href="{{ route('onboarding.show') }}" class="text-[8px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Back to Setup
                </a>
                <button type="submit" class="relative group overflow-hidden rounded-lg bg-slate-900 dark:bg-white px-6 py-2.5 text-[9px] font-black uppercase tracking-[0.3em] text-white dark:text-slate-900 shadow-md shadow-slate-300 dark:shadow-none hover:-translate-y-[1px] transition-all active:scale-95">
                    <span class="relative z-10 flex items-center gap-1.5">
                        Initialize Dashboard
                        <svg class="h-3 w-3 text-emerald-400 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                    <div class="absolute inset-0 bg-indigo-600 dark:bg-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
