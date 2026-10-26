document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;

    const applyTheme = (mode) => {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const dark = mode === 'dark' || (mode === 'system' && prefersDark);
        root.classList.toggle('dark', dark);
    };

    const stored = localStorage.getItem('hub-theme') || 'system';
    applyTheme(stored);

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if ((localStorage.getItem('hub-theme') || 'system') === 'system') {
            applyTheme('system');
        }
    });

    document.querySelectorAll('[data-theme-switch]').forEach((button) => {
        button.addEventListener('click', () => {
            const next = button.getAttribute('data-theme-switch');
            localStorage.setItem('hub-theme', next);
            applyTheme(next);
        });
    });
});
