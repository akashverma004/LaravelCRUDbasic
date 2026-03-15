{{-- Sidebar Notification Bell with Dropdown (Alpine.js async) --}}
<div x-data="notificationBell()" x-init="init()" class="relative">
    {{-- Bell Button --}}
    <button
        @click="toggleDropdown()"
        class="group flex w-full items-center gap-3 rounded-xl px-4 py-3 text-slate-400 transition-colors hover:bg-slate-50 hover:text-cyan-600 dark:hover:bg-white/5 dark:hover:text-cyan-400"
    >
        <div class="relative">
            <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-105" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            <span
                x-show="unreadCount > 0"
                x-text="unreadCount > 99 ? '99+' : unreadCount"
                class="absolute -right-1 -top-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-cyan-500 px-1 text-[9px] font-bold text-white shadow-sm"
                style="display: none;"
            ></span>
        </div>
        <span class="text-xs font-semibold tracking-wide">Notifications</span>
    </button>

    {{-- Dropdown Panel --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        @click.outside="isOpen = false"
        class="absolute bottom-full left-0 mb-2 w-80 rounded-2xl border border-slate-200 bg-white shadow-xl z-50 overflow-hidden dark:border-slate-800 dark:bg-slate-900"
        style="display: none;"
    >
        @include('hrms.components.notification-dropdown-content')
    </div>
</div>
