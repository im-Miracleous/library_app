import Chart from 'chart.js/auto';

let pengunjungChart = null;

document.addEventListener('DOMContentLoaded', function () {
    initChart(window.initialChartData);
});

function initChart(data) {
    const ctx = document.getElementById('pengunjungChart').getContext('2d');

    // Calculate Initial Data
    let rawCounts = data.data;
    let total = rawCounts.reduce((a, b) => parseInt(a) + parseInt(b), 0);
    let percentages = rawCounts.map(count =>
        total > 0 ? ((count / total) * 100) : 0
    );

    // Update Initial Badge
    const totalBadge = document.getElementById('totalVisitorCount');
    if (totalBadge) {
        totalBadge.innerText = total.toLocaleString('id-ID');
    }

    pengunjungChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                // label: 'Jumlah Pengunjung', // Not strictly needed if legend hidden
                data: percentages,
                rawCounts: rawCounts,
                backgroundColor: [
                    '#3B82F6', // Blue - Personal & Akademik
                    '#10B981', // Emerald - Organisasi & Komunitas
                    '#F59E0B', // Amber - Instansi & Perusahaan 
                    '#6366F1'  // Indigo - Kunjungan Khusus
                ],
                borderRadius: 4,
                barThickness: 'flex',
                maxBarThickness: 40
            }]
        },
        options: {
            indexAxis: 'y', // Horizontal
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function (context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
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
                    beginAtZero: true,
                    min: 0,
                    max: 100,
                    grid: { color: 'rgba(0, 0, 0, 0.05)', borderDash: [5, 5] },
                    ticks: {
                        stepSize: 20,
                        callback: function (value) {
                            return value + '%';
                        },
                        font: { family: 'Spline Sans' }
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Spline Sans' }
                    }
                }
            }
        }
    });
}

window.updatePengunjungChart = function (filter) {
    // 2. Fetch Data
    fetch(`?filter=${filter}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            // Recalculate Logic
            let rawCounts = data.data;
            let total = rawCounts.reduce((a, b) => parseInt(a) + parseInt(b), 0);
            let percentages = rawCounts.map(count =>
                total > 0 ? ((count / total) * 100) : 0
            );

            // Update Badge
            const totalBadge = document.getElementById('totalVisitorCount');
            if (totalBadge) {
                totalBadge.innerText = total.toLocaleString('id-ID');
            }

            // Update Chart
            pengunjungChart.data.labels = data.labels;
            pengunjungChart.data.datasets[0].data = percentages;
            pengunjungChart.data.datasets[0].rawCounts = rawCounts;
            pengunjungChart.update();
        })
        .catch(error => console.error('Error updating chart:', error));
};
