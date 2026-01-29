<?php
/**
 * Template part for author box
 *
 * @package piratez_cyberpunk
 */

$author_id = get_the_author_meta('ID');
$author_name = get_the_author();
$author_description = get_the_author_meta('description');
$author_url = get_author_posts_url($author_id);
$author_avatar = get_avatar($author_id, 80);
?>

<div class="author-box">
    <div class="author-box-content">
        <h3 class="author-box-title"><?php esc_html_e('About the Captain', 'piratez-cyberpunk'); ?></h3>
        <div class="author-box-inner">
            <div class="author-avatar">
                <a href="<?php echo esc_url($author_url); ?>">
                    <?php echo $author_avatar; ?>
                </a>
            </div>
            <div class="author-info">
                <h4 class="author-name">
                    <a href="<?php echo esc_url($author_url); ?>"><?php echo esc_html($author_name); ?></a>
                </h4>
                <?php if ($author_description) : ?>
                    <div class="author-bio">
                        <?php echo wp_kses_post($author_description); ?>
                    </div>
                <?php endif; ?>
                <div class="author-links">
                    <a href="<?php echo esc_url($author_url); ?>" class="author-posts-link">
                        <?php
                        $post_count = count_user_posts($author_id);
                        printf(
                            esc_html(_n('View %d post', 'View %d posts', $post_count, 'piratez-cyberpunk')),
                            $post_count
                        );
                        ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
