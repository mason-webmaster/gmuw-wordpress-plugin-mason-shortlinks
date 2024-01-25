<?php

// Enqueue styles
add_action('wp_enqueue_scripts', 'gmuw_sl_styles');
function gmuw_sl_styles() { 
    wp_enqueue_style('gmuw_sl_calendar', plugin_dir_url( __DIR__ ).'css/default.css'); 
}
