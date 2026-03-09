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

// ── Self-Service Profile Component ──────────────────────────────────
Alpine.data('selfServiceProfile', () => ({
    employee: {},
    photoUrl: null,
    editing: false,
    saving: false,
    uploading: false,
    form: {},
    toast: { show: false, message: '', type: 'success' },
    activeTab: 'personal',
    tabs: [
        { id: 'personal', label: 'Personal' },
        { id: 'emergency', label: 'Emergency' },
        { id: 'identity', label: 'Identity' },
        { id: 'bank', label: 'Bank' },
        { id: 'preferences', label: 'Preferences' },
        { id: 'education', label: 'Education' },
        { id: 'experience', label: 'Experience' },
        { id: 'account', label: 'Account' },
    ],
    // Sub-forms for one-to-many
    showEduForm: false,
    showExpForm: false,
    showSkillForm: false,
    eduForm: { degree: '', institution: '', field_of_study: '', year_from: '', year_to: '' },
    expForm: { company: '', designation: '', from_date: '', to_date: '', description: '' },
    skillForm: { name: '', proficiency: '' },
    // Account management
    accountForm: { name: '', email: '' },
    passwordForm: { current_password: '', password: '', password_confirmation: '' },
    savingAccount: false,
    savingPassword: false,
    showDeleteConfirm: false,
    deletePassword: '',

    async init() {
        await this.fetchProfile();
    },

    async fetchProfile() {
        try {
            const { data } = await axios.get('/self-service/profile/data');
            this.employee = data.employee;
            this.photoUrl = data.employee.profile_photo;
            if (data.user) {
                this.accountForm = { name: data.user.name || '', email: data.user.email || '' };
            }
        } catch (e) {
            this.showToast('Failed to load profile.', 'error');
        }
    },

    startEditing() {
        this.form = {
            phone: this.employee.phone || '',
            city: this.employee.city || '',
            address: this.employee.address || '',
            hobbies: this.employee.hobbies || '',
            likes: this.employee.likes || '',
            food_preference: this.employee.food_preference || '',
            health_issues: this.employee.health_issues || '',
            date_of_birth: this.employee.date_of_birth || '',
            gender: this.employee.gender || '',
            marital_status: this.employee.marital_status || '',
            blood_group: this.employee.blood_group || '',
            nationality: this.employee.nationality || '',
            personal_email: this.employee.personal_email || '',
            emergency_contact_name: this.employee.emergency_contact_name || '',
            emergency_contact_phone: this.employee.emergency_contact_phone || '',
            emergency_contact_relationship: this.employee.emergency_contact_relationship || '',
            pan_number: this.employee.pan_number || '',
            aadhaar_number: this.employee.aadhaar_number || '',
            passport_number: this.employee.passport_number || '',
            passport_expiry: this.employee.passport_expiry || '',
            bank_name: this.employee.bank_name || '',
            bank_account_number: this.employee.bank_account_number || '',
            bank_ifsc: this.employee.bank_ifsc || '',
            linkedin_url: this.employee.linkedin_url || '',
            pronouns: this.employee.pronouns || '',
            bio: this.employee.bio || '',
        };
        this.editing = true;
    },

    cancelEditing() {
        this.editing = false;
        this.form = {};
    },

    async saveProfile() {
        this.saving = true;
        try {
            const { data } = await axios.patch('/self-service/profile/info', this.form);
            Object.assign(this.employee, this.form);
            this.editing = false;
            this.showToast(data.message, 'success');
        } catch (e) {
            const msg = e.response?.data?.message || 'Failed to update profile.';
            this.showToast(msg, 'error');
        } finally {
            this.saving = false;
        }
    },

    async uploadPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;
        this.uploading = true;
        const formData = new FormData();
        formData.append('photo', file);
        try {
            const { data } = await axios.post('/self-service/profile/photo', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            this.photoUrl = data.photo_url;
            this.showToast(data.message, 'success');
        } catch (e) {
            this.showToast(e.response?.data?.message || 'Failed to upload photo.', 'error');
        } finally {
            this.uploading = false;
            event.target.value = '';
        }
    },

    async removePhoto() {
        try {
            const { data } = await axios.delete('/self-service/profile/photo');
            this.photoUrl = null;
            this.showToast(data.message, 'success');
        } catch (e) {
            this.showToast('Failed to remove photo.', 'error');
        }
    },

    // ── Education CRUD ──────────────────────────────────────────────
    async addEducation() {
        try {
            const { data } = await axios.post('/self-service/profile/educations', this.eduForm);
            if (!this.employee.educations) this.employee.educations = [];
            this.employee.educations.push(data.education);
            this.eduForm = { degree: '', institution: '', field_of_study: '', year_from: '', year_to: '' };
            this.showEduForm = false;
            this.showToast(data.message, 'success');
        } catch (e) {
            this.showToast(e.response?.data?.message || 'Failed to add education.', 'error');
        }
    },

    async removeEducation(id) {
        try {
            await axios.delete(`/self-service/profile/educations/${id}`);
            this.employee.educations = this.employee.educations.filter(e => e.id !== id);
            this.showToast('Education removed.', 'success');
        } catch (e) {
            this.showToast('Failed to remove education.', 'error');
        }
    },

    // ── Experience CRUD ─────────────────────────────────────────────
    async addExperience() {
        try {
            const { data } = await axios.post('/self-service/profile/experiences', this.expForm);
            if (!this.employee.experiences) this.employee.experiences = [];
            this.employee.experiences.push(data.experience);
            this.expForm = { company: '', designation: '', from_date: '', to_date: '', description: '' };
            this.showExpForm = false;
            this.showToast(data.message, 'success');
        } catch (e) {
            this.showToast(e.response?.data?.message || 'Failed to add experience.', 'error');
        }
    },

    async removeExperience(id) {
        try {
            await axios.delete(`/self-service/profile/experiences/${id}`);
            this.employee.experiences = this.employee.experiences.filter(e => e.id !== id);
            this.showToast('Experience removed.', 'success');
        } catch (e) {
            this.showToast('Failed to remove experience.', 'error');
        }
    },

    // ── Skills CRUD ─────────────────────────────────────────────────
    async addSkill() {
        try {
            const { data } = await axios.post('/self-service/profile/skills', this.skillForm);
            if (!this.employee.skills) this.employee.skills = [];
            this.employee.skills.push(data.skill);
            this.skillForm = { name: '', proficiency: '' };
            this.showSkillForm = false;
            this.showToast(data.message, 'success');
        } catch (e) {
            this.showToast(e.response?.data?.message || 'Failed to add skill.', 'error');
        }
    },

    async removeSkill(id) {
        try {
            await axios.delete(`/self-service/profile/skills/${id}`);
            this.employee.skills = this.employee.skills.filter(s => s.id !== id);
            this.showToast('Skill removed.', 'success');
        } catch (e) {
            this.showToast('Failed to remove skill.', 'error');
        }
    },

    // ── Account Management ────────────────────────────────────
    async updateAccount() {
        this.savingAccount = true;
        try {
            const { data } = await axios.patch('/self-service/profile/account', this.accountForm);
            this.showToast(data.message, 'success');
        } catch (e) {
            const msg = e.response?.data?.message || 'Failed to update account.';
            this.showToast(msg, 'error');
        } finally {
            this.savingAccount = false;
        }
    },

    async updatePassword() {
        this.savingPassword = true;
        try {
            const { data } = await axios.put('/self-service/profile/password', this.passwordForm);
            this.passwordForm = { current_password: '', password: '', password_confirmation: '' };
            this.showToast(data.message, 'success');
        } catch (e) {
            const msg = e.response?.data?.message || Object.values(e.response?.data?.errors || {}).flat()[0] || 'Failed to update password.';
            this.showToast(msg, 'error');
        } finally {
            this.savingPassword = false;
        }
    },

    async deleteAccount() {
        try {
            const { data } = await axios.delete('/self-service/profile/account', {
                data: { password: this.deletePassword }
            });
            window.location.href = data.redirect || '/';
        } catch (e) {
            const msg = e.response?.data?.message || 'Failed to delete account.';
            this.showToast(msg, 'error');
        }
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

Alpine.start();
