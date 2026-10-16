<?php
/** Install/activate launcher only; module management belongs to PirateZ Manager. */
if (!defined('ABSPATH')) { exit; }

function piratez_manager_setup_url() {
    return admin_url('themes.php?page=piratez-manager-setup');
}

function piratez_theme_manager_state() {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $config = array('name' => 'PirateZ Manager', 'type' => 'plugin', 'slug' => 'piratez-manager', 'repo' => 'spaniakos/piratez-manager', 'key' => 'piratez-manager/piratez-manager.php', 'entry' => 'piratez-manager.php');
    $installed = false;
    foreach (get_plugins() as $key => $headers) {
        if (rtrim($headers['UpdateURI'] ?? '', '/') === 'https://github.com/' . $config['repo']) {
            $config['key'] = $key;
            $installed = true;
            break;
        }
    }
    return array('config' => $config, 'installed' => $installed, 'active' => $installed && is_plugin_active($config['key']), 'collision' => !$installed && is_dir(WP_PLUGIN_DIR . '/piratez-manager'));
}

add_action('admin_menu', function () {
    add_theme_page('PirateZ Manager', 'PirateZ Manager', 'edit_theme_options', 'piratez-manager-setup', 'piratez_manager_setup_page');
});
// Redirect the retired page before WordPress checks registered admin pages.
add_action('admin_menu', function () {
    if ( ($GLOBALS['pagenow'] ?? '') === 'themes.php' && ($_GET['page'] ?? '') === 'piratez-components' && current_user_can('edit_theme_options')) {
        $target = defined('PIRATEZ_MANAGER_VERSION') ? (is_multisite() ? network_admin_url('admin.php') : admin_url('admin.php')) . '?page=piratez-manager' : piratez_manager_setup_url();
        wp_safe_redirect($target); exit;
    }
}, 20);

function piratez_manager_setup_page() {
    if (!current_user_can('edit_theme_options')) { return; }
    $state = piratez_theme_manager_state();
    echo '<div class="wrap"><h1>PirateZ Manager</h1><p>Install and activate PirateZ Manager to manage your PirateZ modules. The theme and active plugins keep their independent WordPress updaters.</p>';
    $notice = get_transient('piratez_manager_setup_notice_' . get_current_user_id());
    if ($notice) {
        delete_transient('piratez_manager_setup_notice_' . get_current_user_id());
        echo '<div class="notice notice-' . ($notice['ok'] ? 'success' : 'error') . '"><p>' . esc_html($notice['message']) . '</p></div>';
    }
    if ($state['active']) {
        $url = (is_multisite() ? network_admin_url('admin.php') : admin_url('admin.php')) . '?page=piratez-manager';
        echo '<a class="button button-primary" href="' . esc_url($url) . '">Open PirateZ Manager</a>';
    } elseif ($state['collision']) {
        echo '<p>A different component occupies the piratez-manager folder. Resolve the conflict before installing Manager.</p>';
    } else {
        $operation = $state['installed'] ? 'activate' : 'install';
        $allowed = $state['installed'] ? current_user_can('activate_plugin', $state['config']['key']) : (current_user_can('install_plugins') && wp_is_file_mod_allowed('piratez_manager_setup'));
        if ($allowed) {
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="piratez_manager_setup"><input type="hidden" name="operation" value="' . esc_attr($operation) . '">';
            wp_nonce_field('piratez_manager_setup_' . $operation);
            submit_button($state['installed'] ? 'Activate PirateZ Manager' : 'Install PirateZ Manager');
            echo '</form>';
            if (is_multisite()) { echo '<p>Manager activation from this screen is network-wide.</p>'; }
        } else {
            echo '<p>Your account or installation policy does not permit this operation. Ask your administrator to install or activate Manager.</p>';
        }
    }
    echo '<p><a href="https://github.com/spaniakos/piratez-manager/releases/latest" target="_blank" rel="noopener noreferrer">PirateZ Manager releases</a></p></div>';
}

function piratez_manager_setup_result($ok, $message) {
    set_transient('piratez_manager_setup_notice_' . get_current_user_id(), array('ok' => $ok, 'message' => $message), MINUTE_IN_SECONDS);
    wp_safe_redirect(piratez_manager_setup_url()); exit;
}

function piratez_manager_setup_action() {
    if (!current_user_can('edit_theme_options')) { wp_die('Permission denied.', '', array('response' => 403)); }
    $operation = isset($_POST['operation']) && is_string($_POST['operation']) ? sanitize_key(wp_unslash($_POST['operation'])) : '';
    if (!in_array($operation, array('install', 'activate'), true)) { wp_die('Unknown operation.', '', array('response' => 400)); }
    check_admin_referer('piratez_manager_setup_' . $operation);
    $state = piratez_theme_manager_state();
    if ($operation === 'activate') {
        if (!$state['installed']) { wp_die('Manager is not installed. Refresh this page.', '', array('response' => 409)); }
        if (!current_user_can('activate_plugin', $state['config']['key'])) { wp_die('Permission denied.', '', array('response' => 403)); }
        $result = activate_plugin($state['config']['key'], '', is_multisite());
        piratez_manager_setup_result(!is_wp_error($result), is_wp_error($result) ? $result->get_error_message() : 'PirateZ Manager activated.');
    }
    if (!current_user_can('install_plugins') || !wp_is_file_mod_allowed('piratez_manager_setup')) { wp_die('Installation is not permitted.', '', array('response' => 403)); }
    if ($state['installed'] || $state['collision']) { wp_die('The installation folder is occupied. Refresh this page.', '', array('response' => 409)); }
    $client = new \PirateZTheme\GitHubUpdater($state['config']);
    $release = $client->release(true);
    if (is_wp_error($release)) { piratez_manager_setup_result(false, $release->get_error_message()); }
    if (!$client->compatible($release)) { piratez_manager_setup_result(false, 'Manager requires a newer WordPress or PHP version.'); }
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    $skin = new WP_Ajax_Upgrader_Skin();
    $upgrader = new Plugin_Upgrader($skin);
    $result = $upgrader->install($release['package']);
    $errors = $skin->get_errors();
    $ok = $result === true && !$errors->has_errors();
    piratez_manager_setup_result($ok, $ok ? 'PirateZ Manager installed. Activate it when ready.' : (is_wp_error($result) ? $result->get_error_message() : ($errors->has_errors() ? $errors->get_error_message() : 'Installation could not complete. Use Plugins > Add New > Upload Plugin if filesystem credentials are required.')));
}
add_action('admin_post_piratez_manager_setup', 'piratez_manager_setup_action');
