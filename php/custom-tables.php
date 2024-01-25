<?php

/**
 * Summary: php file which implements the custom tables
 */

function gmuw_sl_create_custom_tables(){

  // Create custom tables needed for the plugin
    // Shortlinks
      gmuw_sl_create_custom_table_shortlinks();

}

function gmuw_sl_create_custom_table_shortlinks(){

  // Get globals
    global $wpdb;

  // Include file that contains the dbDelta function
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

  // Set table name, using the database prefix
    $table_name = $wpdb->prefix . "gmuw_sl_shortlinks";

  // Write SQL statement to create table
    $sql = "CREATE TABLE $table_name (
     ID int(11) NOT NULL AUTO_INCREMENT,
     user_id int(11) NOT NULL,
     shortlink_slug varchar(255) NOT NULL,
     shortlink_target_url varchar(255) NOT NULL,
     when_created datetime NOT NULL,
     when_modified datetime NOT NULL,
     PRIMARY KEY  (ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

  // Execute SQL
    dbDelta($sql);

}
