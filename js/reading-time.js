/**
 * Reading Time Calculator
 *
 * @package piratez_cyberpunk
 */

(function() {
    'use strict';

    function calculateReadingTime(content) {
        const text = content.textContent || content.innerText || '';
        const words = text.trim().split(/\s+/).length;
        const readingTime = Math.ceil(words / 200); // Average 200 words per minute
        return readingTime;
    }

    function updateReadingTime() {
        // Check if reading time is enabled via data attribute
        const readingTimeEnabled = document.body.getAttribute('data-reading-time-enabled');
        if (readingTimeEnabled === 'false') {
            return; // Reading time is disabled
        }

        // Skip if reading time already exists (PHP template already added it)
        if (document.querySelector('.entry-meta .reading-time')) {
            return; // Already added by PHP template, don't duplicate
        }

        const entryContent = document.querySelector('.entry-content');
        if (!entryContent) {
            return;
        }

        const existingReadingTime = document.querySelector('.reading-time-calculated');
        if (existingReadingTime) {
            existingReadingTime.remove();
        }

        const readingTime = calculateReadingTime(entryContent);
        if (readingTime > 0) {
            const readingTimeElement = document.createElement('span');
            readingTimeElement.className = 'reading-time reading-time-calculated';
            readingTimeElement.textContent = readingTime + ' ' + (readingTime === 1 ? 'min read' : 'min read');
            
            const entryMeta = document.querySelector('.entry-meta');
            if (entryMeta) {
                entryMeta.appendChild(readingTimeElement);
            }
        }
    }

    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateReadingTime);
    } else {
        updateReadingTime();
    }
})();
