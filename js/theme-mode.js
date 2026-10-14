/** Apply palette before asynchronous CSS and preserve automatic/manual preferences. */
(function () {
    'use strict';
    const html = document.documentElement;
    const system = window.matchMedia('(prefers-color-scheme: dark)');
    let manual = null;
    try {
        const saved = localStorage.getItem('piratez-theme');
        if (saved === 'light' || saved === 'dark') { manual = saved; }
    } catch (error) { /* Storage may be unavailable; retain in-memory behavior. */ }
    function automatic() {
        return system.matches ? 'dark' : (html.getAttribute('data-default-theme') || 'light');
    }
    function apply(mode) {
        html.setAttribute('data-theme', mode);
        if (document.body) {
            document.body.classList.toggle('dark-mode', mode === 'dark');
            document.body.classList.toggle('light-mode', mode !== 'dark');
        }
        const button = document.querySelector('.dark-mode-toggle');
        if (button) { button.setAttribute('aria-pressed', mode === 'dark' ? 'true' : 'false'); }
    }
    window.piratezThemeMode = {
        refresh: function () { apply(manual || automatic()); },
        toggle: function () {
            manual = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            try { localStorage.setItem('piratez-theme', manual); } catch (error) {}
            apply(manual);
        }
    };
    apply(manual || automatic());
    function systemChanged() { if (!manual) { apply(automatic()); } }
    if (system.addEventListener) { system.addEventListener('change', systemChanged); }
    else if (system.addListener) { system.addListener(systemChanged); }
    window.addEventListener('storage', function (event) {
        if (event.key !== null && event.key !== 'piratez-theme') { return; }
        manual = event.newValue === 'light' || event.newValue === 'dark' ? event.newValue : null;
        apply(manual || automatic());
    });
})();
