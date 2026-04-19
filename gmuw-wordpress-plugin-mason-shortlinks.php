<?php

/**
 * Main plugin file for the Mason shortlinks system
 */

/**
 * Plugin Name:       Mason WordPress: Shortlinks 
 * Author:            Mason Web Administration
 * Plugin URI:        https://github.com/mason-webmaster/gmuw-wordpress-plugin-mason-shortlinks
 * Description:       Mason WordPress Plugin to implement shortlink system
 * Version:           0.9
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

//define constants

//ticket url
define('TICKET_URL','https://gmu.teamdynamix.com/TDClient/33/Portal/Requests/ServiceOfferingDet?ID=97');

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

//database
require('php/database.php');

//register activation hook
register_activation_hook(__FILE__, 'gmuw_sl_plugin_activate');

//register deactivation hook
register_deactivation_hook(__FILE__, 'gmuw_sl_plugin_deactivate');
