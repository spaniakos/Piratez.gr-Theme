/**
 * Main Theme JavaScript
 *
 * @package piratez_cyberpunk
 */

(function () {
    'use strict';

    function initMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navigation = document.querySelector('.main-navigation');
        const primaryMenu = document.querySelector('#primary-menu');
        const body = document.body;

        if (!menuToggle || !navigation) {
            return;
        }

        function closeMenu() {
            navigation.classList.remove('toggled');
            menuToggle.setAttribute('aria-expanded', 'false');
            if (body) body.classList.remove('menu-open');
        }

        menuToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            navigation.classList.toggle('toggled');
            if (body) body.classList.toggle('menu-open');
        });

        document.addEventListener('click', function (event) {
            if (!navigation.contains(event.target) && !menuToggle.contains(event.target)) {
                if (navigation.classList.contains('toggled')) closeMenu();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && navigation.classList.contains('toggled')) {
                closeMenu();
                menuToggle.focus();
            }
        });
    }


    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileMenu);
    } else {
        initMobileMenu();
    }

    // Smooth scroll for anchor links (respect prefers-reduced-motion)
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '#!') {
                return;
            }

            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: prefersReducedMotion ? 'auto' : 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Lazy load images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Add body class for accent intensity
    const accentIntensity = document.body.getAttribute('data-accent-intensity') || 'balanced';
    document.body.classList.add('accent-' + accentIntensity);

    // Sticky Header - WordPress Style (scroll-based)
    // Two-rAF pattern: read scroll in frame 1, write classes in frame 2 to avoid forced reflow.
    if (document.body.classList.contains('sticky-header-enabled')) {
        const header = document.querySelector('.site-header, #masthead');
        if (header) {
            const scrollThreshold = 100; // Start sticking after 100px scroll
            let ticking = false;
            let lastScrollY = 0;

            function writeStickyState() {
                if (lastScrollY > scrollThreshold) {
                    header.classList.add('is-sticky');
                    document.body.classList.add('header-is-sticky');
                } else {
                    header.classList.remove('is-sticky');
                    document.body.classList.remove('header-is-sticky');
                }
                ticking = false;
            }

            function updateStickyHeader() {
                lastScrollY = window.pageYOffset || document.documentElement.scrollTop;
                window.requestAnimationFrame(writeStickyState);
            }

            function onScroll() {
                if (!ticking) {
                    window.requestAnimationFrame(updateStickyHeader);
                    ticking = true;
                }
            }

            // No initial run: correct state at load is "not sticky"; only update on scroll to avoid reflow.
            // Listen to scroll events
            window.addEventListener('scroll', onScroll, { passive: true });
        }
    }

    // Copy link functionality for social sharing
    const copyButtons = document.querySelectorAll('.share-copy');
    copyButtons.forEach(button => {
        button.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            if (url) {
                navigator.clipboard.writeText(url).then(function () {
                    const originalText = button.querySelector('.share-text').textContent;
                    button.querySelector('.share-text').textContent = 'Copied!';
                    button.classList.add('copied');

                    setTimeout(function () {
                        button.querySelector('.share-text').textContent = originalText;
                        button.classList.remove('copied');
                    }, 2000);
                }).catch(function (err) {
                    console.error('Failed to copy:', err);
                });
            }
        });
    });
})();
