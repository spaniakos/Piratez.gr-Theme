/* Full temporary installation from the supplied source; never edits that source.
 * WP_SOURCE=/path/to/wordpress-7.1.3 node tests/wordpress.cjs
 */
const fs = require('fs');
const path = require('path');
const os = require('os');
const assert = require('node:assert/strict');
const { spawn } = require('child_process');
const { chromium } = require('playwright');
const wp = process.env.WP_SOURCE;
if (!wp) throw new Error('WP_SOURCE must point to the supplied source.');
const tmp = fs.mkdtempSync(path.join(os.tmpdir(), 'piratez-wordpress-'));
fs.cpSync(wp, path.join(tmp, 'site'), { recursive: true });
const blueprint = { steps: [{ step: 'runPHP', code: "<?php require '/wordpress/wp-load.php'; switch_theme('piratez-cyberpunk'); wp_insert_post(array('post_title'=>'PirateZ Gutenberg validation','post_status'=>'publish','post_content'=>'<!-- wp:paragraph --><p>Initial fixture</p><!-- /wp:paragraph -->'));" }] };
fs.writeFileSync(path.join(tmp, 'blueprint.json'), JSON.stringify(blueprint));
const endpoint = `<?php require __DIR__.'/wp-load.php';
if (isset($_GET['sidebar'])) { set_theme_mod('piratez_sidebar_display', $_GET['sidebar']==='1'); }
if (isset($_GET['base'])) { set_theme_mod('piratez_base_font_size', (int)$_GET['base']); }
if (isset($_GET['mode'])) { set_theme_mod('piratez_dark_mode_default', $_GET['mode']==='dark'?'dark':'light'); }
header('Content-Type: application/json'); echo wp_json_encode(array('version'=>$wp_version,'blocks'=>count(WP_Block_Type_Registry::get_instance()->get_all_registered())));`;
fs.writeFileSync(path.join(tmp, 'site/piratez-validation.php'), endpoint);
const cli = path.join(path.dirname(require.resolve('@wp-playground/cli/package.json')), 'wp-playground.js');
(async () => {
    const server = spawn('node', [cli, 'server', '--php=8.3', '--wordpress-install-mode=install-from-existing-files', '--mount-before-install', path.join(tmp, 'site') + ':/wordpress', '--mount', path.resolve(__dirname, '..') + ':/wordpress/wp-content/themes/piratez-cyberpunk', '--blueprint', path.join(tmp, 'blueprint.json'), '--login', '--port=9400', '--workers=1']);
    let logs = ''; server.stdout.on('data', d => { logs += d; }); server.stderr.on('data', d => { logs += d; });
    let browser;
    try {
        await new Promise((resolve, reject) => {
            const deadline = Date.now() + 45000;
            const tick = setInterval(() => { if (logs.includes('Ready!')) { clearInterval(tick); resolve(); } else if (Date.now() > deadline) { clearInterval(tick); reject(new Error(logs)); } }, 100);
            server.once('exit', code => { clearInterval(tick); reject(new Error('Server exit: ' + code + logs)); });
        });
        browser = await chromium.launch({ headless: true, ...(process.env.PIRATEZ_CHROMIUM_EXECUTABLE ? { executablePath: process.env.PIRATEZ_CHROMIUM_EXECUTABLE, args: ['--no-sandbox', '--disable-dev-shm-usage'] } : {}) });
        const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });
        const errors = []; page.on('pageerror', e => errors.push(e.message));
        await page.goto('http://127.0.0.1:9400/', { waitUntil: 'domcontentloaded' });
        const postUrl = await page.locator('h2 a', { hasText: 'PirateZ Gutenberg validation' }).getAttribute('href');
        const postId = (await page.locator('article').filter({ has: page.locator('h2 a', { hasText: 'PirateZ Gutenberg validation' }) }).getAttribute('id')).replace('post-', '');
        const baseline = await page.evaluate(async () => { const response = await fetch('/piratez-validation.php'); const text = await response.text(); if (!text) throw new Error('Validation endpoint status: ' + response.status); return JSON.parse(text); });
        assert.equal(baseline.version, '7.1.3'); assert.ok(baseline.blocks >= 115);
        await page.goto('http://127.0.0.1:9400/wp-admin/post.php?post=' + postId + '&action=edit', { waitUntil: 'domcontentloaded' });
        if (page.url().includes('wp-login.php')) {
            await page.locator('#user_login').fill('admin'); await page.locator('#user_pass').fill('password');
            await Promise.all([page.waitForURL(url => !url.href.includes('wp-login.php')), page.locator('#wp-submit').click()]);
        }
        await page.waitForFunction(() => window.wp && wp.blocks && wp.blocks.getBlockType('core/tabs') && wp.data.select('core/editor').getCurrentPostId(), null, { timeout: 30000 }).catch(async error => { console.error('Editor URL:', page.url(), (await page.locator('body').innerText()).slice(0,1000)); throw error; });
        const names = await page.evaluate(async () => {
            const create = wp.blocks.createBlock;
            const paragraph = text => create('core/paragraph', { content: text });
            const blocks = [create('core/heading', { content: 'Gutenberg validation' }), paragraph('Ελληνικό κείμενο and <code>&lt;script&gt;</code>'), create('core/code', { content: '    const value = "' + 'x'.repeat(300) + '";\n\treturn value;' }), create('core/preformatted', { content: '    Preserved indentation\n\tPreserved tab' }), create('core/quote', {}, [paragraph('Quotation')]), create('core/table', { body: [{ cells: [{ content: 'Name', tag: 'td' }, { content: 'Value', tag: 'td' }] }] }), create('core/search', { label: 'Search fixture', buttonText: 'Search' }), create('core/navigation', {}, [create('core/navigation-link', { label: 'Home', url: 'http://127.0.0.1:9400/', kind: 'custom' })]), create('core/query', { queryId: 1, query: { perPage: 2, postType: 'post', inherit: false } }, [create('core/post-template', {}, [create('core/post-title', { isLink: true })])]), create('core/list', {}, [create('core/list-item', { content: 'List item' })]), create('core/columns', {}, [create('core/column', {}, [paragraph('Column one')]), create('core/column', {}, [paragraph('Column two')])]), create('core/group', { layout: { type: 'grid', columnCount: 2 } }, [paragraph('Grid one'), paragraph('Grid two')]), create('core/buttons', {}, [create('core/button', { text: 'Button', url: '#button' })]), create('core/details', { summary: 'Disclosure' }, [create('core/heading', { level: 3, content: 'Details heading' }), paragraph('Details text')]), create('core/accordion', {}, [create('core/accordion-item', {}, [create('core/accordion-heading', { title: 'Accordion question' }), create('core/accordion-panel', {}, [create('core/heading', { level: 3, content: 'Accordion heading' }), paragraph('Accordion answer')])])]), create('core/tabs', {}, [create('core/tab-list', { tabs: [{ label: 'First' }, { label: 'Second' }] }), create('core/tab-panels', {}, [create('core/tab-panel', { label: 'First' }, [paragraph('First tab body')]), create('core/tab-panel', { label: 'Second' }, [paragraph('Second tab body')])])]), create('core/shortcode', { text: '[piratez_take]Editorial content[/piratez_take]' }), create('core/group', { align: 'full' }, [paragraph('Full width content')]), create('core/paragraph', { content: 'Author overrides', style: { color: { text: '#7b2d43' }, typography: { fontSize: '23px' }, spacing: { padding: { top: '31px' } } } })];
            wp.data.dispatch('core/block-editor').resetBlocks(blocks);
            await wp.data.dispatch('core/editor').savePost();
            return blocks.map(block => block.name);
        });
        await page.waitForFunction(() => !wp.data.select('core/editor').isSavingPost());
        const canvas = page.frames().find(frame => frame.name() === 'editor-canvas');
        assert.ok(canvas, 'Expected Gutenberg editor iframe');
        await canvas.waitForSelector('.wp-block-code');
        const editorStyle = await canvas.locator('.wp-block-code').evaluate(el => ({ font: getComputedStyle(el).fontFamily, color: getComputedStyle(el).color }));
        assert.ok(editorStyle.font.includes('monospace'), JSON.stringify(editorStyle));
        const contentStyle = await canvas.locator('link[href*="content.css"]').count();
        assert.ok(contentStyle > 0, 'Shared content stylesheet missing in editor iframe');
        await page.goto(postUrl, { waitUntil: 'networkidle' });
        await page.waitForFunction(() => getComputedStyle(document.querySelector('.wp-block-code code')).whiteSpace === 'pre');
        for (const sidebar of [false, true]) for (const width of [375, 1440]) for (const mode of ['light', 'dark']) {
            await page.evaluate(async sidebar => fetch('/piratez-validation.php?sidebar=' + (sidebar ? '1' : '0')), sidebar);
            await page.setViewportSize({ width, height: 1000 });
            await page.evaluate(mode => localStorage.setItem('piratez-theme', mode), mode);
            await page.reload({ waitUntil: 'networkidle' });
            const computed = await page.locator('.entry-content pre.wp-block-code').evaluate(el => ({ whitespace: getComputedStyle(el.querySelector('code')).whiteSpace, scroll: el.scrollWidth > el.clientWidth, color: getComputedStyle(el).color }));
            assert.equal(computed.whitespace, 'pre'); assert.equal(computed.scroll, true);
            assert.ok(parseFloat(await page.locator('.entry-content pre.wp-block-code').evaluate(el => getComputedStyle(el).marginBottom)) > 0, 'Content spacing overridden by frontend reset');
            assert.ok(parseFloat(await page.locator('.entry-content blockquote').evaluate(el => getComputedStyle(el).paddingTop)) > 0, 'Quote padding overridden by reset');
            assert.equal(computed.color, mode === 'dark' ? 'rgb(224, 224, 224)' : 'rgb(26, 26, 46)');
            assert.ok(await page.locator('.piratez-callout.piratez-take').count());
            assert.equal(await page.locator('.wp-block-query .wp-block-post-title').count(), 2, 'Dynamic query did not render');
            assert.ok(await page.locator('link[rel="stylesheet"]').evaluateAll(links => links.every(link => link.getAttribute('data-piratez-async') === '1' || link.media === 'print')), 'Frontend stylesheet bypassed asynchronous loading');
            assert.equal(await page.locator('.entry-content').evaluate(el => el.textContent.includes('Ελληνικό κείμενο')), true);
            const override = await page.locator('.entry-content p', { hasText: 'Author overrides' }).evaluate(el => getComputedStyle(el).color);
            assert.equal(override, 'rgb(123, 45, 67)');
            if (width === 375) {
                await page.locator('.entry-content .wp-block-navigation__responsive-container-open').click();
                const overlay = page.locator('.entry-content .wp-block-navigation__responsive-container.is-menu-open');
                await overlay.waitFor({ state: 'visible' });
                assert.equal(await overlay.evaluate(el => getComputedStyle(el).backgroundColor), mode === 'dark' ? 'rgb(26, 26, 46)' : 'rgb(255, 255, 255)');
                assert.equal(await overlay.evaluate(el => getComputedStyle(el).color), mode === 'dark' ? 'rgb(224, 224, 224)' : 'rgb(26, 26, 46)');
                await page.keyboard.press('Escape');
                await overlay.waitFor({ state: 'hidden' });
            }

        }
        await page.locator('.wp-block-accordion-heading__toggle').click();
        await page.waitForFunction(() => document.querySelector('.wp-block-accordion-heading__toggle').getAttribute('aria-expanded') === 'true');
        await page.getByRole('tab', { name: 'Second', exact: true }).click();
        assert.equal(await page.getByRole('tab', { name: 'Second', exact: true }).getAttribute('aria-selected'), 'true');
        assert.equal(await page.getByText('Second tab body', { exact: true }).isVisible(), true);
        // Non-default Customizer size must scale rem units only inside the canvas.
        await page.evaluate(async () => fetch('/piratez-validation.php?base=22&mode=dark'));
        await page.reload({ waitUntil: 'networkidle' });
        const frontendSize = await page.locator('.entry-content pre.wp-block-code').evaluate(el => getComputedStyle(el).fontSize);
        await page.goto('http://127.0.0.1:9400/wp-admin/post.php?post=' + postId + '&action=edit', { waitUntil: 'domcontentloaded' });
        await page.waitForFunction(() => window.wp && wp.data && wp.data.select('core/editor').getCurrentPostId());
        await page.waitForSelector('iframe[name="editor-canvas"]');
        const resizedCanvas = page.frames().find(frame => frame.name() === 'editor-canvas');
        assert.ok(resizedCanvas, 'Expected editor canvas after reload');
        await resizedCanvas.waitForSelector('.wp-block-code');
        await resizedCanvas.waitForFunction(() => getComputedStyle(document.documentElement).fontSize === '22px');
        const resizedEditorSize = await resizedCanvas.locator('.wp-block-code').evaluate(el => getComputedStyle(el).fontSize);
        assert.equal(resizedEditorSize, frontendSize, 'Customizer code font size differs between editor and frontend');
        assert.equal(await resizedCanvas.locator('.wp-block-code').evaluate(el => getComputedStyle(el).color), 'rgb(224, 224, 224)');
        assert.notEqual(await page.locator('html').evaluate(el => getComputedStyle(el).fontSize), '22px', 'Admin UI must not be resized');
        assert.deepEqual(errors, [], 'Browser script errors');
        console.log('PASS: live WordPress ' + baseline.version + ', PHP 8.3, editor iframe/save, ' + names.length + ' parent blocks, 8 frontend scenarios, core accordion/tabs interaction, Greek text, shortcodes, author overrides, Customizer size/dark editor parity.');
        if (process.env.PIRATEZ_SCREENSHOT) await page.screenshot({ path: process.env.PIRATEZ_SCREENSHOT, fullPage: true });
    } finally { if (browser) await browser.close(); server.kill('SIGTERM'); }
})().catch(error => { console.error(error); process.exitCode = 1; });
