<?php
/**
 * Phase 6: llms.txt and sitemap.xml for AI readability.
 *
 * - Rewrite rules: /llms.txt, /sitemap.xml
 * - Cached output (transients); invalidate on save_post and via cron or Customizer button
 * - Cron: daily regeneration
 * - Customizer: "Regenerate" button triggers AJAX
 *
 * Only active when current_theme_supports('piratez-ai-index').
 *
 * @package piratez_cyberpunk
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_theme_supports('piratez-ai-index')) {
    return;
}

/** Transient keys and TTL (12 hours) */
define('PIRATEZ_LLMS_TRANSIENT', 'piratez_llms_txt');
define('PIRATEZ_SITEMAP_TRANSIENT', 'piratez_sitemap_xml');
define('PIRATEZ_AI_INDEX_TTL', 12 * HOUR_IN_SECONDS);

/** Cron hook name */
define('PIRATEZ_CRON_AI_INDEX', 'piratez_regenerate_ai_index');

/**
 * Add rewrite rules for llms.txt and sitemap.xml
 */
function piratez_ai_index_rewrite_rules() {
    add_rewrite_rule('^llms\.txt$', 'index.php?piratez_llms_txt=1', 'top');
    add_rewrite_rule('^sitemap\.xml$', 'index.php?piratez_sitemap_xml=1', 'top');
}
add_action('init', 'piratez_ai_index_rewrite_rules');

/**
 * Register query vars
 */
function piratez_ai_index_query_vars($vars) {
    $vars[] = 'piratez_llms_txt';
    $vars[] = 'piratez_sitemap_xml';
    return $vars;
}
add_filter('query_vars', 'piratez_ai_index_query_vars');

/**
 * Serve llms.txt or sitemap.xml on template_redirect
 */
function piratez_ai_index_template_redirect() {
    if (get_query_var('piratez_llms_txt')) {
        piratez_serve_llms_txt();
        exit;
    }
    if (get_query_var('piratez_sitemap_xml')) {
        piratez_serve_sitemap_xml();
        exit;
    }
}
add_action('template_redirect', 'piratez_ai_index_template_redirect', 1);

/**
 * Generate llms.txt content (plain text: title, URL, date, modified, tags, author).
 */
function piratez_generate_llms_txt() {
    $home = get_bloginfo('name');
    $lines = array();
    $lines[] = '# ' . $home;
    $lines[] = '# ' . home_url('/');
    $lines[] = '';

    $posts = get_posts(array(
        'post_type'      => array('post', 'page'),
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'       => 'date',
        'order'         => 'DESC',
        'no_found_rows' => true,
    ));

    foreach ($posts as $post) {
        setup_postdata($post);
        $title    = get_the_title($post);
        $url      = get_permalink($post);
        $date     = get_the_date('c', $post);
        $modified = get_the_modified_date('c', $post);
        $author   = get_the_author_meta('display_name', $post->post_author);
        $tags     = wp_get_post_tags($post->ID);
        $tag_names = array_map(function ($t) {
            return $t->name;
        }, $tags);
        $lines[] = 'Title: ' . $title;
        $lines[] = 'URL: ' . $url;
        $lines[] = 'Date: ' . $date;
        $lines[] = 'Modified: ' . $modified;
        $lines[] = 'Author: ' . $author;
        if (!empty($tag_names)) {
            $lines[] = 'Tags: ' . implode(', ', $tag_names);
        }
        $lines[] = '';
        wp_reset_postdata();
    }

    return implode("\n", $lines);
}

/**
 * Generate sitemap.xml content (minimal urlset with loc and lastmod).
 */
function piratez_generate_sitemap_xml() {
    $urlset = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $urlset .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $posts = get_posts(array(
        'post_type'      => array('post', 'page'),
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'       => 'date',
        'order'         => 'DESC',
        'no_found_rows' => true,
    ));

    foreach ($posts as $post) {
        $urlset .= '  <url>' . "\n";
        $urlset .= '    <loc>' . esc_url(get_permalink($post)) . '</loc>' . "\n";
        $urlset .= '    <lastmod>' . get_the_modified_date('c', $post) . '</lastmod>' . "\n";
        $urlset .= '  </url>' . "\n";
    }

    $urlset .= '</urlset>';
    return $urlset;
}

