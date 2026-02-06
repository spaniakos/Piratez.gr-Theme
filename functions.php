<?php
/**
 * Piratez Cyberpunk Theme Functions
 *
 * @package piratez_cyberpunk
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Theme version
 */
define('PIRATEZ_VERSION', '1.0.0');

/**
 * Theme setup
 */
function piratez_cyberpunk_setup() {
    // Make theme available for translation
    load_theme_textdomain('piratez-cyberpunk', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Set default thumbnail size
    set_post_thumbnail_size(1200, 675, true);

    // Add image sizes
    add_image_size('piratez-featured', 1200, 675, true);
    add_image_size('piratez-thumbnail', 400, 300, true);

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'piratez-cyberpunk'),
        'footer'  => esc_html__('Footer Menu', 'piratez-cyberpunk'),
    ));

    // Switch default core markup for search form, comment form, and comments to output valid HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Add support for editor styles
    add_theme_support('editor-styles');
    add_editor_style('css/editor-style.css');

    // Add support for responsive embeds
    add_theme_support('responsive-embeds');

    // Add support for wide and full alignments
    add_theme_support('align-wide');

    // Add support for block styles
    add_theme_support('wp-block-styles');

    // Feature flags (see THEME_SCOPE.md)
    add_theme_support('piratez-toc');
    add_theme_support('piratez-accessibility');
    add_theme_support('piratez-ai-index');
}
add_action('after_setup_theme', 'piratez_cyberpunk_setup');

/**
 * Set the content width in pixels
 */
function piratez_cyberpunk_content_width() {
    $GLOBALS['content_width'] = apply_filters('piratez_content_width', 1200);
}
add_action('after_setup_theme', 'piratez_cyberpunk_content_width', 0);

/**
 * Return font-family stack for heading/body choice (Inter, Roboto, System UI).
 *
 * @param string $choice Theme mod value.
 * @return string CSS font-family value.
 */
function piratez_get_font_stack($choice) {
    $stacks = array(
        'Inter'     => "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
        'Roboto'    => "'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
        'System UI' => "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
    );
    return isset($stacks[$choice]) ? $stacks[$choice] : $stacks['Inter'];
}

/**
 * Register widget areas
 */
require get_template_directory() . '/inc/widget-areas.php';

/**
 * Render-blocking optimizations (theme CSS and fonts non-blocking)
 */
require get_template_directory() . '/inc/render-blocking.php';

/**
 * Enqueue scripts and styles
 */
