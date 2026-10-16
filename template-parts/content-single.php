<?php
/**
 * Template part for displaying single posts
 *
 * @package piratez_cyberpunk
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
    <header class="entry-header">
        <?php
        the_title('<h1 class="entry-title">', '</h1>');
        ?>
        <div class="entry-meta">
            <?php piratez_posted_on(); ?>
            <?php piratez_posted_by(); ?>
            <?php
            piratez_editorial_component('reading_time');
            ?>
        </div><!-- .entry-meta -->
    </header><!-- .entry-header -->

    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail">
            <?php the_post_thumbnail('piratez-featured'); ?>
        </div>
    <?php endif; ?>

    <?php
    // Table of Contents (gated by piratez-toc feature flag)
    if (piratez_editorial_enabled('toc')) {
        get_template_part('template-parts/table-of-contents');
    }
    ?>

    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages(array(
            'before' => '<div class="page-links">' . esc_html__('Pages:', 'piratez-cyberpunk'),
            'after'  => '</div>',
        ));
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php
        if (get_theme_mod('piratez_post_categories', true)) {
            $categories_list = get_the_category_list('');
            if ($categories_list) {
                echo '<div class="post-categories">';
                echo '<span class="cat-label">' . esc_html__('Posted in', 'piratez-cyberpunk') . '</span>';
                echo '<div class="cat-list">' . $categories_list . '</div>';
                echo '</div>';
            }
        }

        if (get_theme_mod('piratez_post_tags', true)) {
            $tags_list = get_the_tag_list('', '');
            if ($tags_list) {
                echo '<div class="post-tags">';
                echo '<span class="tags-label">' . esc_html__('Tagged', 'piratez-cyberpunk') . '</span>';
                echo '<div class="tags-list">' . $tags_list . '</div>';
                echo '</div>';
            }
        }
        ?>

        <?php
        // Social Sharing
        if (piratez_editorial_enabled('sharing')) {
            get_template_part('template-parts/social-sharing');
        }
        ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
