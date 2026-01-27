<?php

/**
 * Summary: php file which implements customizations to the admin menu
 */


/**
 * Adds plugins settings admin menu item to Wordpress admin menu under settings section
 */
add_action('admin_menu', 'gmuw_sl_add_admin_menu_mason_shortlinks_settings');
function gmuw_sl_add_admin_menu_mason_shortlinks_settings() {

    // Add admin menu page
    add_submenu_page(
        'options-general.php',
        'Mason Shortlinks Settings',
        'Mason Shortlinks',
        'manage_options',
        'gmuw_sl',
        'gmuw_sl_plugin_page',
        0
    );

}

/**
 * Adds shortlinks management admin menu item to Wordpress admin menu
 */
add_action('admin_menu', 'gmuw_sl_add_admin_menu_mason_shortlinks');
function gmuw_sl_add_admin_menu_mason_shortlinks() {

    //add admin page
    add_menu_page(
        'Shortlink Management',   // Page title
        'Shortlinks',   // Menu label
        'create_shortlinks',   // Capability
        'gmuw_sl_shortlink_management',
        'gmuw_sl_shortlink_management_page',
        'dashicons-migrate', //icon
        3.1
    );

}

/**
 * Adds other custom items to the admin menu
 */
add_action( 'admin_menu', 'gmuw_sl_add_custom_menu_links' );
function gmuw_sl_add_custom_menu_links() {

    //redirection plugin redirects page
    add_menu_page(
        'Redirects',   // Page title (unused, but required)
        'Redirects',   // Menu label
        'manage_options',   // Capability
        admin_url( 'tools.php?page=redirection.php' ), // Slug — but here we pass the URL
        '', //callback (none)
        'dashicons-share-alt2', //icon
        3.2
    );

    //redirection plugin groups page
    add_menu_page(
        'Redirect Groups',   // Page title (unused, but required)
        'Redirect Groups',   // Menu label
        'manage_options',   // Capability
        admin_url( 'tools.php?page=redirection.php&sub=groups' ), // Slug — but here we pass the URL
        '', //callback (none)
        'dashicons-open-folder', //icon
        3.3
    );

    //redirection plugin logs page
    add_menu_page(
        'Redirect Log',   // Page title (unused, but required)
        'Redirect Log',   // Menu label
        'manage_options',   // Capability
        admin_url( 'tools.php?page=redirection.php&sub=log&groupby=url' ), // Slug — but here we pass the URL
        '', //callback (none)
        'dashicons-database', //icon
        3.4
    );


}

