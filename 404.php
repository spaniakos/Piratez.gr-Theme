<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package piratez_cyberpunk
 */

get_header();
?>

<main id="primary" class="site-main">

    <section class="error-404 not-found">
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e('404 - Page Not Found', 'piratez-cyberpunk'); ?></h1>
        </header><!-- .page-header -->

        <div class="page-content">
            <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try a search?', 'piratez-cyberpunk'); ?></p>

            <?php
            get_search_form();

            // 404 Page Widget Area
            if (is_active_sidebar('404-page')) {
                dynamic_sidebar('404-page');
            }
            ?>
        </div><!-- .page-content -->
    </section><!-- .error-404 -->

</main><!-- #primary -->

<?php
get_sidebar();
get_footer();
