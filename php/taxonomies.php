<?php

/**
 * Summary: php file which implements the custom taxonomies
 */


// Register taxonomies
add_action('init', function(){

	// Register taxonomies. Register additional taxonomies here as needed.

	// Register generic taxonomy
		register_taxonomy(
			'thing-group',
			'thing',
			array(
			'hierarchical' => true,
			'labels' => array(
				'name' => 'Thing Groups',
				'singular_name' => 'Thing Group',
				'search_items' =>  'Search Thing Groups',
				'all_items' => 'All Thing Groups',
				'parent_item' => 'Parent Thing Group',
				'parent_item_colon' => 'Parent Thing Group:',
				'edit_item' => 'Edit Thing Group',
				'update_item' => 'Update Thing Group',
				'add_new_item' => 'Add New Thing Group',
				'new_item_name' => 'New Thing Group',
				'menu_name' => 'Thing Groups',
				),
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'thing-group' ),
			)
		);

});
