{{-- Shared notification dropdown content (used by both mobile and sidebar) --}}
{{-- Header --}}
<div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-slate-700">
    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Notifications</h3>
    <button
        x-show="unreadCount > 0"
        @click.stop="markAllAsRead()"
        class="text-xs font-medium text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300"
    >
        Mark all as read
    </button>
</div>

{{-- Notification List --}}
<div class="flex-1 overflow-y-auto overscroll-contain" style="max-height: 22rem;">
    {{-- Loading State --}}
    <div x-show="loading" class="flex items-center justify-center py-8">
        <svg class="h-6 w-6 animate-spin text-cyan-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </div>

    {{-- Empty State --}}
    <div x-show="!loading && notifications.length === 0" class="px-4 py-8 text-center">
        <svg class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">No notifications yet</p>
    </div>

    {{-- Notification Items --}}
    <template x-for="notification in notifications" :key="notification.id">
        <a
            :href="notification.data.action_url || '#'"
            @click="markAsRead(notification.id)"
            class="flex gap-3 border-b border-slate-100 px-4 py-3 transition-colors hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/50"
            :class="{ 'bg-cyan-50/50 dark:bg-cyan-900/10': !notification.read_at }"
        >
            {{-- Icon --}}
            <div class="flex-shrink-0 mt-0.5">
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-full"
                    :class="{
                        'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400': notification.data.icon === 'calendar',
                        'bg-green-100 text-green-600 dark:bg-green-500/20 dark:text-green-400': notification.data.icon === 'check-circle',
                        'bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400': notification.data.icon === 'x-circle',
                        'bg-slate-100 text-slate-600 dark:bg-slate-500/20 dark:text-slate-400': !notification.data.icon
                    }"
                >
                    <template x-if="notification.data.icon === 'calendar'">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </template>
                    <template x-if="notification.data.icon === 'check-circle'">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </template>
                    <template x-if="notification.data.icon === 'x-circle'">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </template>
                </div>
            </div>

            {{-- Content --}}
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-slate-900 dark:text-white" x-text="notification.data.title"></p>
                <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400" x-text="notification.data.body"></p>
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500" x-text="notification.time_ago"></p>
            </div>

            {{-- Unread Dot --}}
            <div x-show="!notification.read_at" class="flex-shrink-0 mt-2">
                <div class="h-2 w-2 rounded-full bg-cyan-500"></div>
            </div>
        </a>
    </template>
</div>
