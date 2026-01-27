<?php

/**
 * Enqueue custom admin CSS
 */
add_action('admin_enqueue_scripts', function(){

  // Enqueue admin styles. Enqueue additional css files here as needed.

  // Enqueue datatables stylesheet
  wp_enqueue_style (
    'gmuw_sl_style_admin_datatables',
    plugin_dir_url( __DIR__ ).'datatables/datatables.min.css'
  );

  // Enqueue the custom admin stylesheet
  wp_enqueue_style(
    'gmuw_sl_admin_custom_css', //stylesheet name
    plugin_dir_url( __DIR__ ).'css/custom-mason-shortlinks-admin.css' //path to stylesheet
  );

});
