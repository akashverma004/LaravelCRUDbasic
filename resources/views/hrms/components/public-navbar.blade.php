{{--
    Public top navbar — shown on login, signup, and other guest pages.
    Contains: PeopleFlow branding on left, dark mode toggle on right.
--}}
<nav class="sticky top-0 z-50 border-b border-slate-200 bg-white/80 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6">

        {{-- Brand --}}
        <a href="{{ route('login') }}" class="flex items-center gap-2.5 select-none">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-400 to-blue-500 shadow-sm">
                <span class="text-base font-bold text-white">P</span>
            </div>
            <span class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">PeopleFlow</span>
            <span class="hidden sm:inline-block rounded-full bg-cyan-100 dark:bg-cyan-900/40 px-2 py-0.5 text-xs font-semibold text-cyan-700 dark:text-cyan-300">HRMS</span>
        </a>

        {{-- Right side --}}
        <div class="flex items-center gap-3">
            {{-- Login / Signup links (contextual) --}}
            @if(request()->routeIs('company-signup.*'))
                <a href="{{ route('login') }}"
                   class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                    Sign In
                </a>
            @elseif(request()->routeIs('login'))
                <a href="{{ route('company-signup.create') }}"
                   class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                    Register Company
                </a>
            @endif

            {{-- Dark / Light toggle --}}
            <button id="theme-toggle" type="button"
                    class="flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200">
                {{-- Moon icon --}}
                <svg class="h-4 w-4 block dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
                {{-- Sun icon --}}
                <svg class="h-4 w-4 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
                <span class="hidden sm:inline dark:hidden">Dark</span>
                <span class="hidden sm:hidden dark:sm:inline">Light</span>
            </button>
        </div>
    </div>
</nav>
