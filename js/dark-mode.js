/**
 * Dark/Light Mode Toggle
 *
 * @package piratez_cyberpunk
 */

(function() {
    'use strict';

    const darkModeToggle = document.querySelector('.dark-mode-toggle');
    const body = document.body;
    const html = document.documentElement;

    // Get saved preference or default
    function getSavedTheme() {
        const saved = localStorage.getItem('piratez-theme');
        if (saved) {
            return saved;
        }
        
        // Check for system preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        
        // Get default from theme mod
        const defaultMode = document.body.getAttribute('data-default-theme') || 'light';
        return defaultMode;
    }

    // Apply theme
    function applyTheme(theme) {
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            body.classList.remove('light-mode');
            html.setAttribute('data-theme', 'dark');
        } else {
            body.classList.add('light-mode');
            body.classList.remove('dark-mode');
            html.setAttribute('data-theme', 'light');
        }
        
        // Save preference
        localStorage.setItem('piratez-theme', theme);
    }

    // Initialize theme
    function initTheme() {
        const theme = getSavedTheme();
        applyTheme(theme);
    }

    // Toggle theme
    function toggleTheme() {
        const currentTheme = body.classList.contains('dark-mode') ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
    }

    // Event listeners
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', toggleTheme);
    }

    // Listen for system theme changes
    if (window.matchMedia) {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', (e) => {
            // Only auto-switch if user hasn't manually set a preference
            if (!localStorage.getItem('piratez-theme')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    // Initialize on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTheme);
    } else {
        initTheme();
    }
})();
