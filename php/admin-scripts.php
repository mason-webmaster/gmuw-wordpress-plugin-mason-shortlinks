<?php

/**
 * Summary: php file which implements the admin interface scripts
 */


/**
 * Enqueue admin javascript
 */
add_action('admin_enqueue_scripts','gmuw_sl_enqueue_scripts_admin');
function gmuw_sl_enqueue_scripts_admin() {

  // Enqueue datatables javascript
  wp_enqueue_script(
    'gmuj_gmuw_sl_script_admin_datatables', //script name
    plugin_dir_url( __DIR__ ).'datatables/datatables.min.js' //path to script
  );

  // Enqueue QR code javascript
  wp_enqueue_script(
    'gmuj_gmuw_sl_script_qrcode', //script name
    plugin_dir_url( __DIR__ ).'js/qrcode.min.js', //path to script
    array('jquery') //dependencies
  );

  // Enqueue the plugin admin javascript
  wp_enqueue_script(
    'gmuj_gmuw_sl_script_admin', //script name
    plugin_dir_url( __DIR__ ).'js/admin.js' //path to script
  );

}