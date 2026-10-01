(function () {
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') return;

        const palette = () => {
            const css = getComputedStyle(document.documentElement);
            return {
                text: css.getPropertyValue('--bs-secondary-color').trim() || css.getPropertyValue('--secondary-color').trim(),
                strong: css.getPropertyValue('--text-color').trim(),
                border: css.getPropertyValue('--border-color').trim(),
            };
        };
        const charts = [];

        document.querySelectorAll('[data-ticket-activity]').forEach(canvas => {
            const current = JSON.parse(canvas.dataset.current);
            const previous = JSON.parse(canvas.dataset.previous);
            const month = Number(canvas.dataset.month);
            const year = Number(canvas.dataset.year);
            const colors = palette();
            const chart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    datasets: [
                        {
                            label: String(year), data: current,
                            backgroundColor: current.map((_, index) => !month || index === month - 1 ? '#399e91' : 'rgba(57, 158, 145, .38)'),
                            borderRadius: 5, maxBarThickness: 22,
                        },
                        {
                            label: String(year - 1), data: previous,
                            backgroundColor: 'rgba(154, 54, 77, .55)', borderRadius: 5, maxBarThickness: 22,
                        },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: colors.text, usePointStyle: true, boxWidth: 9 } } },
                    scales: {
                        x: { ticks: { color: colors.text }, grid: { display: false } },
                        y: { beginAtZero: true, ticks: { color: colors.text, precision: 0 }, grid: { color: colors.border } },
                    },
                },
            });
            charts.push(chart);
        });

        document.querySelectorAll('[data-role-distribution]').forEach(canvas => {
            const values = JSON.parse(canvas.dataset.values);
            const total = values.reduce((sum, value) => sum + Number(value), 0);
            const chart = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: JSON.parse(canvas.dataset.labels),
                    datasets: [{ data: values, backgroundColor: JSON.parse(canvas.dataset.colors), borderWidth: 0, hoverOffset: 6 }],
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '74%', plugins: { legend: { display: false } } },
                plugins: [{
                    id: 'centerTotal',
                    afterDraw({ ctx, chartArea }) {
                        ctx.save();
                        ctx.fillStyle = palette().strong;
                        ctx.font = '700 23px Inter, sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(total.toLocaleString('es-MX'), (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 2);
                        ctx.restore();
                    },
                }],
            });
            charts.push(chart);
        });

        document.addEventListener('themechange', function () {
            const colors = palette();
            charts.forEach(chart => {
                if (chart.config.type === 'bar') {
                    chart.options.plugins.legend.labels.color = colors.text;
                    chart.options.scales.x.ticks.color = colors.text;
                    chart.options.scales.y.ticks.color = colors.text;
                    chart.options.scales.y.grid.color = colors.border;
                }
                chart.update('none');
            });
        });
    });
})();
