/**
 * Theme Customizer preview
 *
 * @package piratez_cyberpunk
 */

(function($) {
    'use strict';

    // Site title and description
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-title a').text(to);
        });
    });

    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-description').text(to);
        });
    });

    // Colors
    wp.customize('piratez_primary_accent_color', function(value) {
        value.bind(function(to) {
            $(':root').css('--color-accent-primary', to);
        });
    });

    wp.customize('piratez_secondary_accent_color', function(value) {
        value.bind(function(to) {
            $(':root').css('--color-accent-secondary', to);
        });
    });

    wp.customize('piratez_gold_color', function(value) {
        value.bind(function(to) {
            $(':root').css('--color-accent-gold', to);
        });
    });

    wp.customize('piratez_background_color', function(value) {
        value.bind(function(to) {
            $('body').css('background-color', to);
        });
    });

    // Custom CSS
    wp.customize('piratez_custom_css', function(value) {
        value.bind(function(to) {
            $('#piratez-custom-css').remove();
            if (to) {
                $('head').append('<style id="piratez-custom-css">' + to + '</style>');
            }
        });
    });
})(jQuery);
