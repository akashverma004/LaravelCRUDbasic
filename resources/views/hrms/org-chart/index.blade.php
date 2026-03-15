@extends('hrms.layouts.app')

@section('title', 'Organization Chart - PeopleFlow HRMS')

@section('content')
<div class="space-y-8">
    {{-- Header Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-slate-800">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-cyan-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-center text-center">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Company <span class="text-cyan-500">Hierarchy</span>
            </h1>
            <p class="mt-2 text-sm text-slate-500">Displays the reporting structure of your organization.</p>
            <div class="mt-6 flex flex-wrap justify-center gap-6 text-xs font-semibold text-slate-600 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                    <span>{{ $stats['totalEmployees'] }} Total Employees</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    <span>{{ $stats['managers'] }} Managers</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Canvas --}}
    <div class="relative min-h-[500px]">
        @if($ceo)
            <div x-data="binaryTree()" class="relative overflow-x-auto rounded-3xl border border-slate-200 bg-slate-50 p-12 shadow-inner dark:border-slate-800 dark:bg-slate-900/20 custom-scrollbar">
                <div class="flex justify-center min-w-max">
                    @include('hrms.components.binary-node', ['employee' => $ceo])
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-32 bg-slate-50 rounded-3xl border-2 border-slate-200 border-dashed dark:bg-slate-900/20 dark:border-slate-800">
                <div class="h-16 w-16 flex items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-6 dark:bg-slate-800">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1" /></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">No Structure Found</h3>
                <p class="mt-2 text-sm text-slate-500">There are no employees configured in the hierarchy yet.</p>
            </div>
        @endif
    </div>
</div>

<script>
function binaryTree() {
    return {
        openNodes: {},
        toggle(id) {
            this.openNodes[id] = !this.openNodes[id]
        },
        isOpen(id) {
            return this.openNodes[id]
        }
    }
}
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.4); border-radius: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.05); }
    .dark .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); }
</style>
@endsection
