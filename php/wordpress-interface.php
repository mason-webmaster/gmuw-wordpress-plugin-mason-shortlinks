<?php

/**
 * Summary: php file which implements the user-related customizations
 */

//Redirect users to the dashboard after login, except for administrators and subscribers (which are handled separately).
add_filter( 'login_redirect', 'gmuw_sl_custom_login_redirect', 12, 3 );
function gmuw_sl_custom_login_redirect( $redirect_to, $request, $user ) {

    if ( ! isset( $user->roles ) || ! is_array( $user->roles ) ) {
        return $redirect_to;
    }

    // Exclude the roles handled by other logic
    if ( in_array( 'administrator', $user->roles ) || in_array( 'subscriber', $user->roles ) ) {
        return $redirect_to;
    }

    // Force everyone else to the main Dashboard (index.php)
    return admin_url( 'index.php' );

}

//modify dashboard heading
add_action('admin_head', 'gmuw_sl_rename_dashboard_h1');
function gmuw_sl_rename_dashboard_h1() {

    //get global variables
    global $title;

    // Check if we are on the main dashboard page
    if (get_current_screen()->id === 'dashboard') {

        $title = 'go.gmu.edu';

    }

}

//modify dashboard help tab based on user roles
add_action('admin_head', 'gmuw_sl_remove_dashboard_help_tab');
function gmuw_sl_remove_dashboard_help_tab() {

    // Get the current screen object
    $screen = get_current_screen();

    //if this isn't the dashboard, bail out
    if ($screen->id != 'dashboard') {
        return;
    }

    //remove all existing default tabs (Overview, Navigation, etc.)
    $screen->remove_help_tabs();

}

//disable user/profile screens and hide them from the UI for non-admins
add_action( 'admin_init', 'gmuw_sl_restrict_user_screens' );
function gmuw_sl_restrict_user_screens() {

    //if the user is an admin or we are doing an AJAX request, do nothing
    if ( current_user_can( 'manage_options' ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        return;
    }

    //block access to the actual pages (users.php, profile.php, user-edit.php)
    global $pagenow;
    $restricted_pages = array( 'users.php', 'profile.php', 'user-new.php', 'user-edit.php' );

    if ( in_array( $pagenow, $restricted_pages ) ) {
        wp_die('You do not have permission to access this page.');
    }

    //remove the menu items from the sidebar
    remove_menu_page( 'users.php' );
    remove_menu_page( 'profile.php' );
}

//remove the user profile link from the Admin Top Bar
add_action( 'admin_bar_menu', 'gmuw_sl_remove_admin_bar_user_links', 999 );
function gmuw_sl_remove_admin_bar_user_links( $wp_admin_bar ) {

    if ( ! current_user_can( 'manage_options' ) ) {

        //remove "Edit Profile" (the link to profile.php)
        $wp_admin_bar->remove_node( 'edit-profile' );

        //remove "User Info" (the top part of the dropdown with the avatar/name)
        // Note: Often this is removed to prevent users from clicking their name to reach the profile.
        $wp_admin_bar->remove_node( 'user-info' );

    }
}

//customize WP admin bar
add_action('admin_bar_menu', 'gmuw_sl_customize_admin_bar', 9999);
function gmuw_sl_customize_admin_bar($wp_admin_bar) {

    //remove the WordPress logo
    $wp_admin_bar->remove_node('wp-logo');

}

//remove default dashboard widgets
add_action('wp_dashboard_setup', 'gmuw_sl_custom_clean_dashboard', 999);
function gmuw_sl_custom_clean_dashboard() {

    //remove default wordpress dashboard meta boxes

    // Remove 'At a Glance'
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    
    // Remove 'Activity' (Recent comments/posts)
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    
    // Remove 'Quick Draft'
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    
    // Remove 'WordPress Events and News'
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    
    // Remove 'Site Health Status'
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

    //remove wordpress dashboard meta boxes added by the Mason theme

    // Remove 'theme support'
    remove_meta_box('gmuj_custom_dashboard_meta_box_theme_support', 'dashboard', 'normal');

    // Remove 'mason recommended plugins'
    remove_meta_box('gmuj_custom_dashboard_meta_box_mason_recommended_plugins', 'dashboard', 'side');

    // Remove 'Mason resources'
    remove_meta_box('gmuj_custom_dashboard_meta_box_mason_resources', 'dashboard', 'side');

    // Remove 'Mason configuration messages'
    remove_meta_box('gmuj_custom_dashboard_meta_box_mason_configuration_messages', 'dashboard', 'side');


}

//add user roles to body classes for CSS targeting
add_filter('admin_body_class', 'gmuw_sl_add_user_role_to_body');
function gmuw_sl_add_user_role_to_body($classes) {

    $user = wp_get_current_user();

    if ($user->ID) {
        foreach ($user->roles as $role) {

            //append the role name with a prefix to avoid CSS conflicts
            $classes .= ' role-' . sanitize_html_class($role);

        }
    }

    return $classes;

}
