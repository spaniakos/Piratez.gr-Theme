/**
 * Theme Customizer preview
 *
 * @package piratez_cyberpunk
 */

(function ($) {
    'use strict';

    // Site title and description
    wp.customize('blogname', function (value) {
        value.bind(function (to) {
            $('.site-title a').text(to);
        });
    });

    wp.customize('blogdescription', function (value) {
        value.bind(function (to) {
            $('.site-description').text(to);
        });
    });

    // Phase PRE 5: full light/dark palette — inject style so :root and html[data-theme="dark"] both get correct vars
    var colorVarNames = {
        'piratez_light_bg_primary': '--color-bg-primary',
        'piratez_light_bg_secondary': '--color-bg-secondary',
        'piratez_light_surface': '--color-surface',
        'piratez_light_text_primary': '--color-text-primary',
        'piratez_light_text_secondary': '--color-text-secondary',
        'piratez_light_border': '--color-border',
        'piratez_light_accent_primary': '--color-accent-primary',
        'piratez_light_accent_secondary': '--color-accent-secondary',
        'piratez_light_accent_gold': '--color-accent-gold',
        'piratez_dark_bg_primary': '--color-bg-primary',
        'piratez_dark_bg_secondary': '--color-bg-secondary',
        'piratez_dark_surface': '--color-surface',
        'piratez_dark_text_primary': '--color-text-primary',
        'piratez_dark_text_secondary': '--color-text-secondary',
        'piratez_dark_border': '--color-border',
        'piratez_dark_accent_primary': '--color-accent-primary',
        'piratez_dark_accent_secondary': '--color-accent-secondary',
        'piratez_dark_accent_gold': '--color-accent-gold'
    };
    var lightKeys = ['piratez_light_bg_primary', 'piratez_light_bg_secondary', 'piratez_light_surface', 'piratez_light_text_primary', 'piratez_light_text_secondary', 'piratez_light_border', 'piratez_light_accent_primary', 'piratez_light_accent_secondary', 'piratez_light_accent_gold'];
    var darkKeys = ['piratez_dark_bg_primary', 'piratez_dark_bg_secondary', 'piratez_dark_surface', 'piratez_dark_text_primary', 'piratez_dark_text_secondary', 'piratez_dark_border', 'piratez_dark_accent_primary', 'piratez_dark_accent_secondary', 'piratez_dark_accent_gold'];

    var fontStacks = {
        'Inter': "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
        'Roboto': "'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
        'System UI': "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    };

    function buildColorCss() {
        var lightParts = [], darkParts = [];
        lightKeys.forEach(function (k) {
            var v = wp.customize(k) ? wp.customize(k).get() : '';
            if (v) lightParts.push(colorVarNames[k] + ': ' + v);
        });
        darkKeys.forEach(function (k) {
            var v = wp.customize(k) ? wp.customize(k).get() : '';
            if (v) darkParts.push(colorVarNames[k] + ': ' + v);
        });
        // Typography (theme-controlled)
        var headingFont = wp.customize('piratez_heading_font') ? wp.customize('piratez_heading_font').get() : 'Inter';
        var bodyFont = wp.customize('piratez_body_font') ? wp.customize('piratez_body_font').get() : 'Inter';
        var accentFont = wp.customize('piratez_accent_font') ? wp.customize('piratez_accent_font').get() : 'Press Start 2P';
        var baseSize = wp.customize('piratez_base_font_size') ? wp.customize('piratez_base_font_size').get() : 16;
        lightParts.push('--font-heading: ' + (fontStacks[headingFont] || fontStacks['Inter']));
        lightParts.push('--font-body: ' + (fontStacks[bodyFont] || fontStacks['Inter']));
        if (accentFont === 'Press Start 2P') {
            lightParts.push("--font-accent: 'Press Start 2P', monospace");
        } else if (accentFont === 'Same as heading') {
            lightParts.push('--font-accent: var(--font-heading)');
        } else {
            lightParts.push('--font-accent: var(--font-body)');
        }
        baseSize = Math.max(14, Math.min(22, parseInt(baseSize, 10) || 16));
        return ':root { ' + lightParts.join('; ') + ' } html[data-theme="dark"] { ' + darkParts.join('; ') + ' } html { font-size: ' + baseSize + 'px; }';
    }

    function refreshDynamicColors() {
        var css = buildColorCss();
        $('#piratez-dynamic-colors').remove();
        if (css) $('head').append('<style id="piratez-dynamic-colors">' + css + '</style>');
    }

    lightKeys.concat(darkKeys).forEach(function (key) {
        if (wp.customize(key)) {
            wp.customize(key).bind(refreshDynamicColors);
        }
    });
    ['piratez_heading_font', 'piratez_body_font', 'piratez_accent_font', 'piratez_base_font_size'].forEach(function (key) {
        if (wp.customize(key)) {
            wp.customize(key).bind(refreshDynamicColors);
        }
    });
    refreshDynamicColors();

    // Custom CSS
    wp.customize('piratez_custom_css', function (value) {
        value.bind(function (to) {
            $('#piratez-custom-css').remove();
            if (to) {
                $('head').append('<style id="piratez-custom-css">' + to + '</style>');
            }
        });
    });
})(jQuery);
