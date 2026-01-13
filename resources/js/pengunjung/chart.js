import Chart from 'chart.js/auto';

let pengunjungChart = null;

document.addEventListener('DOMContentLoaded', function () {
    initChart(window.initialChartData);
});

function initChart(data) {
    const ctx = document.getElementById('pengunjungChart').getContext('2d');

    // Gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.8)'); // Emerald
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.2)');

    pengunjungChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Jumlah Pengunjung',
                data: data.data,
                backgroundColor: gradient,
                borderRadius: 6,
                hoverBackgroundColor: '#059669',
                barThickness: 'flex',
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    imageUrl: null,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { family: 'Spline Sans', size: 13 },
                    bodyFont: { family: 'Spline Sans', size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            return context.parsed.y + ' Pengunjung';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)', borderDash: [5, 5] },
                    ticks: {
                        stepSize: 1,
                        font: { family: 'Spline Sans' }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Spline Sans' },
                        maxTicksLimit: 12
                    }
                }
            }
        }
    });
}

window.updatePengunjungChart = function (filter) {
    // 1. Update UI Buttons
    const buttons = ['today', 'week', 'month'];
    buttons.forEach(btn => {
        const el = document.getElementById(`btn-${btn}`);
        if (btn === filter) {
            el.className = 'px-4 py-1.5 text-sm font-bold rounded-lg transition-all bg-primary text-white shadow-md';
        } else {
            el.className = 'px-4 py-1.5 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-white dark:hover:bg-white/5 dark:text-white/60';
        }
    });

    // 2. Fetch Data
    fetch(`?filter=${filter}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            pengunjungChart.data.labels = data.labels;
            pengunjungChart.data.datasets[0].data = data.data;
            pengunjungChart.update();
        })
        .catch(error => console.error('Error updating chart:', error));
};
