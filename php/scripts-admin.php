<?php

/**
 * Enqueue custom admin javascript
 */
add_action('admin_enqueue_scripts', function(){

  // Enqueue the custom admin javascript
  wp_enqueue_script(
    'gmuw_sl_custom_admin_js', //script name
    plugin_dir_url( __DIR__ ).'js/custom-mason-shortlinks-admin.js', //path to script
    array('jquery') //dependencies
  );

  // Enqueue datatables javascript
  wp_enqueue_script(
    'gmuw_pf_script_admin_datatables', //script name
    plugin_dir_url( __DIR__ ).'datatables/datatables.min.js' //path to script
  );

});
