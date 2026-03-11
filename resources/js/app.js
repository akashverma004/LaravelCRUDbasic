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

// ── Leave Manager Component (Employee View) ─────────────────────────
Alpine.data('leaveManager', () => ({
    loading: true,
    leaves: [],
    employees: [],
    isAdmin: false,
    showModal: false,
    isEditing: false,
    editLeaveId: null,
    form: {
        employee_id: '',
        leave_type: '',
        leave_session: 'full_day',
        start_date: '',
        end_date: '',
        reason: '',
        status: 'pending'
    },
    toast: { show: false, message: '', type: 'success' },

    async init() {
        await this.fetchData();
    },

    async fetchData() {
        this.loading = true;
        try {
            const { data } = await axios.get('/leaves/data');
            this.leaves = data.leaves;
            this.employees = data.employees;
            this.isAdmin = data.isAdmin;
        } catch (e) {
            console.error('Failed to load leaves', e);
        } finally {
            this.loading = false;
        }
    },

    openModal() {
        this.isEditing = false;
        this.editLeaveId = null;
        this.form = {
            employee_id: this.employees.length > 0 && !this.isAdmin ? this.employees[0].id : '',
            leave_type: '',
            leave_session: 'full_day',
            start_date: '',
            end_date: '',
            reason: '',
            status: 'pending'
        };
        this.showModal = true;
    },

    editLeave(leave) {
        if (leave.status !== 'pending') return;
        this.isEditing = true;
        this.editLeaveId = leave.id;
        this.form = {
            employee_id: leave.employee_id,
            leave_type: leave.leave_type,
            leave_session: leave.leave_session,
            start_date: leave.start_date.split('T')[0],
            end_date: leave.end_date.split('T')[0],
            reason: leave.reason,
            status: leave.status
        };
        this.showModal = true;
    },

    async saveLeave() {
        try {
            if (this.isEditing) {
                await axios.patch(`/leaves/${this.editLeaveId}`, this.form);
                this.showToast('Leave request updated successfully!');
            } else {
                await axios.post('/leaves', this.form);
                this.showToast('Leave request submitted successfully!');
            }
            this.showModal = false;
            await this.fetchData();
        } catch (e) {
            this.showToast(e.response?.data?.error || 'Failed to save leave request.', 'error');
        }
    },

    async deleteLeave(id) {
        if (!confirm('Are you sure you want to cancel this leave request?')) return;
        try {
            await axios.delete(`/leaves/${id}`);
            this.showToast('Leave request cancelled successfully!');
            await this.fetchData();
        } catch (e) {
            this.showToast(e.response?.data?.error || 'Failed to cancel leave request.', 'error');
        }
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },

    formatDateShort(dateStr) {
        return new Date(dateStr).toLocaleString('en-GB', { day: '2-digit', month: 'short' });
    },

    formatDateFull(dateStr) {
        return new Date(dateStr).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }
}));

