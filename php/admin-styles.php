<?php

/**
 * Summary: php file which implements the admin interface styles
 */


/**
 * Enqueue admin styles
 */
add_action('admin_enqueue_scripts','gmuw_sl_enqueue_styles_admin');
function gmuw_sl_enqueue_styles_admin() {

  // Enqueue datatables stylesheet
  wp_enqueue_style (
    'gmuw_sl_style_admin_datatables', //stylesheet name
    plugin_dir_url( __DIR__ ).'datatables/datatables.min.css' //path to stylesheet
  );

  // Enqueue the plugin admin stylesheets
  wp_enqueue_style (
    'gmuw_sl_style_admin', //stylesheet name
    plugin_dir_url( __DIR__ ).'/css/admin.css' //path to stylesheet
  );

}