<?php
/** Run: php tests/async-styles.php /path/to/wordpress-7.1.3 */
$root = isset($argv[1]) ? rtrim($argv[1], '/') : '';
if (!is_file($root . '/wp-includes/version.php')) { fwrite(STDERR, "Supply the WordPress source directory.\n"); exit(1); }
define('ABSPATH', $root . '/');
require $root . '/wp-includes/plugin.php';
function __($text) { return $text; }
function _doing_it_wrong($function, $message, $version) { throw new Exception($function . ': ' . $message); }
function wp_json_encode($data) { return json_encode($data); }
require $root . '/wp-includes/compat-utf8.php';
require $root . '/wp-includes/utf8.php';
require $root . '/wp-includes/kses.php';
require $root . '/wp-includes/class-wp-token-map.php';
require $root . '/wp-includes/html-api/html5-named-character-references.php';
foreach (array('span', 'attribute-token', 'text-replacement', 'doctype-info', 'decoder', 'tag-processor') as $name) {
    require $root . '/wp-includes/html-api/class-wp-html-' . $name . '.php';
}
require dirname(__DIR__) . '/inc/render-blocking.php';
function check($condition, $message) { if (!$condition) { throw new Exception($message); } }
foreach (array('all', 'screen', 'screen and (min-width: 600px)') as $media) {
    $tag = '<link id="block-css" rel="stylesheet" href="/block.css" integrity="sha256-example" crossorigin="anonymous" media="' . $media . '">';
    $out = piratez_async_stylesheet_tag($tag, 'wp-block-code', '/block.css', $media);
    $p = new WP_HTML_Tag_Processor($out); $p->next_tag();
    check($p->get_attribute('media') === 'print', 'Stylesheet must be asynchronous');
    check($p->get_attribute('integrity') === 'sha256-example', 'Integrity lost');
    check($p->get_attribute('crossorigin') === 'anonymous', 'Crossorigin lost');
    check(strpos($p->get_attribute('onload'), json_encode($media)) !== false, 'Original media lost');
    check(strpos($out, '<noscript>') !== false, 'No-JavaScript fallback missing');
    check(strpos($out, 'id="block-css"', strpos($out, '<noscript>')) === false, 'Duplicate fallback ID');
    check(piratez_async_stylesheet_tag($out, 'wp-block-code', '/block.css', $media) === $out, 'Conversion must be idempotent');
}
$print = '<link rel="stylesheet" href="/print.css" media="print">';
check(piratez_async_stylesheet_tag($print, 'print', '/print.css', 'print') === $print, 'Print styles changed');
$existing = '<link rel="stylesheet" href="/custom.css" onload="custom()">';
check(piratez_async_stylesheet_tag($existing, 'custom', '/custom.css', 'all') === $existing, 'Existing handler changed');
$disabled = '<link rel="stylesheet" href="/disabled.css" disabled>';
check(piratez_async_stylesheet_tag($disabled, 'disabled', '/disabled.css', 'all') === $disabled, 'Disabled stylesheet enabled');
$module = '<script type="module" src="/tabs.js"></script>';
check(piratez_async_stylesheet_tag($module, 'module', '/tabs.js', 'all') === $module, 'Module script changed');
if (isset($argv[2])) {
    file_put_contents($argv[2], piratez_async_stylesheet_tag('<link rel="stylesheet" href="http://piratez.test/async.css" media="screen and (min-width: 600px)">', 'test', 'http://piratez.test/async.css', 'screen and (min-width: 600px)'));
}
echo "PASS: async styles, original media/attributes, fallback, idempotency, print, existing handlers, disabled styles, modules.\n";
