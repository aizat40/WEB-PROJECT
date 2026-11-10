document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.mobile-menu');
    const overlay = document.querySelector('.sidebar-overlay');
    const setMenu = open => {
        document.body.classList.toggle('menu-open', open);
        toggle.setAttribute('aria-expanded', String(open));
    };
    toggle.addEventListener('click', () => setMenu(!document.body.classList.contains('menu-open')));
    overlay.addEventListener('click', () => setMenu(false));
    window.matchMedia('(min-width: 768px)').addEventListener('change', event => {
        if (event.matches) setMenu(false);
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && document.body.classList.contains('menu-open')) {
            setMenu(false);
            toggle.focus();
        }
    });
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.setAttribute('aria-busy', 'true');
            }
        });
    });
});
