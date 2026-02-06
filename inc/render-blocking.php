<?php
/**
 * Render-blocking optimizations (theme assets only).
 *
 * Loads theme stylesheets and Google Fonts in a non-blocking way so they
 * do not delay LCP/FCP. Scripts are already deferred via piratez_defer_scripts.
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
}
add_action('wp_enqueue_scripts', 'piratez_render_blocking_init', 20);
