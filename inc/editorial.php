<?php
/** Theme placement adapter. Content engines live in PirateZ Editorial. */
if (!defined('ABSPATH')) { exit; }
function piratez_editorial_component($name) {
    if (class_exists('PirateZ\\Editorial\\Content')) { echo \PirateZ\Editorial\Content::component($name); }
}
function piratez_editorial_enabled($name) {
    return class_exists('PirateZ\\Editorial\\Settings') && \PirateZ\Editorial\Settings::enabled($name, get_the_ID());
}
add_action('admin_notices',function(){
    if (!current_user_can('activate_plugins') || class_exists('PirateZ\\Editorial\\Content')) return;
    echo '<div class="notice notice-warning"><p>PirateZ Editorial is needed for legacy PirateZ shortcodes and article tools. Install and activate the compatible release before using this theme update. <a href="'.esc_url(admin_url('admin.php?page=piratez-manager')).'">Open PirateZ</a></p></div>';
});

function piratez_page_variant() {
    if (!is_page()) return 'default';
    $slug=get_page_template_slug(get_queried_object_id());
    foreach (['narrow','wide','full-width','landing'] as $variant) if ($slug==='page-templates/'.$variant.'.php') return $variant;
    return 'default';
}
function piratez_page_hide($part) {
    return is_page() && get_post_meta(get_queried_object_id(),'_pz_hide_'.$part,true)==='on';
}
add_filter('body_class',function($classes){if(is_page()){$classes[]='pz-page-'.piratez_page_variant();if(piratez_page_variant()!=='default')$classes=array_values(array_diff($classes,['sidebar-enabled']));}return $classes;},20);
