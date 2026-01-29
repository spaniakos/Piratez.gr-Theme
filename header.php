<?php
/**
 * The header template
 *
 * @package piratez_cyberpunk
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> data-default-theme="<?php echo esc_attr(get_theme_mod('piratez_dark_mode_default', 'light')); ?>" data-accent-intensity="<?php echo esc_attr(get_theme_mod('piratez_accent_intensity', 50)); ?>" data-reading-time-enabled="<?php echo esc_attr(get_theme_mod('piratez_reading_time', true) ? 'true' : 'false'); ?>" data-toc-enabled="<?php echo esc_attr(get_theme_mod('piratez_table_of_contents', true) ? 'true' : 'false'); ?>">
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'piratez-cyberpunk'); ?></a>

    <?php
    // Header Top Widget Area
    if (is_active_sidebar('header-top')) : ?>
        <div class="header-top-widget-area">
            <div class="container">
                <?php dynamic_sidebar('header-top'); ?>
            </div>
        </div>
    <?php endif; ?>

    <header id="masthead" class="site-header">
        <div class="container">
            <div class="site-header-top">
                <div class="site-branding">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        if (is_front_page() && is_home()) : ?>
                            <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                        <?php else : ?>
                            <p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
                        <?php endif;
                    }
                    ?>
                </div><!-- .site-branding -->

                <nav id="site-navigation" class="main-navigation">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="menu-toggle-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                        <span class="screen-reader-text"><?php esc_html_e('Primary Menu', 'piratez-cyberpunk'); ?></span>
                    </button>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'container'      => false,
                        'menu_class'     => 'primary-menu',
                    ));
                    ?>
                </nav><!-- #site-navigation -->
                
                <div class="header-actions">
                    <button class="dark-mode-toggle" aria-label="<?php esc_attr_e('Toggle dark mode', 'piratez-cyberpunk'); ?>">
                        <span class="dark-mode-icon">🌙</span>
                        <span class="light-mode-icon">☀️</span>
                    </button>
                </div><!-- .header-actions -->
            </div><!-- .site-header-top -->
            
            <div class="site-header-row2">
                <?php
                $description = get_bloginfo('description', 'display');
                if (($description || is_customize_preview()) && get_theme_mod('piratez_tagline_display', true)) : ?>
                    <div class="site-header-subtitle">
                        <p class="site-description"><?php echo $description; ?></p>
                    </div><!-- .site-header-subtitle -->
                <?php endif; ?>
            </div><!-- .site-header-row2 -->
        </div><!-- .container -->
    </header><!-- #masthead -->

    <?php
    // Header Below Menu Widget Area
    if (is_active_sidebar('header-below-menu')) : ?>
        <div class="header-below-menu-widget-area">
            <div class="container">
                <?php dynamic_sidebar('header-below-menu'); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
    // Reading Progress Bar
    if (get_theme_mod('piratez_reading_progress', true) && is_singular('post')) {
        get_template_part('template-parts/reading-progress');
    }
    ?>

    <div id="content" class="site-content">
