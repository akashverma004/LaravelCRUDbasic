<div class="space-y-5 pb-8" x-data="analyticsDashboard({
    headcountTrend: @js($headcountTrend),
    absenceTrend: @js($absenceTrend),
    attendanceTrend: @js($attendanceTrend),
    departmentDistribution: @js($departmentDistribution)
})">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white px-4 py-4 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-cyan-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-3 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400">Strategic Intelligence</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Insight Engine</span>
                </div>
                <h1 class="text-base font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Organizational <span class="text-cyan-500">Analytics</span>
                </h1>
                <p class="mt-2 text-[8px] font-bold text-slate-500 uppercase tracking-widest leading-loose">
                    High-fidelity visualization of personnel velocity, absenteeism patterns, and workforce distribution.
                </p>
            </div>

            <div class="flex gap-4">
                <div class="text-right">
                    <p class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Present Today</p>
                    <p class="text-base font-black text-slate-900 dark:text-white">{{ $stats['presentToday'] }} / {{ $stats['totalEmployees'] }}</p>
                </div>
                <div class="h-10 w-px bg-slate-100 dark:bg-white/5"></div>
                <div class="text-right">
                    <p class="text-[9px] font-black uppercase text-slate-400 tracking-widest">On Leave</p>
                    <p class="text-base font-black text-rose-500">{{ $stats['activeLeaves'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Headcount Trend --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900">
            <h4 class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6 py-2 border-b border-slate-50 dark:border-white/5 flex items-center justify-between">
                <span>Headcount Velocity</span>
                <span class="text-[9px] text-slate-400 uppercase tracking-widest">+{{ $headcountTrend[5]['count'] - $headcountTrend[0]['count'] }} net gain (6mo)</span>
            </h4>
            <div class="h-[250px]">
                <canvas id="headcountChart"></canvas>
            </div>
        </div>

        {{-- Absence Trend --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900">
            <h4 class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6 py-2 border-b border-slate-50 dark:border-white/5">Absence Magnitude (Leave Days)</h4>
            <div class="h-[250px]">
                <canvas id="absenceChart"></canvas>
            </div>
        </div>

        {{-- Department Distribution --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900">
            <h4 class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6 py-2 border-b border-slate-50 dark:border-white/5">Force Distribution</h4>
            <div class="h-[250px] flex items-center justify-center">
                <canvas id="deptDistributionChart" class="max-w-[280px]"></canvas>
            </div>
        </div>

        {{-- Attendance Heat --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900">
            <h4 class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6 py-2 border-b border-slate-50 dark:border-white/5">Presence Amplitude (Last 14 Days)</h4>
            <div class="h-[250px]">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        function analyticsDashboard(config) {
            return {
                headcountTrend: config.headcountTrend,
                absenceTrend: config.absenceTrend,
                attendanceTrend: config.attendanceTrend,
                departmentDistribution: config.departmentDistribution,
                
                init() {
                    this.initHeadcount();
                    this.initAbsence();
                    this.initDeptDist();
                    this.initAttendance();
                },

                initHeadcount() {
                    new Chart(document.getElementById('headcountChart'), {
                        type: 'line',
                        data: {
                            labels: this.headcountTrend.map(i => i.month),
                            datasets: [{
                                label: 'Total Employees',
                                data: this.headcountTrend.map(i => i.count),
                                borderColor: '#06b6d4',
                                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                                borderWidth: 4,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#06b6d4',
                                pointBorderWidth: 2
                            }]
                        },
                        options: this.chartOptions()
                    });
                },

                initAbsence() {
                    new Chart(document.getElementById('absenceChart'), {
                        type: 'bar',
                        data: {
                            labels: this.absenceTrend.map(i => i.month),
                            datasets: [{
                                label: 'Total Leave Days',
                                data: this.absenceTrend.map(i => i.days),
                                backgroundColor: '#f43f5e',
                                borderRadius: 8,
                                barThickness: 24
                            }]
                        },
                        options: this.chartOptions()
                    });
                },

                initDeptDist() {
                    new Chart(document.getElementById('deptDistributionChart'), {
                        type: 'doughnut',
                        data: {
                            labels: this.departmentDistribution.map(i => i.name),
                            datasets: [{
                                data: this.departmentDistribution.map(i => i.count),
                                backgroundColor: [
                                    '#06b6d4', '#6366f1', '#ec4899', '#f59e0b', '#8b5cf6', '#10b981'
                                ],
                                borderWidth: 0,
                                cutout: '75%'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true,
                                        font: { size: 9, family: 'Inter', weight: 'bold' },
                                        color: '#64748b'
                                    }
                                }
                            }
                        }
                    });
                },

                initAttendance() {
                    new Chart(document.getElementById('attendanceChart'), {
                        type: 'line',
                        data: {
                            labels: this.attendanceTrend.map(i => i.day),
                            datasets: [{
                                label: 'Attendance %',
                                data: this.attendanceTrend.map(i => i.percentage),
                                borderColor: '#6366f1',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 0
                            }]
                        },
                        options: {
                            ...this.chartOptions(),
                            scales: {
                                ...this.chartOptions().scales,
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    grid: { display: false },
                                    ticks: { font: { size: 8 }, color: '#94a3b8' }
                                }
                            }
                        }
                    });
                },

                chartOptions() {
                    return {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 9 }, color: '#94a3b8' }
                            },
                            y: {
                                border: { display: false },
                                grid: { color: 'rgba(226, 232, 240, 0.4)' },
                                ticks: { font: { size: 9 }, color: '#94a3b8' }
                            }
                        }
                    };
                }
            };
        }
    </script>
</div>
