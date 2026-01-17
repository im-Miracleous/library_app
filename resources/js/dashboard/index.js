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

    // --- COLOR PALETTES ---
    const PALETTE = {
        light: {
            peminjaman: ['#f59e0b', '#3b82f6', '#ef4444', '#10b981', '#f97316', '#64748b'], // Orange, Blue, Red, Emerald, DkOrange, Slate
            pengunjung: ['#3b82f6', '#10b981', '#f59e0b', '#6366f1'] // Blue, Emerald, Amber, Indigo
        },
        dark: {
            peminjaman: ['#fbbf24', '#60a5fa', '#f87171', '#34d399', '#fb923c', '#94a3b8'], // Brighter shades
            pengunjung: ['#60a5fa', '#34d399', '#fbbf24', '#818cf8'] // Brighter Blue, Emerald, Amber, Indigo
        }
    };

    function getColors(type, isDark) {
        const p = isDark ? PALETTE.dark : PALETTE.light;
        return p[type];
    }

    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';

    const emptyStatePengunjung = document.getElementById('pengunjungEmptyState');
    const chartCanvasPengunjung = document.getElementById('pengunjungChart');

    // Initial Empty Check (Peminjaman)
    const peminjamanDataValues = data.peminjaman.data;
    const peminjamanTotal = peminjamanDataValues.reduce((a, b) => a + b, 0); // Includes Diajukan for empty check

    // Total (Include All Statuses)
    const totalPeminjamanEl = document.getElementById('totalPeminjaman');
    if (totalPeminjamanEl) {
        totalPeminjamanEl.innerText = peminjamanTotal.toLocaleString('id-ID');
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
                backgroundColor: getColors('peminjaman', isDark),
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
                        boxWidth: 8,
                        color: isDark ? '#e2e8f0' : '#64748b'
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
                rawCounts: pengunjungCounts, // Store original counts
                backgroundColor: getColors('pengunjung', isDark),
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
                    grid: { color: gridColor, borderDash: [5, 5] },
                    border: { color: gridColor },
                    ticks: {
                        color: isDark ? 'rgba(255, 255, 255, 0.6)' : '#64748b',
                        stepSize: 20,
                        callback: function (value) {
                            return value + '%';
                        }
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        color: isDark ? '#e2e8f0' : '#64748b'
                    }
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

    // Watch for Theme Changes
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                const isNowDark = document.documentElement.classList.contains('dark');
                const newGridColor = isNowDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                const newTickColor = isNowDark ? 'rgba(255, 255, 255, 0.6)' : '#64748b';
                const newLabelColor = isNowDark ? '#e2e8f0' : '#64748b';

                // Update Peminjaman Chart
                if (peminjamanChart) {
                    peminjamanChart.data.datasets[0].backgroundColor = getColors('peminjaman', isNowDark);
                    if (peminjamanChart.options.plugins.legend) {
                        peminjamanChart.options.plugins.legend.labels.color = newLabelColor;
                    }
                    peminjamanChart.update();
                }

                // Update Pengunjung Chart
                if (pengunjungChart) {
                    pengunjungChart.options.scales.x.grid.color = newGridColor;
                    pengunjungChart.options.scales.x.border.color = newGridColor;
                    pengunjungChart.options.scales.x.ticks.color = newTickColor;

                    // Also update Y axis labels (category names)
                    if (pengunjungChart.options.scales.y) {
                        pengunjungChart.options.scales.y.ticks.color = newLabelColor;
                    }

                    pengunjungChart.data.datasets[0].backgroundColor = getColors('pengunjung', isNowDark);
                    pengunjungChart.update();
                }
            }
        });
    });

    observer.observe(document.documentElement, { attributes: true });
}

// Update Dashboard Function (Exposed globally)
window.updateDashboard = function (filter) {
    // Filter Updated

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

            // Total (Include All Statuses)
            const totalPeminjamanEl = document.getElementById('totalPeminjaman');
            if (totalPeminjamanEl) {
                totalPeminjamanEl.innerText = peminjamanTotal.toLocaleString('id-ID');
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
