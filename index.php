<?php
/**
 * The main template file
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

    if (have_posts()) :

        if (is_home() && !is_front_page()) : ?>
            <header class="page-header">
                <h1 class="page-title"><?php single_post_title(); ?></h1>
            </header>
        <?php endif; ?>

        <div class="posts-container">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content', get_post_format());
            endwhile;
            ?>
        </div><!-- .posts-container -->

        <?php
        // Pagination
        the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => __('&larr; Previous', 'piratez-cyberpunk'),
            'next_text' => __('Next &rarr;', 'piratez-cyberpunk'),
        ));

    else :

        get_template_part('template-parts/content', 'none');

    endif;

    // After Content Widget Area
    if (is_active_sidebar('after-content')) {
        dynamic_sidebar('after-content');
    }
    ?>

</main><!-- #primary -->

<?php
get_sidebar();
get_footer();
