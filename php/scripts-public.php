<?php

/**
 * Enqueue custom public javascript
 */
add_action('wp_enqueue_scripts', function(){

  // Enqueue the public custom javascript
  wp_enqueue_script(
    'gmuw_sl_custom_public_js', //script name
    plugin_dir_url( __DIR__ ).'js/custom-mason-shortlinks-public.js', //path to script
    array('jquery') //dependencies
  );

});
