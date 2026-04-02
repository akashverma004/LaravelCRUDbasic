<div x-data="{ sidebarOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 16">
    {{-- Mobile Top Bar --}}
    <header :class="scrolled
            ? 'mx-3 mt-3 rounded-[20px] border-sky-100/80 bg-sky-50/88 shadow-[0_18px_45px_rgba(59,130,246,0.12)] dark:border-sky-500/20 dark:bg-slate-950/88'
            : 'mx-0 mt-0 rounded-none border-x-0 border-t-0 border-b border-slate-200/80 bg-white/95 shadow-sm dark:border-white/10 dark:bg-slate-900/88'"
        class="lg:hidden fixed left-0 right-0 top-0 z-40 border px-3 py-2.5 backdrop-blur-2xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <button @click="sidebarOpen = true" class="rounded-lg border border-sky-100 bg-sky-50 p-2 text-sky-700 transition-all hover:bg-sky-100 dark:border-white/10 dark:bg-slate-900/80 dark:text-slate-200 dark:hover:bg-slate-800/90">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>

            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-[14px] bg-gradient-to-br from-sky-500 via-violet-500 to-indigo-600 shadow-lg shadow-violet-500/20">
                    <span class="text-base font-black tracking-tight text-white">PF</span>
                </div>
                <span class="text-[1.2rem] font-black tracking-tight text-slate-900 dark:text-white leading-none">PeopleFlow</span>
            </a>

            <div class="flex items-center gap-2">
                @auth
                <div x-data="notificationBell()" x-init="init()" class="relative">
                    <button @click="toggleDropdown()" class="relative rounded-lg border border-sky-100 bg-sky-50 p-2 text-sky-700 transition-all hover:bg-sky-100 dark:border-white/10 dark:bg-slate-900/80 dark:text-slate-200 dark:hover:bg-slate-800/90">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        <span x-show="unreadCount > 0" class="absolute -right-1 -top-1 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-cyan-500 text-[9px] font-bold text-white" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                    </button>
                    <div x-show="isOpen" x-transition @click.outside="isOpen = false" class="absolute right-0 top-full mt-2 w-72 rounded-xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-slate-900 z-50 overflow-hidden" style="display:none;">
                        @include('hrms.components.notification-dropdown-content')
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <div class="h-24 lg:hidden"></div>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm lg:hidden" style="display: none;" @click="sidebarOpen = false"></div>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 w-52 transform border-r border-sky-100 bg-gradient-to-b from-white via-sky-50/60 to-white shadow-xl transition-all duration-300 ease-in-out dark:border-sky-500/10 dark:bg-gradient-to-b dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-full flex-col">
            {{-- Logo --}}
            <div class="flex items-center justify-between px-5 py-6 border-b border-sky-100 dark:border-white/5">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-4 group">
                    <div class="flex h-9 w-9 items-center justify-center rounded-[14px] bg-gradient-to-br from-sky-500 via-violet-500 to-indigo-600 shadow-lg shadow-violet-500/20 transition-all group-hover:scale-105">
                        <span class="text-sm font-black text-white leading-none tracking-tight">PF</span>
                    </div>
                    <div class="transition-all group-hover:translate-x-1">
                        <p class="text-[1.15rem] font-black text-slate-900 dark:text-white leading-none tracking-tight">PeopleFlow</p>
                        @auth
                            @php $tenantName = Auth::user()->tenant?->name; @endphp
                            @if($tenantName)
                                <p class="mt-1.5 text-[8px] font-black text-slate-400 dark:text-slate-500 truncate max-w-[120px] uppercase tracking-[0.2em]">{{ $tenantName }}</p>
                            @endif
                        @endauth
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="rounded-lg p-1 text-slate-400 hover:text-slate-600 dark:hover:text-white lg:hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-3 custom-scrollbar">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z'],
                        ['route' => 'departments.index', 'label' => 'Departments', 'icon' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-3.75h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z'],
                        ['route' => 'leaves.my', 'label' => 'Leaves', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
                        ['route' => 'payroll.index', 'label' => 'Payroll', 'icon' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['route' => 'attendance.my', 'label' => 'Attendance', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['route' => 'shifts.index', 'label' => 'Shifts', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
                    ];
                    $navActiveClass = 'border-slate-200/50 bg-white/80 text-cyan-600 shadow-sm dark:border-white/10 dark:bg-white/5 dark:text-cyan-400 ring-1 ring-slate-900/5 dark:ring-white/5';
                    $navIdleClass = 'border-transparent text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white';
                @endphp

                <p class="px-3 py-2 text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Main</p>
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}" wire:navigate
                        class="mb-0.5 flex items-center gap-2.5 overflow-hidden rounded-xl border px-3 py-1.5 transition-all duration-200 group {{ request()->routeIs($item['route'].'*') ? $navActiveClass : $navIdleClass }}">
                        <svg class="h-3.5 w-3.5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                        <span class="text-[9px] font-black uppercase tracking-widest">{{ $item['label'] }}</span>
                    </a>
                @endforeach

                <p class="mt-4 px-3 py-2 text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Management</p>
                @php
                    $govItems = [
                        ['route' => 'employees.index', 'label' => 'Employees', 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                        ['route' => (Auth::user()->hasAnyRole(['admin', 'hr_manager']) ? 'policies.index' : 'policies.viewer'), 'label' => 'Policies', 'icon' => 'M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 0h10.5A2.25 2.25 0 0119.5 12.75v6A2.25 2.25 0 0117.25 21h-10.5A2.25 2.25 0 014.5 18.75v-6A2.25 2.25 0 016.75 10.5z'],
                        ['route' => 'assets.index', 'label' => 'Assets', 'icon' => 'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25'],
                        ['route' => 'workflows.index', 'label' => 'Workflows', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                        ['route' => 'settings.index', 'label' => 'System', 'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z'],
                    ];
                @endphp
                @foreach ($govItems as $item)
                    <a href="{{ route($item['route']) }}" wire:navigate
                        class="mb-0.5 flex items-center gap-2.5 overflow-hidden rounded-xl border px-3 py-1.5 transition-all duration-200 group {{ request()->routeIs($item['route'].'*') ? $navActiveClass : $navIdleClass }}">
                        <svg class="h-3.5 w-3.5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                        <span class="text-[9px] font-black uppercase tracking-widest">{{ $item['label'] }}</span>
                    </a>
                @endforeach
                <a href="{{ route('documents.index') }}" wire:navigate class="mb-1 flex items-center gap-2.5 overflow-hidden rounded-xl border px-3 py-2 transition-all duration-200 group {{ request()->routeIs('documents.*') ? $navActiveClass : $navIdleClass }}">
                    <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.25 11.25L10.5 15.75m9.75-4.875c0 5.591-4.409 10.125-10 10.125a10.02 10.02 0 01-5.63-1.688l-3.396.947a.75.75 0 01-.947-.947l.947-3.396A10.02 10.02 0 012.25 12c0-5.591 4.409-10 10-10a10.02 10.02 0 015.63 1.688l3.396-.947a.75.75 0 01.947-.947l-.947 3.396c1.303 1.258 2.103 3.018 2.103 4.965z" /></svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">Documents</span>
                </a>

                @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                    <p class="mt-6 px-3 py-3 text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">Administration</p>
                    <a href="{{ route('analytics.index') }}" wire:navigate class="mb-1 flex items-center gap-2.5 overflow-hidden rounded-xl border px-3 py-2 transition-all duration-200 group {{ request()->routeIs('analytics.*') ? $navActiveClass : $navIdleClass }}">
                        <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Analytics</span>
                    </a>
                    <a href="{{ route('attendance.index') }}" wire:navigate class="mb-1 flex items-center gap-2.5 overflow-hidden rounded-xl border px-3 py-2 transition-all duration-200 group {{ request()->routeIs('attendance.index') ? $navActiveClass : $navIdleClass }}">
                        <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Logs</span>
                    </a>
                    <a href="{{ route('tenant-users.index') }}" wire:navigate class="mb-1 flex items-center gap-2.5 overflow-hidden rounded-xl border px-3 py-2 transition-all duration-200 group {{ request()->routeIs('tenant-users.*') || request()->routeIs('roles.*') || request()->routeIs('users.*') ? $navActiveClass : $navIdleClass }}">
                        <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Users</span>
                    </a>
                    <a href="{{ route('audit.index') }}" wire:navigate class="mb-1 flex items-center gap-2.5 overflow-hidden rounded-xl border px-3 py-2 transition-all duration-200 group {{ request()->routeIs('audit.*') ? $navActiveClass : $navIdleClass }}">
                        <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.744c0 1.5.346 2.919.969 4.183a11.997 11.997 0 007.031 6.471l.032.012.032-.012a11.998 11.998 0 007.031-6.471c.623-1.264.969-2.683.969-4.183 0-1.29-.204-2.532-.581-3.688A11.959 11.959 0 0112 2.714z" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Audit</span>
                    </a>
                     @if (Auth::user()->hasRole('super_admin'))
                        <a href="{{ route('tenants.index') }}" wire:navigate class="mb-1 flex items-center gap-2.5 overflow-hidden rounded-xl border px-3 py-2 transition-all duration-200 group {{ request()->routeIs('tenants.*') ? $navActiveClass : $navIdleClass }}">
                            <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-3.75h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
                            <span class="text-[10px] font-black uppercase tracking-widest">Workspaces</span>
                        </a>
                     @endif
                @endif
            </nav>

            {{-- User Profile & Logout --}}
            <div class="mt-auto p-4 border-t border-slate-100 dark:border-white/5">
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-2.5 border border-slate-100 transition-colors hover:bg-slate-100 dark:bg-white/5 dark:border-white/5 dark:hover:bg-white/10">
                    <div class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-sm dark:bg-slate-900 dark:border-white/10 dark:text-white overflow-hidden">
                        @if(Auth::user()->profile_photo_url)
                            <img src="{{ Auth::user()->profile_photo_url }}" class="h-full w-full object-cover">
                        @else
                            {{ substr(Auth::user()->name, 0, 1) }}
                        @endif
                        <div class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-500 dark:border-slate-900 z-10"></div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('self-service.profile') }}" wire:navigate class="block truncate text-xs font-bold text-slate-900 transition-colors hover:text-cyan-600 dark:text-white dark:hover:text-cyan-400">
                            {{ Auth::user()->name }}
                        </a>
                        <button type="button" @click="$refs.logoutForm.submit()" class="text-[10px] font-semibold text-slate-500 hover:text-rose-500 dark:text-slate-400 dark:hover:text-rose-400 transition-colors">
                            Sign out
                        </button>
                    </div>
                    <form x-ref="logoutForm" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
                </div>
            </div>
        </div>
    </aside>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.3); border-radius: 4px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
</style>
