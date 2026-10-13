<?php
/** Asynchronous frontend stylesheets, including core Gutenberg assets. */
if (!defined('ABSPATH')) { exit; }

function piratez_render_blocking_style_handles() {
    return array('piratez-style', 'piratez-foundation', 'piratez-cyberpunk', 'piratez-pirate', 'piratez-content', 'piratez-fonts');
}

/**
 * Preserve link attributes, including integrity, crossorigin, RTL, and media.
 * Genuine print styles remain print-only; fallbacks preserve original media.
 * Inline global styles and script modules are handled by core.
 */
function piratez_async_stylesheet_tag($html, $handle, $href, $media) {
    $processor = new WP_HTML_Tag_Processor($html);
    if (!$processor->next_tag(array('tag_name' => 'LINK'))) { return $html; }
    if ($processor->get_attribute('rel') !== 'stylesheet' ||
        $processor->get_attribute('data-piratez-async') !== null ||
        $processor->get_attribute('onload') !== null ||
        $processor->get_attribute('disabled') !== null) {
        return $html;
    }
    $original_media = $processor->get_attribute('media');
    if (!is_string($original_media) || trim($original_media) === '') {
        $original_media = is_string($media) && trim($media) !== '' ? $media : 'all';
    }
    if (strtolower(trim($original_media)) === 'print') { return $html; }

    $fallback = new WP_HTML_Tag_Processor($html);
    $fallback->next_tag(array('tag_name' => 'LINK'));
    $fallback->remove_attribute('id');
    $processor->set_attribute('media', 'print');
    $processor->set_attribute('data-piratez-async', '1');
    $processor->set_attribute('onload', 'this.onload=null;this.media=' . wp_json_encode($original_media) . ';');
    return $processor->get_updated_html() . '<noscript>' . $fallback->get_updated_html() . '</noscript>';
}

// Preserve named helpers for integrations; the processor is idempotent.
function piratez_style_loader_tag_nonblocking($html, $handle, $href, $media) {
    if (!in_array($handle, piratez_render_blocking_style_handles(), true)) { return $html; }
    return piratez_async_stylesheet_tag($html, $handle, $href, $media);
}
function piratez_style_loader_tag_nonblocking_global($html, $handle, $href, $media) {
    return piratez_async_stylesheet_tag($html, $handle, $href, $media);
}
function piratez_style_loader_tag_popup_nonblocking($html, $handle, $href, $media) {
    return strpos($href, 'popup.css') === false ? $html : piratez_async_stylesheet_tag($html, $handle, $href, $media);
}
function piratez_render_blocking_init() {
    if (is_admin() || wp_doing_ajax()) { return; }
    add_filter('style_loader_tag', 'piratez_style_loader_tag_nonblocking_global', 11, 4);
}
add_action('wp_enqueue_scripts', 'piratez_render_blocking_init', 20);

