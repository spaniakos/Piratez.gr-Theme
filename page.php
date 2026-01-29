<?php
/**
 * The template for displaying all pages
 *
 * @package piratez_cyberpunk
 */

get_header();
?>

<main id="primary" class="site-main">

    <?php
    // Before Content Widget Area
    if (is_active_sidebar('before-content')) {
        dynamic_sidebar('before-content');
    }

    while (have_posts()) :
        the_post();

        get_template_part('template-parts/content', 'page');

        // Comments
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;

    endwhile;

    // After Content Widget Area
    if (is_active_sidebar('after-content')) {
        dynamic_sidebar('after-content');
    }
    ?>

</main><!-- #primary -->

<?php
get_sidebar();
get_footer();
