<?php
/**
 * The template for displaying all single posts
 *
 * @package piratez_cyberpunk
 */

get_header();
?>

<main id="primary" class="site-main">

    <?php
    // Before Post Widget Area
    if (is_active_sidebar('before-post')) {
        dynamic_sidebar('before-post');
    }

    while (have_posts()) :
        the_post();

        get_template_part('template-parts/content', 'single');

        // Post Navigation
        the_post_navigation(array(
            'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'piratez-cyberpunk') . '</span> <span class="nav-title">%title</span>',
            'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'piratez-cyberpunk') . '</span> <span class="nav-title">%title</span>',
        ));

        // Related Posts
        if (get_theme_mod('piratez_related_posts', true)) {
            get_template_part('template-parts/related-posts');
        }

        // Author Box
        if (get_theme_mod('piratez_author_box', true)) {
            get_template_part('template-parts/author-box');
        }

        // After Post Widget Area
        if (is_active_sidebar('after-post')) {
            dynamic_sidebar('after-post');
        }

        // Comments
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;

    endwhile;
    ?>

</main><!-- #primary -->

<?php
get_sidebar();
get_footer();
