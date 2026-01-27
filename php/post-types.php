<?php

/**
 * Summary: php file which implements the custom post types
 */


/**
 * Register custom post types
 */
add_action('init', function(){

    // Register generic custom post type. Register additional custom post types here as needed.

    // Define labels for generic post type
    $labels = array(
        'name'                  => 'Things',
        'singular_name'         => 'Thing',
        'menu_name'             => 'Things',
        'name_admin_bar'        => 'Thing',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Thing',
        'new_item'              => 'New Thing',
        'edit_item'             => 'Edit Thing',
        'view_item'             => 'View Thing',
        'all_items'             => 'All Things',
        'search_items'          => 'Search Things',
        'parent_item_colon'     => 'Parent Thing:',
        'not_found'             => 'No Things found.',
        'not_found_in_trash'    => 'No Things found in Trash.',
        'featured_image'        => 'Thing Image',
        'set_featured_image'    => 'Set thing image',
        'remove_featured_image' => 'Remove thing image',
        'use_featured_image'    => 'Use as thing image',
        'archives'              => 'Things archives',
        'insert_into_item'      => 'Insert into thing',
        'uploaded_to_this_item' => 'Uploaded to this thing',
        'filter_items_list'     => 'Filter thing list',
        'items_list_navigation' => 'Thing list navigation',
        'items_list'            => 'Thing list',
    );
 
    // Set up arguments for the register_post_type function
    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'publicly_queryable'=> true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'thing'),
        'capability_type'   => 'post',
        'has_archive'       => true,
        'hierarchical'      => false,
        'menu_position'     => 20,
        'menu_icon'         => 'dashicons-admin-generic',
        'show_in_rest'      => true,
        'supports'          => array('title', 'editor', 'thumbnail'),
    );

    // Register generic custom post type
    register_post_type('thing', $args);

});
