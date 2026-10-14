<?php
/** Optional first-party modules and native GitHub updates. */
if (!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/github-updater.php';

function piratez_components() {
    $items = array(
        'theme' => array('name' => 'PirateZ Cyberpunk', 'type' => 'theme', 'slug' => 'piratez-cyberpunk', 'repo' => 'spaniakos/Piratez.gr-Theme', 'key' => get_template(), 'entry' => 'style.css'),
        'piratez-image-optimize' => array('name' => 'PirateZ Image Optimize', 'type' => 'plugin', 'slug' => 'piratez-image-optimize', 'repo' => 'spaniakos/piratez-image-optimize', 'key' => 'piratez-image-optimize/piratez-image-optimize.php', 'entry' => 'piratez-image-optimize.php'),
        'piratez-cookie-consent' => array('name' => 'PirateZ Cookie Consent', 'type' => 'plugin', 'slug' => 'piratez-cookie-consent', 'repo' => 'spaniakos/piratez-cookie-consent', 'key' => 'piratez-cookie-consent/piratez-cookie-consent.php', 'entry' => 'piratez-cookie-consent.php'),
    );
    if (is_admin()) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $plugins = get_plugins();
        foreach ($items as &$item) {
            if ($item['type'] !== 'plugin' || isset($plugins[$item['key']])) { continue; }
            foreach ($plugins as $key => $headers) {
                if (rtrim($headers['UpdateURI'] ?: $headers['PluginURI'], '/') === 'https://github.com/' . $item['repo']) { $item['key'] = $key; break; }
            }
        }
        unset($item);
    }
    return $items;
}

function piratez_component_updaters() {
    static $clients = null;
    if ($clients === null) {
        $clients = array();
        foreach (piratez_components() as $id => $component) { $clients[$id] = new \PirateZTheme\GitHubUpdater($component); }
    }
    return $clients;
}
piratez_component_updaters();

add_action('admin_menu', function () { add_theme_page('PirateZ Updates & Modules', 'PirateZ Updates & Modules', 'edit_theme_options', 'piratez-components', 'piratez_components_page'); });

function piratez_component_form($id, $operation, $label) {
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="display:inline-block;margin:0 8px 8px 0">';
    echo '<input type="hidden" name="action" value="piratez_component"><input type="hidden" name="component" value="' . esc_attr($id) . '"><input type="hidden" name="operation" value="' . esc_attr($operation) . '">';
    wp_nonce_field('piratez_component_' . $id . '_' . $operation);
    submit_button($label, 'secondary', 'submit', false);
    echo '</form>';
}

function piratez_components_page() {
    if (!current_user_can('edit_theme_options')) { return; }
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $plugins = get_plugins();
    echo '<div class="wrap"><h1>PirateZ Updates & Modules</h1><p>Updates come from the official PirateZ GitHub releases. Install modules individually, then activate them when ready.</p>';
    $notice = get_transient('piratez_component_notice_' . get_current_user_id());
    if ($notice) { delete_transient('piratez_component_notice_' . get_current_user_id()); echo '<div class="notice notice-' . ($notice['ok'] ? 'success' : 'error') . '"><p>' . esc_html($notice['message']) . '</p></div>'; }
    foreach (piratez_component_updaters() as $id => $client) {
        $item = $client->config;
        $installed = $item['type'] === 'theme' || isset($plugins[$item['key']]);
        $version = $installed ? ($item['type'] === 'theme' ? wp_get_theme($item['key'])->get('Version') : $plugins[$item['key']]['Version']) : '';
        $release = $client->release();
        echo '<div class="card"><h2>' . esc_html($item['name']) . '</h2><p>Installed: ' . esc_html($version ?: 'Not installed') . '</p>';
        if (is_wp_error($release)) { echo '<p>' . esc_html($release->get_error_message()) . '</p>'; }
        else {
            echo '<p>Latest release: <a href="' . esc_url($release['url']) . '" target="_blank" rel="noopener noreferrer">' . esc_html($release['version']) . '</a></p>';
            if (!$client->compatible($release)) { echo '<p>Requires WordPress ' . esc_html($release['requires']) . ' and PHP ' . esc_html($release['requires_php']) . '.</p>'; }
            elseif (!$installed && current_user_can('install_plugins')) { piratez_component_form($id, 'install', 'Install module'); }
            elseif ($installed && version_compare($release['version'], $version, '>') && current_user_can('update_' . ($item['type'] === 'theme' ? 'themes' : 'plugins'))) { piratez_component_form($id, 'update', 'Update now'); }
            elseif ($installed) { echo '<p>Up to date.</p>'; }
        }
        if ($installed && $item['type'] === 'plugin' && !is_plugin_active($item['key']) && current_user_can('activate_plugins')) { piratez_component_form($id, 'activate', 'Activate module'); }
        elseif ($installed && $item['type'] === 'plugin' && is_plugin_active($item['key'])) { echo '<p>Module active.</p>'; }
        if (current_user_can('update_' . ($item['type'] === 'theme' ? 'themes' : 'plugins'))) { piratez_component_form($id, 'check', 'Check for updates'); }
        echo '</div>';
    }
    echo '<p>Automatic updates can be enabled in the standard WordPress Themes and Plugins screens. GitHub access does not require an API key for these public repositories.</p></div>';
}

