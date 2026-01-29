<?php
/**
 * The footer template
 *
 * @package piratez_cyberpunk
 */
?>

    </div><!-- #content -->

    <?php
    // Footer Widget Areas
    $footer_columns = get_theme_mod('piratez_footer_columns', 4);
    if ($footer_columns > 0) :
        $footer_active = false;
        for ($i = 1; $i <= 4; $i++) {
            if (is_active_sidebar('footer-' . $i)) {
                $footer_active = true;
                break;
            }
        }
        if ($footer_active) : ?>
            <footer id="colophon" class="site-footer">
                <div class="container">
                    <div class="footer-widgets footer-columns-<?php echo esc_attr($footer_columns); ?>">
                        <?php
                        for ($i = 1; $i <= $footer_columns; $i++) {
                            if (is_active_sidebar('footer-' . $i)) {
                                echo '<div class="footer-widget-column footer-column-' . esc_attr($i) . '">';
                                dynamic_sidebar('footer-' . $i);
                                echo '</div>';
                            }
                        }
                        ?>
                    </div><!-- .footer-widgets -->
                </div><!-- .container -->
            </footer><!-- #colophon -->
        <?php endif;
    endif;
    ?>

    <div class="site-footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <div class="copyright">
                    &copy; <?php echo esc_html(date_i18n('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'piratez-cyberpunk'); ?>
                </div>
                <?php
                if (get_theme_mod('piratez_makeasite_branding', true)) :
                    $branding_text = get_theme_mod('piratez_makeasite_branding_text', __('Built by', 'piratez-cyberpunk') . ' makeasite.gr');
                    $branding_link = get_theme_mod('piratez_makeasite_branding_link', 'https://makeasite.gr');
                    ?>
                    <div class="makeasite-branding">
                        <a href="<?php echo esc_url($branding_link); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html($branding_text); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div><!-- .footer-bottom-content -->
        </div><!-- .container -->
    </div><!-- .site-footer-bottom -->

    <?php
    // Scroll to Top Button
    if (get_theme_mod('piratez_scroll_to_top', true)) {
        get_template_part('template-parts/scroll-to-top');
    }
    ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
