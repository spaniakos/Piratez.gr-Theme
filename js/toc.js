/**
 * Table of Contents Generator
 *
 * @package piratez_cyberpunk
 */

(function() {
    'use strict';

    function generateTableOfContents() {
        // Check if TOC is enabled via data attribute
        const tocEnabled = document.body.getAttribute('data-toc-enabled');
        if (tocEnabled === 'false') {
            return; // TOC is disabled
        }

        const entryContent = document.querySelector('.entry-content');
        if (!entryContent) {
            return;
        }

        // Check if TOC already exists
        if (document.querySelector('.toc-container')) {
            return;
        }

        // Find all headings (h2, h3, h4)
        const headings = entryContent.querySelectorAll('h2, h3, h4');
        if (headings.length < 2) {
            return; // Need at least 2 headings
        }

        // Create TOC container
        const tocContainer = document.createElement('div');
        tocContainer.className = 'toc-container';
        tocContainer.setAttribute('role', 'navigation');
        tocContainer.setAttribute('aria-label', 'Table of Contents');

        const tocTitle = document.createElement('h3');
        tocTitle.className = 'toc-title';
        tocTitle.textContent = 'Table of Contents';
        
        // Add toggle button to TOC title
        const tocToggle = document.createElement('button');
        tocToggle.className = 'toc-toggle';
        tocToggle.setAttribute('aria-expanded', 'false');
        tocToggle.setAttribute('aria-label', 'Toggle Table of Contents');
        tocToggle.innerHTML = '<span class="toc-toggle-icon">▼</span>';
        tocTitle.appendChild(tocToggle);
        
        tocContainer.appendChild(tocTitle);

        const tocList = document.createElement('ul');
        tocList.className = 'toc-list';
        
        // Start collapsed (will be controlled by CSS)
        tocContainer.classList.add('toc-collapsed');

        // Generate IDs for headings and create TOC items
        headings.forEach((heading, index) => {
            // Generate unique ID if not present
            let id = heading.id;
            if (!id) {
                id = 'heading-' + index + '-' + heading.textContent.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                heading.id = id;
            }

            // Create TOC item
            const tocItem = document.createElement('li');
            tocItem.className = 'toc-item toc-level-' + heading.tagName.toLowerCase();

            const tocLink = document.createElement('a');
            tocLink.href = '#' + id;
            tocLink.textContent = heading.textContent;
            tocLink.className = 'toc-link';
            tocLink.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.getElementById(id);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Update URL without jumping
                    history.pushState(null, null, '#' + id);
                }
            });

            tocItem.appendChild(tocLink);
            tocList.appendChild(tocItem);
        });

        tocContainer.appendChild(tocList);

        // Toggle functionality
        tocToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            tocContainer.classList.toggle('toc-collapsed');
        });

        // Insert TOC after first paragraph or at the beginning
        const firstParagraph = entryContent.querySelector('p');
        if (firstParagraph && firstParagraph.nextSibling) {
            entryContent.insertBefore(tocContainer, firstParagraph.nextSibling);
        } else {
            entryContent.insertBefore(tocContainer, entryContent.firstChild);
        }

        // Highlight active TOC item on scroll
        highlightActiveTOCItem(headings, tocList);
    }

    function highlightActiveTOCItem(headings, tocList) {
        const tocLinks = tocList.querySelectorAll('.toc-link');
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
                window.requestAnimationFrame(function() {
                    updateActiveItem();
                    ticking = false;
                });
                ticking = true;
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        updateActiveItem(); // Initial update
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', generateTableOfContents);
    } else {
        generateTableOfContents();
    }
})();
