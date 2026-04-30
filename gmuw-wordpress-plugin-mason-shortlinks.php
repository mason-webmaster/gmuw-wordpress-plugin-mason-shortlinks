<?php

/**
 * Main plugin file for the Mason shortlinks system
 */

/**
 * Plugin Name:       Mason WordPress: Shortlinks 
 * Author:            Mason Web Administration
 * Plugin URI:        https://github.com/mason-webmaster/gmuw-wordpress-plugin-mason-shortlinks
 * Description:       Mason WordPress plugin which implements a Mason-branded shortlink management system (go.gmu.edu). Requires the Redirection plugin.
 * Version:           1.1
 */


// Exit if this file is not called directly.
if (!defined('WPINC')) {
	die;
}

// Set up auto-updates
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
'https://github.com/mason-webmaster/gmuw-wordpress-plugin-mason-shortlinks/',
__FILE__,
'gmuw-wordpress-plugin-mason-shortlinks'
);

// Load custom code modules. Comment lines here to turn on or off individual features

// styles
require('php/styles.php');

// scripts
require('php/scripts.php');

// post types
require('php/post-types.php');

// taxonomies
require('php/taxonomies.php');

// shortcodes
require('php/shortcodes.php');

// dashboard
require('php/dashboard.php');

// shortlinks
require('php/shortlinks.php');

// redirection
require('php/redirection.php');

//plugin activation
require('php/activate-deactivate.php');

//permissions
require('php/permissions.php');

//admin menu
require('php/admin-menu.php');

//admin page
require('php/admin-page.php');

//settings
require('php/settings.php');

//email
require('php/email.php');

//users
require('php/users.php');

//wordpress interface
require('php/wordpress-interface.php');

//database
require('php/database.php');

//cron
require('php/cron.php');

//simple history
require('php/simple-history.php');

//register activation hook
register_activation_hook(__FILE__, 'gmuw_sl_plugin_activate');

//register deactivation hook
register_deactivation_hook(__FILE__, 'gmuw_sl_plugin_deactivate');


/**
 * Redirect all 404 errors to the Mason homepage
 * A high priority (9999) will still allow the Redirection plugin 404 logging to work, whereas a lower priority (like the default priority) will supercede it.
 */
add_action( 'template_redirect', 'gmuw_sl_redirect_404s', 9999 );
function gmuw_sl_redirect_404s() {

    // Only proceed if this is a 404 error page
    if ( is_404() ) {

        wp_redirect( 'https://www.gmu.edu/', 301 );
        exit;

    }

}
