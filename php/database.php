<?php

/**
 * Summary: php file which implements customizations related to the database
 */


function gmuw_sl_create_redirect_meta_table() {
    global $wpdb;

    //set table name with the correct prefix
    $table_name = $wpdb->prefix . 'gmuw_sl_redirectmeta';
    
    //get the character set and collation (usually utf8mb4)
    $charset_collate = $wpdb->get_charset_collate();

    //define the SQL structure
    //note: Use two spaces after PRIMARY KEY and specify column lengths for indexing.
    $sql = "CREATE TABLE $table_name (
        meta_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        redirect_id bigint(20) UNSIGNED NOT NULL DEFAULT '0',
        meta_key varchar(255) DEFAULT NULL,
        meta_value longtext DEFAULT NULL,
        PRIMARY KEY  (meta_id),
        KEY redirect_id (redirect_id),
        KEY meta_key (meta_key(191))
    ) $charset_collate;";

    //include the upgrade.php file to access dbDelta()
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    //execute the query
    dbDelta( $sql );
}
