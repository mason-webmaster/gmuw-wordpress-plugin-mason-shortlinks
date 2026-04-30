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
    $meta_table = "{$wpdb->prefix}gmuw_sl_redirectmeta";

    //set SQL based on mode
    switch($mode){
        case 'top':
            $my_sql="SELECT * FROM $table ORDER BY last_count DESC LIMIT 10;";
            break;
        case 'new':
            $my_sql="SELECT * FROM $table ORDER BY id DESC LIMIT 25;";
            break;
        case 'user':
            //using a subquery
            //$my_sql="SELECT * FROM $table WHERE id IN (SELECT redirect_id FROM $meta_table WHERE meta_key='gmuw_sl_shortlink_user_id' AND meta_value='".get_current_user_id()."') ORDER BY url ASC;";
            //using a join is generally more performant than a subquery
            $my_sql = $wpdb->prepare(
                "SELECT * FROM $table 
                 INNER JOIN $meta_table ON $table.id = $meta_table.redirect_id 
                 WHERE $meta_table.meta_key = 'gmuw_sl_shortlink_user_id' 
                 AND $meta_table.meta_value = %d 
                 ORDER BY $table.url ASC;",
                get_current_user_id()
            );
            break;
        case 'user_groups':
            //get user groups
            $groups = gmuw_sl_get_user_groups_array();

            //if user has no groups, return empty array
            if (empty($groups)) {
                return array();
            }

            //prepare the IN clause placeholders
            //creates a string like "%s, %s, %s" based on the number of groups
            $placeholders = implode(',', array_fill(0, count($groups), '%s'));

            $my_sql = $wpdb->prepare(
                "SELECT DISTINCT $table.* FROM $table
                 INNER JOIN $meta_table ON $table.id = $meta_table.redirect_id
                 WHERE $meta_table.meta_key = 'gmuw_sl_group'
                 AND $meta_table.meta_value IN ($placeholders)
                 ORDER BY $table.url ASC;",
                $groups
            );
            break;
        default:
            $my_sql="SELECT * FROM $table ORDER BY url ASC";
            break;
    }

    //fetch rows
    $results = $wpdb->get_results(
        $wpdb->prepare($my_sql)
    );

    //return
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

//function to handle the redirect export download
add_action('admin_init', 'gmuw_sl_handle_redirect_export_download');
function gmuw_sl_handle_redirect_export_download() {

    // Check if our custom trigger is in the URL
    if (isset($_GET['action']) && in_array($_GET['action'],array('download_redirect_export_wpe','download_redirect_export_apache'))) {

        //check if user has permissions
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to export this data.');
        }

        //set export file filename and file contents
        $myformat='';
        switch ($_GET['action']) {
            case 'download_redirect_export_wpe':
                $my_filename='go-gmu-edu-wpengine-rewrite-rules-' . date('Y-m-d') . '.txt';
                $my_file_contents=gmuw_sl_generate_redirect_export_file_contents('wpe');
                break;
            case 'download_redirect_export_apache':
                $my_filename='go-gmu-edu-htaccess-redirects-' . date('Y-m-d') . '.txt';
                $my_file_contents=gmuw_sl_generate_redirect_export_file_contents('apache');
                break;
        }

        //set headers to force download
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $my_filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        //output the data directly to the output stream
        echo $my_file_contents;

        //stop execution so no WordPress HTML is added to the file
        exit;

    }

}

//function to return the actual redirect export file content based on mode
function gmuw_sl_generate_redirect_export_file_contents($mode){

    //check if user has permissions
    if (!current_user_can('manage_options')) {
        return;
    }

    //initialize return value
    $return_value='';

    //get redirects from database
    global $wpdb;
    $table = "{$wpdb->prefix}redirection_items";
    $results = $wpdb->get_results("SELECT url, action_data FROM wp_redirection_items ORDER BY last_count DESC");

    //if we have redirects
    if ($results) {

        //add comments to .htaccess version
        if ($mode=='apache') {
            $return_value.="# Apache redirects export - generated " . date('Y-m-d H:i:s') . "\n\n";
        }

        //output the data directly to the output stream
        foreach ($results as $result) {

            //WPEngine format : Source [space] Destination
            if ($mode=='wpe') {
                $return_value.=trim($result->url) . ' ' . trim($result->action_data) . "\r\n";
            }

            //Apache format: Redirect 301 /source https://destination.com
            if ($mode=='apache') {
                $return_value.="Redirect 301 " . trim($result->url) . " " . trim($result->action_data) . "\r\n";
            }

        }

        //return
        return $return_value;

    }

}
