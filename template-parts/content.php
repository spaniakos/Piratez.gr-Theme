<?php
/**
 * Template part for displaying posts
 *
 * @package piratez_cyberpunk
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
    <div class="post-thumbnail-slot">
        <?php if (has_post_thumbnail()) : ?>
            <div class="post-thumbnail">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('piratez-featured'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="post-content">
        <header class="entry-header">
            <?php
            if (is_singular()) :
                the_title('<h1 class="entry-title">', '</h1>');
            else :
                the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
            endif;
            ?>
        </header><!-- .entry-header -->

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

        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div><!-- .entry-summary -->

        <footer class="entry-footer">
            <a href="<?php the_permalink(); ?>" class="read-more">
                <?php esc_html_e('Read More', 'piratez-cyberpunk'); ?> →
            </a>
        </footer><!-- .entry-footer -->
    </div><!-- .post-content -->
</article><!-- #post-<?php the_ID(); ?> -->
