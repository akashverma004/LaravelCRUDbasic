@if ($type === 'success')
    <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-200">
        {{ $message }}
    </div>
@elseif ($type === 'error')
    <div class="mb-6 rounded-xl border border-red-400/30 bg-red-400/10 px-4 py-3 text-red-200">
        {{ $message }}
    </div>
@endif
