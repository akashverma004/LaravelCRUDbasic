import './bootstrap';

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

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

    loading: true,
    errors: {},

    async init() {
        await this.fetchProfile();
    },

    async fetchProfile() {
        this.loading = true;
        try {
            const { data } = await axios.get('/self-service/profile/data');
            this.employee = data.employee;
            this.photoUrl = data.employee.profile_photo;
            if (data.user) {
                this.accountForm = { name: data.user.name || '', email: data.user.email || '' };
            }
        } catch (e) {
            this.showToast('Failed to load profile.', 'error');
        } finally {
            this.loading = false;
        }
    },

    startEditing() {
        console.log('Starting edit mode for employee:', this.employee);
        if (!this.employee) return;
        
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
        this.errors = {};
    },

    async saveProfile() {
        this.saving = true;
        this.errors = {};
        try {
            const { data } = await axios.patch('/self-service/profile/info', this.form, {
                headers: { 'Accept': 'application/json' }
            });
            Object.assign(this.employee, this.form);
            this.editing = false;
            this.showToast(data.message, 'success');
        } catch (e) {
            if (e.response?.status === 422) {
                this.errors = e.response.data.errors || {};
            }
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
        this.errors = {};
        try {
            const { data } = await axios.post('/self-service/profile/educations', this.eduForm);
            if (!this.employee.educations) this.employee.educations = [];
            this.employee.educations.push(data.education);
            this.eduForm = { degree: '', institution: '', field_of_study: '', year_from: '', year_to: '' };
            this.showEduForm = false;
            this.showToast(data.message, 'success');
        } catch (e) {
            if (e.response?.status === 422) {
                this.errors = e.response.data.errors || {};
            }
            this.showToast(e.response?.data?.message || 'Failed to add education.', 'error');
        }
    },

    editEducation(edu) {
        this.eduForm = { ...edu };
        this.showEduForm = true;
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
        this.errors = {};
        try {
            const { data } = await axios.post('/self-service/profile/experiences', this.expForm);
            if (!this.employee.experiences) this.employee.experiences = [];
            this.employee.experiences.push(data.experience);
            this.expForm = { company: '', designation: '', from_date: '', to_date: '', description: '' };
            this.showExpForm = false;
            this.showToast(data.message, 'success');
        } catch (e) {
            if (e.response?.status === 422) {
                this.errors = e.response.data.errors || {};
            }
            this.showToast(e.response?.data?.message || 'Failed to add experience.', 'error');
        }
    },

    editExperience(exp) {
        this.expForm = { ...exp };
        this.showExpForm = true;
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
        this.errors = {};
        try {
            const { data } = await axios.patch('/self-service/profile/account', this.accountForm);
            this.showToast(data.message, 'success');
        } catch (e) {
            if (e.response?.status === 422) {
                this.errors = e.response.data.errors || {};
            }
            const msg = e.response?.data?.message || 'Failed to update account.';
            this.showToast(msg, 'error');
        } finally {
            this.savingAccount = false;
        }
    },

    async updatePassword() {
        this.savingPassword = true;
        this.errors = {};
        try {
            const { data } = await axios.put('/self-service/profile/password', this.passwordForm);
            this.passwordForm = { current_password: '', password: '', password_confirmation: '' };
            this.showToast(data.message, 'success');
        } catch (e) {
            if (e.response?.status === 422) {
                this.errors = e.response.data.errors || {};
            }
            const msg = e.response?.data?.message || 'Failed to update password.';
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
    employees: [],
    isManager: false,
    loading: true,
    saving: false,
    toast: { show: false, message: '', type: 'success' },
    showGoalForm: false,
    showReviewForm: false,
    showNoteForm: false,
    
    goalForm: { title: '', description: '', due_date: '', priority: 'medium', employee_id: '' },
    reviewForm: { employee_id: '', review_cycle: '', rating: 3, feedback: '', strengths: '', areas_for_improvement: '', status: 'submitted' },
    noteForm: { employee_id: '', meeting_date: new Date().toISOString().split('T')[0], talking_points: '', action_items: '', private_notes: '' },

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
            this.employees = data.employees;
            this.isManager = data.is_manager;
        } catch (e) {
            this.showToast('Failed to load performance data.', 'error');
        } finally {
            this.loading = false;
        }
    },

    async saveGoal() {
        this.saving = true;
        try {
            await axios.post('/performance/goals', this.goalForm);
            this.showGoalForm = false;
            this.goalForm = { title: '', description: '', due_date: '', priority: 'medium', employee_id: '' };
            this.showToast('Goal added successfully!');
            await this.fetchData();
        } catch (e) {
            this.showToast('Failed to save goal.', 'error');
        } finally {
            this.saving = false;
        }
    },

    async saveReview() {
        this.saving = true;
        try {
            await axios.post('/performance/reviews', this.reviewForm);
            this.showReviewForm = false;
            this.reviewForm = { employee_id: '', review_cycle: '', rating: 3, feedback: '', strengths: '', areas_for_improvement: '', status: 'submitted' };
            this.showToast('Review submitted successfully!');
            await this.fetchData();
        } catch (e) {
            this.showToast('Failed to submit review.', 'error');
        } finally {
            this.saving = false;
        }
    },

    async saveNote() {
        this.saving = true;
        try {
            await axios.post('/performance/notes', this.noteForm);
            this.showNoteForm = false;
            this.noteForm = { employee_id: '', meeting_date: new Date().toISOString().split('T')[0], talking_points: '', action_items: '', private_notes: '' };
            this.showToast('Note logged successfully!');
            await this.fetchData();
        } catch (e) {
            this.showToast('Failed to log note.', 'error');
        } finally {
            this.saving = false;
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
    saving: false,
    toast: { show: false, message: '', type: 'success' },

    balances: {},
    whoIsAway: { today: [], upcoming: [] },
    stats: { pending: 0, approved: 0 },

    async init() {
        await this.fetchData();
    },

    async fetchData() {
        this.loading = true;
        try {
            const { data } = await axios.get('/leaves/data');
            this.leaves = data.leaves || [];
            this.employees = data.employees || [];
            this.isAdmin = data.isAdmin === true;
            this.balances = data.balances || {};
            this.whoIsAway = data.whoIsAway || { today: [], upcoming: [] };
            this.stats = data.stats || { pending: 0, approved: 0 };
        } catch (e) {
            console.error('Failed to load leaves', e);
            this.showToast(e.response?.data?.message || 'Failed to load leave dashboard.', 'error');
        } finally {
            this.loading = false;
        }
    },

    openModal() {
        const defaultEmployeeId = this.isAdmin ? '' : (this.employees[0]?.id ?? '');
        this.isEditing = false;
        this.editLeaveId = null;
        this.form = {
            employee_id: defaultEmployeeId,
            leave_type: 'annual',
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
            start_date: leave.start_date,
            end_date: leave.end_date,
            reason: leave.reason,
            status: leave.status
        };
        this.showModal = true;
    },

    async saveLeave() {
        this.saving = true;
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
            this.showToast(
                e.response?.data?.message ||
                e.response?.data?.error ||
                Object.values(e.response?.data?.errors || {}).flat()[0] ||
                'Failed to save leave request.',
                'error'
            );
        } finally {
            this.saving = false;
        }
    },

    async deleteLeave(id) {
        if (!confirm('Are you sure you want to cancel this leave request?')) return;
        try {
            await axios.delete(`/leaves/${id}`);
            this.showToast('Leave request cancelled successfully!');
            await this.fetchData();
        } catch (e) {
            this.showToast(e.response?.data?.message || e.response?.data?.error || 'Failed to cancel leave request.', 'error');
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
    },

    usagePercent(balance) {
        const limit = Number(balance?.limit || 0);
        const used = Number(balance?.used || 0);

        if (limit <= 0) {
            return 0;
        }

        return Math.min(100, Math.round((used / limit) * 100));
    }
}));

