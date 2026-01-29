<?php
/**
 * Piratez Cyberpunk Theme Customizer
 *
 * @package piratez_cyberpunk
 */

/**
 * Add postMessage support for site title and description
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object
 */
function piratez_cyberpunk_customize_register($wp_customize) {
    // Transport for live preview
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    // ============================================
    // MAIN THEME PANEL
    // ============================================
    $wp_customize->add_panel(
        'piratez_theme_panel',
        array(
            'title'       => __('Theme Settings', 'piratez-cyberpunk'),
            'description' => __('Main theme customization options', 'piratez-cyberpunk'),
            'priority'    => 10,
        )
    );

    // ============================================
    // 1. SITE IDENTITY & BRANDING
    // ============================================
    $wp_customize->add_section(
        'piratez_branding_section',
        array(
            'title'    => __('Site Identity & Branding', 'piratez-cyberpunk'),
            'priority' => 10,
            'panel'    => 'piratez_theme_panel',
        )
    );


    // Tagline Display
    $wp_customize->add_setting(
        'piratez_tagline_display',
        array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'piratez_tagline_display_control',
        array(
            'label'    => __('Display Site Tagline', 'piratez-cyberpunk'),
            'section'  => 'piratez_branding_section',
            'settings' => 'piratez_tagline_display',
            'type'     => 'checkbox',
        )
    );

    // ============================================
    // 2. COLORS & APPEARANCE
    // ============================================
    $wp_customize->add_section(
        'piratez_colors_section',
        array(
            'title'    => __('Colors & Appearance', 'piratez-cyberpunk'),
            'priority' => 20,
            'panel'    => 'piratez_theme_panel',
        )
    );

    // Primary Accent Color
    $wp_customize->add_setting(
        'piratez_primary_accent_color',
        array(
            'default'           => '#0066cc',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'piratez_primary_accent_color_control',
            array(
                'label'    => __('Primary Accent Color', 'piratez-cyberpunk'),
                'section'  => 'piratez_colors_section',
                'settings' => 'piratez_primary_accent_color',
            )
        )
    );

    // Secondary Accent Color
    $wp_customize->add_setting(
        'piratez_secondary_accent_color',
        array(
            'default'           => '#cc0066',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'piratez_secondary_accent_color_control',
            array(
                'label'    => __('Secondary Accent Color (Pirate/Cyberpunk)', 'piratez-cyberpunk'),
                'section'  => 'piratez_colors_section',
                'settings' => 'piratez_secondary_accent_color',
            )
        )
    );

    // Pirate Gold Color
    $wp_customize->add_setting(
        'piratez_gold_color',
        array(
            'default'           => '#b8860b',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'piratez_gold_color_control',
            array(
                'label'    => __('Pirate Gold Color', 'piratez-cyberpunk'),
                'section'  => 'piratez_colors_section',
                'settings' => 'piratez_gold_color',
            )
        )
    );

    // Background Color
    $wp_customize->add_setting(
        'piratez_background_color',
        array(
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'piratez_background_color_control',
            array(
                'label'    => __('Background Color', 'piratez-cyberpunk'),
                'section'  => 'piratez_colors_section',
                'settings' => 'piratez_background_color',
            )
        )
    );

    // Accent Intensity
    $wp_customize->add_setting(
        'piratez_accent_intensity',
        array(
            'default'           => 50,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'piratez_accent_intensity_control',
        array(
            'label'       => __('Pirate/Cyberpunk Accent Intensity', 'piratez-cyberpunk'),
            'description' => __('Control how prominent pirate/cyberpunk elements are (0-100)', 'piratez-cyberpunk'),
            'section'     => 'piratez_colors_section',
            'settings'    => 'piratez_accent_intensity',
            'type'        => 'range',
            'input_attrs' => array(
                'min'  => 0,
                'max'  => 100,
                'step' => 5,
            ),
        )
    );

    // Dark Mode Default
    $wp_customize->add_setting(
        'piratez_dark_mode_default',
        array(
            'default'           => 'light',
            'sanitize_callback' => 'piratez_sanitize_theme_mode',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'piratez_dark_mode_default_control',
        array(
            'label'    => __('Default Theme Mode', 'piratez-cyberpunk'),
            'section'  => 'piratez_colors_section',
            'settings' => 'piratez_dark_mode_default',
            'type'     => 'select',
            'choices'  => array(
                'light' => __('Light', 'piratez-cyberpunk'),
                'dark'  => __('Dark', 'piratez-cyberpunk'),
                'auto'  => __('Auto (System Preference)', 'piratez-cyberpunk'),
            ),
        )
    );

    // ============================================
    // 3. TYPOGRAPHY SETTINGS
    // ============================================
    $wp_customize->add_section(
        'piratez_typography_section',
        array(
            'title'    => __('Typography Settings', 'piratez-cyberpunk'),
            'priority' => 30,
            'panel'    => 'piratez_theme_panel',
        )
    );

    // Heading Font
    $wp_customize->add_setting(
        'piratez_heading_font',
        array(
            'default'           => 'Inter',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'piratez_heading_font_control',
        array(
            'label'    => __('Heading Font', 'piratez-cyberpunk'),
            'section'  => 'piratez_typography_section',
            'settings' => 'piratez_heading_font',
            'type'     => 'select',
            'choices'  => array(
                'Inter'     => 'Inter',
                'Roboto'    => 'Roboto',
                'System UI' => 'System UI',
            ),
        )
    );

    // Body Font
    $wp_customize->add_setting(
        'piratez_body_font',
        array(
            'default'           => 'Inter',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'piratez_body_font_control',
        array(
            'label'    => __('Body Font', 'piratez-cyberpunk'),
            'section'  => 'piratez_typography_section',
            'settings' => 'piratez_body_font',
            'type'     => 'select',
            'choices'  => array(
                'Inter'     => 'Inter',
                'Roboto'    => 'Roboto',
                'System UI' => 'System UI',
            ),
        )
    );

    // ============================================
    // 4. LAYOUT SETTINGS
    // ============================================
    $wp_customize->add_section(
        'piratez_layout_section',
        array(
            'title'    => __('Layout Settings', 'piratez-cyberpunk'),
            'priority' => 35,
            'panel'    => 'piratez_theme_panel',
        )
    );

    // Sidebar Display
    $wp_customize->add_setting(
        'piratez_sidebar_display',
        array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'piratez_sidebar_display_control',
        array(
            'label'       => __('Display Sidebar', 'piratez-cyberpunk'),
            'description' => __('Show or hide the sidebar on desktop. Sidebar is always hidden on mobile.', 'piratez-cyberpunk'),
            'section'     => 'piratez_layout_section',
            'settings'    => 'piratez_sidebar_display',
            'type'        => 'checkbox',
        )
    );

    // ============================================
    // 5. HEADER SETTINGS
    // ============================================
    $wp_customize->add_section(
        'piratez_header_section',
        array(
            'title'    => __('Header Settings', 'piratez-cyberpunk'),
            'priority' => 40,
            'panel'    => 'piratez_theme_panel',
        )
    );

    // Sticky Header
    $wp_customize->add_setting(
        'piratez_sticky_header',
        array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'piratez_sticky_header_control',
        array(
            'label'    => __('Enable Sticky Header', 'piratez-cyberpunk'),
            'section'  => 'piratez_header_section',
            'settings' => 'piratez_sticky_header',
            'type'     => 'checkbox',
        )
    );

    // ============================================
    // 5. FOOTER SETTINGS
    // ============================================
    $wp_customize->add_section(
        'piratez_footer_section',
        array(
            'title'    => __('Footer Settings', 'piratez-cyberpunk'),
            'priority' => 50,
            'panel'    => 'piratez_theme_panel',
        )
    );

    // Footer Columns
    $wp_customize->add_setting(
        'piratez_footer_columns',
        array(
            'default'           => 4,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'piratez_footer_columns_control',
        array(
            'label'    => __('Footer Columns', 'piratez-cyberpunk'),
            'section'  => 'piratez_footer_section',
            'settings' => 'piratez_footer_columns',
            'type'     => 'select',
            'choices'  => array(
                1 => __('1 Column', 'piratez-cyberpunk'),
                2 => __('2 Columns', 'piratez-cyberpunk'),
                3 => __('3 Columns', 'piratez-cyberpunk'),
                4 => __('4 Columns', 'piratez-cyberpunk'),
            ),
        )
    );

    // ============================================
    // 6. SOCIAL MEDIA LINKS
    // ============================================
    $wp_customize->add_section(
        'piratez_social_media_section',
        array(
            'title'    => __('Social Media Links', 'piratez-cyberpunk'),
            'priority' => 60,
            'panel'    => 'piratez_theme_panel',
            'description' => __('Configure your social media links below. Use the shortcode <code>[piratez_social_links]</code> to display them anywhere on your site.', 'piratez-cyberpunk'),
        )
    );

    $social_networks = array(
        'twitter'   => __('Twitter', 'piratez-cyberpunk'),
        'facebook'  => __('Facebook', 'piratez-cyberpunk'),
        'instagram' => __('Instagram', 'piratez-cyberpunk'),
        'linkedin'  => __('LinkedIn', 'piratez-cyberpunk'),
        'github'    => __('GitHub', 'piratez-cyberpunk'),
        'youtube'   => __('YouTube', 'piratez-cyberpunk'),
        'email'     => __('Email', 'piratez-cyberpunk'),
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting(
            'piratez_social_' . $network,
            array(
                'default'           => '',
                'sanitize_callback' => ($network === 'email') ? 'sanitize_email' : 'esc_url_raw',
                'transport'         => 'refresh',
            )
        );

        $wp_customize->add_control(
            'piratez_social_' . $network . '_control',
            array(
                'label'    => $label . ' URL',
                'section'  => 'piratez_social_media_section',
                'settings' => 'piratez_social_' . $network,
                'type'     => 'text',
            )
        );
    }

    // ============================================
    // 7. BLOG FEATURES TOGGLE
    // ============================================
    $wp_customize->add_section(
        'piratez_blog_features_section',
        array(
            'title'    => __('Blog Features', 'piratez-cyberpunk'),
            'priority' => 70,
            'panel'    => 'piratez_theme_panel',
        )
    );

    $blog_features = array(
        'reading_time'        => __('Reading Time', 'piratez-cyberpunk'),
        'reading_progress'    => __('Reading Progress Bar', 'piratez-cyberpunk'),
        'table_of_contents'   => __('Table of Contents', 'piratez-cyberpunk'),
        'author_box'          => __('Author Box', 'piratez-cyberpunk'),
        'related_posts'       => __('Related Posts', 'piratez-cyberpunk'),
        'scroll_to_top'      => __('Scroll to Top Button', 'piratez-cyberpunk'),
        'social_sharing'     => __('Social Sharing Buttons', 'piratez-cyberpunk'),
        'post_date'          => __('Post Date', 'piratez-cyberpunk'),
        'post_author'        => __('Post Author', 'piratez-cyberpunk'),
        'post_updated'       => __('Last Updated Date', 'piratez-cyberpunk'),
        'post_categories'    => __('Post Categories', 'piratez-cyberpunk'),
        'post_tags'          => __('Post Tags', 'piratez-cyberpunk'),
    );

    foreach ($blog_features as $feature => $label) {
        $wp_customize->add_setting(
            'piratez_' . $feature,
            array(
                'default'           => true,
                'sanitize_callback' => 'wp_validate_boolean',
                'transport'         => 'refresh',
            )
        );

        $wp_customize->add_control(
            'piratez_' . $feature . '_control',
            array(
                'label'    => __('Enable', 'piratez-cyberpunk') . ' ' . $label,
                'section'  => 'piratez_blog_features_section',
                'settings' => 'piratez_' . $feature,
                'type'     => 'checkbox',
            )
        );
    }

    // ============================================
    // 8. HOMEPAGE SETTINGS
    // ============================================
    $wp_customize->add_section(
        'piratez_homepage_section',
        array(
            'title'    => __('Homepage Settings', 'piratez-cyberpunk'),
            'priority' => 80,
            'panel'    => 'piratez_theme_panel',
        )
    );

    // Posts Per Page
    $wp_customize->add_setting(
        'piratez_posts_per_page',
        array(
            'default'           => 10,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'piratez_posts_per_page_control',
        array(
            'label'       => __('Posts Per Page', 'piratez-cyberpunk'),
            'section'     => 'piratez_homepage_section',
            'settings'    => 'piratez_posts_per_page',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 1,
                'max'  => 50,
                'step' => 1,
            ),
        )
    );

    // ============================================
    // 9. makeasite.gr INTEGRATION
    // ============================================
    $wp_customize->add_section(
        'piratez_makeasite_section',
        array(
            'title'    => __('makeasite.gr Integration', 'piratez-cyberpunk'),
            'priority' => 90,
            'panel'    => 'piratez_theme_panel',
        )
    );

    // Show makeasite.gr Branding
    $wp_customize->add_setting(
        'piratez_makeasite_branding',
        array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'piratez_makeasite_branding_control',
        array(
            'label'    => __('Show makeasite.gr Branding', 'piratez-cyberpunk'),
            'section'  => 'piratez_makeasite_section',
            'settings' => 'piratez_makeasite_branding',
            'type'     => 'checkbox',
        )
    );

    // Branding Text
    $wp_customize->add_setting(
        'piratez_makeasite_branding_text',
        array(
            'default'           => __('Built by', 'piratez-cyberpunk') . ' makeasite.gr',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'piratez_makeasite_branding_text_control',
        array(
            'label'    => __('Branding Text', 'piratez-cyberpunk'),
            'section'  => 'piratez_makeasite_section',
            'settings' => 'piratez_makeasite_branding_text',
            'type'     => 'text',
        )
    );

    // Branding Link
    $wp_customize->add_setting(
        'piratez_makeasite_branding_link',
        array(
            'default'           => 'https://makeasite.gr',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'piratez_makeasite_branding_link_control',
        array(
            'label'    => __('Branding Link URL', 'piratez-cyberpunk'),
            'section'  => 'piratez_makeasite_section',
            'settings' => 'piratez_makeasite_branding_link',
            'type'     => 'url',
        )
    );

    // ============================================
    // 10. ADVANCED SETTINGS
    // ============================================
    $wp_customize->add_section(
        'piratez_advanced_section',
        array(
            'title'    => __('Advanced Settings', 'piratez-cyberpunk'),
            'priority' => 100,
            'panel'    => 'piratez_theme_panel',
        )
    );

    // Custom CSS
    $wp_customize->add_setting(
        'piratez_custom_css',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_strip_all_tags',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'piratez_custom_css_control',
        array(
            'label'    => __('Custom CSS', 'piratez-cyberpunk'),
            'section'  => 'piratez_advanced_section',
            'settings' => 'piratez_custom_css',
            'type'     => 'textarea',
        )
    );
}
add_action('customize_register', 'piratez_cyberpunk_customize_register');

/**
 * Sanitize theme mode
 */
function piratez_sanitize_theme_mode($input) {
    $valid = array('light', 'dark', 'auto');
    if (in_array($input, $valid, true)) {
        return $input;
    }
    return 'light';
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously
 */
function piratez_cyberpunk_customize_preview_js() {
    wp_enqueue_script(
        'piratez-customizer',
        get_template_directory_uri() . '/js/customizer.js',
        array('customize-preview'),
        PIRATEZ_VERSION,
        true
    );
}
add_action('customize_preview_init', 'piratez_cyberpunk_customize_preview_js');
