@extends('hrms.layouts.app')

@section('title', 'Set Up Departments — Step 2 of 2')

@section('content')
<div class="mx-auto max-w-4xl space-y-10">

    {{-- ── High Impact Progress Header ──────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 px-8 py-10 shadow-2xl dark:bg-slate-950/40 dark:backdrop-blur-xl">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-[80px]"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-[80px]"></div>
        
        <div class="relative">
            <h1 class="text-4xl font-black tracking-tight text-white lg:text-5xl">Almost <span class="text-cyan-400">deployed</span>.</h1>
            <p class="mt-4 text-lg font-medium text-slate-400">Define your organizational units to initialize the command structure.</p>

            <div class="mt-10 flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Protocol One: COMPANY_INFO</span>
                </div>
                <div class="h-px flex-1 bg-white/5"></div>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-400 text-slate-950 shadow-[0_0_15px_rgba(34,211,238,0.3)] font-black text-sm italic">02</div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-white">Protocol Two: UNIT_DEFINITION</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Unit Template Suggestions ─────────────────────── --}}
    <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
        <p class="mb-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Protocol Templates</p>
        <div class="flex flex-wrap gap-3" id="suggestions">
            @php
                $suggestions = [
                    ['name' => 'Engineering',      'code' => 'ENG'],
                    ['name' => 'Human Resources',  'code' => 'HR'],
                    ['name' => 'Sales',             'code' => 'SALES'],
                    ['name' => 'Marketing',         'code' => 'MKT'],
                    ['name' => 'Finance',           'code' => 'FIN'],
                    ['name' => 'Operations',        'code' => 'OPS'],
                    ['name' => 'Product',           'code' => 'PROD'],
                    ['name' => 'Design',            'code' => 'DES'],
                    ['name' => 'Customer Support',  'code' => 'CS'],
                    ['name' => 'Legal',             'code' => 'LEGAL'],
                ];
            @endphp
            @foreach($suggestions as $s)
                <button type="button"
                    data-name="{{ $s['name'] }}"
                    data-code="{{ $s['code'] }}"
                    onclick="addSuggestion(this)"
                    class="suggestion-chip group flex items-center gap-2 rounded-2xl border border-slate-100 bg-white px-5 py-3 text-[10px] font-black uppercase tracking-widest text-slate-600 transition-all hover:border-cyan-400 hover:bg-cyan-50 hover:text-cyan-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400 dark:hover:bg-cyan-900/20 dark:hover:text-cyan-400 cursor-pointer shadow-sm">
                    <span class="text-xs transition-transform group-hover:rotate-90">+</span>
                    {{ $s['name'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── Main Deployment Grid ────────────────────────────────── --}}
    <div x-data="asyncForm({ followRedirect: true })" class="rounded-[2.5rem] border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-50 dark:border-slate-800 px-8 py-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">Active Units</h2>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">Add operational divisions to initialize telemetry.</p>
            </div>
        </div>

        <div x-show="toast.show" x-transition class="mx-8 mt-6 rounded-2xl px-6 py-4 text-sm font-black" :class="toast.type === 'success' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white'" style="display: none;">
            <span x-text="toast.message"></span>
        </div>
        <div x-show="errorMessage" class="mx-8 mt-6 rounded-2xl bg-rose-500 px-6 py-4 text-sm font-black text-white shadow-lg" style="display: none;">
            <span x-text="errorMessage"></span>
        </div>

        <form x-ref="form" @submit.prevent="submit()" method="POST" action="{{ route('onboarding.departments.store') }}" id="dept-form" class="p-8 space-y-6">
            @csrf

            {{-- Department rows container --}}
            <div id="dept-rows" class="space-y-4">
                {{-- Dynamic rows will be here --}}
            </div>

            {{-- Add row button --}}
            <button type="button" onclick="addRow()"
                class="group flex w-full items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-slate-100 bg-slate-50/50 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all hover:border-cyan-400 hover:text-cyan-500 dark:border-slate-800 dark:bg-slate-950/40">
                <svg class="h-4 w-4 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Initialize Another Unit
            </button>

            {{-- Footer Command Center --}}
            <div class="flex items-center justify-between border-t border-slate-50 dark:border-slate-800 pt-8 mt-10">
                <a href="{{ route('onboarding.show') }}" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all">
                    <svg class="h-3 w-3 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Abort to Step 1
                </a>
                <button type="submit" :disabled="saving"
                    class="group relative flex items-center gap-4 overflow-hidden rounded-[1.5rem] bg-slate-900 px-10 py-5 text-[10px] font-black uppercase tracking-widest text-white transition-all hover:bg-cyan-500 hover:text-slate-950 dark:bg-white dark:text-slate-950 dark:hover:bg-cyan-400 shadow-xl">
                    <span x-text="saving ? 'INITIALIZING...' : 'FINAL_INITIALIZATION'"></span>
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </button>
            </div>
        </form>
    </div>

    <p class="text-center text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
        Unit leads and additional parameters can be configured post-deployment.
    </p>
</div>

<script>
    let rowIndex = {{ old('departments') ? count(old('departments')) : 1 }};

    function buildRow(index, name = '', code = '') {
        return `
        <div class="dept-row flex items-center gap-4 animate-fade-in group/row" data-index="${index}">
            <div class="flex-1 grid grid-cols-4 gap-4">
                <div class="col-span-3">
                    <input type="text"
                        name="departments[${index}][name]"
                        value="${escHtml(name)}"
                        placeholder="Unit Designation (e.g. CORE_ENGINEERING)"
                        class="w-full rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-900 transition-all focus:border-cyan-400 focus:bg-white focus:ring-0 dark:border-slate-800 dark:bg-slate-950 dark:text-white"
                        required>
                </div>
                <div>
                    <input type="text"
                        name="departments[${index}][code]"
                        value="${escHtml(code)}"
                        placeholder="CODE"
                        maxlength="10"
                        class="w-full rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 text-sm font-black uppercase tracking-widest text-slate-900 transition-all focus:border-cyan-400 focus:bg-white focus:ring-0 dark:border-slate-800 dark:bg-slate-950 dark:text-white"
                        required>
                </div>
            </div>
            <button type="button" onclick="removeRow(this)"
                class="shrink-0 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-400 transition-all hover:bg-rose-500 hover:text-white dark:bg-slate-950 dark:hover:bg-rose-500 shadow-sm opacity-0 group-hover/row:opacity-100">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>`;
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function addRow() {
        const container = document.getElementById('dept-rows');
        container.insertAdjacentHTML('beforeend', buildRow(rowIndex));
        rowIndex++;
        const rows = container.querySelectorAll('.dept-row');
        const lastRow = rows[rows.length - 1];
        lastRow.querySelector('input[type="text"]').focus();
    }

    function addSuggestion(btn) {
        const name = btn.dataset.name;
        const code = btn.dataset.code;
        const existingCodes = Array.from(document.querySelectorAll('input[name$="[code]"]')).map(i => i.value.toUpperCase().trim());

        if (existingCodes.includes(code.toUpperCase())) {
            btn.classList.add('opacity-40', 'cursor-not-allowed');
            btn.disabled = true;
            return;
        }

        const emptyNameInputs = Array.from(document.querySelectorAll('input[name$="[name]"]')).filter(i => i.value.trim() === '');

        if (emptyNameInputs.length > 0) {
            const row = emptyNameInputs[0].closest('.dept-row');
            emptyNameInputs[0].value = name;
            const codeInput = row.querySelector('input[name$="[code]"]');
            if (codeInput) codeInput.value = code;
        } else {
            const container = document.getElementById('dept-rows');
            container.insertAdjacentHTML('beforeend', buildRow(rowIndex, name, code));
            rowIndex++;
        }

        btn.classList.add('opacity-40', 'line-through', 'cursor-not-allowed');
        btn.disabled = true;
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.dept-row');
        if (rows.length <= 1) return;
        btn.closest('.dept-row').remove();
    }

    // Initialize first row
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('dept-rows');
        if (container.children.length === 0) {
            container.insertAdjacentHTML('beforeend', buildRow(0));
        }
    });
</script>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
</style>
@endsection
