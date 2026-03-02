<div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
    <h2 class="mb-4 text-lg font-semibold">Quick Add Department</h2>
    <form method="POST" action="{{ route('hrms.departments.store') }}" class="space-y-3">
        @csrf
        <input name="name" placeholder="Department name" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
        @error('name')
            <p class="text-sm text-red-400">{{ $message }}</p>
        @enderror

        <input name="code" placeholder="Code (e.g. ENG)" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm @error('code') border-red-500 @enderror" value="{{ old('code') }}" required>
        @error('code')
            <p class="text-sm text-red-400">{{ $message }}</p>
        @enderror

        <input name="lead_name" placeholder="Department lead" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm @error('lead_name') border-red-500 @enderror" value="{{ old('lead_name') }}" required>
        @error('lead_name')
            <p class="text-sm text-red-400">{{ $message }}</p>
        @enderror

        <button class="w-full rounded-lg bg-cyan-500 py-2 font-semibold text-slate-900 transition hover:bg-cyan-400">Add Department</button>
    </form>
</div>
