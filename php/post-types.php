<?

//Get the appropriate dashicon for the custom post type
function gmuw_sl_get_cpt_icon($post_type){

	// Initialize array
	$cpt_icons = array(
		'shortlink'=>'dashicons-admin-links',
	);

	//Return value
	return $cpt_icons[$post_type];

}

//Get the appropriate readable title for the custom post slug
function gmuw_sl_get_cpt_title($post_type){

	// Initialize array
	$cpt_titles = array(
		'shortlink'=>'Shortlink',
	);

	//Return value
	return $cpt_titles[$post_type];

}

// custom post type: shortlink
require('post-type-shortlink.php');

// custom post types analysis functions
require('post-types-analysis.php');

/**
 * Provides custom post type templates
 */
add_filter( 'single_template', 'gmuw_sl_custom_templates' );
function gmuw_sl_custom_templates($template) {
    global $post;

    switch($post->post_type) {
        case 'shortlink':
            return plugin_dir_path( __DIR__ ) . 'templates/single-'.$post->post_type.'.php';
            break;
    }

    return $template;
}

// Get record utility link
function gmuw_sl_record_get_utility_link($post_id,$link_type){

    // Initialize variables
    $return_value='';

    // Build link based on link_type
    switch($link_type) {
        case 'view';
            $return_value.='<a class="admin-icon admin-view" href="'.get_permalink($post_id).'"></a>';
            break;
        case 'edit':
            $return_value.=' <a class="admin-icon admin-edit" href="/wp-admin/post.php?post='.$post_id.'&action=edit"></a>';
            break;
    }

    // Return value
    return $return_value;

}
