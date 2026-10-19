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
            <?php piratez_posted_on(); ?>
            <?php piratez_posted_by(); ?>
            <?php
            piratez_editorial_component('reading_time');
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
