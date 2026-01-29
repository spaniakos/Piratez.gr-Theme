# Piratez Cyberpunk

A WordPress theme for tech blogs: pirate + cyberpunk style, dark/light mode, and blog-friendly features. Built to share the clean design character of [makeasite.gr](https://makeasite.gr) with a more playful personality.

**Requires:** WordPress 5.0+ | PHP 7.4+  
**License:** GPLv3 or later

---

## Features

- **Dark / light mode** — Toggle with optional default in Customizer
- **Sticky header** — Scroll-based; sticks after ~100px (Customizer on/off)
- **Reading time** — Per-post estimate with optional display
- **Reading progress bar** — On single posts (optional)
- **Table of contents** — Floating TOC button + slide-in panel (Phase 1; see below)
- **Editorial callouts** — Shortcodes: PirateZ Take, Hard Truth, Warning, Note (Phase 3; see below)
- **Social links shortcode** — `[piratez_social_links]` with optional class
- **Post metadata** — Date, author, “Updated” when modified, categories/tags; all toggleable in Customizer (Phase 4; see below)
- **Customizer options** — Logo, colors, layout, sidebar, tagline, accent intensity
- **Widget areas** — Header top, below menu, sidebar, footer
- **Responsive** — Mobile-first, 2-column post grid on desktop
- **Accessibility** — WCAG 2.1 AA baseline (Phase 2; see below)

---

## Phase 1 — Reading & Navigation UX

Table of contents is on-demand and non-intrusive:

- **Floating TOC button** — Fixed bottom-right (above scroll-to-top), visible on single posts and pages when there are 2+ headings. Toggle opens the TOC panel.
- **Slide-in panel (desktop)** — Panel slides in from the right (~280px). Lists all h2/h3/h4 with active heading highlighted on scroll.
- **Bottom-sheet (mobile)** — Same panel appears as a bottom sheet (max 70vh) on viewports ≤1024px.
- **Scroll threshold** — Button appears when content has 2+ headings; stays visible once shown.
- **Close options** — Close via × button (top right of panel), ESC key, or (on mobile) tapping a section link.
- **Keyboard** — Full keyboard navigation; focus moves to first link when panel opens, back to button when closed.
- **Anchor offset** — Scroll-to-heading accounts for sticky header (configurable `--piratez-sticky-header-height`; 140px desktop, 170px mobile).
- **Reduced motion** — TOC scroll-to-section uses instant scroll when the user has “Reduce motion” enabled.

TOC can be turned off in **Appearance → Customize** (Table of Contents). It is gated by the theme support flag `piratez-toc` (see THEME_SCOPE.md).

---

## Phase 2 — Accessibility (WCAG 2.1 AA baseline)

Accessibility is always on; there is no toggle.

- **Landmarks** — Single `<main id="primary">` per page; header with `role="banner"`; nav with `aria-label="Primary navigation"`; footer with `role="contentinfo"`.
- **Skip-to-content** — Focus-only skip link to `#primary`; visible when focused, with clear focus ring.
- **:focus-visible** — Visible focus outline (2px accent) on menu toggle, dark-mode toggle, TOC FAB, scroll-to-top, nav links, post navigation, header/footer links, and buttons.
- **Keyboard** — Menus open/close with Enter/Space; mobile menu closes with ESC and returns focus to the toggle. TOC supports ESC and full tab order (Phase 1).
- **Prefers-reduced-motion** — Smooth scrolling disabled when the system requests reduced motion; reading progress bar and TOC/scroll-to-top transitions disabled; anchor and TOC scroll use instant scroll.
- **Contrast** — Light and dark mode CSS variables target AA contrast (4.5:1 normal, 3:1 large); see comments in `css/style.css`.
- **ARIA** — Menu toggle: `aria-expanded`, `aria-controls="primary-menu"`. TOC: `aria-expanded`, `aria-controls`, `aria-label` on panel and FAB. No redundant roles.

---

## Phase 3 — Editorial shortcodes

Styled callout boxes for opinion, blunt truth, caution, and neutral notes:

- **PirateZ Take** — `[piratez_take]...[/piratez_take]` — Opinion or editorial stance.
- **Hard Truth** — `[piratez_hard_truth]...[/piratez_hard_truth]` — Blunt or uncomfortable truth.
- **Warning** — `[piratez_warning]...[/piratez_warning]` — Caution, risk, or “proceed with care”.
- **Note** — `[piratez_note]...[/piratez_note]` — Neutral note or insight; optional `title="Insight"` (or any label) to override the default “Note” header.

All four accept an optional `title="Your label"` attribute. Usage and test snippets are in **Shortcodes** (admin menu) and in `SHORTCODE-TEST-GUIDELINES.md`.

---

## Phase 4 — Post metadata (complete)

Post date, author, and “Updated” (when modified ≠ published) are output via template tags and can be toggled in **Appearance → Customize → Blog Features**:

- **Post Date** — Show “Posted on [date]” (default on).
- **Post Author** — Show “by [author]” (default on).
- **Last Updated Date** — Show “Updated [date]” when the post was modified after publishing (default on).
- **Post Categories** — Show “Posted in” categories in entry footer on single posts (default on).
- **Post Tags** — Show “Tagged” tags in entry footer on single posts (default on).

Single and archive/loop templates use `piratez_posted_on()` and `piratez_posted_by()` from `inc/template-tags.php`; both respect the Customizer toggles.

---

## Installation

1. Download or clone this repo into `wp-content/themes/`.
2. In **Appearance → Themes**, activate **Piratez Cyberpunk**.
3. Go to **Appearance → Customize** to set logo, colors, and options.

---

## Usage

### Social links

Use the shortcode in any post, page, or text widget:

```
[piratez_social_links]
```

With a custom class:

```
[piratez_social_links class="my-footer-links"]
```

Configure URLs in **Appearance → Customize → Social Media Links**.  
Shortcode usage is also listed under **Shortcodes** in the admin menu.

### Editorial callouts (Phase 3)

Use in posts or pages (Block editor: Shortcode or Custom HTML block):

- `[piratez_take]Your opinion.[/piratez_take]`
- `[piratez_hard_truth]Blunt truth.[/piratez_hard_truth]`
- `[piratez_warning]Caution text.[/piratez_warning]`
- `[piratez_note]Neutral note.[/piratez_note]` — optional `title="Insight"` (or any label) for a custom header.

See `SHORTCODE-TEST-GUIDELINES.md` for copy-paste test snippets.

### Customizer

- **Theme Settings** — Accent colors, gold, background, dark mode default
- **Layout** — Sidebar on/off, posts per page
- **Header** — Sticky header, tagline display
- **Footer** — Copyright text
- **Blog Features** — Reading time, progress bar, TOC, author box, related posts, scroll-to-top, social sharing; **post metadata** (Phase 4): post date, post author, last updated date, post categories, post tags
- **Social Media** — URLs for Twitter, Facebook, Instagram, LinkedIn, GitHub, YouTube, Email

---

## Widget areas

| Location   | Description                |
| ---------- | -------------------------- |
| Header Top | Above the main header      |
| Below Menu | Between header and content |
| Sidebar    | Main sidebar (desktop)     |
| Footer     | Footer widget columns      |

---

## File structure

```
piratez-cyberpunk/
├── css/           # style.css, pirate.css, cyberpunk.css
├── js/            # theme.js, dark-mode, reading-time, toc, etc.
├── inc/           # customizer, shortcodes, widget areas, template tags
├── template-parts/
├── style.css      # Theme metadata + base
├── readme.txt     # WordPress-style readme
├── LICENSE        # GPLv3
└── README.md      # This file
```

---

## License

GPLv3 or later. See [LICENSE](LICENSE) and [gnu.org/licenses/gpl.html](https://www.gnu.org/licenses/gpl.html).

---

## Author

**makeasite.gr**  
- Site: [makeasite.gr](https://makeasite.gr)  
- Theme blog: [piratez.gr](https://piratez.gr)
