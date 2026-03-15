{{-- Shared notification dropdown content --}}
{{-- Header Area --}}
<div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 bg-slate-50 dark:border-white/5 dark:bg-slate-900/50">
    <div>
        <h3 class="text-xs font-bold text-slate-900 dark:text-white">Notifications</h3>
        <p class="text-[10px] font-medium text-slate-500 mt-0.5">Recent alerts</p>
    </div>
    <button
        x-show="unreadCount > 0"
        @click.stop="markAllAsRead()"
        class="text-xs font-semibold text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300 transition-colors"
    >
        Mark All Read
    </button>
</div>

{{-- Notifications List --}}
<div class="flex-1 overflow-y-auto overscroll-contain custom-scrollbar" style="max-height: 24rem;">
    {{-- Loading State --}}
    <div x-show="loading" class="flex items-center justify-center py-8">
        <svg class="h-6 w-6 animate-spin text-cyan-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </div>

    {{-- Empty State --}}
    <div x-show="!loading && notifications.length === 0" class="px-6 py-12 text-center">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 mb-3 shadow-sm dark:bg-slate-900 dark:text-slate-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
        </div>
        <p class="text-xs font-semibold text-slate-500">No notifications.</p>
    </div>

    {{-- Notification Items --}}
    <div class="divide-y divide-slate-100 dark:divide-white/5">
        <template x-for="notification in notifications" :key="notification.id">
            <a
                :href="notification.data.action_url || '#'"
                @click="markAsRead(notification.id)"
                class="group flex gap-3 p-4 transition-colors duration-200 hover:bg-slate-50 dark:hover:bg-white/5"
                :class="!notification.read_at ? 'bg-cyan-50/50 dark:bg-cyan-900/10' : ''"
            >
                {{-- Icon --}}
                <div class="flex-shrink-0">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg shadow-sm"
                        :class="{
                            'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400': notification.data.icon === 'calendar',
                            'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400': notification.data.icon === 'check-circle',
                            'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400': notification.data.icon === 'x-circle',
                            'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400': !notification.data.icon
                        }"
                    >
                        <template x-if="notification.data.icon === 'calendar'">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        </template>
                        <template x-if="notification.data.icon === 'check-circle'">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </template>
                        <template x-if="notification.data.icon === 'x-circle'">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </template>
                    </div>
                </div>

                {{-- Content --}}
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-900 dark:text-white" x-text="notification.data.title"></p>
                    <p class="mt-0.5 line-clamp-2 text-xs text-slate-500" x-text="notification.data.body"></p>
                    <p class="mt-1 text-[10px] font-medium text-slate-400" x-text="notification.time_ago"></p>
                </div>

                {{-- Unread Indicator --}}
                <div x-show="!notification.read_at" class="flex-shrink-0 flex items-center">
                    <div class="h-2 w-2 rounded-full bg-cyan-500"></div>
                </div>
            </a>
        </template>
    </div>
</div>
