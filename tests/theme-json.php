<?php
/** Run: php tests/theme-json.php /path/to/wordpress-7.1.3 */
$root = isset($argv[1]) ? rtrim($argv[1], '/') : '';
if (!is_file($root . '/wp-includes/version.php')) { fwrite(STDERR, "Supply the WordPress source directory.\n"); exit(1); }
define('ABSPATH', $root . '/'); define('WPINC', 'wp-includes');
function __($text) { return $text; }
foreach (array('plugin.php', 'functions.php', 'kses.php', 'class-wp-block-type.php', 'class-wp-block-type-registry.php', 'class-wp-block-styles-registry.php', 'global-styles-and-settings.php', 'theme.php', 'blocks.php', 'class-wp-block-supports.php', 'block-supports/typography.php', 'class-wp-theme-json-schema.php', 'class-wp-theme-json.php') as $file) {
    require $root . '/wp-includes/' . $file;
}
$count = 0;
foreach (glob($root . '/wp-includes/blocks/*/block.json') as $file) {
    $metadata = json_decode(file_get_contents($file), true);
    WP_Block_Type_Registry::get_instance()->register($metadata['name'], array('supports' => $metadata['supports'] ?? array(), 'selectors' => $metadata['selectors'] ?? array(), 'styles' => $metadata['styles'] ?? array()));
    $count++;
}
$data = json_decode(file_get_contents(dirname(__DIR__) . '/theme.json'), true);
$theme = new WP_Theme_JSON($data, 'theme');
$sanitized = $theme->get_raw_data();
// Core resolves preset references into CSS custom properties.
$data['styles']['blocks']['core/code']['typography']['fontFamily'] = 'var(--wp--preset--font-family--monospace)';
$data['styles']['blocks']['core/code']['typography']['fontSize'] = 'var(--wp--preset--font-size--code)';
foreach (array('core/code', 'core/button') as $block) {
    if ($sanitized['styles']['blocks'][$block] !== $data['styles']['blocks'][$block]) { throw new Exception('Block styles removed during sanitization: ' . $block); }
}
if ($sanitized['settings']['layout'] !== $data['settings']['layout']) { throw new Exception('Layout settings changed'); }
if ($sanitized['settings']['color']['palette']['theme'] !== $data['settings']['color']['palette']) { throw new Exception('Palette changed'); }
if ($sanitized['settings']['typography']['fontFamilies']['theme'] !== $data['settings']['typography']['fontFamilies']) { throw new Exception('Font families changed'); }
echo "PASS: theme.json sanitized by supplied core with $count block definitions; layout, palette, fonts, and block styles retained.\n";
