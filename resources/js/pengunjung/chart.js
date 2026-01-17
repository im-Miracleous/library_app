import Chart from 'chart.js/auto';

let pengunjungChart = null;

document.addEventListener('DOMContentLoaded', function () {
    initChart(window.initialChartData);
});

function initChart(data) {
    const ctx = document.getElementById('pengunjungChart').getContext('2d');

    // --- COLOR PALETTES ---
    const PALETTE = {
        light: ['#3B82F6', '#10B981', '#F59E0B', '#6366F1', '#EC4899'], // Blue, Emerald, Amber, Indigo, Pink
        dark: ['#60A5FA', '#34D399', '#FBBF24', '#818CF8', '#F472B6']   // Brighter shades
    };

    function getColors(isDark) {
        return isDark ? PALETTE.dark : PALETTE.light;
    }

    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
    const tickColor = isDark ? 'rgba(255, 255, 255, 0.6)' : '#64748b';

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
                backgroundColor: getColors(isDark),
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
                    grid: { color: gridColor, borderDash: [5, 5] },
                    border: { color: gridColor },
                    ticks: {
                        color: tickColor,
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
                        color: isDark ? '#e2e8f0' : '#64748b',
                        font: { family: 'Spline Sans' }
                    }
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

                if (pengunjungChart) {
                    pengunjungChart.options.scales.x.grid.color = newGridColor;
                    pengunjungChart.options.scales.x.border.color = newGridColor;
                    pengunjungChart.options.scales.x.ticks.color = newTickColor;

                    // Update Y axis labels
                    if (pengunjungChart.options.scales.y) {
                        pengunjungChart.options.scales.y.ticks.color = newLabelColor;
                    }

                    // Update Dataset Colors
                    pengunjungChart.data.datasets[0].backgroundColor = getColors(isNowDark);

                    pengunjungChart.update();
                }
            }
        });
    });

    observer.observe(document.documentElement, { attributes: true });
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
