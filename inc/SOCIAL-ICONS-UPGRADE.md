# Social Media Icons Upgrade Guide

## Current Implementation

The social media links shortcode currently uses inline SVG icons. These are:
- ✅ No external dependencies
- ✅ Scalable and crisp at any size
- ✅ Lightweight
- ✅ Work immediately

## Upgrade to Image Files

To replace SVG icons with image files:

### Step 1: Create Icons Directory
Create the following directory structure:
```
/wp-content/themes/piratez-cyberpunk/images/social/
```

### Step 2: Add Icon Files
Add your icon files (SVG, PNG, or WebP) with these exact names:
- `twitter.svg` (or `.png`, `.webp`)
- `facebook.svg`
- `instagram.svg`
- `linkedin.svg`
- `github.svg`
- `youtube.svg`
- `email.svg`

### Step 3: Update Shortcode Code
In `/inc/shortcodes.php`, find the icon output section (around line 82) and replace:

**Current (SVG):**
```php
$output .= '<span class="social-icon social-icon-' . esc_attr($network) . '">' . $data['icon'] . '</span>';
```

**New (Image):**
```php
$icon_path = get_template_directory_uri() . '/images/social/' . esc_attr($network) . '.svg';
$icon_exists = file_exists(get_template_directory() . '/images/social/' . esc_attr($network) . '.svg');
if ($icon_exists) {
    $output .= '<span class="social-icon social-icon-' . esc_attr($network) . '">';
    $output .= '<img src="' . esc_url($icon_path) . '" alt="' . esc_attr($data['label']) . '" width="20" height="20">';
    $output .= '</span>';
} else {
    // Fallback to SVG if image doesn't exist
    $output .= '<span class="social-icon social-icon-' . esc_attr($network) . '">' . $data['icon'] . '</span>';
}
```

### Step 4: Update CSS (Optional)
If using image files, update `/css/style.css`:

```css
.social-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.social-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}
```

## Alternative: Font Awesome Icons

If you prefer Font Awesome icons:

1. **Enqueue Font Awesome** in `functions.php`:
```php
wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
```

2. **Update icon array** in `shortcodes.php`:
```php
'twitter' => array('label' => __('Twitter', 'piratez-cyberpunk'), 'icon' => '<i class="fab fa-twitter"></i>'),
'facebook' => array('label' => __('Facebook', 'piratez-cyberpunk'), 'icon' => '<i class="fab fa-facebook"></i>'),
// etc...
```

## Recommended Icon Sources

- **Simple Icons**: https://simpleicons.org/ (Free SVG icons)
- **Font Awesome**: https://fontawesome.com/ (Free tier available)
- **Heroicons**: https://heroicons.com/ (Free SVG icons)
- **Feather Icons**: https://feathericons.com/ (Free SVG icons)

## Current SVG Icons

The current SVG icons are brand-accurate and use `fill="currentColor"` which allows them to inherit text color. They can be styled via CSS:

```css
.social-link:hover .social-icon svg {
    color: var(--color-accent-primary);
}
```
