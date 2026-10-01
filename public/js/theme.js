(function () {
    const storageKey = 'darkModeEnabled';
    let storedTheme = null;

    try {
        storedTheme = localStorage.getItem(storageKey);
    } catch (_) {
        // The theme still works when browser storage is unavailable.
    }

    const systemPrefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? false;
    const initialTheme = storedTheme === null ? (systemPrefersDark ? 'dark' : 'light') : (storedTheme === 'true' ? 'dark' : 'light');
    document.documentElement.setAttribute('data-bs-theme', initialTheme);

    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('darkModeToggle');
        if (!toggle) return;

        function renderToggle() {
            const dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            toggle.setAttribute('aria-pressed', String(dark));
            toggle.setAttribute('aria-label', dark ? 'Activar modo claro' : 'Activar modo oscuro');
            toggle.setAttribute('title', dark ? 'Activar modo claro' : 'Activar modo oscuro');
            const icon = toggle.querySelector('i');
            if (icon) icon.className = dark ? 'fas fa-sun' : 'fas fa-moon';
        }

        renderToggle();
        toggle.addEventListener('click', function () {
            const dark = document.documentElement.getAttribute('data-bs-theme') !== 'dark';
            document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
            try {
                localStorage.setItem(storageKey, String(dark));
            } catch (_) {
                // Keep the chosen theme for this page even without storage.
            }
            renderToggle();
            document.dispatchEvent(new CustomEvent('themechange', { detail: { theme: dark ? 'dark' : 'light' } }));
        });
    });
})();
