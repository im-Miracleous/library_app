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
    const peminjamanDataValues = data.peminjaman.data;
    const peminjamanTotal = peminjamanDataValues.reduce((a, b) => a + b, 0); // Includes Diajukan for empty check

    // Calculate Valid Total (Exclude Diajukan - Index 0)
    // Indexes: 0=Diajukan, 1=Berjalan, 2=Terlambat, 3=Selesai, 4=Rusak, 5=Hilang
    const peminjamanValidTotal = peminjamanDataValues.slice(1).reduce((a, b) => a + b, 0);

    const totalPeminjamanEl = document.getElementById('totalPeminjaman');
    if (totalPeminjamanEl) {
        totalPeminjamanEl.innerText = peminjamanValidTotal.toLocaleString('id-ID');
    }

    if (peminjamanTotal === 0) {
        chartCanvas.classList.add('opacity-0', 'pointer-events-none');
        emptyState.classList.remove('hidden');
        emptyState.classList.add('flex');
    }

    // Initial Empty Check (Pengunjung)
    let pengunjungTotal = 0;
    if (data.pengunjung.datasets && data.pengunjung.datasets[0]) {
        pengunjungTotal = data.pengunjung.datasets[0].data.reduce((a, b) => a + b, 0);
    }

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

    // 2. Pengunjung Bar Chart (Categorical - Horizontal Percentage)
    let pengunjungCounts = data.pengunjung.datasets[0].data;
    let pengunjungTotalCalc = pengunjungCounts.reduce((a, b) => parseInt(a) + parseInt(b), 0);

    // Update Total Counter (Initial Load)
    const totalCounter = document.getElementById('totalPengunjung');
    if (totalCounter) {
        totalCounter.innerText = pengunjungTotalCalc.toLocaleString('id-ID');
    }

    let pengunjungPercentages = pengunjungCounts.map(count =>
        pengunjungTotalCalc > 0 ? ((count / pengunjungTotalCalc) * 100) : 0
    );

    pengunjungChart = new Chart(ctxPengunjung, {
        type: 'bar',
        data: {
            labels: data.pengunjung.labels,
            datasets: [{
                ...data.pengunjung.datasets[0],
                data: pengunjungPercentages, // Use percentages
                rawCounts: pengunjungCounts // Store original counts
            }]
        },
        options: {
            indexAxis: 'y', // Makes it horizontal
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function (context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label = context.label + ': ';
                            }
                            let percentage = context.parsed.x.toFixed(1) + '%';
                            let count = context.dataset.rawCounts[context.dataIndex];

                            return label + percentage + ' (' + count + ')';
                        }
                    }
                }
            },
            scales: {
                x: {
                    min: 0,
                    max: 100, // Fixed range 0-100%
                    grid: { color: 'rgba(0, 0, 0, 0.05)', borderDash: [5, 5] },
                    ticks: {
                        stepSize: 20,
                        callback: function (value) {
                            return value + '%';
                        }
                    }
                },
                y: {
                    grid: { display: false }
                }
            },
            elements: {
                bar: {
                    borderRadius: 4,
                    borderSkipped: false,
                    maxBarThickness: 40
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

            // Calculate Valid Total (Exclude Diajukan - Index 0)
            const peminjamanValidTotal = peminjamanData.slice(1).reduce((a, b) => a + b, 0);
            const totalPeminjamanEl = document.getElementById('totalPeminjaman');
            if (totalPeminjamanEl) {
                totalPeminjamanEl.innerText = peminjamanValidTotal.toLocaleString('id-ID');
            }

            const emptyState = document.getElementById('peminjamanEmptyState');
            const chartCanvas = document.getElementById('peminjamanChart');

            if (peminjamanTotal === 0) {
                // Show Empty State
                chartCanvas.classList.add('opacity-0', 'pointer-events-none');
                emptyState.classList.add('flex');
                emptyState.classList.remove('hidden');
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
            let pengunjungTotal = 0;
            if (data.pengunjung.datasets && data.pengunjung.datasets[0]) {
                pengunjungTotal = data.pengunjung.datasets[0].data.reduce((a, b) => parseInt(a) + parseInt(b), 0);
            }

            // Update Total Counter
            const totalCounter = document.getElementById('totalPengunjung');
            if (totalCounter) {
                totalCounter.innerText = pengunjungTotal.toLocaleString('id-ID'); // Format number
            }

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

                // Calculate Percentages
                let rawCounts = data.pengunjung.datasets[0].data;
                let percentages = rawCounts.map(count =>
                    pengunjungTotal > 0 ? ((count / pengunjungTotal) * 100) : 0
                );

                pengunjungChart.data.labels = data.pengunjung.labels;
                pengunjungChart.data.datasets = [{
                    ...data.pengunjung.datasets[0],
                    data: percentages,
                    rawCounts: rawCounts
                }];
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
