<?php

/**
 * Summary: php file which implements the roles and permissions
 */


// function to return an array of all custom post type capabilities
function gmuw_sl_all_custom_post_type_capabilities() {

    // enumerate all custom post type capabilities
    $all_custom_post_type_capabilities = array(
        //shortlinks
        'edit_shortlinks',
        'edit_others_shortlinks',
        'delete_shortlinks',
        'publish_shortlinks',
        'read_private_shortlinks',
        'delete_private_shortlinks',
        'delete_published_shortlinks',
        'delete_others_shortlinks',
        'edit_private_shortlinks',
        'edit_published_shortlinks',
    );

    // return value
    return $all_custom_post_type_capabilities;

}

// function to return an array of all custom taxonomy capabilities
function gmuw_sl_all_custom_taxonomy_capabilities() {

    // enumerate all custom taxonomy capabilities
    $all_custom_taxonomy_capabilities = array(
      /*
      //example_taxonomy
      'manage_example_taxonomy',
      'edit_example_taxonomy',
      'delete_example_taxonomy',
      'assign_example_taxonomy',
      */
    );

    // return value
    return $all_custom_taxonomy_capabilities;

}

// function to return an array of all other custom capabilities
function gmuw_sl_all_other_custom_capabilities() {

    // enumerate all other custom capabilities
    $all_other_custom_capabilities = array(
      /*
      'other_custom_capability',
      */
    );

    // return value
    return $all_other_custom_capabilities;

}

// function to return an array of all custom capabilities
function gmuw_sl_all_custom_capabilities() {

    // enumerate all custom capabilities
    $all_custom_capabilities = array_merge(
        gmuw_sl_all_custom_post_type_capabilities(),
        gmuw_sl_all_custom_taxonomy_capabilities(),
        gmuw_sl_all_other_custom_capabilities(),
    );

    // return value
    return $all_custom_capabilities;

}

function gmuw_sl_roles_and_caps() {

  // shortlink_creator
  remove_role('shortlink_creator'); //in case we updated the caps below
  add_role('shortlink_creator','Shortlink Creator',array(
    'read' => true,
    'edit_shortlinks' => true,
    'edit_others_shortlinks' => true,
    'delete_shortlinks' => true,
    'publish_shortlinks' => true,
    'read_private_shortlinks' => true,
    'delete_private_shortlinks' => true,
    'delete_published_shortlinks' => true,
    'delete_others_shortlinks' => true,
    'edit_private_shortlinks' => true,
    'edit_published_shortlinks' => true,
    'assign_shortlink_example_taxonomy' => true,
  ));

  // administrator
  // provide all custom capabilities for admins
    $role = get_role( 'administrator' );

    foreach ( gmuw_sl_all_custom_capabilities() as $capability ) {
      $role->add_cap( $capability );
    }

}

function gmuw_sl_roles_and_caps_cleanup() {

    // shortlink_creator
    remove_role('shortlink_creator');

    // remove all custom capabilities for admins
    $role = get_role( 'administrator' );
    foreach ( gmuw_sl_all_custom_capabilities() as $capability ) {
        $role->remove_cap( $capability );
    }

}