// ── Asset Manager Component ──────────────────────────────────────────
Alpine.data('assetManager', () => ({
    loading: true,
    isAdmin: false,
    assets: [],
    categories: {},
    employees: [],
    showAddModal: false,
    isEditing: false,
    editAssetId: null,
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
            this.employees = data.employees || [];
        } catch (e) {
            console.error('Failed to load assets', e);
        } finally {
            this.loading = false;
        }
    },

    openAddModal() {
        this.isEditing = false;
        this.editAssetId = null;
        this.addForm = { name: '', category: 'laptop', serial_number: '', status: 'available', employee_id: '' };
        this.showAddModal = true;
    },

    editAsset(asset) {
        this.isEditing = true;
        this.editAssetId = asset.id;
        this.addForm = { 
            name: asset.name, 
            category: asset.category, 
            serial_number: asset.serial_number || '', 
            status: asset.status, 
            employee_id: asset.employee_id || '' 
        };
        this.showAddModal = true;
    },

    async saveAsset() {
        try {
            if (this.isEditing) {
                await axios.patch(`/assets/${this.editAssetId}`, {
                    status: this.addForm.status,
                    employee_id: this.addForm.employee_id || null,
                    notes: this.addForm.notes || ''
                });
                this.showToast('Asset updated successfully!');
            } else {
                await axios.post('/assets', {
                    ...this.addForm,
                    employee_id: this.addForm.employee_id || null
                });
                this.showToast('Asset registered successfully!');
            }
            this.showAddModal = false;
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
    generating: false,
    generateMonth: '',
    structureForm: { employee_id: '', base_salary: '' },

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

    async generatePayslips() {
        if (!this.generateMonth) return;
        this.generating = true;
        try {
            const { data } = await axios.post('/payroll/generate', { month: this.generateMonth });
            this.showGenerateModal = false;
            this.showToast(data.message);
            await this.fetchData();
        } catch (e) {
            this.showToast('Failed to generate payslips.', 'error');
        } finally {
            this.generating = false;
        }
    },

    async saveStructure() {
        try {
            await axios.post('/payroll/structures', this.structureForm);
            this.showStructureModal = false;
            this.structureForm = { employee_id: '', base_salary: '' };
            this.showToast('Structure added!');
            await this.fetchData();
        } catch (e) {
            this.showToast('Failed to add structure.', 'error');
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
    shiftForm: { name: '', start_time: '', end_time: '', color: '#0ea5e9' },
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
            this.assignForm = { employee_id: '', shift_id: '', date: '', notes: '' };
            this.showToast('Shift assigned!');
        } catch (e) {
            this.showToast('Failed to assign shift.', 'error');
        } finally {
            this.toggling = false;
        }
    },

    async saveShift() {
        this.toggling = true;
        try {
            const { data } = await axios.post('/shifts/templates', this.shiftForm);
            this.shifts.push(data.shift);
            this.showShiftModal = false;
            this.shiftForm = { name: '', start_time: '', end_time: '', color: '#0ea5e9' };
            this.showToast('Shift template created!');
        } catch (e) {
            this.showToast('Failed to create shift template.', 'error');
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

Alpine.data('workflowInbox', (config = {}) => ({
    dataUrl: config.dataUrl || '/workflows/data',
    storeUrl: config.storeUrl || '/workflows',
    templateStoreUrl: config.templateStoreUrl || '/workflows/templates',
    templateUpdateBase: config.templateUpdateBase || '/workflows/templates',
    templateArchiveBase: config.templateArchiveBase || '/workflows/templates',
    showUrlBase: config.showUrlBase || '/workflows',
    approveUrlBase: config.approveUrlBase || '/workflows',
    cancelUrlBase: config.cancelUrlBase || '/workflows',
    rejectUrlBase: config.rejectUrlBase || '/workflows',
    resubmitUrlBase: config.resubmitUrlBase || '/workflows',
    fulfillUrlBase: config.fulfillUrlBase || '/workflows',
    loading: false,
    saving: false,
    savingDecision: false,
    savingFulfill: false,
    savingTemplate: false,
    cancellingRequestId: null,
    archivingTemplateId: null,
    canCreate: true,
    isAdmin: false,
    attachmentFile: null,
    attachmentName: '',
    requests: [],
    templates: [],
    timeline: [],
    detailsEntries: [],
    fulfillmentAsset: null,
    selectedRequest: null,
    availableAssets: [],
    decisionMode: 'approve',
    decisionComment: '',
    decisionError: '',
    fulfillError: '',
    templateErrors: [],
    formErrors: [],
    formMode: 'create',
    editingWorkflowId: null,
    summary: {
        pending: 0,
        approved: 0,
        fulfilled: 0,
        rejected: 0,
        awaiting_my_approval: 0,
    },
    filters: {
        q: '',
        scope: 'all',
        status: 'all',
        type: 'all',
    },
    form: {
        type: 'reimbursement',
        workflow_template_id: '',
        title: '',
        description: '',
        amount: '',
        details: {
            category: '',
            expense_date: '',
            merchant: '',
            receipt_reference: '',
            notes: '',
            asset_category: '',
            urgency: '',
            needed_by: '',
            preferred_model: '',
            business_reason: '',
            field_name: '',
            current_value: '',
            requested_value: '',
            effective_from: '',
            reason: '',
            change_type: '',
            requested_salary: '',
            justification: '',
            last_working_day: '',
            exit_type: '',
            handover_owner: '',
        },
    },
    templateForm: {
        id: null,
        type: 'reimbursement',
        name: '',
        description: '',
        default_title: '',
        default_description: '',
        approval_steps: [
            { role: 'manager', label: 'Manager Review' },
            { role: 'hr_manager', label: 'HR Approval' },
        ],
        is_active: true,
    },
    fulfillForm: {
        asset_id: '',
        comment: '',
    },
    modals: {
        create: false,
        decision: false,
        timeline: false,
        fulfill: false,
        templates: false,
    },
    toast: { show: false, message: '', type: 'success' },
    summaryCards: [
        { key: 'pending', label: 'Pending', hint: 'Open', tone: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300' },
        { key: 'awaiting_my_approval', label: 'Awaiting Me', hint: 'Action', tone: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-300' },
        { key: 'approved', label: 'Approved', hint: 'Done', tone: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' },
        { key: 'fulfilled', label: 'Fulfilled', hint: 'Closed', tone: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-300' },
        { key: 'rejected', label: 'Rejected', hint: 'Closed', tone: 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300' },
    ],

    async init() {
        await this.fetchRequests();
        this.handleDeepLink();
    },

    async fetchRequests() {
        this.loading = true;
        try {
            const { data } = await axios.get(this.dataUrl, { params: this.filters });
            this.requests = data.requests || [];
            this.summary = data.summary || this.summary;
            this.canCreate = data.canCreate !== false;
            this.availableAssets = data.availableAssets || [];
            this.templates = data.templates || [];
            this.isAdmin = data.isAdmin === true;
        } catch (error) {
            this.showToast(error.response?.data?.message || 'Failed to load workflows.', 'error');
        } finally {
            this.loading = false;
        }
    },

    openCreateModal() {
        this.resetCreateForm();
        this.attachmentFile = null;
        this.attachmentName = '';
        this.formErrors = [];
        this.formMode = 'create';
        this.editingWorkflowId = null;
        this.modals.create = true;
    },

    resetCreateForm() {
        this.form = {
            type: 'reimbursement',
            workflow_template_id: '',
            title: '',
            description: '',
            amount: '',
            details: this.emptyDetails(),
        };
    },

    openDecisionModal(item, mode) {
        this.selectedRequest = item;
        this.decisionMode = mode;
        this.decisionComment = '';
        this.decisionError = '';
        this.modals.decision = true;
    },

    openTemplateModal() {
        this.resetTemplateForm();
        this.templateErrors = [];
        this.modals.templates = true;
    },

    openFulfillModal(item) {
        this.selectedRequest = item;
        this.fulfillForm = {
            asset_id: '',
            comment: '',
        };
        this.fulfillError = '';
        this.modals.fulfill = true;
    },

    async openTimeline(id) {
        try {
            const { data } = await axios.get(`${this.showUrlBase}/${id}`);
            this.selectedRequest = data.request;
            this.timeline = data.request.timeline || [];
            this.fulfillmentAsset = data.request.fulfilled_asset || null;
            const hiddenDetailKeys = ['fulfilled_asset_id', 'fulfilled_asset_name', 'fulfillment_note', 'fulfilled_at'];
            this.detailsEntries = Object.entries(data.request.details || {})
                .filter(([key]) => !hiddenDetailKeys.includes(key))
                .map(([key, value]) => ({
                key: key.replace(/_/g, ' '),
                value: Array.isArray(value) ? value.join(', ') : (typeof value === 'object' && value !== null ? JSON.stringify(value) : String(value)),
                }));
            this.modals.timeline = true;
        } catch (error) {
            this.showToast(error.response?.data?.message || 'Failed to load timeline.', 'error');
        }
    },

    closeModal(key) {
        this.modals[key] = false;
        if (key === 'decision') {
            this.decisionComment = '';
            this.decisionError = '';
        }
        if (key === 'timeline') {
            this.timeline = [];
            this.detailsEntries = [];
            this.fulfillmentAsset = null;
        }
        if (key === 'fulfill') {
            this.fulfillForm = {
                asset_id: '',
                comment: '',
            };
            this.fulfillError = '';
        }
        if (key === 'templates') {
            this.templateErrors = [];
        }
        if (key === 'create') {
            this.formMode = 'create';
            this.editingWorkflowId = null;
        }
    },

    handleAttachment(event) {
        const [file] = event.target.files || [];
        this.attachmentFile = file || null;
        this.attachmentName = file ? file.name : '';
    },

    handleTypeChange() {
        this.form.workflow_template_id = '';
        this.form.amount = this.form.type === 'reimbursement' ? this.form.amount : '';
        this.form.details = this.emptyDetails();
    },

    filteredTemplates() {
        return this.templates.filter(template => template.type === this.form.type && template.is_active);
    },

    applySelectedTemplate() {
        const template = this.templates.find(item => String(item.id) === String(this.form.workflow_template_id));
        if (!template) {
            return;
        }

        if (template.type !== this.form.type) {
            this.form.workflow_template_id = '';
            return;
        }

        if (!this.form.title && template.default_title) {
            this.form.title = template.default_title;
        }

        if (!this.form.description && template.default_description) {
            this.form.description = template.default_description;
        }
    },

    async submitRequest() {
        this.saving = true;
        this.formErrors = [];
        try {
            const payload = new FormData();
            payload.append('type', this.form.type);
            if (this.form.workflow_template_id) payload.append('workflow_template_id', this.form.workflow_template_id);
            payload.append('title', this.form.title);
            if (this.form.description) payload.append('description', this.form.description);
            if (this.form.amount !== '') payload.append('amount', this.form.amount);

            Object.entries(this.normalizedDetails()).forEach(([key, value]) => {
                payload.append(`details[${key}]`, value);
            });

            if (this.attachmentFile) {
                payload.append('attachment', this.attachmentFile);
            }

            const endpoint = this.formMode === 'resubmit' && this.editingWorkflowId
                ? `${this.resubmitUrlBase}/${this.editingWorkflowId}/resubmit`
                : this.storeUrl;

            const { data } = await axios.post(endpoint, payload, {
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'multipart/form-data',
                }
            });

            this.requests.unshift(data.request);
            this.closeModal('create');
            this.showToast(data.message || 'Request submitted.', 'success');
            await this.fetchRequests();
        } catch (error) {
            this.formErrors = this.extractErrors(error, 'Failed to submit request.');
        } finally {
            this.saving = false;
        }
    },

    async submitDecision() {
        this.savingDecision = true;
        this.decisionError = '';
        try {
            if (this.decisionMode === 'reject' && !this.decisionComment.trim()) {
                this.decisionError = 'Rejection reason is required.';
                this.savingDecision = false;
                return;
            }

            const base = this.decisionMode === 'approve' ? this.approveUrlBase : this.rejectUrlBase;
            const endpoint = `${base}/${this.selectedRequest.id}/${this.decisionMode}`;
            const payload = { comment: this.decisionComment || null };
            const { data } = await axios.post(endpoint, payload, {
                headers: { Accept: 'application/json' }
            });

            const index = this.requests.findIndex(item => item.id === data.request.id);
            if (index !== -1) {
                this.requests.splice(index, 1, data.request);
            }

            this.closeModal('decision');
            this.showToast(data.message || 'Decision saved.', 'success');
            await this.fetchRequests();
        } catch (error) {
            this.decisionError = this.extractErrors(error, 'Failed to save decision.').join(' ');
        } finally {
            this.savingDecision = false;
        }
    },

    async submitFulfill() {
        this.savingFulfill = true;
        this.fulfillError = '';

        try {
            if (!this.fulfillForm.asset_id) {
                this.fulfillError = 'Select an available asset to continue.';
                this.savingFulfill = false;
                return;
            }

            const endpoint = `${this.fulfillUrlBase}/${this.selectedRequest.id}/fulfill-asset`;
            const payload = {
                asset_id: this.fulfillForm.asset_id,
                comment: this.fulfillForm.comment || null,
            };

            const { data } = await axios.post(endpoint, payload, {
                headers: { Accept: 'application/json' }
            });

            const index = this.requests.findIndex(item => item.id === data.request.id);
            if (index !== -1) {
                this.requests.splice(index, 1, data.request);
            }

            this.closeModal('fulfill');
            this.showToast(data.message || 'Asset request fulfilled.', 'success');
            await this.fetchRequests();
        } catch (error) {
            this.fulfillError = this.extractErrors(error, 'Failed to fulfill asset request.').join(' ');
        } finally {
            this.savingFulfill = false;
        }
    },

    async cancelRequest(item) {
        this.cancellingRequestId = item.id;
        try {
            const { data } = await axios.post(`${this.cancelUrlBase}/${item.id}/cancel`, {}, {
                headers: { Accept: 'application/json' }
            });

            const index = this.requests.findIndex(request => request.id === data.request.id);
            if (index !== -1) {
                this.requests.splice(index, 1, data.request);
            }

            this.showToast(data.message || 'Request cancelled.', 'success');
            await this.fetchRequests();
        } catch (error) {
            this.showToast(this.extractErrors(error, 'Failed to cancel request.').join(' '), 'error');
        } finally {
            this.cancellingRequestId = null;
        }
    },

    async openResubmitModal(id) {
        try {
            const { data } = await axios.get(`${this.showUrlBase}/${id}`);
            const request = data.request;
            this.resetCreateForm();
            this.formMode = 'resubmit';
            this.editingWorkflowId = request.id;
            this.form.type = request.type;
            this.form.workflow_template_id = request.template_id ? String(request.template_id) : '';
            this.form.title = request.title || '';
            this.form.description = request.description || '';
            this.form.amount = request.amount_value ?? '';
            this.form.details = { ...this.emptyDetails(), ...(request.details || {}) };
            this.attachmentFile = null;
            this.attachmentName = request.attachment?.name || '';
            this.formErrors = [];
            this.modals.create = true;
        } catch (error) {
            this.showToast(this.extractErrors(error, 'Failed to open resubmit form.').join(' '), 'error');
        }
    },

    resetTemplateForm() {
        this.templateForm = {
            id: null,
            type: 'reimbursement',
            name: '',
            description: '',
            default_title: '',
            default_description: '',
            approval_steps: [
                { role: 'manager', label: 'Manager Review' },
                { role: 'hr_manager', label: 'HR Approval' },
            ],
            is_active: true,
        };
    },

    editTemplate(template) {
        this.templateForm = {
            id: template.id,
            type: template.type,
            name: template.name || '',
            description: template.description || '',
            default_title: template.default_title || '',
            default_description: template.default_description || '',
            approval_steps: (template.approval_steps || []).length
                ? template.approval_steps.map(step => ({ role: step.role || 'manager', label: step.label || '' }))
                : [{ role: 'manager', label: 'Manager Review' }],
            is_active: template.is_active === true,
        };
        this.templateErrors = [];
        this.modals.templates = true;
    },

    addTemplateStep() {
        this.templateForm.approval_steps.push({ role: 'manager', label: '' });
    },

    removeTemplateStep(index) {
        this.templateForm.approval_steps.splice(index, 1);
        if (!this.templateForm.approval_steps.length) {
            this.addTemplateStep();
        }
    },

    async submitTemplate() {
        this.savingTemplate = true;
        this.templateErrors = [];

        try {
            const payload = {
                type: this.templateForm.type,
                name: this.templateForm.name,
                description: this.templateForm.description || null,
                default_title: this.templateForm.default_title || null,
                default_description: this.templateForm.default_description || null,
                approval_steps: this.templateForm.approval_steps,
                is_active: this.templateForm.is_active ? 1 : 0,
            };

            const endpoint = this.templateForm.id
                ? `${this.templateUpdateBase}/${this.templateForm.id}`
                : this.templateStoreUrl;

            const method = this.templateForm.id ? 'patch' : 'post';
            const { data } = await axios[method](endpoint, payload, {
                headers: { Accept: 'application/json' }
            });

            const index = this.templates.findIndex(item => item.id === data.template.id);
            if (index === -1) {
                this.templates.unshift(data.template);
            } else {
                this.templates.splice(index, 1, data.template);
            }

            this.showToast(data.message || 'Template saved.', 'success');
            this.resetTemplateForm();
            await this.fetchRequests();
        } catch (error) {
            this.templateErrors = this.extractErrors(error, 'Failed to save template.');
        } finally {
            this.savingTemplate = false;
        }
    },

    async archiveTemplate(template) {
        this.archivingTemplateId = template.id;
        try {
            const { data } = await axios.post(`${this.templateArchiveBase}/${template.id}/archive`, {}, {
                headers: { Accept: 'application/json' }
            });

            const index = this.templates.findIndex(item => item.id === data.template.id);
            if (index !== -1) {
                this.templates.splice(index, 1, data.template);
            }

            this.showToast(data.message || 'Template archived.', 'success');
            await this.fetchRequests();
        } catch (error) {
            this.templateErrors = this.extractErrors(error, 'Failed to archive template.');
        } finally {
            this.archivingTemplateId = null;
        }
    },

    handleDeepLink() {
        const params = new URLSearchParams(window.location.search);
        const workflowId = params.get('workflow');
        const modal = params.get('modal');
        const action = params.get('action');

        if (!workflowId) {
            return;
        }

        if (modal === 'timeline') {
            this.openTimeline(workflowId);
        } else if (action === 'resubmit') {
            this.openResubmitModal(workflowId);
        }

        params.delete('workflow');
        params.delete('modal');
        params.delete('action');
        const nextQuery = params.toString();
        const nextUrl = `${window.location.pathname}${nextQuery ? `?${nextQuery}` : ''}`;
        window.history.replaceState({}, '', nextUrl);
    },

    assetOptionLabel(asset) {
        const parts = [asset.name];
        if (asset.category) {
            parts.push(asset.category.replace(/-/g, ' '));
        }
        if (asset.serial_number) {
            parts.push(asset.serial_number);
        }

        return parts.join(' - ');
    },

    emptyDetails() {
        return {
            category: '',
            expense_date: '',
            merchant: '',
            receipt_reference: '',
            notes: '',
            asset_category: '',
            urgency: '',
            needed_by: '',
            preferred_model: '',
            business_reason: '',
            field_name: '',
            current_value: '',
            requested_value: '',
            effective_from: '',
            reason: '',
            change_type: '',
            requested_salary: '',
            justification: '',
            last_working_day: '',
            exit_type: '',
            handover_owner: '',
        };
    },

    statusTone(status) {
        const tones = {
            pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
            approved: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
            fulfilled: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-300',
            cancelled: 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
            rejected: 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
        };

        return tones[status] || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
    },

    decisionTone(decision) {
        const tones = {
            pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
            approved: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
            rejected: 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
            cancelled: 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
        };

        return tones[decision] || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
    },

    extractErrors(error, fallback) {
        if (error.response?.status === 422 && error.response.data?.errors) {
            return Object.values(error.response.data.errors).flat();
        }

        return [error.response?.data?.message || fallback];
    },

    normalizedDetails() {
        const details = this.form.details || {};

        return Object.fromEntries(
            Object.entries(details).filter(([, value]) => value !== null && value !== '')
        );
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        clearTimeout(this.toastTimeout);
        this.toastTimeout = setTimeout(() => {
            this.toast.show = false;
        }, 3000);
    },
}));

Alpine.start();
