=== Piratez Cyberpunk ===
Contributors: makeasite
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl.html

Pirate + cyberpunk WordPress theme for tech blogs. Dark/light mode, sticky header, reading time, floating TOC, editorial callouts (Phase 3), WCAG 2.1 AA accessibility, social links shortcode.

== Description ==

Piratez Cyberpunk is a professional WordPress theme that shares the clean design character of makeasite.gr but with playful pirate and cyberpunk accents. Perfect for tech blogs with a fun personality while maintaining a business-oriented, architecture-focused foundation.

Features:
* Dark / light mode toggle
* Sticky header (scroll-based)
* Reading time and reading progress bar
* Table of contents: floating button + slide-in panel (desktop) / bottom-sheet (mobile); close via button, ESC, or link; keyboard accessible; respects reduced motion
* Editorial callouts (Phase 3): [piratez_take], [piratez_hard_truth], [piratez_warning], [piratez_note] (optional title)
* Social media links shortcode [piratez_social_links]
* Customizer options (logos, colors, layout, sidebar, tagline)
* Widget areas: header top, below menu, sidebar, footer
* Responsive, mobile-first layout
* Accessibility (WCAG 2.1 AA baseline): landmarks, skip link, :focus-visible on interactive elements, full keyboard navigation (menus + TOC), prefers-reduced-motion, contrast-safe variables, minimal ARIA

== Frequently Asked Questions ==

= How do I show social links? =

Use the shortcode [piratez_social_links] in any post, page, or widget. Configure URLs in Appearance > Customize > Social Media Links. You can add a class: [piratez_social_links class="my-class"].

= Where is the theme available? =

This theme is distributed via GitHub. See Theme URI in style.css for the repository or project URL.

= Table of contents (Phase 1) =

On single posts and pages with 2+ headings, a floating TOC button appears (bottom-right, above the scroll-to-top button). Click to open a slide-in panel (desktop) or bottom-sheet (mobile). Close with the × button, ESC key, or (on mobile) by clicking a section link. TOC can be turned off in Appearance > Customize. Scroll-to-heading accounts for the sticky header.

= Accessibility (Phase 2) =

The theme follows a WCAG 2.1 AA baseline: semantic landmarks (header, nav, main, footer), skip-to-content link, visible :focus-visible styles, full keyboard navigation for menus and TOC, and respect for prefers-reduced-motion (no smooth scroll or transitions when the user requests reduced motion). There is no on/off toggle; accessibility is always on.

= Editorial callouts (Phase 3) =

Four shortcodes for styled callouts: [piratez_take] (opinion), [piratez_hard_truth] (blunt truth), [piratez_warning] (caution), [piratez_note] (neutral note). All accept optional title="Your label". Use in posts/pages via Shortcode or Custom HTML block. See Shortcodes in the admin menu or SHORTCODE-TEST-GUIDELINES.md for examples.

== Changelog ==

= 1.0.0 =
* Initial release.
* Dark/light mode, sticky header, reading time, TOC.
* Social links shortcode and Customizer options.
* Widget areas and responsive layout.

= 1.x (Phase 1, 2 & 3) =
* Phase 1: Floating TOC button, slide-in panel (desktop), bottom-sheet (mobile), close button and ESC, keyboard nav, scroll-to-heading offset for sticky header, reduced-motion support.
* Phase 2: Landmarks (role="banner", aria-label on nav, role="contentinfo" on footer), :focus-visible styles, Escape to close mobile menu, prefers-reduced-motion (scroll-behavior and transitions), contrast comments, ARIA on menu toggle and TOC.
* Phase 3: Editorial shortcodes — PirateZ Take, Hard Truth, Warning, Note; optional title attribute; admin Shortcodes page and SHORTCODE-TEST-GUIDELINES.md.

== Upgrade Notice ==

= 1.0.0 =
* Initial release.

== Resources ==

* Google Fonts (Inter, Press Start 2P) - loaded from fonts.googleapis.com; see Google Fonts terms.
* Theme uses no other bundled third-party assets; SVG icons in shortcode are inline and part of the theme (GPL).
