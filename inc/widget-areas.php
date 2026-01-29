<?php
/**
 * Register widget areas
 *
 * @package piratez_cyberpunk
 */

function piratez_cyberpunk_widgets_init() {
    // Main Sidebar
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'piratez-cyberpunk'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here to appear in your sidebar.', 'piratez-cyberpunk'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // Header Top Widget Area
    register_sidebar(array(
        'name'          => esc_html__('Header Top', 'piratez-cyberpunk'),
        'id'            => 'header-top',
        'description'   => esc_html__('Add widgets here to appear above the header/navigation (full width).', 'piratez-cyberpunk'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Header Below Menu Widget Area
    register_sidebar(array(
        'name'          => esc_html__('Header Below Menu', 'piratez-cyberpunk'),
        'id'            => 'header-below-menu',
        'description'   => esc_html__('Add widgets here to appear below the main navigation.', 'piratez-cyberpunk'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Before Content Widget Area
    register_sidebar(array(
        'name'          => esc_html__('Before Content', 'piratez-cyberpunk'),
        'id'            => 'before-content',
        'description'   => esc_html__('Add widgets here to appear above main post/page content.', 'piratez-cyberpunk'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // After Content Widget Area
    register_sidebar(array(
        'name'          => esc_html__('After Content', 'piratez-cyberpunk'),
        'id'            => 'after-content',
        'description'   => esc_html__('Add widgets here to appear below main post/page content.', 'piratez-cyberpunk'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Before Post Widget Area (Single Posts Only)
    register_sidebar(array(
        'name'          => esc_html__('Before Post', 'piratez-cyberpunk'),
        'id'            => 'before-post',
        'description'   => esc_html__('Add widgets here to appear above single post content.', 'piratez-cyberpunk'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // After Post Widget Area (Single Posts Only)
    register_sidebar(array(
        'name'          => esc_html__('After Post', 'piratez-cyberpunk'),
        'id'            => 'after-post',
        'description'   => esc_html__('Add widgets here to appear below single post content (before comments).', 'piratez-cyberpunk'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Footer Widget Areas (4 columns)
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name'          => sprintf(esc_html__('Footer Column %d', 'piratez-cyberpunk'), $i),
            'id'            => 'footer-' . $i,
            'description'   => sprintf(esc_html__('Add widgets here to appear in footer column %d.', 'piratez-cyberpunk'), $i),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ));
    }

    // Homepage Hero Widget Area
    register_sidebar(array(
        'name'          => esc_html__('Homepage Hero', 'piratez-cyberpunk'),
        'id'            => 'homepage-hero',
        'description'   => esc_html__('Add widgets here to appear in the hero section on the homepage.', 'piratez-cyberpunk'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // Homepage Featured Widget Area
    register_sidebar(array(
        'name'          => esc_html__('Homepage Featured', 'piratez-cyberpunk'),
        'id'            => 'homepage-featured',
        'description'   => esc_html__('Add widgets here to appear in the featured content section on the homepage.', 'piratez-cyberpunk'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // Homepage Above Content Widget Area
    register_sidebar(array(
        'name'          => esc_html__('Homepage Above Content', 'piratez-cyberpunk'),
        'id'            => 'homepage-above-content',
        'description'   => esc_html__('Add widgets here to appear above blog posts on the homepage.', 'piratez-cyberpunk'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Homepage Below Content Widget Area
    register_sidebar(array(
        'name'          => esc_html__('Homepage Below Content', 'piratez-cyberpunk'),
        'id'            => 'homepage-below-content',
        'description'   => esc_html__('Add widgets here to appear below blog posts on the homepage.', 'piratez-cyberpunk'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // 404 Page Widget Area
    register_sidebar(array(
        'name'          => esc_html__('404 Page', 'piratez-cyberpunk'),
        'id'            => '404-page',
        'description'   => esc_html__('Add widgets here to appear on the 404 error page.', 'piratez-cyberpunk'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'piratez_cyberpunk_widgets_init');
