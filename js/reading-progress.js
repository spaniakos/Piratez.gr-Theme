/**
 * Reading Progress Bar
 *
 * @package piratez_cyberpunk
 */

(function() {
    'use strict';

    function initReadingProgress() {
        const progressBar = document.querySelector('.reading-progress-bar');
        if (!progressBar) {
            return;
        }

        const article = document.querySelector('.entry-content') || document.querySelector('.site-main');
        if (!article) {
            return;
        }

        function updateProgress() {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const articleTop = article.offsetTop;
            const articleHeight = article.offsetHeight;
            const articleBottom = articleTop + articleHeight;

            let progress = 0;

            if (scrollTop >= articleTop) {
                const scrolled = scrollTop - articleTop;
                const maxScroll = articleHeight - windowHeight;
                progress = Math.min((scrolled / maxScroll) * 100, 100);
            }

            progressBar.style.width = progress + '%';
        }

        // Throttle scroll events
        let ticking = false;
        function onScroll() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    updateProgress();
                    ticking = false;
                });
                ticking = true;
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        updateProgress(); // Initial calculation
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReadingProgress);
    } else {
        initReadingProgress();
    }
})();
