(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('navigation');
        const toggle = document.getElementById('sidebarToggle');
        const close = document.getElementById('sidebarClose');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (!sidebar || !toggle || !backdrop) return;

        const mobile = window.matchMedia('(max-width: 992px)');
        let mobileOpen = false;

        function savedCollapsed() {
            try { return localStorage.getItem('sidebarCollapsed') === 'true'; }
            catch (_) { return false; }
        }

        function render() {
            if (mobile.matches) {
                sidebar.classList.remove('collapsed');
                sidebar.classList.toggle('show', mobileOpen);
                sidebar.inert = !mobileOpen;
                backdrop.hidden = !mobileOpen;
                document.body.classList.toggle('navigation-open', mobileOpen);
                toggle.setAttribute('aria-expanded', String(mobileOpen));
                toggle.setAttribute('aria-label', mobileOpen ? 'Cerrar menú' : 'Abrir menú');
            } else {
                mobileOpen = false;
                sidebar.classList.remove('show');
                sidebar.classList.toggle('collapsed', savedCollapsed());
                sidebar.inert = false;
                backdrop.hidden = true;
                document.body.classList.remove('navigation-open');
                toggle.setAttribute('aria-expanded', String(!sidebar.classList.contains('collapsed')));
                toggle.setAttribute('aria-label', sidebar.classList.contains('collapsed') ? 'Expandir menú' : 'Contraer menú');
            }
        }

        function closeMobile() {
            if (!mobileOpen) return;
            mobileOpen = false;
            render();
            toggle.focus();
        }

        toggle.addEventListener('click', function () {
            if (mobile.matches) {
                mobileOpen = !mobileOpen;
            } else {
                const collapsed = !sidebar.classList.contains('collapsed');
                try { localStorage.setItem('sidebarCollapsed', String(collapsed)); } catch (_) {}
            }
            render();
        });
        close?.addEventListener('click', closeMobile);
        backdrop.addEventListener('click', closeMobile);
        sidebar.querySelectorAll('a').forEach(link => link.addEventListener('click', function () {
            if (mobile.matches) closeMobile();
        }));
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeMobile();
        });
        mobile.addEventListener('change', render);
        render();
    });
})();
