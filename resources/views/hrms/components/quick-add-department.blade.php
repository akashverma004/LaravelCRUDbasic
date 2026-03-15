<div class="rounded-[2.5rem] border border-slate-200 bg-white p-10 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
    <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-8">Quick Forge <span class="text-cyan-500 ml-2">DIV_INIT</span></h2>
    <form method="POST" action="{{ route('hrms.departments.store') }}" class="space-y-6">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Division Label</label>
                <input name="name" placeholder="e.g. CORE_ENGINEERING" class="w-full rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-900 focus:border-cyan-400 focus:bg-white focus:ring-0 dark:border-slate-800 dark:bg-slate-950 dark:text-white transition-all @error('name') border-rose-500 @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <p class="mt-2 text-[8px] font-black text-rose-500 uppercase tracking-widest ml-4">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Protocol ID</label>
                <input name="code" placeholder="e.g. ENG_01" class="w-full rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 text-sm font-black uppercase text-slate-900 dark:border-slate-800 dark:bg-slate-950 dark:text-white transition-all @error('code') border-rose-500 @enderror" value="{{ old('code') }}" required>
                @error('code')
                    <p class="mt-2 text-[8px] font-black text-rose-500 uppercase tracking-widest ml-4">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 mb-2">Command Lead</label>
                <input name="lead_name" placeholder="e.g. SARAH_CONNOR" class="w-full rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-900 dark:border-slate-800 dark:bg-slate-950 dark:text-white transition-all @error('lead_name') border-rose-500 @enderror" value="{{ old('lead_name') }}" required>
                @error('lead_name')
                    <p class="mt-2 text-[8px] font-black text-rose-500 uppercase tracking-widest ml-4">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button class="w-full rounded-2xl bg-cyan-400 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-950 shadow-xl shadow-cyan-400/20 transition-all hover:bg-cyan-300 hover:-translate-y-1">
            Initialize Division
        </button>
    </form>
</div>
