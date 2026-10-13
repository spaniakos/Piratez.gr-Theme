/* First generate link markup: php tests/async-styles.php "$WP_SOURCE" /tmp/async-link.html
 * Then: ASYNC_LINK=/tmp/async-link.html node tests/async-browser.cjs
 */
const fs = require('fs');
const assert = require('node:assert/strict');
const { chromium } = require('playwright');
const link = fs.readFileSync(process.env.ASYNC_LINK, 'utf8');
(async () => {
    const browser = await chromium.launch({ headless: true, ...(process.env.PIRATEZ_CHROMIUM_EXECUTABLE ? { executablePath: process.env.PIRATEZ_CHROMIUM_EXECUTABLE, args: ['--no-sandbox', '--disable-dev-shm-usage'] } : {}) });
    try {
        for (const javaScriptEnabled of [true, false]) {
            const context = await browser.newContext({ javaScriptEnabled, viewport: { width: 1024, height: 768 } });
            await context.route('http://piratez.test/async.css', async route => {
                await new Promise(resolve => setTimeout(resolve, 150));
                await route.fulfill({ contentType: 'text/css', body: '.probe{color:rgb(12,34,56)}' });
            });
            const page = await context.newPage();
            await page.setContent('<!doctype html><html><head>' + link + '</head><body><p class="probe">Delayed stylesheet</p></body></html>');
            await page.waitForFunction(() => getComputedStyle(document.querySelector('.probe')).color === 'rgb(12, 34, 56)');
            await page.setViewportSize({ width: 375, height: 768 });
            await page.waitForFunction(() => getComputedStyle(document.querySelector('.probe')).color !== 'rgb(12, 34, 56)');
            assert.equal(await page.locator('link').first().getAttribute('media'), javaScriptEnabled ? 'screen and (min-width: 600px)' : 'print');
            await context.close();
        }
        console.log('PASS: delayed async CSS activates; media queries survive; no-JavaScript fallback applies.');
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
