<?php
/** Independent theme updates, with optional Manager ownership of the catalogue. */
if (!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/github-updater.php';

function piratez_theme_updater() {
    static $client = null;
    if ($client === null) {
        $client = new \PirateZTheme\GitHubUpdater(array('name' => 'PirateZ Cyberpunk', 'type' => 'theme', 'slug' => 'piratez-cyberpunk', 'repo' => 'spaniakos/Piratez.gr-Theme', 'key' => get_template(), 'entry' => 'style.css'));
    }
    return $client;
}
piratez_theme_updater();

// Plugins load before themes. Deferring the fallback also covers activation,
// deactivation and rollback without persistent migration flags or lost settings.
add_action('after_setup_theme', function () {
    if (!defined('PIRATEZ_MANAGER_VERSION')) { require_once __DIR__ . '/modules-legacy.php'; }
}, 5);
