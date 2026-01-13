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

    // Gradient for Peminjaman
    const gradientPeminjaman = ctxPeminjaman.createLinearGradient(0, 0, 0, 400);
    gradientPeminjaman.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Blue
    gradientPeminjaman.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    // 1. Peminjaman Line Chart
    peminjamanChart = new Chart(ctxPeminjaman, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Peminjaman',
                data: data.peminjaman,
                borderColor: '#3b82f6',
                backgroundColor: gradientPeminjaman,
                borderWidth: 3,
                tension: 0.4, // Smooth curves
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
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
                    titleFont: { family: 'Spline Sans', size: 13 },
                    bodyFont: { family: 'Spline Sans', size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)', borderDash: [5, 5] },
                    ticks: { font: { family: 'Spline Sans' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Spline Sans' }, maxTicksLimit: 8 }
                }
            }
        }
    });

    // 2. Pengunjung Bar Chart
    pengunjungChart = new Chart(ctxPengunjung, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Pengunjung',
                data: data.pengunjung,
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
            updateChart(peminjamanChart, data.labels, data.peminjaman);
            updateChart(pengunjungChart, data.labels, data.pengunjung);
        })
        .catch(error => console.error('Error fetching dashboard data:', error));
};

function updateChart(chart, labels, data) {
    chart.data.labels = labels;
    chart.data.datasets[0].data = data;
    chart.update();
}
