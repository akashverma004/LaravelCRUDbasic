<nav class="transition-colors duration-300 dark:bg-gradient-to-r dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 dark:border-slate-700 bg-gradient-to-r from-slate-50 via-slate-100 to-slate-50 border-slate-200 border-b shadow-lg" x-data="{ open: false }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">P</span>
                    </div>
                    <span class="font-bold text-lg dark:text-white text-slate-900 hidden sm:inline">PeopleFlow</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                    Dashboard
                </a>
                <a href="{{ route('org-chart.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                    Organization
                </a>
                <a href="{{ route('employees.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                    Employees
                </a>
                <a href="{{ route('departments.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                    Departments
                </a>
                <a href="{{ route('leaves.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                    Leave Requests
                </a>
                @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                    <a href="{{ route('policies.leave.edit') }}" class="px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                        Policies
                    </a>
                @endif
            </div>

            <!-- Right Side - Theme Toggle & User Dropdown -->
            <div class="hidden md:flex items-center gap-2">
                <!-- Theme Toggle Button -->
                <button id="theme-toggle" type="button" class="p-2 rounded-lg dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600 bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                </button>

                <div x-data="{ userDropdown: false }" class="relative">
                    <!-- User Dropdown Trigger -->
                    <button @click="userDropdown = !userDropdown" class="flex items-center gap-3 px-3 py-2 rounded-lg dark:hover:bg-slate-700 dark:text-slate-200 hover:bg-slate-200 transition-colors duration-200 text-slate-700">
                        <!-- Avatar -->
                        <div class="w-9 h-9 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <!-- User Info -->
                        <div class="text-left">
                            <div class="text-sm font-semibold dark:text-white text-slate-900">{{ Auth::user()->name }}</div>
                            <div class="text-xs dark:text-slate-400 text-slate-500">{{ Auth::user()->email }}</div>
                        </div>
                        <!-- Chevron -->
                        <svg :class="{'rotate-180': userDropdown}" class="w-4 h-4 transition-transform duration-200 dark:text-slate-300 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userDropdown" @click.outside="userDropdown = false" class="absolute right-0 mt-2 w-56 rounded-lg shadow-xl dark:bg-slate-800 dark:border-slate-700 bg-white border-slate-200 border z-50" style="display: none;">
                        <!-- User Info Section -->
                        <div class="px-4 py-3 dark:border-slate-700 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-semibold dark:text-white text-slate-900">{{ Auth::user()->name }}</div>
                                    <div class="text-xs dark:text-slate-400 text-slate-500">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-2">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                View Profile
                            </a>
                        </div>

                        <!-- Logout Section -->
                        <div class="dark:border-slate-700 border-t border-slate-200 py-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-900 hover:bg-opacity-20 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center gap-2">
                <!-- Mobile Theme Toggle -->
                <button id="theme-toggle" type="button" class="p-2 rounded-lg dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600 bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                </button>
                <button @click="open = !open" class="dark:text-slate-300 dark:hover:text-white text-slate-700 hover:text-slate-900 focus:outline-none transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{'hidden': open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path :class="{'hidden': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" class="md:hidden pb-4 space-y-2" style="display: none;">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                Dashboard
            </a>
            <a href="{{ route('org-chart.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                Organization Chart
            </a>
            <a href="{{ route('employees.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                Employees
            </a>
            <a href="{{ route('departments.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                Departments
            </a>
            <a href="{{ route('leaves.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                Leave Requests
            </a>
            @if (Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                <a href="{{ route('policies.leave.edit') }}" class="block px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                    Policies
                </a>
            @endif
            <!-- Mobile User Menu -->
            <div class="pt-2 dark:border-slate-700 border-t border-slate-200">
                <!-- Mobile Theme Toggle -->
                <button id="theme-toggle" type="button" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <span>Toggle Theme</span>
                </button>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 text-slate-700 hover:text-slate-900 hover:bg-slate-200 transition-colors duration-200">
                    <div class="w-8 h-8 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="text-left">
                        <div class="font-semibold dark:text-white text-slate-900">{{ Auth::user()->name }}</div>
                        <div class="text-xs dark:text-slate-400 text-slate-500">View Profile</div>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-red-400 hover:text-red-300 dark:hover:bg-red-900 dark:hover:bg-opacity-20 hover:bg-red-100 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
