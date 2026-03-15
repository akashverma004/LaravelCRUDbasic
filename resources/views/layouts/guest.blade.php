<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'PeopleFlow HRMS') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme.js'])

        <script>
            // Apply theme before rendering to avoid flash
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        </script>
        <style>
            body { font-family: 'Inter', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="antialiased bg-slate-50 text-slate-900 min-h-screen selection:bg-violet-500 selection:text-white relative overflow-x-hidden">
        {{-- Flare-inspired Mesh Gradient Background --}}
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-violet-200/50 blur-[120px] animate-pulse"></div>
            <div class="absolute top-[20%] -right-[5%] w-[35%] h-[35%] rounded-full bg-blue-200/40 blur-[100px] animate-pulse" style="animation-delay: 2s"></div>
            <div class="absolute -bottom-[10%] left-[15%] w-[45%] h-[45%] rounded-full bg-emerald-100/40 blur-[130px] animate-pulse" style="animation-delay: 4s"></div>
            <div class="absolute top-[40%] left-[30%] w-[30%] h-[30%] rounded-full bg-purple-100/30 blur-[110px] animate-pulse" style="animation-delay: 1s"></div>
            {{-- Subtle Noise Overlay --}}
            <div class="absolute inset-0 opacity-[0.03] mix-blend-overlay pointer-events-none bg-[url('https://grainy-gradients.vercel.app/noise.svg')]"></div>
        </div>

        {{ $slot }}
    </body>
</html>
