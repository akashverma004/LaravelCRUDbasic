<div x-data="{ sidebarOpen: false }">
    <header class="lg:hidden sticky top-0 z-40 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur dark:border-slate-700 dark:bg-slate-900/95">
        <div class="flex items-center justify-between">
            <button @click="sidebarOpen = true" class="rounded-lg border border-slate-300 p-2 text-slate-700 dark:border-slate-600 dark:text-slate-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-400 to-blue-500">
                    <span class="text-lg font-bold text-white">P</span>
                </div>
                <span class="text-base font-bold text-slate-900 dark:text-white">PeopleFlow</span>
            </a>

            <button id="theme-toggle" type="button" class="rounded-lg border border-slate-300 p-2 text-slate-700 dark:border-slate-600 dark:text-slate-200">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
            </button>
        </div>
    </header>

    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/60 lg:hidden" style="display: none;" @click="sidebarOpen = false"></div>

    <aside
        class="fixed inset-y-0 left-0 z-50 w-72 transform border-r border-slate-200 bg-gradient-to-b from-white to-slate-100 shadow-2xl transition-transform duration-200 dark:border-slate-700 dark:from-slate-900 dark:to-slate-950"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    >
        <div class="flex h-full flex-col">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-400 to-blue-500">
                        <span class="text-xl font-bold text-white">P</span>
                    </div>
                    <div>
                        <p class="text-base font-bold text-slate-900 dark:text-white">PeopleFlow</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">HRMS Navigation</p>
                    </div>
                </a>

                <button @click="sidebarOpen = false" class="rounded-lg p-2 text-slate-600 dark:text-slate-300 lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300' : 'text-slate-700 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('org-chart.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('org-chart.*') ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300' : 'text-slate-700 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 21h14a1 1 0 001-1v-9H4v9a1 1 0 001 1z"></path></svg>
                    <span>Organization</span>
                </a>

                <a href="{{ route('employees.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('employees.*') ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300' : 'text-slate-700 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H11a4 4 0 00-4 4v2m10 0H7m9-10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Employees</span>
                </a>

                <a href="{{ route('departments.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('departments.*') ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300' : 'text-slate-700 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M6 18V8m4 10V4m4 14v-6m4 6v-9"></path></svg>
                    <span>Departments</span>
                </a>

                <a href="{{ route('leaves.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('leaves.*') ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300' : 'text-slate-700 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 8h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Leave Requests</span>
                </a>

                @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                    <a href="{{ route('policies.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('policies.*') ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300' : 'text-slate-700 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3a1.5 1.5 0 00-1.5 1.5v.443a6.015 6.015 0 00-1.447.6l-.314-.314a1.5 1.5 0 00-2.122 0L3.23 6.366a1.5 1.5 0 000 2.122l.314.314a6.014 6.014 0 00-.6 1.447H2.5A1.5 1.5 0 001 11.75v1.5A1.5 1.5 0 002.5 14.75h.443c.14.512.34.997.6 1.447l-.314.314a1.5 1.5 0 000 2.122l1.136 1.136a1.5 1.5 0 002.122 0l.314-.314c.45.26.935.46 1.447.6v.443a1.5 1.5 0 001.5 1.5h1.5a1.5 1.5 0 001.5-1.5v-.443c.512-.14.997-.34 1.447-.6l.314.314a1.5 1.5 0 002.122 0l1.136-1.136a1.5 1.5 0 000-2.122l-.314-.314c.26-.45.46-.935.6-1.447h.443a1.5 1.5 0 001.5-1.5v-1.5a1.5 1.5 0 00-1.5-1.5h-.443a6.014 6.014 0 00-.6-1.447l.314-.314a1.5 1.5 0 000-2.122l-1.136-1.136a1.5 1.5 0 00-2.122 0l-.314.314a6.015 6.015 0 00-1.447-.6V4.5a1.5 1.5 0 00-1.5-1.5h-1.5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 100-6 3 3 0 000 6z"></path></svg>
                        <span>Policies</span>
                    </a>

                    <a href="{{ route('tenant-users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('tenant-users.*') ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300' : 'text-slate-700 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9a3 3 0 11-6 0 3 3 0 016 0zM5 13a4 4 0 018 0v5H5v-5zm10 8h6v-1a4 4 0 00-4-4h-2"></path></svg>
                        <span>Workspace Users</span>
                    </a>
                @endif

                @can('manage-tenants')
                    <a href="{{ route('tenants.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('tenants.*') ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300' : 'text-slate-700 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2H10a2 2 0 00-2 2v2m-3 0h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"></path></svg>
                        <span>Platform Tenants</span>
                    </a>
                @endcan
            </nav>

            <div class="border-t border-slate-200 p-4 dark:border-slate-700">
                <div class="mb-3 flex items-center gap-3 rounded-lg bg-slate-200/70 px-3 py-2 dark:bg-slate-800">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 text-sm font-bold text-white">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ Auth::user()->name }}</p>
                        <p class="truncate text-xs text-slate-600 dark:text-slate-400">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>Profile</span>
                    </a>

                    <button id="theme-toggle" type="button" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                        <span>Toggle Theme</span>
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-red-500 hover:bg-red-100 dark:text-red-300 dark:hover:bg-red-900/20">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>
</div>
