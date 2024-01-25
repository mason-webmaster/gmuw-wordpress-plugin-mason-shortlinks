<?php

/**
 * Main plugin file for the Mason WordPress: Shortlinks
 */

/**
 * Plugin Name:       Mason WordPress: Shortlinks
 * Author:            Jan Macario
 * Plugin URI:        https://github.com/mason-webmaster/gmuw-wordpress-plugin-mason-shortlinks
 * Description:       
 * Version:           0.9
 */


// Exit if this file is not called directly.
	if (!defined('WPINC')) {
		die;
	}

// Set up auto-updates
	require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/jmacario-gmu/gmuw-wordpress-plugin-mason-shortlinks/',
	__FILE__,
	'gmuw-wordpress-plugin-mason-shortlinks'
	);

// Branding
include('php/fnsBranding.php');

// post types
include('php/post-types.php');

// roles/permissions
include('php/roles-permissions.php');

// roles/permissions
include('php/custom-tables.php');

// Admin menu
include('php/admin-menu.php');

// Admin page
include('php/admin-page.php');

// Plugin settings
include('php/settings.php');

// Admin scripts
require('php/admin-scripts.php');

// Admin styles
require('php/admin-styles.php');

// Cron (WP cron)
require('php/cron.php');

// Scripts
require('php/scripts.php');

// Styles
require('php/styles.php');

// customization of the WordPress dashboard
include('php/custom-dashboard.php');

// customization of the WordPress admin section
include('php/custom-admin.php');

// Plugin activation/deactivation
require('php/activation-deactivation.php');

//Register activation hook
register_activation_hook(
	__FILE__,
	'gmuw_sl_plugin_activate'
);

//Register deactivation hook
register_deactivation_hook(
	__FILE__,
	'gmuw_sl_plugin_deactivate'
);


