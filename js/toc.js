/**
 * Table of Contents — Floating button + slide-in panel
 *
 * @package piratez_cyberpunk
 */

(function () {
    'use strict';

    const SCROLL_THRESHOLD = 100;
    const TOC_PANEL_ID = 'piratez-toc-panel';
    const REDUCED_MOTION = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function generateTableOfContents() {
        const tocEnabled = document.body.getAttribute('data-toc-enabled');
        if (tocEnabled === 'false') {
            return;
        }

        const tocRoot = document.getElementById('toc-root');
        const entryContent = document.querySelector('.entry-content');
        if (!tocRoot || !entryContent) {
            return;
        }

        const headings = entryContent.querySelectorAll('h2, h3, h4');
        if (headings.length < 2) {
            return;
        }

        // Ensure headings have IDs
        headings.forEach((heading, index) => {
            if (!heading.id) {
                heading.id = 'heading-' + index + '-' + heading.textContent.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });

        // Panel (nav list)
        const panel = document.createElement('div');
        panel.id = TOC_PANEL_ID;
        panel.className = 'piratez-toc-panel';
        panel.setAttribute('role', 'navigation');
        panel.setAttribute('aria-label', 'Table of Contents');
        panel.hidden = true;

        const panelHeader = document.createElement('div');
        panelHeader.className = 'piratez-toc-panel-header';

        const panelTitle = document.createElement('h3');
        panelTitle.className = 'toc-title';
        panelTitle.textContent = 'Table of Contents';
        panelHeader.appendChild(panelTitle);

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'piratez-toc-panel-close';
        closeBtn.setAttribute('aria-label', 'Close table of contents');
        closeBtn.innerHTML = '<span aria-hidden="true">&times;</span>';
        panelHeader.appendChild(closeBtn);

        panel.appendChild(panelHeader);

        const tocList = document.createElement('ul');
        tocList.className = 'toc-list';

        headings.forEach((heading) => {
            const tocItem = document.createElement('li');
            tocItem.className = 'toc-item toc-level-' + heading.tagName.toLowerCase();
            const tocLink = document.createElement('a');
            tocLink.href = '#' + heading.id;
            tocLink.textContent = heading.textContent;
            tocLink.className = 'toc-link';
            tocLink.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.getElementById(heading.id);
                if (target) {
                    target.scrollIntoView({ behavior: REDUCED_MOTION ? 'auto' : 'smooth', block: 'start' });
                    history.pushState(null, null, '#' + heading.id);
                }
                if (window.matchMedia('(max-width: 1024px)').matches) {
                    setPanelOpen(false);
                }
            });
            tocItem.appendChild(tocLink);
            tocList.appendChild(tocItem);
        });

        panel.appendChild(tocList);
        tocRoot.appendChild(panel);

        // FAB button
        const fab = document.createElement('button');
        fab.type = 'button';
        fab.className = 'piratez-toc-fab';
        fab.setAttribute('aria-label', 'Table of contents');
        fab.setAttribute('aria-expanded', 'false');
        fab.setAttribute('aria-controls', TOC_PANEL_ID);
        fab.innerHTML = '<span class="piratez-toc-fab-icon" aria-hidden="true">≡</span>';
        tocRoot.insertBefore(fab, panel);

        tocRoot.setAttribute('aria-hidden', 'false');

        // Show FAB when TOC exists; also show after scroll threshold (so it stays visible when scrolling)
        tocRoot.classList.add('toc-fab-visible');

        function setPanelOpen(open) {
            const isOpen = !!open;
            fab.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            panel.hidden = !isOpen;
            tocRoot.classList.toggle('toc-panel-open', isOpen);
            if (isOpen) {
                const firstLink = tocList.querySelector('.toc-link');
                if (firstLink) {
                    firstLink.focus();
                }
            } else {
                fab.focus();
            }
        }

        fab.addEventListener('click', function () {
            setPanelOpen(panel.hidden);
        });

        closeBtn.addEventListener('click', function () {
            setPanelOpen(false);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !panel.hidden) {
                setPanelOpen(false);
            }
        });

        // Scroll threshold: show FAB after scrolling down (only add class, never remove — keeps FAB discoverable)
        let ticking = false;
        function updateFabVisible() {
            if (window.pageYOffset > SCROLL_THRESHOLD) {
                tocRoot.classList.add('toc-fab-visible');
            }
        }
        function onScrollThreshold() {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    updateFabVisible();
                    ticking = false;
                });
                ticking = true;
            }
        }
        window.addEventListener('scroll', onScrollThreshold, { passive: true });
        updateFabVisible();

        highlightActiveTOCItem(headings, tocList);
    }

    function highlightActiveTOCItem(headings, tocList) {
        const tocItems = tocList.querySelectorAll('.toc-item');

        function updateActiveItem() {
            let current = '';
            const windowHeight = window.innerHeight;
            const scrollPosition = window.pageYOffset + (windowHeight / 3);

            headings.forEach((heading) => {
                const headingTop = heading.offsetTop;
                if (scrollPosition >= headingTop) {
                    current = heading.id;
                }
            });

            tocItems.forEach((item) => {
                item.classList.remove('active');
            });

            if (current) {
                const activeLink = tocList.querySelector('a[href="#' + current + '"]');
                if (activeLink) {
                    activeLink.parentElement.classList.add('active');
                }
            }
        }

        let ticking = false;
        function onScroll() {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    updateActiveItem();
                    ticking = false;
                });
                ticking = true;
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        updateActiveItem();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', generateTableOfContents);
    } else {
        generateTableOfContents();
    }
})();