/**
 * Get llms.txt content (from cache or generate).
 */
function piratez_get_llms_txt_content() {
    $cached = get_transient(PIRATEZ_LLMS_TRANSIENT);
    if ($cached !== false) {
        return $cached;
    }
    $content = piratez_generate_llms_txt();
    set_transient(PIRATEZ_LLMS_TRANSIENT, $content, PIRATEZ_AI_INDEX_TTL);
    return $content;
}

/**
 * Get sitemap.xml content (from cache or generate).
 */
function piratez_get_sitemap_xml_content() {
    $cached = get_transient(PIRATEZ_SITEMAP_TRANSIENT);
    if ($cached !== false) {
        return $cached;
    }
    $content = piratez_generate_sitemap_xml();
    set_transient(PIRATEZ_SITEMAP_TRANSIENT, $content, PIRATEZ_AI_INDEX_TTL);
    return $content;
}

/**
 * Serve llms.txt with correct headers
 */
function piratez_serve_llms_txt() {
    header('Content-Type: text/plain; charset=UTF-8');
    header('X-Robots-Tag: noindex');
    echo piratez_get_llms_txt_content();
}

/**
 * Serve sitemap.xml with correct headers
 */
function piratez_serve_sitemap_xml() {
    header('Content-Type: application/xml; charset=UTF-8');
    echo piratez_get_sitemap_xml_content();
}

/**
 * Invalidate cache when a post is saved
 */
function piratez_ai_index_invalidate_on_save() {
    delete_transient(PIRATEZ_LLMS_TRANSIENT);
    delete_transient(PIRATEZ_SITEMAP_TRANSIENT);
}
add_action('save_post', 'piratez_ai_index_invalidate_on_save');

/**
 * Regenerate and repopulate transients (for cron and AJAX)
 */
/**
 * Write llms.txt and sitemap.xml to site root (ABSPATH) when writable.
 * Called after regenerating transients so physical files exist for direct request or static hosting.
 */
function piratez_ai_index_write_files($llms_content, $sitemap_content) {
    if (!is_string($llms_content) || !is_string($sitemap_content)) {
        return;
    }
    $root = trailingslashit(ABSPATH);
    if (!is_writable($root)) {
        return;
    }
    $llms_file   = $root . 'llms.txt';
    $sitemap_file = $root . 'sitemap.xml';
    if (file_put_contents($llms_file, $llms_content, LOCK_EX) === false) {
        return;
    }
    file_put_contents($sitemap_file, $sitemap_content, LOCK_EX);
}

function piratez_ai_index_regenerate() {
    $llms    = piratez_generate_llms_txt();
    $sitemap = piratez_generate_sitemap_xml();
    set_transient(PIRATEZ_LLMS_TRANSIENT, $llms, PIRATEZ_AI_INDEX_TTL);
    set_transient(PIRATEZ_SITEMAP_TRANSIENT, $sitemap, PIRATEZ_AI_INDEX_TTL);
    piratez_ai_index_write_files($llms, $sitemap);
}

/**
 * Cron callback: regenerate cache
 */
function piratez_cron_regenerate_ai_index() {
    piratez_ai_index_regenerate();
}
add_action(PIRATEZ_CRON_AI_INDEX, 'piratez_cron_regenerate_ai_index');

/**
 * Schedule daily cron event if not already scheduled
 */
function piratez_ai_index_schedule_cron() {
    if (!current_theme_supports('piratez-ai-index')) {
        return;
    }
    if (get_transient('piratez_ai_index_cron_scheduled')) {
        return;
    }
    if (wp_next_scheduled(PIRATEZ_CRON_AI_INDEX)) {
        set_transient('piratez_ai_index_cron_scheduled', 1, DAY_IN_SECONDS);
        return;
    }
    wp_schedule_event(time(), 'daily', PIRATEZ_CRON_AI_INDEX);
    set_transient('piratez_ai_index_cron_scheduled', 1, DAY_IN_SECONDS);
}
add_action('init', 'piratez_ai_index_schedule_cron', 99);

