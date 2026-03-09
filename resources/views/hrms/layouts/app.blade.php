<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PeopleFlow HRMS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Apply theme before rendering to avoid flash
        const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="min-h-screen font-sans antialiased transition-colors duration-300 dark:bg-gradient-to-br dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 dark:text-slate-100 bg-gradient-to-br from-slate-50 via-slate-100 to-slate-50 text-slate-900">
    @include('hrms.components.navbar')

    <main class="flex min-h-screen flex-col lg:pl-72">
        {{-- Desktop Top-Right Utility Bar --}}
        <div class="hidden lg:block sticky top-0 z-30 border-b border-slate-200/60 bg-white/80 backdrop-blur-lg dark:border-slate-700/60 dark:bg-slate-900/80">
            <div class="mx-auto flex max-w-7xl items-center justify-end gap-2 px-4 py-2.5 sm:px-6 lg:px-8">
                {{-- Notification Bell --}}
                @auth
                <div x-data="notificationBell()" x-init="init()" class="relative">
                    <button @click="toggleDropdown()" class="relative rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount" class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white" style="display:none;"></span>
                    </button>
                    <div
                        x-show="isOpen" x-transition @click.outside="isOpen = false"
                        class="absolute right-0 top-full mt-2 w-80 rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-800 z-[60] max-h-[28rem] flex flex-col"
                        style="display:none;"
                    >
                        @include('hrms.components.notification-dropdown-content')
                    </div>
                </div>
                @endauth

                {{-- Theme Toggle --}}
                <button id="theme-toggle-topbar" type="button" class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                    <svg class="h-5 w-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path></svg>
                    <svg class="h-5 w-5 block dark:hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                </button>
            </div>
        </div>

        <div class="mx-auto w-full max-w-7xl flex-grow px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            @if (session('status'))
                @include('hrms.components.alert', ['type' => 'success', 'message' => session('status')])
            @endif

            @if ($errors->any())
                @include('hrms.components.alert', ['type' => 'error', 'message' => 'Please fix the errors below.'])
            @endif

            @yield('content')
        </div>

        @include('hrms.components.footer')
    </main>
</body>
</html>
