<?php

/**
 * Summary: php file which implements customizations related to the dashboard
 */



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
function gmuw_sl_get_redirects($mode='') {
    global $wpdb;

    //set table
    $table = "{$wpdb->prefix}redirection_items";

    //set SQL based on mode
    switch($mode){
        case 'top':
            $my_sql="SELECT * FROM $table ORDER BY last_count DESC LIMIT 10;";
            break;
        case 'new':
            $my_sql="SELECT * FROM $table ORDER BY id DESC LIMIT 25;";
            break;
        case 'user':
            $my_sql="SELECT * FROM $table WHERE id IN (SELECT redirect_id FROM wp_gmuw_sl_redirectmeta WHERE meta_key='gmuw_sl_shortlink_user_id' AND meta_value='".get_current_user_id()."') ORDER BY url ASC;";
            break;
        default:
            $my_sql="SELECT * FROM $table ORDER BY url ASC";
            break;
    }

    //fetch rows
    $results = $wpdb->get_results(
        $wpdb->prepare($my_sql)
    );

    return $results;
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
            "SELECT url, action_data, group_id, last_count FROM $table WHERE id = %d LIMIT 1",
            $id
        ),
        ARRAY_A // return as associative array
    );

    return $row; // returns ['url' => '...', 'action_data' => '...'] or null
}

/**
 * function to get related user ID for a redirect by redirect id
 */
function gmuw_sl_redirect_user_id_by_redirect_id($redirect_id){

    //get user id from redirect meta
    return get_redirect_meta($redirect_id, 'gmuw_sl_shortlink_user_id');

}

/**
 * function to return redirect record by shortlink label
 */
function gmuw_sl_get_redirect_record_by_label( $shortlink_label ) {
    global $wpdb;

    $shortlink_label = sanitize_text_field($shortlink_label);
    $table   = "{$wpdb->prefix}redirection_items";

    //fetch the record
    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT id FROM $table WHERE url = %s",
            '/'.$shortlink_label
        )
    );

    return $row;
}

/**
 * Generate HTML options for the department groups.
 * @return string HTML option tags.
 */
function gmuw_render_user_group_options($current_user_group_slug='') {

    $my_user_groups=gmuw_sl_get_user_groups_array();

    if (empty($my_user_groups)) {
        return '<option value="">No groups found</option>';
    }

    $output = '<option value="">Select a group...</option>';

    foreach ($my_user_groups as $user_group) {

        //is this the current group?
        $selected='';
        if ($user_group==$current_user_group_slug) { $selected="selected"; }

        $output .= sprintf(
            '<option %s value="%s">%s</option>',
            $selected,
            esc_attr($user_group),
            esc_html($user_group)
        );
    }

    return $output;

}
