@extends('hrms.layouts.app')

@section('title', 'Set Up Departments — Step 2 of 2')

@section('content')
<div class="mx-auto max-w-3xl">

    {{-- ── Stepper ─────────────────────────────────────────────── --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Almost there! 🏢</h1>
        <p class="mt-1 text-slate-500 dark:text-slate-400">Add the departments in your company. You can always add more later.</p>

        <div class="mt-6 flex items-center gap-0">
            {{-- Step 1 Done --}}
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white">
                    {{-- checkmark --}}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span class="text-sm font-medium text-emerald-500">Company Info</span>
            </div>
            <div class="mx-4 h-px flex-1 bg-cyan-400"></div>
            {{-- Step 2 Active --}}
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-500 text-sm font-bold text-slate-900">2</div>
                <span class="text-sm font-semibold text-cyan-500">Departments</span>
            </div>
        </div>
    </div>

    {{-- ── Common department suggestions ─────────────────────── --}}
    <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50 p-4">
        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Quick-add common departments</p>
        <div class="flex flex-wrap gap-2" id="suggestions">
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
                    class="suggestion-chip rounded-full border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1 text-xs font-medium text-slate-700 dark:text-slate-300 transition hover:border-cyan-400 hover:bg-cyan-50 dark:hover:bg-cyan-950 hover:text-cyan-700 dark:hover:text-cyan-300 cursor-pointer select-none">
                    + {{ $s['name'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── Form ────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Your Departments</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Add at least one department to continue.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('onboarding.departments.store') }}" id="dept-form" class="p-6 space-y-4">
            @csrf

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-950 dark:border-red-800 px-4 py-3 text-sm text-red-600 dark:text-red-400">
                    Please fix the errors below.
                </div>
            @endif

            {{-- Department rows container --}}
            <div id="dept-rows" class="space-y-3">

                {{-- Restore on validation failure --}}
                @if(old('departments'))
                    @foreach(old('departments') as $i => $dept)
                        <div class="dept-row flex items-center gap-3" data-index="{{ $i }}">
                            <div class="flex-1 grid grid-cols-3 gap-2">
                                <div class="col-span-2">
                                    <input type="text"
                                        name="departments[{{ $i }}][name]"
                                        value="{{ $dept['name'] ?? '' }}"
                                        placeholder="Department Name"
                                        class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white @error('departments.'.$i.'.name') border-red-500 @enderror"
                                        required>
                                    @error("departments.$i.name")
                                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <input type="text"
                                        name="departments[{{ $i }}][code]"
                                        value="{{ $dept['code'] ?? '' }}"
                                        placeholder="Code"
                                        maxlength="10"
                                        class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm font-mono uppercase transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white @error('departments.'.$i.'.code') border-red-500 @enderror"
                                        required>
                                    @error("departments.$i.code")
                                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <button type="button" onclick="removeRow(this)"
                                class="shrink-0 rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-950">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endforeach
                @else
                    {{-- Default: one empty row --}}
                    <div class="dept-row flex items-center gap-3" data-index="0">
                        <div class="flex-1 grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <input type="text"
                                    name="departments[0][name]"
                                    placeholder="Department Name"
                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                                    required>
                            </div>
                            <div>
                                <input type="text"
                                    name="departments[0][code]"
                                    placeholder="Code"
                                    maxlength="10"
                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm font-mono uppercase transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                                    required>
                            </div>
                        </div>
                        <button type="button" onclick="removeRow(this)"
                            class="shrink-0 rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-950">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

            </div>

            {{-- Column headers (hidden but provides context) --}}
            <div class="flex items-center gap-3 text-xs text-slate-400 dark:text-slate-500 -mt-1 px-0">
                <div class="flex-1 grid grid-cols-3 gap-2">
                    <span class="col-span-2 pl-1">Department Name</span>
                    <span class="pl-1">Short Code</span>
                </div>
                <div class="w-8"></div>
            </div>

            {{-- Add row button --}}
            <button type="button" onclick="addRow()"
                class="flex items-center gap-2 rounded-lg border border-dashed border-slate-300 dark:border-slate-700 px-4 py-2 text-sm text-slate-500 dark:text-slate-400 transition hover:border-cyan-400 hover:text-cyan-500 w-full justify-center">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add another department
            </button>

            {{-- Footer --}}
            <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-800 pt-5">
                <a href="{{ route('onboarding.show') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition">
                    ← Back to Step 1
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-6 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    🎉 Complete Setup
                </button>
            </div>
        </form>
    </div>

    <p class="mt-4 text-center text-xs text-slate-400 dark:text-slate-500">
        You can add more departments, edit names, and assign leads later from the Departments section.
    </p>
</div>

<script>
    let rowIndex = {{ old('departments') ? count(old('departments')) : 1 }};

    function buildRow(index, name = '', code = '') {
        return `
        <div class="dept-row flex items-center gap-3 animate-fade-in" data-index="${index}">
            <div class="flex-1 grid grid-cols-3 gap-2">
                <div class="col-span-2">
                    <input type="text"
                        name="departments[${index}][name]"
                        value="${escHtml(name)}"
                        placeholder="Department Name"
                        class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        required>
                </div>
                <div>
                    <input type="text"
                        name="departments[${index}][code]"
                        value="${escHtml(code)}"
                        placeholder="Code"
                        maxlength="10"
                        class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm font-mono uppercase transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        required>
                </div>
            </div>
            <button type="button" onclick="removeRow(this)"
                class="shrink-0 rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-950">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
        // Focus the name field of the newly added row
        const rows = container.querySelectorAll('.dept-row');
        const lastRow = rows[rows.length - 1];
        lastRow.querySelector('input[type="text"]').focus();
    }

    function addSuggestion(btn) {
        const name = btn.dataset.name;
        const code = btn.dataset.code;

        // Check if this code already exists in the form
        const existingCodes = Array.from(document.querySelectorAll('input[name$="[code]"]'))
            .map(i => i.value.toUpperCase().trim());

        if (existingCodes.includes(code.toUpperCase())) {
            // Flash the chip to signal it's already added
            btn.classList.add('opacity-40', 'cursor-not-allowed');
            btn.disabled = true;
            return;
        }

        // Check if there's an empty row to fill
        const emptyNameInputs = Array.from(document.querySelectorAll('input[name$="[name]"]'))
            .filter(i => i.value.trim() === '');

        if (emptyNameInputs.length > 0) {
            const row = emptyNameInputs[0].closest('.dept-row');
            emptyNameInputs[0].value = name;
            const codeInput = row.querySelector('input[name$="[code]"]');
            if (codeInput) codeInput.value = code;
        } else {
            // Add a new row with the suggestion pre-filled
            const container = document.getElementById('dept-rows');
            container.insertAdjacentHTML('beforeend', buildRow(rowIndex, name, code));
            rowIndex++;
        }

        // Visually mark the chip as used
        btn.classList.add('opacity-40', 'line-through', 'cursor-not-allowed');
        btn.disabled = true;
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.dept-row');
        // Always keep at least one row
        if (rows.length <= 1) return;
        btn.closest('.dept-row').remove();
    }
</script>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.18s ease; }
    input[type="text"]:not([name$="[code]"]) + div ~ div input { text-transform: uppercase; }
</style>
@endsection