function piratez_cyberpunk_scripts() {
    // Main stylesheet
    wp_enqueue_style('piratez-style', get_stylesheet_uri(), array(), PIRATEZ_VERSION);

    // Professional foundation CSS
    wp_enqueue_style('piratez-foundation', get_template_directory_uri() . '/css/style.css', array('piratez-style'), PIRATEZ_VERSION);

    // Cyberpunk accent CSS
    wp_enqueue_style('piratez-cyberpunk', get_template_directory_uri() . '/css/cyberpunk.css', array('piratez-foundation'), PIRATEZ_VERSION);

    // Pirate accent CSS
    wp_enqueue_style('piratez-pirate', get_template_directory_uri() . '/css/pirate.css', array('piratez-foundation'), PIRATEZ_VERSION);

    // Output custom CSS
    $custom_css = get_theme_mod('piratez_custom_css', '');
    if ($custom_css) {
        wp_add_inline_style('piratez-foundation', $custom_css);
    }

    // Output dynamic colors (Phase PRE 5: full light/dark palette; migrate old mods to light)
    $light_vars = array(
        '--color-bg-primary'       => get_theme_mod('piratez_light_bg_primary') ?: get_theme_mod('piratez_background_color', '#ffffff'),
        '--color-bg-secondary'     => get_theme_mod('piratez_light_bg_secondary', '#f5f5f5'),
        '--color-surface'           => get_theme_mod('piratez_light_surface', '#ffffff'),
        '--color-text-primary'     => get_theme_mod('piratez_light_text_primary', '#1a1a2e'),
        '--color-text-secondary'   => get_theme_mod('piratez_light_text_secondary', '#666666'),
        '--color-border'           => get_theme_mod('piratez_light_border', '#e0e0e0'),
        '--color-accent-primary'    => get_theme_mod('piratez_light_accent_primary') ?: get_theme_mod('piratez_primary_accent_color', '#0066cc'),
        '--color-accent-secondary'  => get_theme_mod('piratez_light_accent_secondary') ?: get_theme_mod('piratez_secondary_accent_color', '#cc0066'),
        '--color-accent-gold'       => get_theme_mod('piratez_light_accent_gold') ?: get_theme_mod('piratez_gold_color', '#b8860b'),
    );
    $dark_vars = array(
        '--color-bg-primary'       => get_theme_mod('piratez_dark_bg_primary', '#1a1a2e'),
        '--color-bg-secondary'     => get_theme_mod('piratez_dark_bg_secondary', '#0f0f1a'),
        '--color-surface'           => get_theme_mod('piratez_dark_surface', '#252540'),
        '--color-text-primary'     => get_theme_mod('piratez_dark_text_primary', '#e0e0e0'),
        '--color-text-secondary'   => get_theme_mod('piratez_dark_text_secondary', '#b0b0b0'),
        '--color-border'           => get_theme_mod('piratez_dark_border', '#3a3a5a'),
        '--color-accent-primary'    => get_theme_mod('piratez_dark_accent_primary', '#00a8ff'),
        '--color-accent-secondary'  => get_theme_mod('piratez_dark_accent_secondary', '#ff0080'),
        '--color-accent-gold'       => get_theme_mod('piratez_dark_accent_gold', '#ffd700'),
    );
    $light_decls = array();
    foreach ($light_vars as $var => $val) {
        $light_decls[] = $var . ': ' . esc_attr($val);
    }
    $dark_decls = array();
    foreach ($dark_vars as $var => $val) {
        $dark_decls[] = $var . ': ' . esc_attr($val);
    }

    // Typography (theme-controlled; blog-friendly)
    $heading_font = get_theme_mod('piratez_heading_font', 'Inter');
    $body_font    = get_theme_mod('piratez_body_font', 'Inter');
    $accent_font  = get_theme_mod('piratez_accent_font', 'Press Start 2P');
    $base_size    = max(14, min(22, absint(get_theme_mod('piratez_base_font_size', 16))));
    $light_decls[] = '--font-heading: ' . piratez_get_font_stack($heading_font);
    $light_decls[] = '--font-body: ' . piratez_get_font_stack($body_font);
    if ($accent_font === 'Press Start 2P') {
        $light_decls[] = "--font-accent: 'Press Start 2P', monospace";
    } elseif ($accent_font === 'Same as heading') {
        $light_decls[] = '--font-accent: var(--font-heading)';
    } else {
        $light_decls[] = '--font-accent: var(--font-body)';
    }

    $dynamic_css = ':root { ' . implode('; ', $light_decls) . '; }
html[data-theme="dark"] { ' . implode('; ', $dark_decls) . '; }
html { font-size: ' . $base_size . 'px; }
';
    wp_add_inline_style('piratez-foundation', $dynamic_css);

    // Google Fonts (Inter, Roboto, Press Start 2P for Customizer choices)
    wp_enqueue_style('piratez-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&family=Press+Start+2P&display=swap', array(), null);
    
    // Add preconnect for Google Fonts
    add_action('wp_head', 'piratez_font_preconnect', 1);

    // Main theme JavaScript (no dependencies for core functionality)
    wp_enqueue_script('piratez-theme', get_template_directory_uri() . '/js/theme.js', array(), PIRATEZ_VERSION, true);
    
    // Add defer attribute for performance
    add_filter('script_loader_tag', 'piratez_defer_scripts', 10, 2);

    // Dark mode JavaScript
    wp_enqueue_script('piratez-dark-mode', get_template_directory_uri() . '/js/dark-mode.js', array('piratez-theme'), PIRATEZ_VERSION, true);

    // Reading time JavaScript - only if enabled
    if (get_theme_mod('piratez_reading_time', true)) {
        wp_enqueue_script('piratez-reading-time', get_template_directory_uri() . '/js/reading-time.js', array('piratez-theme'), PIRATEZ_VERSION, true);
    }

    // Scroll to top JavaScript
    wp_enqueue_script('piratez-scroll-top', get_template_directory_uri() . '/js/scroll-top.js', array('piratez-theme'), PIRATEZ_VERSION, true);

    // Reading progress JavaScript
    if (get_theme_mod('piratez_reading_progress', true)) {
        wp_enqueue_script('piratez-reading-progress', get_template_directory_uri() . '/js/reading-progress.js', array('piratez-theme'), PIRATEZ_VERSION, true);
    }

    // Table of contents JavaScript - only if theme supports and Customizer enabled
    if (current_theme_supports('piratez-toc') && get_theme_mod('piratez_table_of_contents', true)) {
        wp_enqueue_script('piratez-toc', get_template_directory_uri() . '/js/toc.js', array('piratez-theme'), PIRATEZ_VERSION, true);
    }

    // Localize script for AJAX
    wp_localize_script('piratez-theme', 'piratezData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('piratez-nonce'),
    ));

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    // Lazy load images
    add_filter('wp_get_attachment_image_attributes', 'piratez_lazy_load_images', 10, 3);
}
add_action('wp_enqueue_scripts', 'piratez_cyberpunk_scripts');

/**
 * Add lazy loading to images
 */
function piratez_lazy_load_images($attr, $attachment, $size) {
    if (!is_admin() && !wp_is_mobile()) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    return $attr;
}

/**
 * Font preconnect for performance
 */
function piratez_font_preconnect() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}

/**
 * Defer script loading for performance
 */
function piratez_defer_scripts($tag, $handle) {
    if ($handle === 'piratez-theme' || strpos($handle, 'piratez-') === 0) {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}

/**
 * Add body class for accent intensity
 */
function piratez_body_class_accent($classes) {
    $accent_intensity = get_theme_mod('piratez_accent_intensity', 50);
    if ($accent_intensity <= 30) {
        $classes[] = 'accent-subtle';
    } elseif ($accent_intensity >= 70) {
        $classes[] = 'accent-bold';
    } else {
        $classes[] = 'accent-balanced';
    }
    
    // Add sidebar enabled class
    if (get_theme_mod('piratez_sidebar_display', true)) {
        $classes[] = 'sidebar-enabled';
    }
    
    // Add sticky header enabled class
    $sticky_header = get_theme_mod('piratez_sticky_header', true);
    if ($sticky_header) {
        $classes[] = 'sticky-header-enabled';
    }
    
    return $classes;
}
add_filter('body_class', 'piratez_body_class_accent');

/**
 * Custom template functions
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom template tags
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Shortcodes
 */
require get_template_directory() . '/inc/shortcodes.php';

/**
 * Modify main query to use custom posts per page setting
 */
function piratez_modify_main_query($query) {
    // Only modify the main query on the frontend
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    // Only apply to blog/home/archive pages
    if (is_home() || is_archive() || is_category() || is_tag() || is_author() || is_search()) {
        $posts_per_page = get_theme_mod('piratez_posts_per_page', 10);
        if ($posts_per_page > 0) {
            $query->set('posts_per_page', absint($posts_per_page));
        }
    }
}
add_action('pre_get_posts', 'piratez_modify_main_query');
