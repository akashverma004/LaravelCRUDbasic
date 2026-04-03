<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PeopleFlow HRMS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script>
        // Apply theme before rendering to avoid flash
        const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(34, 211, 238, 0.3); }
    </style>
</head>
<body class="min-h-screen antialiased transition-colors duration-500 selection:bg-cyan-500 selection:text-white dark:bg-slate-950 dark:text-slate-100 bg-slate-50 text-slate-900 overflow-x-hidden">
    @include('hrms.components.navbar')

    <main class="flex min-h-screen flex-col overflow-x-hidden max-w-full lg:pl-52">
        {{-- Topbar --}}
        <div x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 16" class="fixed top-0 left-52 right-0 z-40 hidden lg:block">
            <div :class="scrolled
                    ? 'mx-3 mt-3 rounded-[22px] border-white/50 bg-white/72 px-5 shadow-[0_18px_42px_rgba(15,23,42,0.10)] dark:border-white/10 dark:bg-slate-950/70'
                    : 'mx-0 mt-0 rounded-none border-x-0 border-t-0 border-b border-slate-200/80 bg-white/92 px-6 shadow-sm dark:border-white/5 dark:bg-slate-950/82'"
                class="flex items-center justify-end py-2.5 backdrop-blur-2xl transition-all duration-300">
                <div class="flex items-center gap-4">
                {{-- Quick Access --}}
                <div class="flex items-center border-r border-slate-200 pr-4 mr-1 dark:border-white/5">
                    <a href="{{ route('org-chart.index') }}" wire:navigate class="group flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 transition-all hover:bg-slate-100 dark:border-white/5 dark:bg-white/5 dark:hover:bg-white/10" title="Org Chart">
                        <svg class="h-4 w-4 text-slate-500 group-hover:text-cyan-500 transition-colors dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                    </a>
                </div>

                {{-- Search --}}
                <button @click.stop="$dispatch('open-command-palette')" class="group flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 transition-all hover:bg-slate-100 dark:border-white/5 dark:bg-white/5 dark:hover:bg-white/10">
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-cyan-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span class="text-xs font-medium text-slate-500 group-hover:text-slate-700 dark:text-slate-400 dark:group-hover:text-white">Search...</span>
                    <div class="flex items-center gap-1 rounded bg-white border border-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                        <span>⌘</span><span>K</span>
                    </div>
                </button>

                <div class="flex items-center gap-2">
                    {{-- Notifications --}}
                    @auth
                    <div x-data="notificationBell()" x-init="init()" class="relative">
                        <button @click="toggleDropdown()" class="group relative flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 transition-all hover:bg-slate-100 dark:border-white/5 dark:bg-white/5 dark:hover:bg-white/10">
                            <svg class="h-4 w-4 text-slate-500 group-hover:text-cyan-500 transition-colors dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                            <span x-show="unreadCount > 0" class="absolute right-2 top-2 flex h-2 w-2 items-center justify-center">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-cyan-500 opacity-75"></span>
                                <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                            </span>
                        </button>
                        <div
                            x-show="isOpen" 
                            x-transition
                            @click.outside="isOpen = false"
                            class="absolute right-0 top-full mt-3 w-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-slate-900 z-50 flex flex-col"
                            style="display:none;"
                        >
                            @include('hrms.components.notification-dropdown-content')
                        </div>
                    </div>
                    @endauth

                    {{-- Theme Toggle --}}
                    <button id="theme-toggle-topbar" type="button" class="group flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 transition-all hover:bg-slate-100 dark:border-white/5 dark:bg-white/5 dark:hover:bg-white/10">
                        <svg class="h-4 w-4 hidden dark:block text-slate-400 group-hover:text-amber-400 transition-colors" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path></svg>
                        <svg class="h-4 w-4 block dark:hidden text-slate-500 group-hover:text-cyan-500 transition-colors" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    </button>
                </div>
                </div>
            </div>
        </div>
        <livewire:shared.command-palette />
        <div class="relative w-full flex-grow overflow-hidden px-4 pb-8 pt-24 lg:px-5">
            {{-- Background Decoration --}}
            <div class="absolute -left-20 top-20 -z-10 h-80 w-80 rounded-full bg-cyan-500/5 blur-[100px] pointer-events-none"></div>
            <div class="absolute -right-20 bottom-20 -z-10 h-80 w-80 rounded-full bg-indigo-500/5 blur-[100px] pointer-events-none"></div>

            @if (session('status'))
                @include('hrms.components.alert', ['type' => 'success', 'message' => session('status')])
            @endif

            @if ($errors->any())
                @include('hrms.components.alert', ['type' => 'error', 'message' => 'Please fix the highlighted issues.'])
            @endif

            @yield('content')
            {{ $slot ?? '' }}
        </div>

        @include('hrms.components.footer')
    </main>

    @livewireScriptConfig
    @stack('scripts')
</body>
</html>
