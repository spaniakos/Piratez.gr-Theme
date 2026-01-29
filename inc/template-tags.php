<?php
/**
 * Custom template tags
 *
 * @package piratez_cyberpunk
 */

/**
 * Display post date (and optional "Updated" when modified differs from published).
 * Respects Customizer: Display post date, Display last updated.
 */
function piratez_posted_on() {
    if (!get_theme_mod('piratez_post_date', true)) {
        return;
    }

    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if (get_the_time('U') !== get_the_modified_time('U')) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
    }

    $time_string = sprintf(
        $time_string,
        esc_attr(get_the_date('c')),
        esc_html(get_the_date())
    );

    $posted_on = sprintf(
        esc_html_x('Posted on %s', 'post date', 'piratez-cyberpunk'),
        '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
    );

    echo '<span class="posted-on">' . $posted_on . '</span>';

    if (get_the_time('U') !== get_the_modified_time('U') && get_theme_mod('piratez_post_updated', true)) {
        $updated = sprintf(
            '<time class="updated" datetime="%1$s">%2$s</time>',
            esc_attr(get_the_modified_date('c')),
            esc_html(get_the_modified_date())
        );
        echo ' <span class="updated-on">' . sprintf(esc_html_x('Updated %s', 'post modified date', 'piratez-cyberpunk'), $updated) . '</span>';
    }
}

/**
 * Display post author. Respects Customizer: Display post author.
 */
function piratez_posted_by() {
    if (!get_theme_mod('piratez_post_author', true)) {
        return;
    }

    $byline = sprintf(
        esc_html_x('by %s', 'post author', 'piratez-cyberpunk'),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
    );

    echo '<span class="byline"> ' . $byline . '</span>';
}
