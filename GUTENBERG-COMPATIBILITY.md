# Gutenberg compatibility — PirateZ 1.1.0

## Supported baseline

Targets the supplied WordPress 7.1.3 source archive (115 core block definitions), PHP 7.4 or newer. No Gutenberg plugin is required. Theme stays a classic PHP theme with the existing Customizer and editorial shortcodes.

## Implementation

- `theme.json` v3 defines shared palette, typography, spacing, normal/wide widths, and code/button defaults.
- `css/content.css` provides low-specificity defaults for core content and raw HTML; core remains responsible for nested layout, responsive viewport support, accessibility state, and interactive scripts.
- `css/editor-style.css` restores the missing canvas stylesheet. Shared Customizer variables reach the editor iframe through `enqueue_block_assets`. The editor uses the configured default palette; reader-local frontend preferences are separate.
- Code panels preserve tabs/indentation and scroll long lines inside the panel. Inline code wraps without widening the page. No syntax highlighter or copy button is bundled.
- Full alignment spans the viewport minus gutters on desktop without a sidebar; it stays inside the available article column with a sidebar and on smaller screens.
- Frontend stylesheet links remain asynchronous. WordPress's HTML parser preserves attributes and original media; no-JavaScript fallbacks use the original link. Existing onload handlers, disabled links, and genuine print styles remain untouched. Inline styles retain native core handling.
- Theme classic scripts use WordPress's native `defer` strategy. Core script modules are not rewritten.
- WordPress retains image loading/fetch-priority decisions. The TOC preserves author anchors, uses safe encoded links, accounts for nested positions, and opens controlled disclosures before scrolling.

## Validation completed

- PHP 7.4 syntax parsing and PHP 8.5 runtime lint: 30 PHP files.
- CSS parsing: 6 stylesheets. JavaScript syntax: 12 files.
- Supplied core `WP_Theme_JSON` sanitization with all 115 definitions registered: palette, fonts, layout, and block styles survive.
- Supplied core `WP_HTML_Tag_Processor` tests: async conversion, attribute/media retention, fallbacks, idempotency, print/existing-handler/disabled-link exceptions, scripts unchanged.
- Chromium: 18 content scenarios (frontend/editor, light/dark, 375/768/1440px; frontend sidebar on/off). Code scrolling/whitespace/contrast, explicit author overrides, columns/grid, buttons, disclosures, and quoted TOC anchors pass.
- Live temporary WordPress 7.1.3 installation on PHP 8.3: editor iframe assets, creation/saving of 19 parent-block fixtures, 8 frontend scenarios, dynamic query rendering, asynchronous stylesheet markers, accordion/tab interaction, mobile navigation overlays in both palettes (including Escape), Greek text, existing editorial shortcode, explicit author styles, and Customizer base-size/dark editor parity.
- Chromium: delayed stylesheet loading and JavaScript-disabled fallbacks pass; original media queries remain effective after loading and viewport changes.

The tests include a live disposable installation as well as component fixtures. They do not certify every core block or third-party plugin combination. Media providers, remaining block/template contexts, and the production site’s data/plugins still require staging review. Third-party blocks are outside the core coverage inventory.

## Reproduce component tests

```sh
php tests/async-styles.php /path/to/wordpress-7.1.3 /tmp/piratez-async-link.html
php tests/theme-json.php /path/to/wordpress-7.1.3
cd tests
npm install
npx playwright install chromium
WP_SOURCE=/path/to/wordpress-7.1.3 node content.cjs
WP_SOURCE=/path/to/wordpress-7.1.3 node wordpress.cjs
ASYNC_LINK=/tmp/piratez-async-link.html node async-browser.cjs
```

## Release checks

Before merging/releasing: verify the supplied installation, real iframe editor previews, editable/saved content, all applicable core block contexts (including child/template blocks), slow-network loading, keyboard interactions, existing posts/shortcodes, and plugins on a staging copy. “Tested up to: 7.1.3” records the temporary installation checks above; it does not imply every block variation or plugin combination has been certified. Complete staging checks before claiming 100% site compatibility.

## Core inventory

Children and structural blocks are tested in their required parent/template context; not every definition is independently insertable in a post.

