(function () {
    function startClock() {
        const clock = document.getElementById('headerClockTime');
        const date = document.getElementById('headerClockDate');
        if (!clock || !date) return;

        const timeFormatter = new Intl.DateTimeFormat('es-MX', {
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false,
        });
        const dateFormatter = new Intl.DateTimeFormat('es-MX', {
            day: '2-digit', month: '2-digit', year: 'numeric',
        });
        const fullDateFormatter = new Intl.DateTimeFormat('es-MX', { dateStyle: 'full' });

        function render() {
            const now = new Date();
            clock.textContent = timeFormatter.format(now);
            clock.dateTime = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
            date.textContent = dateFormatter.format(now);
            date.dateTime = `${now.getFullYear()}-${(now.getMonth() + 1).toString().padStart(2, '0')}-${now.getDate().toString().padStart(2, '0')}`;
            date.title = fullDateFormatter.format(now);
        }

        render();
        window.setInterval(render, 1000);
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) render();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startClock);
    } else {
        startClock();
    }
})();
