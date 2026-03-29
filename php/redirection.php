<?php

/**
 * Summary: php file which implements customizations related to the dashboard
 */


/**
 * When a new user is created, create a corresponding redirection group
 */
add_action( 'user_register', 'gmuw_sl_create_redirection_group_for_user' );
function gmuw_sl_create_redirection_group_for_user( $user_id ) {
    global $wpdb;

    // Insert the new row
    $wpdb->insert(
        "{$wpdb->prefix}redirection_groups",
        [
            'name'     => 'user_' . $user_id,
            'tracking'     => 1,
            'module_id'     => 1,
            'status'     => 'enabled',
            'position' => gmuw_sl_redirection_next_group_position(),
        ],
        [ '%s', '%d', '%d', '%s', '%d' ]
    );
}



/**
 * function to get the current user's redirection group ID
 */

function gmuw_sl_redirection_get_user_redirection_group_id($user_id = '') {
    if ( ! is_user_logged_in() ) {
        return null;
    }

    global $wpdb;

    if (empty($user_id)) { $user_id = get_current_user_id(); }

    $group_name = 'user_' . (int)$user_id;

    $table = "{$wpdb->prefix}redirection_groups";

    // Fetch the ID for this user's group
    $group_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM $table WHERE name = %s LIMIT 1",
            $group_name
        )
    );

    return $group_id ? (int) $group_id : null;
}




/**
 * function to return the current highest redirection groups position
 */
function gmuw_sl_redirection_highest_group_position(){

    global $wpdb;

    $max_position = (int) $wpdb->get_var(
        "SELECT MAX(position) FROM {$wpdb->prefix}redirection_groups"
    );

    return $max_position;

}

/**
 * function to return the next redirection groups position
 */
function gmuw_sl_redirection_next_group_position(){

    return gmuw_sl_redirection_highest_group_position()+1;

}

/**
 * function to return the current highest redirect position
 */
function gmuw_sl_redirection_highest_redirect_position(){

    global $wpdb;

    $max_position = (int) $wpdb->get_var(
        "SELECT MAX(position) FROM {$wpdb->prefix}redirection_items"
    );

    return $max_position;

}

/**
 * function to return the next redirect position
 */
function gmuw_sl_redirection_next_redirect_position(){

    return gmuw_sl_redirection_highest_redirect_position()+1;

}

/**
 * function to return all redirects
 */
function gmuw_sl_get_redirects() {
    global $wpdb;

    $table = "{$wpdb->prefix}redirection_items";

    // Fetch all rows for this group
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table ORDER BY position ASC"
        )
    );

    return $results; // returns an array of objects
}

/**
 * function to return current user's redirects
 */
function gmuw_sl_get_redirects_current_user() {
    global $wpdb;

    $table = "{$wpdb->prefix}redirection_items";

    // Fetch all rows for this group
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table WHERE group_id=".gmuw_sl_redirection_get_user_redirection_group_id()." ORDER BY position ASC"
        )
    );

    return $results; // returns an array of objects
}

/**
 * function to return redirects by group ID
 */
function gmuw_sl_get_redirects_by_group_id( $group_id ) {
    global $wpdb;

    // Ensure it's an integer
    $group_id = (int) $group_id;

    $table = "{$wpdb->prefix}redirection_items";

    // Fetch all rows for this group
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table WHERE group_id = %d ORDER BY position ASC",
            gmuw_sl_redirection_get_user_redirection_group_id()
        )
    );

    return $results; // returns an array of objects
}

/**
 * function to return redirect group name by group ID
 */
function gmuw_sl_redirects_get_group_name_by_id( $group_id ) {
    global $wpdb;

    // Ensure it's an integer
    $group_id = (int) $group_id;

    $table = "{$wpdb->prefix}redirection_groups";

    // Fetch the name for this group
    $name = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT name FROM $table WHERE id = %d LIMIT 1",
            $group_id
        )
    );

    return $name ? $name : null;
}


/**
 * function to return redirect data by ID
 */
function gmuw_sl_get_redirect_fields_by_id( $id ) {
    global $wpdb;

    $id = (int) $id;
    $table   = "{$wpdb->prefix}redirection_items";

    // Fetch only the fields you need
    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT url, action_data, group_id FROM $table WHERE id = %d LIMIT 1",
            $id
        ),
        ARRAY_A // return as associative array
    );

    return $row; // returns ['url' => '...', 'action_data' => '...'] or null
}



/**
 * function to see if given user owns a particular redirect
 */
function gmuw_sl_user_owns_redirect($redirect_id){

    //get redirect's group id
    $redirect_group_id=gmuw_sl_get_redirect_fields_by_id($redirect_id)['group_id'];
    //get group name
    $redirect_group_name=gmuw_sl_redirects_get_group_name_by_id($redirect_group_id);
    //get current user group name
    $user_group_name='user_'.get_current_user_id();

    //is the user group name the same as the redirect group name?
    return ($redirect_group_name==$user_group_name);

}

/**
 * function to get related user ID for a redirect by redirect id
 */
function gmuw_sl_redirect_user_id_by_redirect_id($redirect_id){

    //get redirect's group id
    $redirect_group_id=gmuw_sl_get_redirect_fields_by_id($redirect_id)['group_id'];

    //if group_id is not 0
    if ($redirect_group_id>0) {
        //get group name
        $redirect_group_name=gmuw_sl_redirects_get_group_name_by_id($redirect_group_id);
        //get related user id from group name
        $user_id=explode('_',$redirect_group_name)[1];
    } else {
        $user_id=0;
    }

    //is the user group name the same as the redirect group name?
    return $user_id;

}
