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

        let lastProgress = 0;

        // Two-rAF: read dimensions/scroll in frame 1, write width in frame 2 to avoid forced reflow.
        function writeProgressState() {
            progressBar.style.width = lastProgress + '%';
            progressBar.setAttribute('aria-valuenow', String(Math.round(lastProgress)));
            ticking = false;
        }

        function readProgress() {
            const windowHeight = window.innerHeight;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const articleTop = article.getBoundingClientRect().top + scrollTop;
            const articleHeight = article.offsetHeight;

            let progress = 0;
            if (scrollTop >= articleTop) {
                const scrolled = scrollTop - articleTop;
                const maxScroll = articleHeight - windowHeight;
                progress = maxScroll > 0 ? Math.min((scrolled / maxScroll) * 100, 100) : 100;
            }
            lastProgress = progress;
            window.requestAnimationFrame(writeProgressState);
        }

        let ticking = false;
        function onScroll() {
            if (!ticking) {
                window.requestAnimationFrame(readProgress);
                ticking = true;
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll, { passive: true });
        window.addEventListener('pageshow', onScroll);
        if (window.ResizeObserver) { new ResizeObserver(onScroll).observe(article); }
        // Initial calculation (defer to next frame to avoid forced reflow in same tick as other inits)
        requestAnimationFrame(readProgress);
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReadingProgress);
    } else {
        initReadingProgress();
    }
})();

