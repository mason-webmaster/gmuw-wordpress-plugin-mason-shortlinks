<?php

/**
 * Summary: php file which implements the user-related customizations
 */


/**
 * add custom user columns
 */
add_filter( 'manage_users_columns', function( $columns ) {

	//links to users redirects
    $columns['redirect_group_link'] = 'User Redirects';
    
    //return updated columns
    return $columns;

});


/**
 * content for custom user columns
 */
add_filter( 'manage_users_custom_column', function( $output, $column_name, $user_id ) {

	//links to users redirects
    if ( $column_name === 'redirect_group_link' ) {
        $url = admin_url('tools.php?page=redirection.php&filterby%5Bgroup%5D='.gmuw_sl_redirection_get_user_redirection_group_id($user_id));
        return '<a href="' . esc_url( $url ) . '">View User Redirects</a>';
    }

	//return
    return $output;

}, 10, 3 );

/**
 * function to get user name
 */
function gmuw_sl_get_username($user_id) {

    //do we have a user id?
    if ($user_id>0) {
        return get_user_by('id', $user_id)->user_login;
    } else {
        return '-';
    }

}
