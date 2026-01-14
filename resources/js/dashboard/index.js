import Chart from 'chart.js/auto';

// Global variables to hold chart instances
let peminjamanChart = null;
let pengunjungChart = null;

document.addEventListener('DOMContentLoaded', function () {
    initCharts(window.initialChartData);
});

// Initialize Charts
function initCharts(data) {
    const ctxPeminjaman = document.getElementById('peminjamanChart').getContext('2d');
    const ctxPengunjung = document.getElementById('pengunjungChart').getContext('2d');
    const emptyState = document.getElementById('peminjamanEmptyState');
    const chartCanvas = document.getElementById('peminjamanChart');

    const emptyStatePengunjung = document.getElementById('pengunjungEmptyState');
    const chartCanvasPengunjung = document.getElementById('pengunjungChart');

    // Initial Empty Check (Peminjaman)
    const peminjamanTotal = data.peminjaman.data.reduce((a, b) => a + b, 0);
    if (peminjamanTotal === 0) {
        chartCanvas.classList.add('opacity-0', 'pointer-events-none');
        emptyState.classList.remove('hidden');
        emptyState.classList.add('flex');
    }

    // Initial Empty Check (Pengunjung)
    const pengunjungTotal = data.pengunjung.data.reduce((a, b) => a + b, 0);
    if (pengunjungTotal === 0) {
        chartCanvasPengunjung.classList.add('opacity-0', 'pointer-events-none');
        emptyStatePengunjung.classList.remove('hidden');
        emptyStatePengunjung.classList.add('flex');
    }

    // 1. Peminjaman Doughnut Chart (Status Breakdown)
    peminjamanChart = new Chart(ctxPeminjaman, {
        type: 'doughnut',
        data: {
            labels: data.peminjaman.labels,
            datasets: [{
                data: data.peminjaman.data,
                backgroundColor: [
                    '#f59e0b', // Diajukan (Orange)
                    '#3b82f6', // Berjalan (Blue)
                    '#ef4444', // Terlambat (Red)
                    '#10b981', // Selesai (Emerald)
                    '#f97316', // Rusak (Orange-500)
                    '#64748b'  // Hilang (Slate-500)
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'right',
                    labels: {
                        font: { family: 'Spline Sans', size: 11 },
                        usePointStyle: true,
                        boxWidth: 8
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            let value = context.parsed;
                            let total = context.chart._metasets[context.datasetIndex].total;
                            let percentage = ((value / total) * 100).toFixed(1) + '%';
                            return label + value + ' (' + percentage + ')';
                        }
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: true
                }
            },
            cutout: '60%', // Doughnut thickness
        }
    });

    // 2. Pengunjung Bar Chart
    pengunjungChart = new Chart(ctxPengunjung, {
        type: 'bar',
        data: {
            labels: data.pengunjung.labels,
            datasets: [{
                label: 'Pengunjung',
                data: data.pengunjung.data,
                backgroundColor: '#10b981', // Emerald
                borderRadius: 4,
                hoverBackgroundColor: '#059669'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)', borderDash: [5, 5] },
                    ticks: { stepSize: 1 }
                },
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 8 }
                }
            }
        }
    });
}

// Update Dashboard Function (Exposed globally)
window.updateDashboard = function (filter) {
    // 1. Update Buttons UI
    const buttons = ['today', 'week', 'month'];
    buttons.forEach(btn => {
        const el = document.getElementById(`btn-${btn}`);
        if (btn === filter) {
            el.className = 'px-4 py-1.5 text-sm font-bold rounded-lg transition-all bg-primary text-white shadow-md';
        } else {
            el.className = 'px-4 py-1.5 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 dark:text-white/60 dark:hover:bg-white/5';
        }
    });

    // 2. Fetch New Data
    fetch(`?filter=${filter}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            // Update Charts
            // Peminjaman (Dougnut)
            const peminjamanData = data.peminjaman.data;
            const peminjamanTotal = peminjamanData.reduce((a, b) => a + b, 0);
            const emptyState = document.getElementById('peminjamanEmptyState');
            const chartCanvas = document.getElementById('peminjamanChart');

            if (peminjamanTotal === 0) {
                // Show Empty State
                chartCanvas.classList.add('opacity-0', 'pointer-events-none');
                emptyState.classList.remove('hidden');
                emptyState.classList.add('flex');
            } else {
                // Show Chart
                chartCanvas.classList.remove('opacity-0', 'pointer-events-none');
                emptyState.classList.add('hidden');
                emptyState.classList.remove('flex');

                peminjamanChart.data.labels = data.peminjaman.labels;
                peminjamanChart.data.datasets[0].data = data.peminjaman.data;
                peminjamanChart.update();
            }

            // Pengunjung (Bar)
            const pengunjungData = data.pengunjung.data;
            const pengunjungTotal = pengunjungData.reduce((a, b) => a + b, 0);
            const emptyStatePengunjung = document.getElementById('pengunjungEmptyState');
            const chartCanvasPengunjung = document.getElementById('pengunjungChart');

            if (pengunjungTotal === 0) {
                chartCanvasPengunjung.classList.add('opacity-0', 'pointer-events-none');
                emptyStatePengunjung.classList.remove('hidden');
                emptyStatePengunjung.classList.add('flex');
            } else {
                chartCanvasPengunjung.classList.remove('opacity-0', 'pointer-events-none');
                emptyStatePengunjung.classList.add('hidden');
                emptyStatePengunjung.classList.remove('flex');

                pengunjungChart.data.labels = data.pengunjung.labels;
                pengunjungChart.data.datasets[0].data = data.pengunjung.data;
                pengunjungChart.update();
            }
        })
        .catch(error => console.error('Error fetching dashboard data:', error));
};

function updateChart(chart, labels, data) {
    chart.data.labels = labels;
    chart.data.datasets[0].data = data;
    chart.update();
}
