<div x-data="{ sidebarOpen: false }">
    {{-- Mobile Top Bar --}}
    <header class="lg:hidden sticky top-0 z-40 border-b border-slate-200 bg-white/80 px-4 py-3 backdrop-blur-xl shadow-sm dark:border-white/10 dark:bg-slate-900/80">
        <div class="flex items-center justify-between">
            <button @click="sidebarOpen = true" class="rounded-lg border border-slate-200 bg-slate-50 p-2 text-slate-600 transition-all hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-white dark:hover:bg-white/10">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-500 to-indigo-500 shadow-sm">
                    <span class="text-base font-bold text-white">PF</span>
                </div>
                <span class="text-lg font-bold text-slate-900 dark:text-white">PeopleFlow</span>
            </a>

            <div class="flex items-center gap-2">
                @auth
                <div x-data="notificationBell()" x-init="init()" class="relative">
                    <button @click="toggleDropdown()" class="relative rounded-lg border border-slate-200 bg-slate-50 p-2 text-slate-600 transition-all hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-white dark:hover:bg-white/10">
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

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm lg:hidden" style="display: none;" @click="sidebarOpen = false"></div>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 w-64 transform bg-white border-r border-slate-200 shadow-xl transition-all duration-300 ease-in-out dark:bg-slate-950 dark:border-white/10 lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-full flex-col">
            {{-- Logo --}}
            <div class="flex items-center justify-between px-6 py-8 border-b border-slate-100 dark:border-white/5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 group">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 border border-white/10 shadow-2xl transition-all group-hover:scale-110 dark:bg-white/5">
                        <span class="text-xs font-black text-white leading-none">PF</span>
                    </div>
                    <div class="transition-all group-hover:translate-x-1">
                        <p class="text-sm font-black text-slate-900 dark:text-white leading-none uppercase tracking-tighter">People<span class="text-cyan-500">Flow</span></p>
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
            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-4 custom-scrollbar">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z'],
                        ['route' => 'self-service.profile', 'label' => 'My Profile', 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                        ['route' => 'org-chart.index', 'label' => 'Org Chart', 'icon' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'],
                        ['route' => 'departments.index', 'label' => 'Departments', 'icon' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-3.75h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z'],
                        ['route' => 'leaves.my', 'label' => 'Leaves', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
                        ['route' => 'workflows.index', 'label' => 'Workflows', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                        ['route' => 'assets.index', 'label' => 'Assets', 'icon' => 'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25'],
                        ['route' => 'payroll.index', 'label' => 'Payroll', 'icon' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['route' => 'performance.index', 'label' => 'Performance', 'icon' => 'M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941'],
                    ];
                @endphp

                <p class="px-3 py-4 text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">Main Console</p>
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}" 
                        class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all mb-1 group {{ request()->routeIs($item['route'].'*') ? 'bg-slate-900 text-white shadow-xl dark:bg-white/10 dark:text-cyan-400' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white' }}">
                        <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">{{ $item['label'] }}</span>
                    </a>
                @endforeach

                <p class="mt-8 px-3 py-4 text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">Personnel Hub</p>
                <a href="{{ route('employees.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all mb-1 group {{ request()->routeIs('employees.*') ? 'bg-slate-900 text-white shadow-xl dark:bg-white/10 dark:text-cyan-400' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white' }}">
                    <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">Directory</span>
                </a>
                <a href="{{ route('documents.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all mb-1 group {{ request()->routeIs('documents.*') ? 'bg-slate-900 text-white shadow-xl dark:bg-white/10 dark:text-cyan-400' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white' }}">
                    <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.25 11.25L10.5 15.75m9.75-4.875c0 5.591-4.409 10.125-10 10.125a10.02 10.02 0 01-5.63-1.688l-3.396.947a.75.75 0 01-.947-.947l.947-3.396A10.02 10.02 0 012.25 12c0-5.591 4.409-10 10-10a10.02 10.02 0 015.63 1.688l3.396-.947a.75.75 0 01.947.947l-.947 3.396c1.303 1.258 2.103 3.018 2.103 4.965z" /></svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">Archives</span>
                </a>

                @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                    <p class="mt-8 px-3 py-4 text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">Control Center</p>
                    <a href="{{ route('analytics.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all mb-1 group {{ request()->routeIs('analytics.*') ? 'bg-slate-900 text-white shadow-xl dark:bg-white/10 dark:text-cyan-400' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white' }}">
                        <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Intelligence</span>
                    </a>
                    <a href="{{ route('workflows.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all mb-1 group {{ request()->routeIs('workflows.*') ? 'bg-slate-900 text-white shadow-xl dark:bg-white/10 dark:text-cyan-400' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white' }}">
                        <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Sequences</span>
                    </a>
                    <a href="{{ route('tenant-users.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all mb-1 group {{ request()->routeIs('tenant-users.*') || request()->routeIs('roles.*') || request()->routeIs('users.*') ? 'bg-slate-900 text-white shadow-xl dark:bg-white/10 dark:text-cyan-400' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white' }}">
                        <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Lattice</span>
                    </a>
                     @if (Auth::user()->hasRole('super_admin'))
                        <a href="{{ route('tenants.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all mb-1 group {{ request()->routeIs('tenants.*') ? 'bg-slate-900 text-white shadow-xl dark:bg-white/10 dark:text-cyan-400' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-3.75h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
                            <span class="text-[10px] font-black uppercase tracking-widest">Workspaces</span>
                        </a>
                     @endif
                @endif
            </nav>

            {{-- User Profile & Logout --}}
            <div class="mt-auto p-4 border-t border-slate-100 dark:border-white/5">
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-2.5 border border-slate-100 transition-colors hover:bg-slate-100 dark:bg-white/5 dark:border-white/5 dark:hover:bg-white/10">
                    <div class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-sm dark:bg-slate-900 dark:border-white/10 dark:text-white">
                        {{ substr(Auth::user()->name, 0, 1) }}
                        <div class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-500 dark:border-slate-900"></div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('self-service.profile') }}" class="block truncate text-xs font-bold text-slate-900 transition-colors hover:text-cyan-600 dark:text-white dark:hover:text-cyan-400">
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
