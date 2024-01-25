<?php

/**
 * Summary: php file which implements the theme initialization tasks
 */

function gmuw_sl_plugin_activate(){

  //activation tasks
  // Create custom tables
  gmuw_sl_create_custom_tables();
  // set roles and caps
  gmuw_sl_roles_and_caps();
}

function gmuw_sl_plugin_deactivate() {

  //deactivation tasks
  // clean up roles and caps
  gmuw_sl_roles_and_caps_cleanup();

}
