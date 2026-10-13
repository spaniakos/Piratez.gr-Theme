<?php
/** Shared Gutenberg assets and Customizer design variables. */
if (!defined('ABSPATH')) { exit; }

function piratez_design_variables_css($editor = false) {
    $light_vars = array(
        '--color-bg-primary'       => get_theme_mod('piratez_light_bg_primary') ?: get_theme_mod('piratez_background_color', '#ffffff'),
        '--color-bg-secondary'     => get_theme_mod('piratez_light_bg_secondary', '#f5f5f5'),
        '--color-surface'           => get_theme_mod('piratez_light_surface', '#ffffff'),
        '--color-text-primary'     => get_theme_mod('piratez_light_text_primary', '#1a1a2e'),
        '--color-text-secondary'   => get_theme_mod('piratez_light_text_secondary', '#666666'),
        '--color-border'           => get_theme_mod('piratez_light_border', '#e0e0e0'),
        '--color-accent-primary'    => get_theme_mod('piratez_light_accent_primary') ?: get_theme_mod('piratez_primary_accent_color', '#0066cc'),
        '--color-accent-secondary'  => get_theme_mod('piratez_light_accent_secondary') ?: get_theme_mod('piratez_secondary_accent_color', '#cc0066'),
        '--color-accent-gold'       => get_theme_mod('piratez_light_accent_gold') ?: get_theme_mod('piratez_gold_color', '#b8860b'),
    );
    $dark_vars = array(
        '--color-bg-primary'       => get_theme_mod('piratez_dark_bg_primary', '#1a1a2e'),
        '--color-bg-secondary'     => get_theme_mod('piratez_dark_bg_secondary', '#0f0f1a'),
        '--color-surface'           => get_theme_mod('piratez_dark_surface', '#252540'),
        '--color-text-primary'     => get_theme_mod('piratez_dark_text_primary', '#e0e0e0'),
        '--color-text-secondary'   => get_theme_mod('piratez_dark_text_secondary', '#b0b0b0'),
        '--color-border'           => get_theme_mod('piratez_dark_border', '#3a3a5a'),
        '--color-accent-primary'    => get_theme_mod('piratez_dark_accent_primary', '#00a8ff'),
        '--color-accent-secondary'  => get_theme_mod('piratez_dark_accent_secondary', '#ff0080'),
        '--color-accent-gold'       => get_theme_mod('piratez_dark_accent_gold', '#ffd700'),
    );
    $light_decls = array();
    foreach ($light_vars as $var => $val) {
        $light_decls[] = $var . ': ' . esc_attr($val);
    }
    $dark_decls = array();
    foreach ($dark_vars as $var => $val) {
        $dark_decls[] = $var . ': ' . esc_attr($val);
    }

    // Typography (theme-controlled; blog-friendly)
    $heading_font = get_theme_mod('piratez_heading_font', 'Inter');
    $body_font    = get_theme_mod('piratez_body_font', 'Inter');
    $accent_font  = get_theme_mod('piratez_accent_font', 'Press Start 2P');
    $base_size    = max(14, min(22, absint(get_theme_mod('piratez_base_font_size', 16))));
    $light_decls[] = '--piratez-base-font-size: ' . $base_size . 'px';
    $light_decls[] = '--font-heading: ' . piratez_get_font_stack($heading_font);
    $light_decls[] = '--font-body: ' . piratez_get_font_stack($body_font);
    if ($accent_font === 'Press Start 2P') {
        $light_decls[] = "--font-accent: 'Press Start 2P', monospace";
    } elseif ($accent_font === 'Same as heading') {
        $light_decls[] = '--font-accent: var(--font-heading)';
    } else {
        $light_decls[] = '--font-accent: var(--font-body)';
    }

    $dynamic_css = ':root { ' . implode('; ', $light_decls) . '; }
html[data-theme="dark"] { ' . implode('; ', $dark_decls) . '; }
html { font-size: ' . $base_size . 'px; }
';
    if ($editor) {
        $vars = get_theme_mod('piratez_dark_mode_default', 'light') === 'dark' ? array_merge($light_decls, $dark_decls) : $light_decls;
        return ':root, .editor-styles-wrapper { ' . implode('; ', $vars) . '; }';
    }
    return $dynamic_css;

}

/** enqueue_block_assets also reaches the iframe canvas in WordPress 7.1. */
function piratez_block_assets() {
    // Frontend resets must precede content defaults. Keep site chrome out of admin.
    $deps = is_admin() ? array() : array('piratez-cyberpunk', 'piratez-pirate');
    wp_enqueue_style('piratez-content', get_theme_file_uri('/css/content.css'), $deps, PIRATEZ_VERSION);
    if (is_admin()) {
        wp_add_inline_style('piratez-content', piratez_design_variables_css(true));
        wp_enqueue_style('piratez-editor-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&family=Press+Start+2P&display=swap', array(), null);
    }
}
add_action('enqueue_block_assets', 'piratez_block_assets');

/** Keep root rem sizing in the iframe consistent with frontend Customizer sizing. */
function piratez_editor_canvas_assets() {
    wp_enqueue_script('piratez-editor-canvas', get_theme_file_uri('/js/editor-canvas.js'), array('wp-dom-ready'), PIRATEZ_VERSION, true);
    wp_localize_script('piratez-editor-canvas', 'piratezEditor', array('baseFontSize' => max(14, min(22, absint(get_theme_mod('piratez_base_font_size', 16))))));
}
add_action('enqueue_block_editor_assets', 'piratez_editor_canvas_assets');
