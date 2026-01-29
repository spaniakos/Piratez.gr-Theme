# Piratez Cyberpunk — Theme Scope

## One-line intent

An opinionated, accessible, AI-readable publishing system for serious blogs.

## Scope

This theme is:

- **Blog-first:** Optimized for long-form posts, reading time, table of contents, and clear typography.
- **Theme-scoped:** All behaviour lives in the theme; no plugin ecosystem. Perks are bundled intentionally.
- **Accessibility baseline:** WCAG 2.1 AA target (landmarks, skip link, keyboard nav, focus styles, reduced motion).
- **AI-readable:** Optional llms.txt / sitemap support for discoverability; no SEO scoring or content rewriting.
- **Performance-neutral:** No external JS libraries; minimal JS; lazy-load media; cache-friendly outputs.

## Feature flags

Declared via `add_theme_support()`; enqueues and hooks are gated with `current_theme_supports()`:

- **piratez-toc** — Table of contents (floating toggle, slide-in panel). Used for TOC script and template part.
- **piratez-accessibility** — Optional accessibility enhancements. Used in Phase 2 (e.g. optional a11y scripts if split out). Core a11y (skip link, landmarks) is always present.
- **piratez-ai-index** — AI indexing (llms.txt, sitemap filters). Used in Phase 6; `inc/sitemap.php` is required only when this flag is set.

## Explicit non-goals (frozen)

This theme will **not**:

- Require or ship plugins / addons.
- Add admin UI or settings pages beyond the WordPress Customizer.
- Provide SEO dashboards, scoring, or keyword tools.
- Bundle analytics or tracking.
- Redesign or replace the core comments system.
- Support page builders (Gutenberg blocks are supported; no drag-and-drop page builders).
- Add e-commerce or checkout flows.
- Add marketing popups, newsletter signup overlays, or similar.

If a feature does not improve reading, clarity, or longevity of content, it does not belong.
