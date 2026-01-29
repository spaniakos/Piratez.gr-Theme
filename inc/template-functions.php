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
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed: 200 words per minute
    return $reading_time;
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

    $args = array(
        'category__in'   => $categories,
        'post__not_in'    => array($post_id),
        'posts_per_page'  => $limit,
        'orderby'         => 'rand',
        'ignore_sticky_posts' => 1,
    );

    return new WP_Query($args);
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
