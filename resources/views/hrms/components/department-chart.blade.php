<div class="rounded-[2.5rem] border border-slate-200 bg-white p-10 shadow-sm dark:border-slate-800 dark:bg-slate-900/50 lg:col-span-2">
    <div class="mb-10 flex items-center justify-between">
        <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Workforce Density <span class="text-cyan-500 ml-2">DIV_ALLOC</span></h2>
    </div>
    <div class="relative h-72">
        <canvas id="departmentChart"></canvas>
    </div>
</div>

<script>
    (function() {
        const labels = @json($departmentBreakdown->pluck('name'));
        const data = @json($departmentBreakdown->pluck('employees_count'));

        new Chart(document.getElementById('departmentChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Employees',
                    data,
                    backgroundColor: 'rgba(34, 211, 238, 0.4)',
                    borderColor: '#22d3ee',
                    borderWidth: 2,
                    borderRadius: 12,
                    hoverBackgroundColor: 'rgba(34, 211, 238, 0.8)',
                    barThickness: 32,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 10, weight: '900', family: 'Inter' },
                        bodyFont: { size: 10, weight: 'bold', family: 'Inter' },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 9, weight: '900' } }
                    },
                    y: {
                        grid: { color: 'rgba(226, 232, 240, 0.1)' },
                        ticks: { color: '#64748b', font: { size: 9, weight: 'bold' } },
                        beginAtZero: true
                    },
                },
            },
        });
    })();
</script>
