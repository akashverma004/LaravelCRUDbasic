<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PeopleFlow HRMS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    @include('hrms.components.navbar')

    <div class="mx-auto max-w-7xl px-6 py-8">
        @if (session('status'))
            @include('hrms.components.alert', ['type' => 'success', 'message' => session('status')])
        @endif

        @if ($errors->any())
            @include('hrms.components.alert', ['type' => 'error', 'message' => 'Please fix the errors below.'])
        @endif

        @yield('content')
    </div>

    @include('hrms.components.footer')
</body>
</html>
