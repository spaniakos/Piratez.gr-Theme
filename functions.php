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
define('PIRATEZ_VERSION', '1.3.0');

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
require get_template_directory() . '/inc/block-editor.php';
require get_template_directory() . '/inc/modules.php';

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

    wp_add_inline_style('piratez-foundation', piratez_design_variables_css());

    // Google Fonts (Inter, Roboto, Press Start 2P for Customizer choices)
    wp_enqueue_style('piratez-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&family=Press+Start+2P&display=swap', array(), null);
    
    // Add preconnect for Google Fonts
    add_action('wp_head', 'piratez_font_preconnect', 1);

    // Main theme JavaScript (no dependencies for core functionality)
    wp_enqueue_script('piratez-theme', get_template_directory_uri() . '/js/theme.js', array(), PIRATEZ_VERSION, true);
    
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

    // Let core resolve deferred classic-script dependencies.
    foreach (array('piratez-theme', 'piratez-dark-mode', 'piratez-reading-time', 'piratez-scroll-top', 'piratez-reading-progress', 'piratez-toc') as $handle) {
        wp_script_add_data($handle, 'strategy', 'defer');
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

    // Preserve native image loading priority
    add_filter('wp_get_attachment_image_attributes', 'piratez_lazy_load_images', 10, 3);
}
add_action('wp_enqueue_scripts', 'piratez_cyberpunk_scripts');

/**
 * Preserve core loading priority; provide async decoding when unset
 */
function piratez_lazy_load_images($attr, $attachment, $size) {
    if (!is_admin() && !wp_is_mobile()) {
        if (!isset($attr['decoding'])) {
            $attr['decoding'] = 'async';
        }
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
 * AI Index (llms.txt, sitemap.xml) - only when theme supports it
 */
add_action('after_setup_theme', function () {
    if (current_theme_supports('piratez-ai-index')) {
        require get_template_directory() . '/inc/sitemap.php';
    }
}, 20);

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


/** Small inline palette bootstrap runs before asynchronous styles, without a blocking request. */
function piratez_theme_mode_bootstrap() {
    $script = file_get_contents(get_template_directory() . '/js/theme-mode.js');
    wp_print_inline_script_tag($script, array('id' => 'piratez-theme-mode'));
}
add_action('wp_head', 'piratez_theme_mode_bootstrap', 1);
