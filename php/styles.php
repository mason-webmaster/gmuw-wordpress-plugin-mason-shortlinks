<?php

/**
 * Summary: php file which implements the custom stylesheets
 */


// Load admin and public styles. Comment lines here to turn on or off individual features.

// Load admin styles
require('styles-admin.php');

// Load public styles
require('styles-public.php');


//ensure that stylesheets are always loaded by setting the stylesheet version to the current unix time
add_action('wp_default_styles', function($styles){

	$styles->default_version=time();

});
