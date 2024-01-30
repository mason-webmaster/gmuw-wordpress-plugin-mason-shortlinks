<?php

/**
 * Summary: php file which implements user-related functionality
 */


/**
 * Initially set up new user's dashboard meta boxes configuration when a new user is created
 */
add_action( 'user_register', 'gmuw_sl_user_add_customize_dashboard', 10, 1 );
function gmuw_sl_user_add_customize_dashboard( $user_id ) {

	//set default meta value representing which WP dashboard boxes are hidden
	$default_meta_value='a:6:{i:0;s:18:"dashboard_activity";i:1;s:44:"gmuj_custom_dashboard_meta_box_theme_support";i:2;s:17:"dashboard_primary";i:3;s:56:"gmuj_custom_dashboard_meta_box_mason_recommended_plugins";i:4;s:46:"gmuj_custom_dashboard_meta_box_mason_resources";i:5;s:59:"gmuj_custom_dashboard_meta_box_mason_configuration_messages";}';

	//convert the serialized value to an array (it will be automatically serialized later when it is inserted) 
	$default_meta_value_array=maybe_unserialize($default_meta_value);

    //update (add) user meta
    update_user_meta($user_id, 'metaboxhidden_dashboard', $default_meta_value_array);

}