// ── Asset Manager Component ──────────────────────────────────────────
Alpine.data('assetManager', (config = {}) => ({
    loading: true,
    isAdmin: false,
    assets: [],
    categories: {},
    employees: [],
    workflowsUrl: config.workflowsUrl || '/workflows',
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

    openWorkflowRequest() {
        window.location.href = `${this.workflowsUrl}?action=new&type=asset-request`;
    },

    openReturnWorkflow(asset) {
        window.location.href = this.buildWorkflowUrl('asset-return', asset);
    },

    openRepairWorkflow(asset) {
        window.location.href = this.buildWorkflowUrl('asset-repair', asset);
    },

    buildWorkflowUrl(type, asset) {
        const params = new URLSearchParams({
            action: 'new',
            type,
            asset: String(asset.id),
            asset_name: asset.name || '',
            asset_category: asset.category || '',
            serial_number: asset.serial_number || '',
        });

        return `${this.workflowsUrl}?${params.toString()}`;
    },
}));

// ── Payroll Manager Component ──────────────────────────────────────
Alpine.data('payrollManager', () => ({
    loading: true,
    isAdmin: false,
    activeTab: 'payslips',
    payslips: [],
    employees: [],           // ALL employees, each has .pay_structure (or null)
    stats: { totalEmployees: 0, totalPayroll: 0, draftCount: 0, paidCount: 0 },
    showGenerateModal: false,
    showStructureModal: false,
    toast: { show: false, message: '', type: 'success' },
    generating: false,
    generateMonth: '',
    structureForm: { employee_id: '', base_salary: '', allowances: [], deductions: [] },
    structureModalEmployee: null,  // the employee being configured
    searchQuery: '',
    employeeSearch: '',
    selectedPayslip: null,
    showDetailsModal: false,

    async init() {
        await this.fetchData();
    },

    async fetchData() {
        this.loading = true;
        try {
            const { data } = await axios.get('/payroll/data');
            this.isAdmin = data.isAdmin;
            this.payslips = data.payslips || [];
            if (this.isAdmin) {
                this.employees = data.employees || [];
                this.stats = data.stats || this.stats;
            }
        } catch (e) {
            console.error('Failed to load payroll', e);
        } finally {
            this.loading = false;
        }
    },

    get filteredPayslips() {
        if (!this.searchQuery.trim()) return this.payslips;
        const q = this.searchQuery.toLowerCase();
        return this.payslips.filter(ps =>
            (ps.month || '').toLowerCase().includes(q) ||
            (ps.employee?.full_name || '').toLowerCase().includes(q)
        );
    },

    formatCurrency(val) {
        return '₹ ' + parseFloat(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    },

    // ── Structure CRUD ─────────────────────────────────────────
    // Opens modal to SET UP salary for an employee without one
    openSetupStructure(emp) {
        this.structureModalEmployee = emp;
        this.structureForm = { employee_id: emp.id, base_salary: '', allowances: [], deductions: [] };
        this.showStructureModal = true;
    },

    // Opens modal to EDIT an existing salary structure
    openEditStructure(emp) {
        const s = emp.pay_structure;
        this.structureModalEmployee = emp;
        this.structureForm = {
            employee_id: emp.id,
            base_salary: s.base_salary,
            allowances: (s.allowances || []).map(a => ({ ...a })),
            deductions: (s.deductions || []).map(d => ({ ...d })),
        };
        this.showStructureModal = true;
    },

    addAllowance() {
        this.structureForm.allowances.push({ name: '', amount: '' });
    },
    removeAllowance(i) {
        this.structureForm.allowances.splice(i, 1);
    },
    addDeduction() {
        this.structureForm.deductions.push({ name: '', amount: '' });
    },
    removeDeduction(i) {
        this.structureForm.deductions.splice(i, 1);
    },

    async saveStructure() {
        try {
            const payload = {
                ...this.structureForm,
                allowances: this.structureForm.allowances.filter(a => a.name && a.amount),
                deductions: this.structureForm.deductions.filter(d => d.name && d.amount),
            };
            // Always POST to storeStructure — backend uses updateOrCreate
            await axios.post('/payroll/structures', payload);
            this.showStructureModal = false;
            this.showToast('Salary structure saved!');
            await this.fetchData();
        } catch (e) {
            const msg = e.response?.data?.message
                || Object.values(e.response?.data?.errors || {}).flat()[0]
                || 'Failed to save structure.';
            this.showToast(msg, 'error');
        }
    },

    async deleteStructure(emp) {
        const s = emp.pay_structure;
        if (!s) return;
        if (!confirm(`Remove salary structure for ${emp.full_name}? They will no longer be on payroll.`)) return;
        try {
            await axios.delete(`/payroll/structures/${s.id}`);
            this.showToast('Structure removed.');
            await this.fetchData();
        } catch (e) {
            this.showToast('Failed to remove structure.', 'error');
        }
    },

    get filteredEmployees() {
        if (!this.employeeSearch.trim()) return this.employees;
        const q = this.employeeSearch.toLowerCase();
        return this.employees.filter(e =>
            (e.full_name || '').toLowerCase().includes(q) ||
            (e.job_title || '').toLowerCase().includes(q)
        );
    },

    // ── Payslip actions ────────────────────────────────────────
    async markAsPaid(ps) {
        if (!confirm('Mark this payslip as paid?')) return;
        try {
            await axios.post(`/payroll/payslips/${ps.id}/pay`);
            ps.status = 'paid';
            this.stats.draftCount = Math.max(0, this.stats.draftCount - 1);
            this.stats.paidCount++;
            this.stats.totalPayroll = parseFloat(this.stats.totalPayroll) + parseFloat(ps.net_pay);
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
            
            // Show toast for a longer time so user reads "queued"
            this.toast = { show: true, message: data.message, type: 'success' };
            setTimeout(() => { this.toast.show = false; }, 6000);
            
            // We don't fetch data immediately since it's queued in the background.
        } catch (e) {
            this.showToast('Failed to queue payroll generation.', 'error');
        } finally {
            this.generating = false;
        }
    },

    viewPayslip(ps) {
        this.selectedPayslip = ps;
        this.showDetailsModal = true;
    },

    async sendPayslip(ps) {
        if (!confirm(`Send payslip for ${ps.month} to the employee's email?`)) return;
        try {
            const { data } = await axios.post(`/payroll/payslips/${ps.id}/send`);
            this.showToast(data.message || 'Payslip emailed successfully! ✉️');
        } catch (e) {
            const msg = e.response?.data?.message || 'Failed to send payslip email.';
            this.showToast(msg, 'error');
        }
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

Alpine.data('employeeDirectory', (config = {}) => ({
    dataUrl: config.dataUrl || '/employees/data',
    storeUrl: config.storeUrl || '/employees',
    deleteUrlBase: config.deleteUrlBase || '/employees',
    restoreUrlBase: config.restoreUrlBase || '/employees',
    storageBase: config.storageBase || '/storage',
    loading: false,
    saving: false,
    deletingId: null,
    showCreateModal: false,
    employees: [],
    meta: { current_page: 1, last_page: 1, total: 0 },
    filters: {
        q: config.filters?.q || '',
        department_id: config.filters?.department_id || '',
        role_id: config.filters?.role_id || '',
        page: 1,
    },
    departments: config.departments || [],
    roles: config.roles || [],
    managers: config.managers || [],
    countries: config.countries || [],
    states: config.states || [],
    formErrors: [],
    toast: { show: false, message: '', type: 'success' },
    form: {},

    init() {
        this.resetForm();
        this.fetchData();
    },

    resetForm() {
        this.form = {
            full_name: '',
            email: '',
            password: '',
            phone: '',
            job_title: '',
            department_id: '',
            manager_id: '',
            role_id: '',
            employment_type: 'full-time',
            salary: '',
            joined_on: new Date().toISOString().split('T')[0],
            status: 'active',
            country: 'IN',
            state: '',
            city: '',
            address: '',
            zip_code: '',
            personal_email: '',
            date_of_birth: '',
            gender: '',
            blood_group: '',
            marital_status: '',
            bio: '',
            emergency_contact_name: '',
            emergency_contact_phone: '',
            emergency_contact_relationship: '',
            pan_number: '',
            aadhaar_number: '',
            bank_name: '',
            bank_account_number: '',
            bank_ifsc: '',
        };
    },

    async fetchData(page = null) {
        this.loading = true;
        if (page) {
            this.filters.page = page;
        }

        try {
            const { data } = await axios.get(this.dataUrl, { params: this.filters });
            this.employees = data.employees || [];
            this.meta = data.meta || this.meta;
        } catch (error) {
            this.showToast('Failed to load employees.', 'error');
        } finally {
            this.loading = false;
        }
    },

    openCreateModal() {
        this.resetForm();
        this.formErrors = [];
        this.showCreateModal = true;
    },

    closeCreateModal() {
        this.showCreateModal = false;
        this.formErrors = [];
    },

    async submitCreate() {
        this.saving = true;
        this.formErrors = [];

        try {
            await axios.post(this.storeUrl, this.form, {
                headers: { Accept: 'application/json' }
            });
            this.closeCreateModal();
            this.showToast('Employee created successfully.');
            this.fetchData(1);
        } catch (error) {
            this.formErrors = this.extractErrors(error, 'Failed to create employee.');
        } finally {
            this.saving = false;
        }
    },

    async deleteEmployee(employee) {
        if (!confirm(`Archive ${employee.full_name}?`)) return;

        this.deletingId = employee.id;
        try {
            await axios.delete(`${this.deleteUrlBase}/${employee.id}`, {
                headers: { Accept: 'application/json' }
            });
            this.showToast('Employee archived successfully.');
            await this.fetchData();
        } catch (error) {
            this.showToast(this.extractErrors(error, 'Failed to archive employee.').join(' '), 'error');
        } finally {
            this.deletingId = null;
        }
    },

    async restoreEmployee(employee) {
        if (!confirm(`Unarchive ${employee.full_name}?`)) return;

        this.deletingId = employee.id;
        try {
            await axios.patch(`${this.restoreUrlBase}/${employee.id}/restore`, {}, {
                headers: { Accept: 'application/json' }
            });
            this.showToast('Employee unarchived successfully.');
            await this.fetchData(this.meta.current_page || 1);
        } catch (error) {
            this.showToast(this.extractErrors(error, 'Failed to unarchive employee.').join(' '), 'error');
        } finally {
            this.deletingId = null;
        }
    },

    avatarUrl(employee) {
        return employee.profile_photo ? `${this.storageBase}/${employee.profile_photo}` : null;
    },

    statusTone(status) {
        return {
            active: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
            'on-leave': 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
            resigned: 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
        }[status] || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },

    extractErrors(error, fallback) {
        if (error.response?.status === 422 && error.response.data?.errors) {
            return Object.values(error.response.data.errors).flat();
        }

        return [error.response?.data?.message || fallback];
    },
}));

Alpine.data('departmentDirectory', (config = {}) => ({
    dataUrl: config.dataUrl || '/departments/data',
    storeUrl: config.storeUrl || '/departments',
    deleteUrlBase: config.deleteUrlBase || '/departments',
    canManage: config.canManage || false,
    employees: config.employees || [],
    loading: false,
    saving: false,
    deletingId: null,
    showCreateModal: false,
    departments: [],
    formErrors: [],
    toast: { show: false, message: '', type: 'success' },
    form: {
        name: '',
        code: '',
        lead_employee_id: '',
        lead_name: '',
    },

    init() {
        this.fetchData();
    },

    async fetchData() {
        this.loading = true;
        try {
            const { data } = await axios.get(this.dataUrl);
            this.departments = data.departments || [];
        } catch (error) {
            this.showToast('Failed to load departments.', 'error');
        } finally {
            this.loading = false;
        }
    },

    openCreateModal() {
        this.form = { name: '', code: '', lead_employee_id: '', lead_name: '' };
        this.formErrors = [];
        this.showCreateModal = true;
    },

    closeCreateModal() {
        this.showCreateModal = false;
        this.formErrors = [];
    },

    async submitCreate() {
        this.saving = true;
        this.formErrors = [];

        try {
            await axios.post(this.storeUrl, this.form, {
                headers: { Accept: 'application/json' }
            });
            this.closeCreateModal();
            this.showToast('Department created successfully.');
            await this.fetchData();
        } catch (error) {
            this.formErrors = this.extractErrors(error, 'Failed to create department.');
        } finally {
            this.saving = false;
        }
    },

    async deleteDepartment(department) {
        if (!confirm(`Delete ${department.name}?`)) return;

        this.deletingId = department.id;
        try {
            await axios.delete(`${this.deleteUrlBase}/${department.id}`, {
                headers: { Accept: 'application/json' }
            });
            this.showToast('Department deleted successfully.');
            await this.fetchData();
        } catch (error) {
            this.showToast(this.extractErrors(error, 'Failed to delete department.').join(' '), 'error');
        } finally {
            this.deletingId = null;
        }
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },

    extractErrors(error, fallback) {
        if (error.response?.status === 422 && error.response.data?.errors) {
            return Object.values(error.response.data.errors).flat();
        }

        return [error.response?.data?.message || fallback];
    },
}));

Alpine.data('tenantUserManager', (config = {}) => ({
    dataUrl: config.dataUrl || '/tenant-users/data',
    storeUrl: config.storeUrl || '/tenant-users/create',
    inviteUrl: config.inviteUrl || '/tenant-users/invite',
    loading: false,
    savingUser: false,
    savingInvite: false,
    users: [],
    invitations: [],
    roles: [],
    meta: { current_page: 1, last_page: 1, total: 0 },
    showCreateModal: false,
    showInviteModal: false,
    userErrors: [],
    inviteErrors: [],
    toast: { show: false, message: '', type: 'success' },
    userForm: { name: '', email: '', password: '', role_name: '' },
    inviteForm: { name: '', email: '', role_name: '' },

    init() {
        this.fetchData();
    },

    async fetchData(page = null) {
        this.loading = true;
        try {
            const { data } = await axios.get(this.dataUrl, { params: page ? { page } : {} });
            this.users = data.users || [];
            this.invitations = data.invitations || [];
            this.roles = data.roles || [];
            this.meta = data.meta || this.meta;
            if (!this.userForm.role_name && this.roles.length) this.userForm.role_name = this.roles[0].name;
            if (!this.inviteForm.role_name && this.roles.length) this.inviteForm.role_name = this.roles[0].name;
        } catch (error) {
            this.showToast('Failed to load tenant users.', 'error');
        } finally {
            this.loading = false;
        }
    },

    openCreateModal() {
        this.userForm = { name: '', email: '', password: '', role_name: this.roles[0]?.name || '' };
        this.userErrors = [];
        this.showCreateModal = true;
    },

    openInviteModal() {
        this.inviteForm = { name: '', email: '', role_name: this.roles[0]?.name || '' };
        this.inviteErrors = [];
        this.showInviteModal = true;
    },

    closeModal(type) {
        if (type === 'create') this.showCreateModal = false;
        if (type === 'invite') this.showInviteModal = false;
    },

    async submitUser() {
        this.savingUser = true;
        this.userErrors = [];
        try {
            await axios.post(this.storeUrl, this.userForm, { headers: { Accept: 'application/json' } });
            this.closeModal('create');
            this.showToast('User created successfully.');
            await this.fetchData(1);
        } catch (error) {
            this.userErrors = this.extractErrors(error, 'Failed to create user.');
        } finally {
            this.savingUser = false;
        }
    },

    async submitInvite() {
        this.savingInvite = true;
        this.inviteErrors = [];
        try {
            await axios.post(this.inviteUrl, this.inviteForm, { headers: { Accept: 'application/json' } });
            this.closeModal('invite');
            this.showToast('Invitation created successfully.');
            await this.fetchData();
        } catch (error) {
            this.inviteErrors = this.extractErrors(error, 'Failed to create invitation.');
        } finally {
            this.savingInvite = false;
        }
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },

    extractErrors(error, fallback) {
        if (error.response?.status === 422 && error.response.data?.errors) {
            return Object.values(error.response.data.errors).flat();
        }

        return [error.response?.data?.message || fallback];
    },
}));

Alpine.data('tenantDirectory', (config = {}) => ({
    dataUrl: config.dataUrl || '/platform/tenants/data',
    storeUrl: config.storeUrl || '/platform/tenants',
    updateUrlBase: config.updateUrlBase || '/platform/tenants',
    deleteUrlBase: config.deleteUrlBase || '/platform/tenants',
    loading: false,
    saving: false,
    deletingId: null,
    showModal: false,
    mode: 'create',
    editTenantId: null,
    tenants: [],
    meta: { current_page: 1, last_page: 1, total: 0 },
    filters: {
        q: config.filters?.q || '',
        status: config.filters?.status || '',
        page: 1,
    },
    formErrors: [],
    toast: { show: false, message: '', type: 'success' },
    form: {},

    init() {
        this.resetForm();
        this.fetchData();
    },

    resetForm() {
        this.form = {
            name: '',
            code: '',
            slug: '',
            email: '',
            phone: '',
            address: '',
            country: 'IN',
            timezone: 'Asia/Kolkata',
            currency: 'INR',
            is_active: true,
        };
    },

    async fetchData(page = null) {
        this.loading = true;
        if (page) this.filters.page = page;
        try {
            const { data } = await axios.get(this.dataUrl, { params: this.filters });
            this.tenants = data.tenants || [];
            this.meta = data.meta || this.meta;
        } catch (error) {
            this.showToast('Failed to load tenants.', 'error');
        } finally {
            this.loading = false;
        }
    },

    openCreateModal() {
        this.mode = 'create';
        this.editTenantId = null;
        this.resetForm();
        this.formErrors = [];
        this.showModal = true;
    },

    openEditModal(tenant) {
        this.mode = 'edit';
        this.editTenantId = tenant.id;
        this.form = {
            name: tenant.name || '',
            code: tenant.code || '',
            slug: tenant.slug || '',
            email: tenant.email || '',
            phone: tenant.phone || '',
            address: tenant.address || '',
            country: tenant.country || 'IN',
            timezone: tenant.timezone || 'Asia/Kolkata',
            currency: tenant.currency || 'INR',
            is_active: tenant.is_active === true,
        };
        this.formErrors = [];
        this.showModal = true;
    },

    closeModal() {
        this.showModal = false;
        this.formErrors = [];
    },

    async submitTenant() {
        this.saving = true;
        this.formErrors = [];
        try {
            const endpoint = this.mode === 'edit' && this.editTenantId
                ? `${this.updateUrlBase}/${this.editTenantId}`
                : this.storeUrl;
            const method = this.mode === 'edit' ? 'patch' : 'post';
            await axios[method](endpoint, this.form, { headers: { Accept: 'application/json' } });
            this.closeModal();
            this.showToast(this.mode === 'edit' ? 'Tenant updated successfully.' : 'Tenant created successfully.');
            await this.fetchData(this.mode === 'create' ? 1 : this.meta.current_page);
        } catch (error) {
            this.formErrors = this.extractErrors(error, 'Failed to save tenant.');
        } finally {
            this.saving = false;
        }
    },

    async deleteTenant(tenant) {
        if (!confirm(`Delete ${tenant.name}?`)) return;
        this.deletingId = tenant.id;
        try {
            await axios.delete(`${this.deleteUrlBase}/${tenant.id}`, { headers: { Accept: 'application/json' } });
            this.showToast('Tenant deleted successfully.');
            await this.fetchData();
        } catch (error) {
            this.showToast(this.extractErrors(error, 'Failed to delete tenant.').join(' '), 'error');
        } finally {
            this.deletingId = null;
        }
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },

    extractErrors(error, fallback) {
        if (error.response?.status === 422 && error.response.data?.errors) {
            return Object.values(error.response.data.errors).flat();
        }

        return [error.response?.data?.message || fallback];
    },
}));

Alpine.data('leaveReviewManager', (config = {}) => ({
    dataUrl: config.dataUrl || '/leaves/pending/data',
    approveUrlBase: config.approveUrlBase || '/leaves',
    rejectUrlBase: config.rejectUrlBase || '/leaves',
    loading: false,
    processingId: null,
    tab: config.tab || 'pending',
    leaves: [],
    meta: { current_page: 1, last_page: 1, total: 0 },
    toast: { show: false, message: '', type: 'success' },

    init() {
        this.fetchData();
    },

    async fetchData(page = null) {
        this.loading = true;
        try {
            const params = { tab: this.tab };
            if (page) params.page = page;
            const { data } = await axios.get(this.dataUrl, { params });
            this.leaves = data.leaves || [];
            this.meta = data.meta || this.meta;
        } catch (error) {
            this.showToast('Failed to load leave requests.', 'error');
        } finally {
            this.loading = false;
        }
    },

    async setTab(tab) {
        this.tab = tab;
        await this.fetchData(1);
    },

    async decide(leave, action) {
        this.processingId = leave.id;
        try {
            const endpoint = `${action === 'approve' ? this.approveUrlBase : this.rejectUrlBase}/${leave.id}/${action}`;
            const { data } = await axios.patch(endpoint, {}, { headers: { Accept: 'application/json' } });
            const index = this.leaves.findIndex(item => item.id === leave.id);
            if (index !== -1) this.leaves.splice(index, 1, data.leave);
            this.showToast(data.message || `Leave request ${action}d.`);
            await this.fetchData(this.meta.current_page);
        } catch (error) {
            this.showToast(error.response?.data?.message || `Failed to ${action} leave request.`, 'error');
        } finally {
            this.processingId = null;
        }
    },

    statusTone(status) {
        return {
            pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
            approved: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
            rejected: 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
        }[status] || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

Alpine.data('attendanceCard', (config = {}) => ({
    punchInUrl: config.punchInUrl,
    pauseUrl: config.pauseUrl,
    resumeUrl: config.resumeUrl,
    punchOutUrl: config.punchOutUrl,
    status: config.status || null,
    loading: false,
    flash: { show: false, message: '', type: 'success' },

    async act(url, payload = {}, successMessage = 'Updated.') {
        this.loading = true;
        try {
            const { data } = await axios.post(url, payload, { headers: { Accept: 'application/json' } });
            this.flash = { show: true, message: data.message || successMessage, type: 'success' };
            window.location.reload();
        } catch (error) {
            this.flash = { show: true, message: error.response?.data?.message || 'Action failed.', type: 'error' };
        } finally {
            this.loading = false;
            setTimeout(() => { this.flash.show = false; }, 3000);
        }
    },
}));

Alpine.data('adminDashboardActions', (config = {}) => ({
    departmentStoreUrl: config.departmentStoreUrl,
    assignManagerUrl: config.assignManagerUrl,
    departmentForm: {
        name: '',
        code: '',
        lead_employee_id: '',
    },
    managerForm: {
        employee_id: '',
        manager_id: '',
        effective_date: config.defaultEffectiveDate || '',
    },
    leaveTypeData: config.leaveTypeChartData || { labels: [], values: [] },
    leaveTypeColors: ['#1e40af', '#059669', '#9ca3af', '#92400e'],
    departmentSaving: false,
    managerSaving: false,
    departmentErrors: {},
    managerErrors: {},
    toast: { show: false, message: '', type: 'success' },

    async submitDepartment() {
        this.departmentSaving = true;
        this.departmentErrors = {};

        try {
            const { data } = await axios.post(this.departmentStoreUrl, this.departmentForm, {
                headers: { Accept: 'application/json' },
            });

            this.departmentForm = { name: '', code: '', lead_employee_id: '' };
            this.showToast(data.message || 'Department created successfully.');
        } catch (error) {
            if (error.response?.status === 422) {
                this.departmentErrors = error.response.data.errors || {};
                return;
            }

            this.showToast(error.response?.data?.message || 'Unable to create department.', 'error');
        } finally {
            this.departmentSaving = false;
        }
    },

    async submitManagerAssignment() {
        this.managerSaving = true;
        this.managerErrors = {};

        try {
            const { data } = await axios.post(this.assignManagerUrl, this.managerForm, {
                headers: { Accept: 'application/json' },
            });

            this.showToast(data.message || 'Manager assigned successfully.');
        } catch (error) {
            if (error.response?.status === 422) {
                this.managerErrors = error.response.data.errors || {};
                return;
            }

            this.showToast(error.response?.data?.message || 'Unable to assign manager.', 'error');
        } finally {
            this.managerSaving = false;
        }
    },

    fieldError(errors, field) {
        return errors?.[field]?.[0] || '';
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

Alpine.data('asyncForm', (config = {}) => ({
    saving: false,
    errors: {},
    errorMessage: '',
    toast: { show: false, message: '', type: 'success' },

    async submit() {
        this.saving = true;
        this.errors = {};
        this.errorMessage = '';

        try {
            const form = this.$refs.form;
            const payload = new FormData(form);
            const { data } = await axios.post(config.url || form.action, payload, {
                headers: { Accept: 'application/json' },
            });

            this.showToast(data.message || config.successMessage || 'Saved successfully.');

            if (config.resetOnSuccess) {
                form.reset();
            }

            if (data.redirect && config.followRedirect !== false) {
                window.location.href = data.redirect;
                return;
            }

            if (config.reloadOnSuccess) {
                window.location.reload();
            }
        } catch (error) {
            if (error.response?.status === 422) {
                this.errors = error.response.data.errors || {};
                this.errorMessage = Object.values(this.errors).flat()[0] || 'Please fix the highlighted errors.';
                return;
            }

            this.errorMessage = error.response?.data?.message || 'Unable to save changes.';
            this.showToast(this.errorMessage, 'error');
        } finally {
            this.saving = false;
        }
    },

    firstError(field) {
        return this.errors?.[field]?.[0] || '';
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

Alpine.data('roleManager', (config = {}) => ({
    roles: config.roles || [],
    permissionGroups: config.permissionGroups || [],
    storeUrl: config.storeUrl,
    showModal: false,
    saving: false,
    errorMessage: '',
    toast: { show: false, message: '', type: 'success' },
    form: {
        id: null,
        name: '',
        display_name: '',
        description: '',
        permissions: [],
    },

    openCreate() {
        this.form = { id: null, name: '', display_name: '', description: '', permissions: [] };
        this.errorMessage = '';
        this.showModal = true;
    },

    openEdit(role) {
        this.form = {
            id: role.id,
            name: role.name,
            display_name: role.display_name,
            description: role.description || '',
            permissions: role.permissions.map((permission) => permission.id),
        };
        this.errorMessage = '';
        this.showModal = true;
    },

    closeModal() {
        this.showModal = false;
    },

    async saveRole() {
        this.saving = true;
        this.errorMessage = '';

        try {
            const payload = {
                ...this.form,
                permissions: this.form.permissions,
            };

            let response;
            if (this.form.id) {
                response = await axios.post(`/roles/${this.form.id}`, { ...payload, _method: 'PATCH' }, { headers: { Accept: 'application/json' } });
            } else {
                response = await axios.post(this.storeUrl, payload, { headers: { Accept: 'application/json' } });
            }

            const role = response.data.role;
            if (this.form.id) {
                this.roles = this.roles.map((item) => item.id === role.id ? role : item);
            } else {
                this.roles.unshift(role);
            }

            this.showToast(response.data.message || 'Role saved successfully.');
            this.closeModal();
        } catch (error) {
            this.errorMessage = error.response?.data?.message || Object.values(error.response?.data?.errors || {}).flat()[0] || 'Unable to save role.';
        } finally {
            this.saving = false;
        }
    },

    async removeRole(role) {
        if (!confirm(`Delete ${role.display_name}?`)) return;

        try {
            await axios.post(role.delete_url, { _method: 'DELETE' }, { headers: { Accept: 'application/json' } });
            this.roles = this.roles.filter((item) => item.id !== role.id);
            this.showToast('Role deleted successfully.');
        } catch (error) {
            this.showToast(error.response?.data?.message || 'Unable to delete role.', 'error');
        }
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

Alpine.data('userRoleManager', (config = {}) => ({
    users: config.users || [],
    roles: config.roles || [],
    selectedUser: null,
    selectedRoles: [],
    showModal: false,
    saving: false,
    errorMessage: '',
    toast: { show: false, message: '', type: 'success' },

    openModal(user) {
        this.selectedUser = user;
        this.selectedRoles = user.roles.map((role) => role.id);
        this.errorMessage = '';
        this.showModal = true;
    },

    closeModal() {
        this.showModal = false;
        this.selectedUser = null;
        this.selectedRoles = [];
    },

    async saveRoles() {
        if (!this.selectedUser) return;

        this.saving = true;
        this.errorMessage = '';

        try {
            const { data } = await axios.post(this.selectedUser.update_url, {
                _method: 'PATCH',
                roles: this.selectedRoles,
            }, {
                headers: { Accept: 'application/json' },
            });

            const updatedUser = data.user;
            this.users = this.users.map((user) => user.id === updatedUser.id ? updatedUser : user);
            this.showToast(data.message || 'User roles updated successfully.');
            this.closeModal();
        } catch (error) {
            this.errorMessage = error.response?.data?.message || Object.values(error.response?.data?.errors || {}).flat()[0] || 'Unable to update user roles.';
        } finally {
            this.saving = false;
        }
    },

    showToast(message, type = 'success') {
        this.toast = { show: true, message, type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
}));

Alpine.data('leaveDetailManager', (config = {}) => ({
    approveUrl: config.approveUrl,
    rejectUrl: config.rejectUrl,
    deleteUrl: config.deleteUrl,
    backUrl: config.backUrl,
    loading: false,
    toast: { show: false, message: '', type: 'success' },

    async act(url, method = 'PATCH', successMessage = 'Updated.') {
        this.loading = true;

        try {
            const payload = method === 'DELETE' ? { _method: 'DELETE' } : { _method: method };
            const { data } = await axios.post(url, payload, {
                headers: { Accept: 'application/json' },
            });

            this.toast = { show: true, message: data.message || successMessage, type: 'success' };
            window.location.href = data.redirect || this.backUrl || window.location.href;
        } catch (error) {
            this.toast = { show: true, message: error.response?.data?.message || error.response?.data?.error || 'Action failed.', type: 'error' };
            this.loading = false;
            setTimeout(() => { this.toast.show = false; }, 3000);
            return;
        }

        this.loading = false;
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
        type: 'general',
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
            asset_id: '',
            asset_name: '',
            serial_number: '',
            return_condition: '',
            requested_return_date: '',
            issue_type: '',
            reported_condition: '',
            reported_at: '',
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
        type: 'general',
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
            type: 'general',
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
            type: 'general',
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

        if (action === 'new') {
            this.openCreateModal();
            this.applyDeepLinkCreatePreset(params);
        } else if (workflowId) {
            if (modal === 'timeline') {
                this.openTimeline(workflowId);
            } else if (action === 'resubmit') {
                this.openResubmitModal(workflowId);
            }
        }

        ['workflow', 'modal', 'action', 'type', 'asset', 'asset_name', 'asset_category', 'serial_number']
            .forEach((key) => params.delete(key));
        const nextQuery = params.toString();
        const nextUrl = `${window.location.pathname}${nextQuery ? `?${nextQuery}` : ''}`;
        window.history.replaceState({}, '', nextUrl);
    },

    applyDeepLinkCreatePreset(params) {
        const type = params.get('type');
        if (!type) {
            return;
        }

        this.form.type = type;
        this.form.workflow_template_id = '';
        this.form.amount = type === 'reimbursement' ? this.form.amount : '';
        this.form.details = this.emptyDetails();

        if (type === 'asset-request') {
            this.form.title = 'Asset request';
            this.form.description = 'Requesting a new asset assignment for work use.';
            return;
        }

        const assetId = params.get('asset');
        const assetName = params.get('asset_name') || 'Assigned Asset';
        const assetCategory = params.get('asset_category') || '';
        const serialNumber = params.get('serial_number') || '';
        const today = new Date().toISOString().slice(0, 10);

        this.form.details.asset_id = assetId || '';
        this.form.details.asset_name = assetName;
        this.form.details.asset_category = assetCategory;
        this.form.details.serial_number = serialNumber;

        if (type === 'asset-return') {
            this.form.title = `Return ${assetName}`;
            this.form.description = 'Requesting approval to return this assigned asset.';
            this.form.details.requested_return_date = today;
            return;
        }

        if (type === 'asset-repair') {
            this.form.title = `Repair request for ${assetName}`;
            this.form.description = 'Reporting an issue with this assigned asset for repair or maintenance.';
            this.form.details.reported_at = today;
        }
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
            asset_id: '',
            asset_name: '',
            serial_number: '',
            return_condition: '',
            requested_return_date: '',
            issue_type: '',
            reported_condition: '',
            reported_at: '',
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

Alpine.data('commandPalette', () => ({
    isOpen: false,
    query: '',
    selectedIndex: 0,
    items: [
        { title: 'My Profile', category: 'Self-Service', url: '/self-service/profile', type: 'page' },
        { title: 'My Leaves', category: 'Self-Service', url: '/leaves/my', type: 'page' },
        { title: 'Workflows & Inbox', category: 'Operations', url: '/workflows', type: 'page' },
        { title: 'Documents', category: 'Self-Service', url: '/documents', type: 'page' },
        { title: 'Performance', category: 'Growth', url: '/performance', type: 'page' },
        { title: 'People', category: 'Company', url: '/employees', type: 'page' },
        { title: 'Departments', category: 'Company', url: '/departments', type: 'page' },
        { title: 'Policies', category: 'Company', url: '/policies', type: 'page' },
        { title: 'Asset Management', category: 'Operations', url: '/assets', type: 'page' },
        { title: 'New Leave Request', category: 'Quick Action', url: '/leaves/my?action=new', type: 'action' },
        { title: 'New Workflow Request', category: 'Quick Action', url: '/workflows?action=new', type: 'action' },
        { title: 'Night Mode', category: 'Theme', url: '#theme-toggle', type: 'action' },
    ],

    open() {
        this.isOpen = true;
        this.query = '';
        this.selectedIndex = 0;
        this.$nextTick(() => this.$refs.searchInput.focus());
    },

    close() {
        this.isOpen = false;
    },

    get filteredItems() {
        if (!this.query) return this.items.slice(0, 8);
        const q = this.query.toLowerCase();
        return this.items.filter(i => 
            i.title.toLowerCase().includes(q) || 
            i.category.toLowerCase().includes(q)
        ).slice(0, 10);
    },

    next() {
        this.selectedIndex = (this.selectedIndex + 1) % this.filteredItems.length;
    },

    prev() {
        this.selectedIndex = (this.selectedIndex - 1 + this.filteredItems.length) % this.filteredItems.length;
    },

    select() {
        const item = this.filteredItems[this.selectedIndex];
        if (!item) return;

        if (item.url === '#theme-toggle') {
            document.getElementById('theme-toggle-topbar')?.click();
            this.close();
            return;
        }

        window.location.href = item.url;
    }
}));

Livewire.start();