function piratez_component_result($ok, $message) {
    set_transient('piratez_component_notice_' . get_current_user_id(), array('ok' => $ok, 'message' => $message), MINUTE_IN_SECONDS);
    wp_safe_redirect(admin_url('themes.php?page=piratez-components')); exit;
}

function piratez_component_action() {
    if (!current_user_can('edit_theme_options')) { wp_die('Permission denied.', '', array('response' => 403)); }
    $id = sanitize_key(wp_unslash($_POST['component'] ?? ''));
    $operation = sanitize_key(wp_unslash($_POST['operation'] ?? ''));
    $clients = piratez_component_updaters();
    if (!isset($clients[$id]) || !in_array($operation, array('check', 'install', 'update', 'activate'), true)) { wp_die('Unknown component or operation.', '', array('response' => 400)); }
    check_admin_referer('piratez_component_' . $id . '_' . $operation);
    $client = $clients[$id]; $item = $client->config;
    $cap = $operation === 'activate' ? 'activate_plugins' : ($operation === 'install' ? 'install_plugins' : 'update_' . ($item['type'] === 'theme' ? 'themes' : 'plugins'));
    if (!current_user_can($cap) || !wp_is_file_mod_allowed('piratez_components')) { wp_die('This operation is not permitted on this installation.', '', array('response' => 403)); }
    if ($item['type'] === 'theme' && in_array($operation, array('install', 'activate'), true)) { wp_die('Unsupported theme operation.', '', array('response' => 400)); }
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    if ($operation === 'activate') {
        $result = activate_plugin($item['key']);
        piratez_component_result(!is_wp_error($result), is_wp_error($result) ? $result->get_error_message() : 'Module activated.');
    }
    $release = $client->release(true);
    if (is_wp_error($release)) { piratez_component_result(false, $release->get_error_message()); }
    if ($operation === 'check') {
        if ($item['type'] === 'theme') { delete_site_transient('update_themes'); wp_update_themes(); }
        else { delete_site_transient('update_plugins'); wp_update_plugins(); }
        piratez_component_result(true, 'Update information refreshed. Latest release: ' . $release['version']);
    }
    if (!$client->compatible($release)) { piratez_component_result(false, 'This release requires a newer WordPress or PHP version.'); }
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    $skin = new WP_Ajax_Upgrader_Skin();
    if ($operation === 'install') {
        if (isset(get_plugins()[$item['key']])) { piratez_component_result(false, 'The module is already installed.'); }
        $upgrader = new Plugin_Upgrader($skin); $result = $upgrader->install($release['package']);
    } else {
        $version = $item['type'] === 'theme' ? wp_get_theme($item['key'])->get('Version') : (get_plugins()[$item['key']]['Version'] ?? '');
        if (!$version || !version_compare($release['version'], $version, '>')) { piratez_component_result(false, 'No newer release is available.'); }
        $transient = get_site_transient('update_' . ($item['type'] === 'theme' ? 'themes' : 'plugins'));
        if (!is_object($transient)) { $transient = new stdClass(); }
        if (!isset($transient->response) || !is_array($transient->response)) { $transient->response = array(); }
        $update = $client->update(false, array('UpdateURI' => $client->uri()), $item['key'], array());
        $transient->response[$item['key']] = $item['type'] === 'theme' ? $update : (object) array_merge($update, array('new_version' => $release['version'], 'plugin' => $item['key']));
        set_site_transient('update_' . ($item['type'] === 'theme' ? 'themes' : 'plugins'), $transient);
        $upgrader = $item['type'] === 'theme' ? new Theme_Upgrader($skin) : new Plugin_Upgrader($skin);
        if ($item['type'] === 'theme') { $result = $upgrader->upgrade($item['key']); }
        else {
            // Match WordPress' AJAX updater: keep active modules active during replacement.
            $results = $upgrader->bulk_upgrade(array($item['key']));
            $result = is_array($results) && isset($results[$item['key']]) ? $results[$item['key']] : $results;
            if (is_array($result)) { $result = true; }
        }
    }
    $errors = $skin->get_errors();
    $ok = $result === true && !$errors->has_errors();
    piratez_component_result($ok, $ok ? ($operation === 'install' ? 'Module installed. Activate it when ready.' : 'Update completed.') : (is_wp_error($result) ? $result->get_error_message() : ($errors->has_errors() ? $errors->get_error_message() : 'Installation could not complete. Use the standard WordPress installer if filesystem credentials are required.')));
}
add_action('admin_post_piratez_component', 'piratez_component_action');
