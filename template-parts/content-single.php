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
            <span class="posted-on">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                    <?php echo esc_html(get_the_date()); ?>
                </time>
            </span>
            <span class="byline">
                <?php esc_html_e('by', 'piratez-cyberpunk'); ?> 
                <span class="author vcard">
                    <a class="url fn n" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                        <?php echo esc_html(get_the_author()); ?>
                    </a>
                </span>
            </span>
            <?php
            if (get_theme_mod('piratez_reading_time', true)) {
                $reading_time = piratez_calculate_reading_time(get_the_content());
                if ($reading_time) {
                    echo '<span class="reading-time">' . esc_html($reading_time) . ' ' . esc_html__('min read', 'piratez-cyberpunk') . '</span>';
                }
            }
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
    if (current_theme_supports('piratez-toc') && get_theme_mod('piratez_table_of_contents', true)) {
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
        $categories_list = get_the_category_list('');
        if ($categories_list) {
            echo '<div class="post-categories">';
            echo '<span class="cat-label">' . esc_html__('Posted in', 'piratez-cyberpunk') . '</span>';
            echo '<div class="cat-list">' . $categories_list . '</div>';
            echo '</div>';
        }

        $tags_list = get_the_tag_list('', '');
        if ($tags_list) {
            echo '<div class="post-tags">';
            echo '<span class="tags-label">' . esc_html__('Tagged', 'piratez-cyberpunk') . '</span>';
            echo '<div class="tags-list">' . $tags_list . '</div>';
            echo '</div>';
        }
        ?>

        <?php
        // Social Sharing
        if (get_theme_mod('piratez_social_sharing', true)) {
            get_template_part('template-parts/social-sharing');
        }
        ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