/**
 * AJAX: Regenerate llms.txt and sitemap (Customizer button)
 */
function piratez_ajax_regenerate_ai_index() {
    check_ajax_referer('piratez_regenerate_ai_index', 'nonce');
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error(array('message' => __('Permission denied.', 'piratez-cyberpunk')));
    }
    piratez_ai_index_regenerate();
    wp_send_json_success(array('message' => __('llms.txt and sitemap.xml regenerated.', 'piratez-cyberpunk')));
}
add_action('wp_ajax_piratez_regenerate_ai_index', 'piratez_ajax_regenerate_ai_index');

/**
 * Customizer: AI Index section and Regenerate button
 *
 * Custom control class is defined inside this callback so it only runs when
 * the Customizer is loaded (WP_Customize_Control is available).
 */
function piratez_ai_index_customize_register($wp_customize) {
    if (!current_theme_supports('piratez-ai-index')) {
        return;
    }

    if (!class_exists('WP_Customize_Control')) {
        return;
    }

    if (!class_exists('Piratez_AI_Index_Button_Control')) {
        /**
         * Customizer control: button that triggers AJAX regeneration
         */
        class Piratez_AI_Index_Button_Control extends WP_Customize_Control {

            public $type = 'piratez_ai_index_button';

            public function render_content() {
                ?>
                <button type="button" class="button button-secondary" id="piratez-regenerate-ai-index-btn">
                    <?php esc_html_e('Regenerate llms.txt & sitemap.xml', 'piratez-cyberpunk'); ?>
                </button>
                <span id="piratez-ai-index-status" class="piratez-ai-index-status" style="margin-left:8px;"></span>
                <?php
            }
        }
    }

    $wp_customize->add_section(
        'piratez_ai_index_section',
        array(
            'title'       => __('AI Index / Sitemap', 'piratez-cyberpunk'),
            'description' => __('Regenerate llms.txt and sitemap.xml. Content is served at yoursite.com/llms.txt and yoursite.com/sitemap.xml (flush permalinks if you get 404). When the site root is writable, physical files are also written there on regenerate.', 'piratez-cyberpunk'),
            'priority'    => 200,
            'panel'       => 'piratez_theme_panel',
        )
    );

    $wp_customize->add_setting('piratez_ai_index_regenerate_trigger', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control(
        new Piratez_AI_Index_Button_Control(
            $wp_customize,
            'piratez_ai_index_regenerate_trigger',
            array(
                'section' => 'piratez_ai_index_section',
                'label'   => __('Regenerate now', 'piratez-cyberpunk'),
                'type'    => 'piratez_ai_index_button',
            )
        )
    );
}
add_action('customize_register', 'piratez_ai_index_customize_register', 20);

/**
 * Enqueue Customizer script for Regenerate button AJAX
 */
function piratez_ai_index_customizer_scripts() {
    if (!current_theme_supports('piratez-ai-index')) {
        return;
    }
    wp_enqueue_script(
        'piratez-ai-index-customizer',
        get_template_directory_uri() . '/js/ai-index-customizer.js',
        array('jquery'),
        defined('PIRATEZ_VERSION') ? PIRATEZ_VERSION : '1.0.0',
        true
    );
    wp_localize_script('piratez-ai-index-customizer', 'piratezAiIndex', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('piratez_regenerate_ai_index'),
    ));
}
add_action('customize_controls_enqueue_scripts', 'piratez_ai_index_customizer_scripts');

/**
 * Flush rewrite rules on theme activation
 */
function piratez_ai_index_activation() {
    piratez_ai_index_rewrite_rules();
    flush_rewrite_rules();
    delete_transient('piratez_ai_index_cron_scheduled');
}
add_action('after_switch_theme', 'piratez_ai_index_activation');
