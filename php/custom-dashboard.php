<?php

/**
 * php file to handle WordPress dashboard customizations
 */


/**
 * Adds meta boxes to WordPress admin dashboard
 *
 */
add_action('wp_dashboard_setup', 'gmuw_sl_custom_dashboard_meta_boxes');
function gmuw_sl_custom_dashboard_meta_boxes() {

  // Declare global variables
  global $wp_meta_boxes;

  // does user have capability?
  if ( ! current_user_can('edit_shortlinks') ) { return; }

  /* Add meta boxes */
  //add_meta_box("gmuw_sl_custom_dashboard_meta_box_example", "Example Meta Box Title", "gmuw_sl_custom_dashboard_meta_box_example", "dashboard","normal");

}

/**
 * Provides format for the dashboard custom post type summary meta boxes
 */
function gmuw_sl_custom_dashboard_meta_box_cpt_summary($post_type,$content) {

  //Output content

  //start flex container
  echo '<div class="dash-meta-cpt-summary">';

  //icon
  echo '<a href="/wp-admin/edit.php?post_type='.$post_type.'"><div class="dashicons-before '.gmuw_sl_get_cpt_icon($post_type).'"></div></a>';

  //start cpt summary info
  echo '<div>';

  //output cpt summary data
  echo $content;

  //end cpt summary info
  echo '</div>';

  //end flex container
  echo '</div>';

}
