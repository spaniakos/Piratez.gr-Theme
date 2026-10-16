<?php
/** Independent theme updates and an optional Manager install/activate launcher. */
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

require_once __DIR__ . '/manager-setup.php';
