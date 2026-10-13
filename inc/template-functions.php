<?php
/**
 * Template functions
 *
 * @package piratez_cyberpunk
 */

/**
 * Calculate reading time for a post
 *
 * @param string $content Post content
 * @return int Reading time in minutes
 */
function piratez_calculate_reading_time($content) {
    $content = strip_shortcodes($content);
    $content = preg_replace('~<br\s*/?>~i', ' ', $content);
    $content = preg_replace('~</(?:p|div|li|h[1-6]|blockquote|pre|section|article|figcaption|tr)>~i', ' ', $content);
    $text = html_entity_decode(wp_strip_all_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = wp_check_invalid_utf8($text, true);
    preg_match_all("/[\\p{L}\\p{N}]+(?:['’\\-][\\p{L}\\p{N}]+)*/u", $text, $words);
    return (int) ceil(count($words[0]) / 200);

}

/**
 * Get related posts
 *
 * @param int $post_id Post ID
 * @param int $limit Number of posts to return
 * @return WP_Query
 */
function piratez_get_related_posts($post_id = null, $limit = 3) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $categories = wp_get_post_categories($post_id);
    if (empty($categories)) {
        return false;
    }

    $limit = max(1, min(12, absint($limit)));
    $revision = get_option('piratez_related_revision', 1);
    $key = 'piratez_related_' . md5($post_id . ':' . $limit . ':' . $revision . ':' . get_locale());
    $ids = get_transient($key);
    if ($ids === false) {
        $ids = get_posts(array('category__in' => $categories, 'post__not_in' => array($post_id), 'post_type' => 'post', 'post_status' => 'publish', 'has_password' => false, 'posts_per_page' => $limit, 'orderby' => array('date' => 'DESC', 'ID' => 'DESC'), 'ignore_sticky_posts' => true, 'no_found_rows' => true, 'fields' => 'ids', 'suppress_filters' => false));
        set_transient($key, $ids, HOUR_IN_SECONDS);
    }
    if (!$ids) { return false; }
    return new WP_Query(array('post_type' => 'post', 'post_status' => 'publish', 'has_password' => false, 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => count($ids), 'ignore_sticky_posts' => true, 'no_found_rows' => true));

}

/**
 * Get accent intensity class
 *
 * @return string
 */
function piratez_get_accent_intensity_class() {
    $intensity = get_theme_mod('piratez_accent_intensity', 50);
    
    if ($intensity <= 30) {
        return 'accent-subtle';
    } elseif ($intensity >= 70) {
        return 'accent-bold';
    }
    
    return 'accent-balanced';
}


/** Content/category changes invalidate cached related-post selections. */
function piratez_related_invalidate($post_id, $post = null) {
    $post = $post instanceof WP_Post ? $post : get_post($post_id);
    if (!$post || $post->post_type !== 'post' || wp_is_post_revision($post_id)) { return; }
    update_option('piratez_related_revision', (int) get_option('piratez_related_revision', 1) + 1, false);
}
add_action('save_post', 'piratez_related_invalidate', 10, 2);
add_action('deleted_post', 'piratez_related_invalidate', 10, 2);
add_action('set_object_terms', 'piratez_related_invalidate', 10, 1);
