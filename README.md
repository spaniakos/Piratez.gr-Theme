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
- **Table of contents** — Collapsible, optional per post
- **Social links shortcode** — `[piratez_social_links]` with optional class
- **Customizer options** — Logo, colors, layout, sidebar, tagline, accent intensity
- **Widget areas** — Header top, below menu, sidebar, footer
- **Responsive** — Mobile-first, 2-column post grid on desktop
- **Accessibility** — Skip link, keyboard focus, underlined content links

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

### Customizer

- **Theme Settings** — Accent colors, gold, background, dark mode default
- **Layout** — Sidebar on/off, posts per page
- **Header** — Sticky header, tagline display
- **Footer** — Copyright text
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
