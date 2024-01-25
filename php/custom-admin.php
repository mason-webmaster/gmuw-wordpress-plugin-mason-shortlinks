<?php

/**
 * custom php code related to the admin section
 */


/**
 * Allows posts to be searched by ID in the admin area.
 * 
 * @param WP_Query $query The WP_Query instance (passed by reference).
 */
add_action('pre_get_posts','gmuw_sl_admin_search_include_ids');
function gmuw_sl_admin_search_include_ids($query) {

    // Bail if we are not in the admin area
    if ( ! is_admin() ) {
        return;
    }

    // Bail if this is not the search query.
    if ( ! $query->is_main_query() && ! $query->is_search() ) {
        return;
    }   

    // Get the value that is being searched.
    $search_string = get_query_var( 's' );

    // Bail if the search string is not an integer.
    if ( ! filter_var( $search_string, FILTER_VALIDATE_INT ) ) {
        return;
    }

    // Set WP Query's p value to the searched post ID.
    $query->set( 'p', intval( $search_string ) );

    // Reset the search value to prevent standard search from being used.
    $query->set( 's', '' );

}

/**
 * Get admin list post modified column cells
 *
 */
function gmuw_sl_get_admin_list_post_modified_cells_content($post_id) {

    // initialize return value
    $return_value='';

    $return_value .= '<p class="mod-date">';
    $return_value .= '<em>'.get_the_modified_date('', $post_id).' '.get_the_modified_time('', $post_id).'</em><br />';
    if ( !empty( get_the_modified_author($post_id) ) ) {
        $return_value .= '<small>by <strong>'.get_the_modified_author($post_id).'<strong></small>';
    } else {
        $return_value .= get_userdata(get_post_field( 'post_author', $post_id ))->user_login;
    }
    $return_value .= '</p>';

    // return value
    return $return_value;

}
