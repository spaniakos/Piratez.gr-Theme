/** Match rem-based theme typography inside the iframe without resizing admin UI. */
(function () {
    'use strict';
    wp.domReady(function () {
        const observed = new WeakSet();
        function sizeCanvas(frame) {
            const doc = frame.contentDocument;
            if (doc && doc.documentElement) {
                doc.documentElement.style.fontSize = piratezEditor.baseFontSize + 'px';
            }
        }
        function syncCanvases() {
            document.querySelectorAll('iframe[name="editor-canvas"]').forEach(function (frame) {
                if (!observed.has(frame)) {
                    observed.add(frame);
                    frame.addEventListener('load', function () { sizeCanvas(frame); });
                }
                sizeCanvas(frame);
            });
        }
        new MutationObserver(syncCanvases).observe(document.body, { childList: true, subtree: true });
        syncCanvases();
    });
})();