| Definition | Declared supports relevant to styling | Site-level status |
| --- | --- | --- |
| `core/accordion` | align, color, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/accordion-heading` | color, typography, spacing, interactivity | Live fixture verified; other variations require staging review |
| `core/accordion-item` | color, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/accordion-panel` | color, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/archives` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/audio` | align, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/avatar` | align, color, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/block` | interactivity | Core behavior retained; remaining contexts require staging review |
| `core/breadcrumbs` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/button` | color, typography, spacing, interactivity | Live fixture verified; other variations require staging review |
| `core/buttons` | align, color, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/calendar` | align, color, typography, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/categories` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/code` | align, color, typography, spacing, interactivity | Live fixture verified; other variations require staging review |
| `core/column` | color, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/columns` | align, color, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/comment-author-name` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/comment-content` | color, typography, spacing | Core behavior retained; remaining contexts require staging review |
| `core/comment-date` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/comment-edit-link` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/comment-reply-link` | color, typography, spacing | Core behavior retained; remaining contexts require staging review |
| `core/comment-template` | align, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/comments` | align, color, typography, spacing | Core behavior retained; remaining contexts require staging review |
| `core/comments-pagination` | align, color, typography, layout, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/comments-pagination-next` | color, typography, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/comments-pagination-numbers` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/comments-pagination-previous` | color, typography, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/comments-title` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/cover` | align, color, typography, spacing, layout, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/details` | align, color, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/embed` | align, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/file` | align, color, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/footnotes` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/freeform` | Core defaults | Core behavior retained; remaining contexts require staging review |
| `core/gallery` | align, color, spacing, layout, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/group` | align, color, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/heading` | align, color, typography, spacing, interactivity | Live fixture verified; other variations require staging review |
| `core/home-link` | typography, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/html` | interactivity | Core behavior retained; remaining contexts require staging review |
| `core/icon` | align, color, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/image` | align, color, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/latest-comments` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/latest-posts` | align, color, typography, spacing, layout, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/legacy-widget` | Core defaults | Core behavior retained; remaining contexts require staging review |
| `core/list` | color, typography, spacing, interactivity | Live fixture verified; other variations require staging review |
| `core/list-item` | color, typography, spacing, interactivity | Live fixture verified; other variations require staging review |
| `core/loginout` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/math` | color, typography, spacing | Core behavior retained; remaining contexts require staging review |
| `core/media-text` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/missing` | interactivity | Core behavior retained; remaining contexts require staging review |
| `core/more` | interactivity | Core behavior retained; remaining contexts require staging review |
| `core/navigation` | align, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/navigation-link` | typography, interactivity | Live fixture verified; other variations require staging review |
| `core/navigation-overlay-close` | color, typography, spacing | Core behavior retained; remaining contexts require staging review |
| `core/navigation-submenu` | typography, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/nextpage` | interactivity | Core behavior retained; remaining contexts require staging review |
| `core/page-list` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/page-list-item` | interactivity | Core behavior retained; remaining contexts require staging review |
| `core/paragraph` | align, color, typography, spacing, interactivity | Live fixture verified; other variations require staging review |
| `core/pattern` | interactivity | Core behavior retained; remaining contexts require staging review |
| `core/playlist` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/playlist-track` | interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-author` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-author-biography` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-author-name` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-comments-count` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-comments-form` | color, typography, spacing | Core behavior retained; remaining contexts require staging review |
| `core/post-comments-link` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-content` | align, color, typography, spacing, layout, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-date` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-excerpt` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-featured-image` | align, color, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-navigation-link` | color, typography, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-template` | align, color, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/post-terms` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-time-to-read` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/post-title` | align, color, typography, spacing, interactivity | Live fixture verified; other variations require staging review |
| `core/preformatted` | color, typography, spacing, interactivity | Live fixture verified; other variations require staging review |
| `core/pullquote` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/query` | align, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/query-no-results` | align, color, typography, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/query-pagination` | align, color, typography, layout, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/query-pagination-next` | color, typography, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/query-pagination-numbers` | color, typography, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/query-pagination-previous` | color, typography, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/query-title` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/query-total` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/quote` | align, color, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/read-more` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/rss` | align, color, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/search` | align, color, typography, spacing, interactivity | Live fixture verified; other variations require staging review |
| `core/separator` | align, color, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/shortcode` | Core defaults | Live fixture verified; other variations require staging review |
| `core/site-logo` | align, color, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/site-tagline` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/site-title` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/social-link` | interactivity | Core behavior retained; remaining contexts require staging review |
| `core/social-links` | align, color, spacing, layout, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/spacer` | spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/tab-list` | color, typography, spacing, layout | Live fixture verified; other variations require staging review |
| `core/tab-panel` | color, typography, spacing, layout | Live fixture verified; other variations require staging review |
| `core/tab-panels` | color, typography, spacing | Live fixture verified; other variations require staging review |
| `core/table` | align, color, typography, spacing, interactivity | Live fixture verified; other variations require staging review |
| `core/tabs` | align, color, typography, spacing, layout, interactivity | Live fixture verified; other variations require staging review |
| `core/tag-cloud` | align, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/template-part` | align, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/term-count` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/term-description` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/term-name` | align, color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/term-template` | align, color, typography, spacing, layout, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/terms-query` | align, layout, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/text-columns` | interactivity | Core behavior retained; remaining contexts require staging review |
| `core/verse` | color, typography, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/video` | align, spacing, interactivity | Core behavior retained; remaining contexts require staging review |
| `core/widget-group` | Core defaults | Core behavior retained; remaining contexts require staging review |
