<div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 lg:col-span-2">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Workforce Distribution</h2>
    </div>
    <canvas id="departmentChart" class="max-h-72"></canvas>
</div>

<script>
    const labels = @json($departmentBreakdown->pluck('name'));
    const data = @json($departmentBreakdown->pluck('employees_count'));

    new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Employees',
                data,
                backgroundColor: 'rgba(34, 211, 238, 0.7)',
                borderRadius: 8,
            }],
        },
        options: {
            responsive: true,
            plugins: {legend: {display: false}},
            scales: {
                x: {ticks: {color: '#94a3b8'}},
                y: {ticks: {color: '#94a3b8'}, beginAtZero: true},
            },
        },
    });
</script>
