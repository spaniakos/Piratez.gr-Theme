<?php
/** Theme presentation helpers. */
/**
 * Get accent intensity class
 *
 * @return string
 */
function piratez_get_accent_intensity_class() {
    $intensity = get_theme_mod('piratez_accent_intensity', 50);
    
    if ($intensity <= 30) {
        return 'accent-subtle';
    } elseif ($intensity >= 70) {
        return 'accent-bold';
    }
    
    return 'accent-balanced';
}
