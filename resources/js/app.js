import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

const css = (name, fallback) => {
    const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();

    return value || fallback;
};

function buildChart(canvas) {
    if (canvas.dataset.rendered === 'true') {
        return;
    }

    let labels = [];
    let values = [];

    try {
        labels = JSON.parse(canvas.dataset.labels || '[]');
        values = JSON.parse(canvas.dataset.values || '[]');
    } catch {
        return;
    }

    if (!labels.length) {
        return;
    }

    const unit = canvas.dataset.unit || '';
    const label = canvas.dataset.label || 'Nilai';
    const color = canvas.dataset.color || css('--color-brand-500', '#138B68');
    const context = canvas.getContext('2d');
    const gradient = context.createLinearGradient(0, 0, 0, canvas.clientHeight || 260);
    gradient.addColorStop(0, `${color}2e`);
    gradient.addColorStop(1, `${color}00`);

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label,
                    data: values,
                    borderColor: color,
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.32,
                    spanGaps: true,
                    pointRadius: 3.5,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: color,
                    pointBorderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            layout: { padding: { top: 8, right: 6, left: 0, bottom: 0 } },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#16211d',
                    titleFont: { family: 'Inter', size: 11, weight: '600' },
                    bodyFont: { family: 'Inter', size: 12, weight: '500' },
                    padding: 10,
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        label: (item) => `${item.parsed.y} ${unit}`.trim(),
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        color: '#66736e',
                        font: { family: 'Inter', size: 10 },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 6,
                    },
                },
                y: {
                    beginAtZero: false,
                    grid: { color: 'rgba(227, 234, 230, 0.8)', drawTicks: false },
                    border: { display: false },
                    ticks: {
                        color: '#66736e',
                        font: { family: 'Inter', size: 10 },
                        padding: 8,
                        callback: (value) => `${value} ${unit}`.trim(),
                    },
                },
            },
        },
    });

    canvas.dataset.rendered = 'true';
}

function initCharts() {
    document.querySelectorAll('canvas[data-chart]').forEach(buildChart);
}

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
    initCharts();
});

document.addEventListener('livewire:navigated', initCharts);
