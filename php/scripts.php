<?php

/**
 * Summary: php file which implements the custom javascripts
 */


// 

/**
 * enqueue general javascript
 */
add_action('wp_enqueue_scripts', 'gmuw_sl_enqueue_general_scripts');
add_action('admin_enqueue_scripts', 'gmuw_sl_enqueue_general_scripts');
function gmuw_sl_enqueue_general_scripts(){

  // enqueue the general custom javascript
  wp_enqueue_script(
    'gmuw_sl_custom_js', //script name
    plugin_dir_url( __DIR__ ).'js/custom-mason-shortlinks.js', //path to script
    array('jquery') //dependencies
  );

  // enqueue the QR code script
  wp_enqueue_script(
    'qr-code-styling-js',
    plugin_dir_url( __DIR__ ).'js/lib/qr-code-styling.js', //path to script
    array(),
    null,
    true
  );

}

/**
 * enqueue public javascript
 */
require('scripts-public.php');

/**
 * enqueue admin javascript
 */
require('scripts-admin.php');
