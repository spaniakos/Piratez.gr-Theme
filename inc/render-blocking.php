<?php
/**
 * Render-blocking optimizations.
 *
 * - Theme CSS: non-blocking via first filter (piratez_style_loader_tag_nonblocking).
 * - All other CSS (plugins, core): non-blocking via second filter (Option B).
 * Scripts are deferred in functions.php.
 *
 * @package piratez_cyberpunk
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme style handles to load with media="print" + onload (non-blocking).
 *
 * @return array Style handle names.
 */
function piratez_render_blocking_style_handles() {
    return array(
        'piratez-style',
        'piratez-foundation',
        'piratez-cyberpunk',
        'piratez-pirate',
        'piratez-fonts',
    );
}

/**
 * Make theme stylesheets non-blocking: load with media="print", switch to "all" on load.
 * Adds noscript fallback so CSS still applies when JS is disabled.
 *
 * @param string $html  The link tag for the enqueued style.
 * @param string $handle The style's registered handle.
 * @param string $href  The stylesheet's source URL.
 * @param string $media The stylesheet's media attribute.
 * @return string Modified link tag (and noscript fallback).
 */
function piratez_style_loader_tag_nonblocking($html, $handle, $href, $media) {
    $handles = piratez_render_blocking_style_handles();
    if (!in_array($handle, $handles, true)) {
        return $html;
    }

    if (strpos($html, "media='print'") !== false || strpos($html, 'media="print"') !== false) {
        return $html;
    }

    $media_attr = 'print';
    $onload     = "this.media='all'";
    $html       = str_replace("media='all'", 'media="' . esc_attr($media_attr) . '" onload="' . esc_attr($onload) . '"', $html);
    $html       = str_replace('media="all"', 'media="' . esc_attr($media_attr) . '" onload="' . esc_attr($onload) . '"', $html);

    $noscript = '<noscript><link rel="stylesheet" href="' . esc_url($href) . '" media="all"></noscript>';
    return $html . $noscript;
}

/**
 * Make all non-theme stylesheets non-blocking (Option B: plugin/core CSS).
 * Theme handles are left to the filter above; this runs after and only touches other styles.
 *
 * @param string $html  The link tag for the enqueued style.
 * @param string $handle The style's registered handle.
 * @param string $href  The stylesheet's source URL.
 * @param string $media The stylesheet's media attribute.
 * @return string Modified link tag (and noscript fallback).
 */
function piratez_style_loader_tag_nonblocking_global($html, $handle, $href, $media) {
    $theme_handles = piratez_render_blocking_style_handles();
    if (in_array($handle, $theme_handles, true)) {
        return $html;
    }

    if (strpos($html, "media='print'") !== false || strpos($html, 'media="print"') !== false) {
        return $html;
    }

    $media_attr = 'print';
    $onload     = "this.media='all'";
    $html       = str_replace("media='all'", 'media="' . esc_attr($media_attr) . '" onload="' . esc_attr($onload) . '"', $html);
    $html       = str_replace('media="all"', 'media="' . esc_attr($media_attr) . '" onload="' . esc_attr($onload) . '"', $html);

    $noscript = '<noscript><link rel="stylesheet" href="' . esc_url($href) . '" media="all"></noscript>';
    return $html . $noscript;
}

/**
 * Register render-blocking filters (front end only).
 */
function piratez_render_blocking_init() {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    add_filter('style_loader_tag', 'piratez_style_loader_tag_nonblocking', 10, 4);
    add_filter('style_loader_tag', 'piratez_style_loader_tag_nonblocking_global', 11, 4);
}
add_action('wp_enqueue_scripts', 'piratez_render_blocking_init', 20);
