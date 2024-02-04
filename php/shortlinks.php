<?php

// functionality related to shortlinks


//function to get the post id of the most recent shortlink post with the given postmeta slug
function gmuj_sl_shortlink_post_id_by_slug($shortlink_slug) {

	//Declare global variables
	global $wpdb;

	//Set basic arguments for the get posts function
	$args = array(
		'post_type'  => 'shortlink',
		'post_status' => 'publish',
		'nopaging' => true,
		'order' => 'DESC',
		'orderby' => 'date',
		'numberposts' => 1,
		'meta_query' => array(
		  array(
		    'key'   => 'shortlink_slug',
		    'value' => $shortlink_slug,
		    'compare' => '=',
		  ),
		)
	);

	// Pull posts matching this slug
	$shortlinks = get_posts($args);

	foreach ($shortlinks as $shortlink) {
		$my_post_id = $shortlink->ID;
	}

	// Do we have a result?
	if (!empty($shortlinks)) {
	  return $my_post_id; 
	} else {
	  return false;
	}

}


//Add action to redirect if we determine that should use our custom event template
add_action( 'template_redirect', 'gmuj_sl_custom_event_template' );

function gmuj_sl_custom_event_template($template) {

    //First of all, let's see if this request is a 404 (according to WordPress)?
    if (is_404()) {

        //Now let's see if this URL represents a real event in the DB, and should therefore not be considered as a 404.
        //First we'll have to get the URL path to see if this represents a real event in the DB. So, get the potential url_slug from the URL.
        global $wp;
        $current_slug = add_query_arg( array(), $wp->request );

        //What is the post ID for the shortlink corresponding to this slug?
        $my_post_id = gmuj_sl_shortlink_post_id_by_slug($current_slug);

        //So did we have a shortlink with this slug?
        if ($my_post_id) {

        	//get redirect URL for this shortlink
			$my_redirect_url = get_post_meta($my_post_id, 'shortlink_target_url', true);

            //Process redirect
			wp_redirect( $my_redirect_url, 301 ); 
			exit;
        }

    }

    return $template;
}
