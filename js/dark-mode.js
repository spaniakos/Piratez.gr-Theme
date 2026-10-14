/** Toggle wiring; palette bootstrap lives in theme-mode.js in the document head. */
(function () {
    'use strict';
    function init() {
        if (!window.piratezThemeMode) { return; }
        window.piratezThemeMode.refresh();
        const toggle = document.querySelector('.dark-mode-toggle');
        if (toggle) { toggle.addEventListener('click', window.piratezThemeMode.toggle); }
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); }
    else { init(); }
})();
