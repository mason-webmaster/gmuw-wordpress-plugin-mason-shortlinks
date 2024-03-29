<?php
/**
 * Summary: php file which handles the custom post type
 */


/**
 * Register a custom post type for shortlinks
 */
add_action('init', 'gmuw_sl_register_custom_post_type_shortlinks');
function gmuw_sl_register_custom_post_type_shortlinks() {

    $labels = array(
        'name'                  => 'Shortlinks',
        'singular_name'         => 'Shortlink',
        'menu_name'             => 'Shortlinks',
        'name_admin_bar'        => 'Shortlink',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Shortlink',
        'new_item'              => 'New Shortlink',
        'edit_item'             => 'Edit Shortlink',
        'view_item'             => 'View Shortlink',
        'all_items'             => 'All Shortlinks',
        'search_items'          => 'Search Shortlinks',
        'parent_item_colon'     => 'Parent Shortlinks:',
        'not_found'             => 'No Shortlinks found.',
        'not_found_in_trash'    => 'No Shortlinks found in Trash.',
        'featured_image'        => 'Shortlink Image',
        'set_featured_image'    => 'Set shortlink image',
        'remove_featured_image' => 'Remove shortlink image',
        'use_featured_image'    => 'Use as shortlink image',
        'archives'              => 'Shortlinks archives',
        'insert_into_item'      => 'Insert into shortlink',
        'uploaded_to_this_item' => 'Uploaded to this shortlink',
        'filter_items_list'     => 'Filter shortlinks list',
        'items_list_navigation' => 'Shortlinks list navigation',
        'items_list'            => 'Shortlinks list',
    );
 
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'shortlink'),
        'capability_type'    => array('shortlink','shortlinks'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => gmuw_sl_get_cpt_icon('shortlink'),
        'show_in_rest'       => true,
        'supports'           => array('title', 'editor'),
    );
 
    register_post_type('shortlink', $args);
}

// Add additional columns to post list
add_filter ('manage_shortlink_posts_columns', 'gmuw_sl_set_columns_shortlink');
function gmuw_sl_set_columns_shortlink ($columns) {

    // unset the 'date' column (so we can add it back at the end)
    $date = $columns['date'];
    unset( $columns['date'] );

    return array_merge(
        $columns,
        array(
            //postmeta fields
            'shortlink_slug' => 'Slug',
            'shortlink_url' => 'Shortlink URL',
            'shortlink_target_url' => 'Target URL',
            'shortlink_approved' => 'Approved?',
            'shortlink_qr_code' => 'QR Code',
            'date' => $date,
            'modified' => 'Modified Date',
        )
    );

}

// Generate field output for additional columns in the website post list
add_action ('manage_shortlink_posts_custom_column', 'gmuw_sl_columns_shortlink', 10, 2);
function gmuw_sl_columns_shortlink ($column, $post_id) {
	switch ($column) {
        case 'shortlink_slug':
            echo '<a href="/'.get_post_meta($post_id, $column, true).'/">';
            echo get_post_meta($post_id, $column, true);
            echo '</a>';
            break;
        case 'shortlink_url':
            echo '<a href="/'.get_post_meta($post_id, 'shortlink_slug', true).'/">';
            echo home_url( '/' ) . get_post_meta($post_id, 'shortlink_slug', true);
            echo '</a>';
            break;
        case 'shortlink_target_url':
            echo get_post_meta($post_id, $column, true);
            break;
        case 'shortlink_approved':
            echo get_post_meta($post_id, 'shortlink_approved', true)==1 ? '<span class="gmuw-status gmuw-status-approved">Yes</span>' : '<span class="gmuw-status gmuw-status-unapproved">No</span>';
            break;
        case 'shortlink_qr_code':
            echo '<div class="gmuw-sl-admin-list-qr-code">';
            echo '<input class="gmuw-sl-qr-code-value" type="hidden" value="'.get_site_url() . '/' . get_post_meta($post_id, 'shortlink_slug', true).'" />';
            echo '<div class="gmuw-sl-qr-code-output" style="width:100px; height:100px;"></div>';
            echo '<a class="gmuw-sl-qr-code-download" href="#" download="qrcode.png">Download</a>';
            echo '</div>';
            break;
        case 'modified':
            echo gmuw_sl_get_admin_list_post_modified_cells_content($post_id);
            break;
	}
}

// Set which fields are sortable
add_filter( 'manage_edit-shortlink_sortable_columns', 'gmuw_sl_sortable_shortlink_columns' );
function gmuw_sl_sortable_shortlink_columns($columns) {
    $columns['shortlink_slug'] = 'shortlink_slug';
    $columns['modified'] = 'modified';
    return $columns;
}

/**
 * Adds meta box to WordPress admin dashboard
 *
 */
add_action('wp_dashboard_setup', 'gmuw_sl_custom_dashboard_meta_box_shortlink');
function gmuw_sl_custom_dashboard_meta_box_shortlink() {

  // Declare global variables
  global $wp_meta_boxes;

  // does user have capability?
  if ( ! current_user_can('edit_shortlinks') ) { return; }

  /* Add 'shortlinks' meta box */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlinks", "Shortlinks", "gmuw_sl_custom_dashboard_meta_box_shortlinks", "dashboard","normal");

}

/**
 * Provides content for the dashboard meta box
 */
function gmuw_sl_custom_dashboard_meta_box_shortlinks() {

  //Initialize variables
  $cpt_slug='shortlink';
  $content='';

  //basic totals
  $content.='<p>'.gmuw_sl_get_cpt_totals($cpt_slug).'</p>';

  //Display meta box
  gmuw_sl_custom_dashboard_meta_box_cpt_summary($cpt_slug,$content);

}

/**
 * The approved postmeta field should be set programatically using the other submitted post meta fields, notably the target url
 * This function handles setting the approved postmeta field when the record is saved
  */
add_action( 'save_post', 'gmuw_sl_save_post_shortlink' );
function gmuw_sl_save_post_shortlink($post_id) {

    // If this is a revision, get real post ID
    if ( $parent_id = wp_is_post_revision( $post_id ) )
        $post_id = $parent_id;

    // Check if this post is the right type of post
    if (get_post_type($post_id)=='shortlink') {

        //if we're not an admin...
        if (!current_user_can('manage_options')) {

            // unhook this function so it doesn't loop infinitely
            remove_action( 'save_post', 'gmuw_sl_save_post_shortlink' );

            // find parent post_id
            if ( $post_parent_id = wp_get_post_parent_id( $post_id ) ) {
                $post_id = $post_parent_id;
            }

            //assume we are not approved
            $shortlink_approved=0;

            // get info needed to set approved post meta field
            // get shortlink target url
            $shortlink_target_url = get_post_meta($post_id, 'shortlink_target_url', true );

            // does the target url start with "https://(something).gmu.edu"?
            if (preg_match('/^https:\/\/[a-zA-Z0-9-.]+\.gmu\.edu/i',$shortlink_target_url)) {
                $shortlink_approved=1;
            }

            // update the approved postmeta field
            update_post_meta($post_id, 'shortlink_approved', $shortlink_approved);

            // re-hook this function
            add_action( 'save_post', 'gmuw_sl_save_post_shortlink' );
        }

    }

}
