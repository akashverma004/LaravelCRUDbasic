import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// ── Notification Bell Component ─────────────────────────────────────
Alpine.data('notificationBell', () => ({
    isOpen: false,
    loading: false,
    notifications: [],
    unreadCount: 0,
    pollInterval: null,

    init() {
        this.fetchUnreadCount();
        // Poll every 30 seconds
        this.pollInterval = setInterval(() => this.fetchUnreadCount(), 30000);
    },

    destroy() {
        if (this.pollInterval) clearInterval(this.pollInterval);
    },

    async fetchUnreadCount() {
        try {
            const { data } = await axios.get('/api/notifications/unread-count');
            this.unreadCount = data.count;
        } catch (e) {
            // Silently fail — user might have logged out
        }
    },

    async fetchNotifications() {
        this.loading = true;
        try {
            const { data } = await axios.get('/api/notifications');
            this.notifications = data.notifications;
        } catch (e) {
            this.notifications = [];
        } finally {
            this.loading = false;
        }
    },

    async toggleDropdown() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            await this.fetchNotifications();
        }
    },

    async markAsRead(id) {
        try {
            await axios.post(`/api/notifications/${id}/read`);
            const n = this.notifications.find(n => n.id === id);
            if (n) n.read_at = new Date().toISOString();
            this.unreadCount = Math.max(0, this.unreadCount - 1);
        } catch (e) {
            // Silently fail
        }
    },

    async markAllAsRead() {
        try {
            await axios.post('/api/notifications/read-all');
            this.notifications.forEach(n => n.read_at = n.read_at || new Date().toISOString());
            this.unreadCount = 0;
        } catch (e) {
            // Silently fail
        }
    },
}));

Alpine.start();
