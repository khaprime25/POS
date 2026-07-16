import './bootstrap';

import Alpine from 'alpinejs';

import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {

    const canvas = document.getElementById('salesChart');

    if (!canvas) return;

    const labels = JSON.parse(canvas.dataset.labels);
    const data = JSON.parse(canvas.dataset.data);

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data,
                borderColor: '#6F4E37',
                backgroundColor: 'rgba(111,78,55,.12)',
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#6F4E37',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            interaction: {
                mode: 'index',
                intersect: false
            },

            plugins: {
                legend: {
                    display: false
                },

                tooltip: {
                    backgroundColor: '#1f2328',
                    padding: 12,

                    callbacks: {
                        title: (items) => items[0].label,
                        label: (item) => item.raw.toLocaleString() + ' Ks'
                    }
                }
            },

            scales: {
                x: {
                    grid: {
                        display: false
                    },

                    border: {
                        display: false
                    },

                    ticks: {
                        color: '#6b7280'
                    }
                },

                y: {
                    beginAtZero: true,

                    grid: {
                        color: 'rgba(0,0,0,.06)'
                    },

                    border: {
                        display: false
                    },

                    ticks: {
                        color: '#6b7280',
                        callback: (value) => value.toLocaleString() + ' Ks'
                    }
                }
            }
        }
    });
});
