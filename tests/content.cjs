/* Run: WP_SOURCE=/path/to/wordpress node tests/content.cjs
 * Install Playwright in tests/ and install its Chromium browser first.
 */
const fs = require('fs');
const path = require('path');
const assert = require('node:assert/strict');
const { chromium } = require('playwright');
const theme = path.resolve(__dirname, '..');
const wp = process.env.WP_SOURCE;
if (!wp) throw new Error('WP_SOURCE must point to the supplied WordPress source.');
const core = path.join(wp, 'wp-includes/css/dist/block-library');
const code = 'const value = "' + 'x'.repeat(300) + '";';
const content = `<h2 id="custom&quot;anchor">Nested content</h2><p>Κείμενο <code>&lt;script&gt;</code></p>
<pre class="wp-block-code"><code>    ${code}\n\treturn value;</code></pre>
<figure class="wp-block-table"><table><thead><tr><th>Column</th></tr></thead><tbody><tr><td>${'word '.repeat(80)}</td></tr></tbody></table></figure>
<div class="wp-block-columns is-layout-flex"><div class="wp-block-column"><p>Column one</p></div><div class="wp-block-column"><p>Column two</p></div></div>
<div class="wp-block-group is-layout-grid"><p>Grid one</p><p>Grid two</p></div>
<div class="wp-block-buttons"><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#">Button</a></div></div>
<p class="has-text-color" style="color:rgb(123, 45, 67);padding:31px;font-size:23px">Author overrides</p>
<details class="wp-block-details"><summary>Disclosure</summary><h3>Hidden heading</h3><p>Hidden text</p></details>
<figure class="wp-block-image alignwide"><img width="1600" height="800" alt="" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1600' height='800'/%3E"></figure>
<div class="wp-block-group alignfull"><p>Full width</p></div>
<aside class="piratez-callout piratez-take"><p class="piratez-callout-title">PirateZ Take</p><p>Editorial content</p></aside>`;
const light = '--color-bg-primary:#fff;--color-bg-secondary:#f5f5f5;--color-surface:#fff;--color-text-primary:#1a1a2e;--color-border:#e0e0e0;--color-accent-primary:#0066cc;--font-body:system-ui;--font-heading:system-ui;';
const dark = '--color-bg-primary:#1a1a2e;--color-bg-secondary:#0f0f1a;--color-surface:#252540;--color-text-primary:#e0e0e0;--color-border:#3a3a5a;--color-accent-primary:#00a8ff;';
function fixture(editor, mode, sidebar) {
    const sheets = [fs.readFileSync(path.join(core, 'style.css'), 'utf8'), fs.readFileSync(path.join(core, 'theme.css'), 'utf8')];
    sheets.push(fs.readFileSync(path.join(theme, 'css', editor ? 'editor-style.css' : 'style.css'), 'utf8'));
    sheets.push(fs.readFileSync(path.join(theme, 'css/content.css'), 'utf8'));
    sheets.push(`:root{${light}--wp--style--global--content-size:800px;--wp--style--global--wide-size:1200px}html[data-theme=dark]{${dark}}.editor-styles-wrapper{${mode === 'dark' ? dark : light}}.is-layout-flex{display:flex;gap:2rem}.is-layout-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:2rem}`);
    return `<!doctype html><html data-theme="${mode}"><head><meta name="viewport" content="width=device-width,initial-scale=1"><style>${sheets.join('\n')}</style></head><body class="${sidebar ? 'sidebar-enabled' : ''}">${editor ? `<div class="editor-styles-wrapper"><div class="is-root-container">${content}</div></div>` : `<div class="site-content"><main class="site-main"><article><div class="entry-content">${content}</div></article></main>${sidebar ? '<aside class="sidebar">Sidebar</aside>' : ''}</div><nav id="toc-root" class="piratez-toc-root"></nav>`}</body></html>`;
}
(async () => {
    const browser = await chromium.launch({ headless: true, ...(process.env.PIRATEZ_CHROMIUM_EXECUTABLE ? { executablePath: process.env.PIRATEZ_CHROMIUM_EXECUTABLE, args: ['--no-sandbox', '--disable-dev-shm-usage'] } : {}) });
    let scenarios = 0;
    try {
        for (const editor of [false, true]) for (const mode of ['light', 'dark']) for (const width of [375, 768, 1440]) for (const sidebar of editor ? [false] : [false, true]) {
            const page = await browser.newPage({ viewport: { width, height: 900 } });
            await page.setContent(fixture(editor, mode, sidebar));
            const result = await page.evaluate(() => {
                const pre = document.querySelector('pre');
                const override = document.querySelector('.has-text-color');
                return { overflow: document.documentElement.scrollWidth > innerWidth, codeScroll: pre.scrollWidth > pre.clientWidth, whitespace: getComputedStyle(pre.querySelector('code')).whiteSpace, codeColor: getComputedStyle(pre).color, color: getComputedStyle(override).color, padding: getComputedStyle(override).paddingTop, font: getComputedStyle(override).fontSize, columns: getComputedStyle(document.querySelector('.wp-block-columns')).display, grid: getComputedStyle(document.querySelector('.is-layout-grid')).display, button: getComputedStyle(document.querySelector('.wp-block-button__link')).textDecorationLine };
            });
            const label = JSON.stringify({ editor, mode, width, sidebar });
            assert.equal(result.overflow, false, 'Page overflow: ' + label);
            assert.equal(result.codeScroll, true, 'Code panel should scroll: ' + label);
            assert.equal(result.whitespace, 'pre', 'Whitespace changed: ' + label);
            assert.equal(result.codeColor, mode === 'dark' ? 'rgb(224, 224, 224)' : 'rgb(26, 26, 46)', 'Code contrast: ' + label);
            assert.equal(result.color, 'rgb(123, 45, 67)'); assert.equal(result.padding, '31px'); assert.equal(result.font, '23px');
            assert.equal(result.columns, 'flex'); assert.equal(result.grid, 'grid'); assert.equal(result.button, 'none');
            if (!editor && width === 1440 && mode === 'light' && !sidebar) {
                await page.addScriptTag({ path: path.join(theme, 'js/toc.js') });
                await page.locator('.piratez-toc-fab').click();
                await page.locator('.toc-link', { hasText: 'Hidden heading' }).click();
                assert.equal(await page.locator('details').evaluate(el => el.open), true);
                await page.locator('.toc-link', { hasText: 'Nested content' }).click();
                assert.ok((await page.evaluate(() => location.hash)).includes('%22'));
            }
            scenarios++; await page.close();
        }
        console.log('PASS: ' + scenarios + ' content scenarios; code scrolling, colors, overrides, nested layouts, buttons, disclosures, TOC anchors.');
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
