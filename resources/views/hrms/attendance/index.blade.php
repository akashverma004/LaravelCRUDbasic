@extends('hrms.layouts.app')
@section('title', 'Attendance Tracking | PeopleFlow')

@section('content')
<div x-data="attendanceManager()" class="max-w-[1400px] mx-auto pb-24">
    {{-- Header --}}
    <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Attendance Tracking</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">View and manage employee daily clock-ins, clock-outs, and overtime.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="date" x-model="filterDate" @change="fetchData" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-sky-200 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:hover:border-white/20">
            <button @click="fetchData" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm transition-all hover:bg-slate-50 dark:border-white/10 dark:bg-slate-800 dark:text-white dark:hover:bg-slate-700/80">
                <svg class="h-5 w-5" :class="{'animate-spin': loading}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Total Hours Logged</p>
                    <div class="mt-1 flex items-baseline gap-2">
                        <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white" x-text="stats.total_hours || 0"></span>
                        <span class="text-sm font-semibold text-slate-500">hrs</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Present Today</p>
                    <div class="mt-1 flex items-baseline gap-2">
                        <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white" x-text="stats.present_today || 0"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Active Now</p>
                    <div class="mt-1 flex items-baseline gap-2">
                        <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white" x-text="stats.active_now || 0"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Section --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900 overflow-hidden">
        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-slate-200 p-5 dark:border-white/10">
            <div class="relative w-full max-w-sm">
                <svg class="absolute left-3.5 top-2.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input x-model="searchQuery" type="text" placeholder="Search by name, role..." class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-sm font-medium text-slate-900 transition focus:border-cyan-500 focus:bg-white focus:ring-1 focus:ring-cyan-500 dark:border-white/5 dark:bg-white/5 dark:text-white dark:focus:border-cyan-400 dark:focus:bg-slate-900">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400" x-text="filteredRecords.length + ' Records'"></span>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500 dark:text-slate-400">
                <thead class="bg-slate-50/80 text-[10px] uppercase font-black tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Employee</th>
                        <th class="px-6 py-4 text-center">Date</th>
                        <th class="px-6 py-4 text-center">First In</th>
                        <th class="px-6 py-4 text-center">Last Out</th>
                        <th class="px-6 py-4 text-center">Total Hours</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-slate-900">
                    <template x-show="!loading && filteredRecords.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <span class="text-[11px] font-black tracking-widest uppercase block mb-1 text-slate-400 dark:text-slate-500">No Records Found</span>
                                <span class="text-sm">No time logs exist for this date matching your search.</span>
                            </td>
                        </tr>
                    </template>
                    
                    <template x-for="record in filteredRecords" :key="record.id">
                        <tr @click="openEditModal(record)" class="group hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 font-bold text-slate-600 dark:text-slate-300" x-text="record.employee?.full_name?.charAt(0) || '?'"></div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-cyan-600 transition-colors" x-text="record.employee?.full_name"></p>
                                        <p class="text-[10px] uppercase tracking-widest text-slate-500" x-text="record.employee?.job_title"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700 dark:text-slate-300" x-text="record.date"></td>
                            <td class="px-6 py-4 text-center">
                                <span class="rounded bg-slate-100 dark:bg-slate-800 px-2 py-1 font-mono text-xs font-bold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700"
                                      x-text="record.clock_in_at || '--:--'"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="rounded bg-slate-100 dark:bg-slate-800 px-2 py-1 font-mono text-xs font-bold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700"
                                      x-text="record.clock_out_at || '--:--'"></span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700 dark:text-slate-300" x-text="record.total_hours_formatted"></td>
                            <td class="px-6 py-4 text-center">
                                <template x-if="record.status === 'clocked_in'">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[9px] font-black tracking-widest uppercase text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Active</span>
                                </template>
                                <template x-if="record.status === 'completed'">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[9px] font-black tracking-widest uppercase text-slate-600 dark:bg-white/10 dark:text-slate-300">Completed</span>
                                </template>
                                <template x-if="record.status !== 'clocked_in' && record.status !== 'completed'">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-[9px] font-black tracking-widest uppercase text-amber-600 dark:bg-amber-500/10 dark:text-amber-400" x-text="record.status"></span>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Slide-in Panel for Editing Timesheet --}}
    <div x-show="showEditModal" style="display: none;" class="relative z-50">
        {{-- Overlay --}}
        <div x-show="showEditModal" x-transition.opacity @click="closeEditModal()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showEditModal"
                         x-transition:enter="transform transition ease-in-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-300"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         class="pointer-events-auto w-screen max-w-md bg-white shadow-2xl dark:bg-slate-900 flex flex-col h-full">

                        {{-- Header --}}
                        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 dark:border-white/5">
                            <h2 class="text-xl font-black text-slate-900 dark:text-white">Edit Timesheet</h2>
                            <button @click="closeEditModal()" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-white/5 mx-[-8px]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        {{-- Body Form --}}
                        <div class="flex-1 overflow-y-auto px-6 py-6 custom-scrollbar space-y-6">
                            {{-- Employee preview --}}
                            <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                <div class="h-10 w-10 rounded-full bg-cyan-100 flex items-center justify-center font-bold text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-400"
                                     x-text="editForm.employee_name?.charAt(0) || '?'"></div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white" x-text="editForm.employee_name"></p>
                                    <p class="text-xs text-slate-500 font-medium" x-text="'Record for ' + editForm.date"></p>
                                </div>
                            </div>

                            {{-- Time Inputs --}}
                            <div class="grid grid-cols-2 gap-4 border-t border-slate-100 dark:border-white/5 pt-6">
                                <div>
                                    <label class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Clock In Time <span class="text-red-500">*</span></label>
                                    <input type="time" x-model="editForm.clock_in" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold transition focus:border-cyan-500 focus:bg-white focus:ring-1 focus:ring-cyan-500 dark:border-white/10 dark:bg-slate-800 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Clock Out Time <span class="text-red-500">*</span></label>
                                    <input type="time" x-model="editForm.clock_out" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold transition focus:border-cyan-500 focus:bg-white focus:ring-1 focus:ring-cyan-500 dark:border-white/10 dark:bg-slate-800 dark:text-white">
                                </div>
                            </div>

                        </div>

                        {{-- Footer Buttons --}}
                        <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 dark:border-white/5 dark:bg-slate-900 flex justify-end gap-3">
                            <button @click="closeEditModal()" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-slate-800 dark:text-white dark:hover:bg-slate-700/80">Cancel</button>
                            <button @click="saveEdit" :disabled="saving" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50">
                                <span x-show="!saving">Save Changes</span>
                                <span x-show="saving">Saving...</span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('attendanceManager', () => ({
            records: [],
            stats: {},
            loading: false,
            saving: false,
            searchQuery: '',
            filterDate: new Date().toISOString().split('T')[0], // Today
            
            showEditModal: false,
            editForm: {
                id: null,
                employee_name: '',
                date: '',
                clock_in: '',
                clock_out: '',
            },

            async init() {
                await this.fetchData();
            },

            async fetchData() {
                this.loading = true;
                try {
                    const response = await axios.get('/attendance-management/data', { params: { date: this.filterDate } });
                    this.records = response.data.records;
                    this.stats = response.data.stats;
                } catch (error) {
                    console.error("Failed to load attendance logs", error);
                } finally {
                    this.loading = false;
                }
            },

            get filteredRecords() {
                if (!this.searchQuery.trim()) return this.records;
                const lower = this.searchQuery.toLowerCase();
                return this.records.filter(r => 
                    (r.employee?.full_name || '').toLowerCase().includes(lower) ||
                    (r.employee?.job_title || '').toLowerCase().includes(lower)
                );
            },

            openEditModal(record) {
                this.editForm.id = record.id;
                this.editForm.employee_name = record.employee?.full_name;
                this.editForm.date = record.date;
                this.editForm.clock_in = record.clock_in_at || '';
                this.editForm.clock_out = record.clock_out_at || '';
                this.showEditModal = true;
            },

            closeEditModal() {
                this.showEditModal = false;
            },

            async saveEdit() {
                this.saving = true;
                try {
                    await axios.post('/attendance-management/update', {
                        record_id: this.editForm.id,
                        clock_in_at: this.editForm.clock_in,
                        clock_out_at: this.editForm.clock_out,
                    });
                    this.closeEditModal();
                    await this.fetchData(); // Refresh list to reflect adjusted logic
                } catch (error) {
                    alert('Error saving timesheet. Check input format.');
                } finally {
                    this.saving = false;
                }
            }
        }));
    });
</script>
@endsection
