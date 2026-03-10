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
            const { data } = await axios.patch('/self-service/profile/info', this.form, {
                headers: { 'Accept': 'application/json' }
            });
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

// ── Document Manager Component ──────────────────────────────────────
Alpine.data('documentManager', () => ({
    documents: [],
    categories: {},
    employees: [],
    isAdmin: false,
    loading: true,
    uploading: false,
    showUploadForm: false,
    filter: 'all',
    search: '',
    page: 1,
    pagination: { lastPage: 1 },
    toast: { show: false, message: '', type: 'success' },
    uploadForm: {
        title: '', category: 'general', file: null,
        expiry_date: '', employee_id: '', notes: '',
    },

    async init() {
        await this.fetchDocs();
        // Check if admin by trying to fetch employees
        try {
            const { data } = await axios.get('/documents/employees');
            this.employees = data.employees || [];
            this.isAdmin = true;
        } catch (e) {
            this.isAdmin = false;
        }
    },

    async fetchDocs() {
        this.loading = true;
        try {
            const params = { page: this.page };
            if (this.filter !== 'all') params.category = this.filter;
            if (this.search) params.q = this.search;

            const { data } = await axios.get('/documents/data', { params });
            this.documents = data.documents.data;
            this.categories = data.categories;
            this.pagination = { lastPage: data.documents.last_page };
        } catch (e) {
            this.showToast('Failed to load documents.', 'error');
        } finally {
            this.loading = false;
        }
    },

    async upload() {
        if (!this.uploadForm.title || !this.uploadForm.file) {
            this.showToast('Title and file are required.', 'error');
            return;
        }
        this.uploading = true;
        try {
            const fd = new FormData();
            fd.append('title', this.uploadForm.title);
            fd.append('category', this.uploadForm.category);
            fd.append('file', this.uploadForm.file);
            if (this.uploadForm.expiry_date) fd.append('expiry_date', this.uploadForm.expiry_date);
            if (this.uploadForm.employee_id) fd.append('employee_id', this.uploadForm.employee_id);
            if (this.uploadForm.notes) fd.append('notes', this.uploadForm.notes);

            const { data } = await axios.post('/documents', fd, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            this.documents.unshift(data.document);
            this.uploadForm = { title: '', category: 'general', file: null, expiry_date: '', employee_id: '', notes: '' };
            const fileInput = document.getElementById('docFileInput');
            if (fileInput) fileInput.value = '';
            this.showUploadForm = false;
            this.showToast(data.message, 'success');
        } catch (e) {
            const msg = e.response?.data?.message || Object.values(e.response?.data?.errors || {}).flat()[0] || 'Upload failed.';
            this.showToast(msg, 'error');
        } finally {
            this.uploading = false;
        }
    },

    async deleteDoc(id) {
        if (!confirm('Are you sure you want to delete this document?')) return;
        try {
            const { data } = await axios.delete(`/documents/${id}`);
            this.documents = this.documents.filter(d => d.id !== id);
            this.showToast(data.message, 'success');
        } catch (e) {
            this.showToast(e.response?.data?.message || 'Failed to delete.', 'error');
        }
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

// ── Performance Manager Component ──────────────────────────────────
Alpine.data('performanceManager', () => ({
    activeTab: 'goals',
    goals: [],
    reviews: [],
    notes: [],
    isManager: false,
    loading: true,
    toast: { show: false, message: '', type: 'success' },
    showGoalForm: false,
    showReviewForm: false,
    showNoteForm: false,

    async init() {
        await this.fetchData();
    },

    async fetchData() {
        this.loading = true;
        try {
            const { data } = await axios.get('/performance/data');
            this.goals = data.goals;
            this.reviews = data.reviews;
            this.notes = data.notes;
            this.isManager = data.is_manager;
        } catch (e) {
            this.showToast('Failed to load performance data.', 'error');
        } finally {
            this.loading = false;
        }
    },

    async updateGoalProgress(goal) {
        const newProgress = prompt('Enter new progress (0-100):', goal.progress);
        if (newProgress === null || newProgress === '') return;
        
        try {
            await axios.patch(`/performance/goals/${goal.id}`, {
                progress: parseInt(newProgress),
                status: parseInt(newProgress) >= 100 ? 'completed' : 'active'
            });
            await this.fetchData();
            this.showToast('Goal updated successfully!');
        } catch (e) {
            this.showToast('Update failed.', 'error');
        }
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

// ── Analytics Dashboard Component ──────────────────────────────────
Alpine.data('analyticsDashboard', () => ({
    loading: true,
    stats: { totalEmployees: 0, activeLeaves: 0, presentToday: 0 },
    headcountTrend: [],
    absenceTrend: [],
    attendanceTrend: [],
    departmentDistribution: [],

    async init() {
        await this.fetchData();
    },

    async fetchData() {
        this.loading = true;
        try {
            const { data } = await axios.get('/analytics/data');
            this.stats = data.stats;
            this.headcountTrend = data.headcountTrend;
            this.absenceTrend = data.absenceTrend;
            this.attendanceTrend = data.attendanceTrend;
            this.departmentDistribution = data.departmentDistribution;
        } catch (e) {
            console.error('Failed to load analytics', e);
        } finally {
            this.loading = false;
        }
    }
}));

// ── Onboarding Portal Component ────────────────────────────────────
Alpine.data('onboardingPortal', () => ({
    loading: true,
    toggling: false,
    isAdmin: false,
    onboarding: null,
    onboardings: [],
    templates: [],
    availableEmployees: [],
    showAssignModal: false,
    toast: { show: false, message: '', type: 'success' },
    assignForm: { employee_id: '', template_id: '' },

    async init() {
        await this.fetchData();
    },

    async fetchData() {
        this.loading = true;
        try {
            const { data } = await axios.get('/onboarding/data');
            this.isAdmin = data.isAdmin;
            if (this.isAdmin) {
                this.onboardings = data.onboardings;
                this.templates = data.templates;
                this.availableEmployees = data.availableEmployees;
            } else {
                this.onboarding = data.onboarding;
            }
        } catch (e) {
            console.error('Failed to load onboarding', e);
        } finally {
            this.loading = false;
        }
    },

    async toggleTask(task) {
        if (this.toggling) return;
        this.toggling = true;
        const newState = !task.is_completed;
        try {
            const { data } = await axios.patch(`/onboarding/tasks/${task.id}/complete`, {
                is_completed: newState
            });
            task.is_completed = newState;
            this.onboarding.progress = data.progress;
            if (data.onboarding_status === 'completed') {
                this.showToast('Congratulations! You have completed your onboarding. 🎉');
            }
        } catch (e) {
            this.showToast('Failed to update task.', 'error');
        } finally {
            this.toggling = false;
        }
    },

    async assignWorkflow() {
        this.toggling = true;
        try {
            await axios.post('/onboarding/assign', this.assignForm);
            this.showAssignModal = false;
            this.assignForm = { employee_id: '', template_id: '' };
            this.showToast('Workflow assigned successfully!');
            await this.fetchData();
        } catch (e) {
            this.showToast('Failed to assign workflow.', 'error');
        } finally {
            this.toggling = false;
        }
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

// ── Audit Log Manager Component ─────────────────────────────────────
Alpine.data('auditManager', () => ({
    loading: true,
    logs: [],
    detailsModal: false,
    selectedLog: null,

    async init() {
        await this.fetchData();
    },

    async fetchData() {
        this.loading = true;
        try {
            const { data } = await axios.get('/audit/data');
            this.logs = data.logs.data;
        } catch (e) {
            console.error('Failed to load logs', e);
        } finally {
            this.loading = false;
        }
    },

    showDetails(log) {
        this.selectedLog = log;
        this.detailsModal = true;
    },

    formatDate(dateStr) {
        return new Date(dateStr).toLocaleString('en-GB', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }
}));

// ── Asset Manager Component ──────────────────────────────────────────
Alpine.data('assetManager', () => ({
    loading: true,
    isAdmin: false,
    assets: [],
    categories: {},
    showAddModal: false,
    addForm: { name: '', category: 'laptop', serial_number: '', status: 'available', employee_id: '' },
    toast: { show: false, message: '', type: 'success' },

    async init() {
        await this.fetchData();
    },

    async fetchData() {
        this.loading = true;
        try {
            const { data } = await axios.get('/assets/data');
            this.assets = data.assets;
            this.categories = data.categories;
            this.isAdmin = data.isAdmin;
        } catch (e) {
            console.error('Failed to load assets', e);
        } finally {
            this.loading = false;
        }
    },

    async saveAsset() {
        try {
            await axios.post('/assets', this.addForm);
            this.showAddModal = false;
            this.addForm = { name: '', category: 'laptop', serial_number: '', status: 'available', employee_id: '' };
            this.showToast('Asset registered successfully!');
            await this.fetchData();
        } catch (e) {
            this.showToast('Failed to save asset.', 'error');
        }
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

// ── Payroll Manager Component ──────────────────────────────────────
Alpine.data('payrollManager', () => ({
    loading: true,
    isAdmin: false,
    activeTab: 'payslips',
    payslips: [],
    structures: [],
    availableEmployees: [],
    showGenerateModal: false,
    showStructureModal: false,
    toast: { show: false, message: '', type: 'success' },

    async init() {
        await this.fetchData();
    },

    async fetchData() {
        this.loading = true;
        try {
            const { data } = await axios.get('/payroll/data');
            this.isAdmin = data.isAdmin;
            this.payslips = data.payslips;
            if (this.isAdmin) {
                this.structures = data.structures;
                this.availableEmployees = data.availableEmployees;
            }
        } catch (e) {
            console.error('Failed to load payroll', e);
        } finally {
            this.loading = false;
        }
    },

    async markAsPaid(ps) {
        if (!confirm('Mark this payslip as paid?')) return;
        try {
            await axios.post(`/payroll/payslips/${ps.id}/pay`);
            ps.status = 'paid';
            this.showToast('Payment confirmed!');
        } catch (e) {
            this.showToast('Failed to update status.', 'error');
        }
    },

    viewPayslip(ps) {
        // Logic to show detailed breakdown in a modal
        alert(`Details for ${ps.month}:\nBase: $${ps.base_salary}\nAllowances: $${ps.total_allowances}\nDeductions: $${ps.total_deductions}\nNet: $${ps.net_pay}`);
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

// ── Shift Manager Component ────────────────────────────────────────
Alpine.data('shiftManager', () => ({
    loading: true,
    toggling: false,
    isAdmin: false,
    shifts: [],
    schedules: [],
    employees: [],
    weekDays: [],
    periodLabel: '',
    currentStart: null,
    showShiftModal: false,
    showAssignModal: false,
    assignForm: { employee_id: '', shift_id: '', date: '', notes: '' },
    toast: { show: false, message: '', type: 'success' },

    async init() {
        this.currentStart = new Date();
        const day = this.currentStart.getDay();
        const diff = this.currentStart.getDate() - day + (day === 0 ? -6 : 1);
        this.currentStart.setDate(diff);
        await this.fetchData();
    },

    async fetchData() {
        this.loading = true;
        const startStr = this.currentStart.toISOString().split('T')[0];
        try {
            const { data } = await axios.get('/shifts/data?start=' + startStr);
            this.shifts = data.shifts;
            this.schedules = data.schedules;
            this.employees = data.employees;
            this.isAdmin = data.isAdmin;
            this.periodLabel = data.period.label;
            this.buildWeekDays(new Date(data.period.start));
        } catch (e) {
            console.error('Failed to load shifts', e);
        } finally {
            this.loading = false;
        }
    },

    buildWeekDays(start) {
        const days = [];
        const names = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        for (let i = 0; i < 7; i++) {
            const d = new Date(start);
            d.setDate(start.getDate() + i);
            days.push({
                date: d.toISOString().split('T')[0],
                name: names[i],
                label: d.getDate() + ' ' + d.toLocaleString('en-GB', { month: 'short' })
            });
        }
        this.weekDays = days;
    },

    getSchedules(employeeId, date) {
        return this.schedules.filter(s => {
            const sameDate = s.date.startsWith(date);
            return employeeId ? (s.employee_id === parseInt(employeeId) && sameDate) : sameDate;
        });
    },

    async changeWeek(dir) {
        this.currentStart.setDate(this.currentStart.getDate() + (dir * 7));
        await this.fetchData();
    },

    async assignShift() {
        this.toggling = true;
        try {
            const { data } = await axios.post('/shifts/assign', this.assignForm);
            this.schedules.push(data.schedule);
            this.showAssignModal = false;
            this.showToast('Shift assigned!');
        } catch (e) {
            this.showToast('Failed to assign shift.', 'error');
        } finally {
            this.toggling = false;
        }
    },

    async deleteAssignment(id) {
        if (!confirm('Remove this assignment?')) return;
        try {
            await axios.delete(`/shifts/schedule/${id}`);
            this.schedules = this.schedules.filter(s => s.id !== id);
        } catch (e) {
            this.showToast('Failed to delete.', 'error');
        }
    },

    formatTime(time) {
        return time.substring(0, 5);
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

Alpine.start();
