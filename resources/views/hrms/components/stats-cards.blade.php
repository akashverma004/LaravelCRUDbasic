<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    @foreach ($stats as $card)
        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-lg shadow-cyan-900/10">
            <p class="text-sm text-slate-400">{{ $card['label'] }}</p>
            <p class="mt-2 text-3xl font-semibold text-cyan-300">{{ $card['value'] }}</p>
        </div>
    @endforeach
</div>
