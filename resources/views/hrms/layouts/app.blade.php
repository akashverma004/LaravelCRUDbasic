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

    <main class="min-h-screen lg:pl-72">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
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
