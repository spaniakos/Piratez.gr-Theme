<?php
/**
 * Template part for social sharing buttons
 *
 * @package piratez_cyberpunk
 */

if (!is_singular()) {
    return;
}

$post_url = urlencode(get_permalink());
$post_title = urlencode(get_the_title());
$post_excerpt = urlencode(get_the_excerpt());
?>

<div class="social-sharing">
    <h4 class="share-label"><?php esc_html_e('Share this post', 'piratez-cyberpunk'); ?></h4>
    <div class="share-buttons">
        <a href="https://twitter.com/intent/tweet?url=<?php echo $post_url; ?>&text=<?php echo $post_title; ?>" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="share-button share-twitter"
           aria-label="<?php esc_attr_e('Share on Twitter', 'piratez-cyberpunk'); ?>">
            <span class="share-icon">🐦</span>
            <span class="share-text"><?php esc_html_e('Twitter', 'piratez-cyberpunk'); ?></span>
        </a>

        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $post_url; ?>" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="share-button share-facebook"
           aria-label="<?php esc_attr_e('Share on Facebook', 'piratez-cyberpunk'); ?>">
            <span class="share-icon">📘</span>
            <span class="share-text"><?php esc_html_e('Facebook', 'piratez-cyberpunk'); ?></span>
        </a>

        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $post_url; ?>&title=<?php echo $post_title; ?>" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="share-button share-linkedin"
           aria-label="<?php esc_attr_e('Share on LinkedIn', 'piratez-cyberpunk'); ?>">
            <span class="share-icon">💼</span>
            <span class="share-text"><?php esc_html_e('LinkedIn', 'piratez-cyberpunk'); ?></span>
        </a>

        <button class="share-button share-copy" 
                data-url="<?php echo esc_attr(get_permalink()); ?>"
                aria-label="<?php esc_attr_e('Copy link', 'piratez-cyberpunk'); ?>">
            <span class="share-icon">🔗</span>
            <span class="share-text"><?php esc_html_e('Copy Link', 'piratez-cyberpunk'); ?></span>
        </button>
    </div>
</div>
