/**
 * Scroll to Top Button
 *
 * @package piratez_cyberpunk
 */

(function() {
    'use strict';

    function initScrollToTop() {
        const scrollButton = document.querySelector('.scroll-to-top');
        if (!scrollButton) {
            return;
        }

        const scrollThreshold = 300;
        let showButton = false;

        // Two-rAF: read scroll in frame 1, write classList in frame 2 to avoid forced reflow.
        function writeScrollTopState() {
            if (showButton) {
                scrollButton.classList.add('visible');
            } else {
                scrollButton.classList.remove('visible');
            }
            ticking = false;
        }

        function readScrollTop() {
            showButton = (window.pageYOffset || document.documentElement.scrollTop) > scrollThreshold;
            window.requestAnimationFrame(writeScrollTopState);
        }

        let ticking = false;
        function onScroll() {
            if (!ticking) {
                window.requestAnimationFrame(readScrollTop);
                ticking = true;
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        // Initial check (defer to next frame to avoid forced reflow in same tick as other inits)
        requestAnimationFrame(readScrollTop);

        // Scroll to top on click
        scrollButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScrollToTop);
    } else {
        initScrollToTop();
    }
})();
