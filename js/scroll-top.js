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

        function toggleButton() {
            if (window.pageYOffset > scrollThreshold) {
                scrollButton.classList.add('visible');
            } else {
                scrollButton.classList.remove('visible');
            }
        }

        // Show/hide button on scroll
        let ticking = false;
        function onScroll() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    toggleButton();
                    ticking = false;
                });
                ticking = true;
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        toggleButton(); // Initial check

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
