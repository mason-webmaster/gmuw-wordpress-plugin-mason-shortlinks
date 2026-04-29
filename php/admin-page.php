<?php

/**
 * Summary: php file which implements custom admin pages
 */


/**
 * generates the plugin page
 */
function gmuw_sl_plugin_page(){

    // Only continue if this user has the 'manage options' capability
    if (!current_user_can('manage_options')) return;

    // Begin HTML output
    echo "<div class='wrap'>";

    // Page title
    echo "<h1>" . esc_html(get_admin_page_title()) . "</h1>";

    //display settings
    gmuw_sl_plugin_settings_form();

    // Finish HTML output
    echo "</div>";
    
}

/**
 * generates the shortlink management page
 */
function gmuw_sl_shortlink_management_page(){

    // Only continue if this user has the 'create_shortlinks' capability
    if (!current_user_can('create_shortlinks')) return;

    // Begin HTML output
    echo "<div class='wrap'>";

    //back to dashboard link
    ?>
    <p class="gmuw-sl-back-link">
        <a href="<?php echo esc_url( admin_url( 'index.php' ) ); ?>">
            <span class="dashicons dashicons-arrow-left-alt2" style="vertical-align: middle; font-size: 16px; text-decoration: none;"></span> 
            <?php esc_html_e( 'Back to Dashboard', 'gmuw-sl' ); ?>
        </a>
    </p>
    <?php

    // Page title
    echo "<h1>" . esc_html(get_admin_page_title()) . "</h1>";

    //shortlink deleted admin notice
    if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == 1 ) {
        echo '<div class="notice notice-success is-dismissible"><p>Shortlink successfully deleted.</p></div>';
    }

    //is this an export
    if (isset($_GET['displaymode']) && $_GET['displaymode']=='redirect_export'){

        //check if user has permissions
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to export this data.');
        }

        //heading
        echo '<h2>Export Redirects/Rewrite Rules</h2>';
        
        //display content
        echo gmuw_sl_redirects_export_content();
        
        //return
        return;

    }

    //is this just a general listing? is no shortlink id specified?
    if (!isset($_GET['redirect_id']) || !$_GET['redirect_id']) {

        //current user redirects or all redirects?
        if (isset($_GET['displaymode'])){

            switch ($_GET['displaymode']) {

                case 'user':
                    //heading
                    echo '<h2>Your Shortlinks</h2>';
                    //display links to other modes
                    echo '<p><a href="'.esc_url( remove_query_arg( 'displaymode' ) ).'">View all shortlinks</a><br /><a href="'.esc_url( add_query_arg( 'displaymode', 'user_groups' ) ).'">View only shortlinks in your group(s)</a><br />&nbsp;</p>';
                    //get redirects
                    $my_redirects=gmuw_sl_get_redirects('user');
                    break;
                case 'user_groups':
                    //heading
                    echo '<h2>Shortlinks in Your Group(s)</h2>';
                    //display links to other modes
                    echo '<p><a href="'.esc_url( remove_query_arg( 'displaymode' ) ).'">View all shortlinks</a><br /><a href="'.esc_url( add_query_arg( 'displaymode', 'user' ) ).'">View only your shortlinks</a><br />&nbsp;</p>';
                    //get redirects
                    $my_redirects=gmuw_sl_get_redirects('user_groups');
                    break;
            }

        } else {

            //heading
            echo '<h2>All Shortlinks</h2>';
            //display links to other modes
            echo '<p><a href="'.esc_url( add_query_arg( 'displaymode', 'user' ) ).'">View only your shortlinks</a><br /><a href="'.esc_url( add_query_arg( 'displaymode', 'user_groups' ) ).'">View only shortlinks in your group(s)</a><br />&nbsp;</p>';

            //get redirects
            $my_redirects=gmuw_sl_get_redirects();

        }

        //return redirect table
        echo gmuw_sl_shortlinks_table($my_redirects);

        return;

    }

    // store whether this is an edit request
    $is_edit = (isset($_GET['mode']) && $_GET['mode']=='edit') ? true : false;

    // get basic shortlink info
    $redirect_id = (int) $_GET['redirect_id'];
    $shortlink_label=ltrim(gmuw_sl_get_redirect_fields_by_id($redirect_id)['url'],'/');
    $shortlink_url=home_url().'/'.$shortlink_label;
    $shortlink_target_url=gmuw_sl_get_redirect_fields_by_id($redirect_id)['action_data'];
    $shortlink_hits=gmuw_sl_get_redirect_fields_by_id($redirect_id)['last_count'];
    $shortlink_notes=esc_html(get_redirect_meta($redirect_id,'gmuw_sl_shortlink_notes'));

    // heading
    if (!$is_edit) echo '<h2>Shortlink: '. $shortlink_label .' ('.$redirect_id.')</h2>';
    if ($is_edit) echo '<h2>Edit Shortlink: '. $shortlink_label .' ('.$redirect_id.')</h2>';

    //viewing
    if (!$is_edit) {

        //display shortlink summary
        echo '<p class="shortlink_display"><a href="'.$shortlink_url.'" target="_blank">'.$shortlink_url.'</a> -> <a href="'.$shortlink_target_url.'" target="_blank">'.gmuw_sl_get_redirect_fields_by_id($redirect_id)['action_data'].'</a></span></p>';

        //display shortlink data
        echo '<table class="shortlink_data">';
        echo '<tr><th>Label</th><td>'.$shortlink_label.'<td></tr>';
        echo '<tr><th>Target URL</th><td>'.$shortlink_target_url.'<td></tr>';
        echo '<tr><th>Group</th><td>'.get_redirect_meta($redirect_id,'gmuw_sl_group').'<td></tr>';
        echo '<tr><th>Hit Count</th><td><span class="highlight-metric">'.number_format($shortlink_hits) . '</span><td></tr>';
        echo '<tr><th>User</th><td>' . gmuw_sl_get_username(get_redirect_meta($redirect_id, 'gmuw_sl_shortlink_user_id')) . '<td></tr>';
        echo '<tr><th>Created</th><td>'.get_redirect_meta($redirect_id, 'when_created').' ('.gmuw_sl_get_username(get_redirect_meta($redirect_id, 'user_created')).')<td></tr>';
        echo '<tr><th>Last Edited</th><td>'.get_redirect_meta($redirect_id, 'when_last_edited').' ('.gmuw_sl_get_username(get_redirect_meta($redirect_id, 'user_last_edited')).')<td></tr>';
        if ($shortlink_notes) {
            echo '<tr><th>Notes<br /><span style="font-weight:normal;">(visible to all users)</span></th><td>'.$shortlink_notes.'<td></tr>';
        }
        echo '</table>';

        //display edit link, if user can edit
        if (gmuw_sl_current_user_can_edit_shortlink($redirect_id)) {
            echo '<a href="'. esc_url( add_query_arg( 'mode', 'edit' ) ) .'" class="button button-primary">Edit Shortlink</a>';
        }

        //qr code
        $filename = sanitize_title('go-gmu-edu-'.$shortlink_label) . '-qr-code';
        echo '<script src="https://cdn.jsdelivr.net/npm/qr-code-styling@1.6.0/lib/qr-code-styling.js"></script>';
        echo '<div class="gmuw-sl-admin-list-qr-code" data-filename="'.$filename.'">';
        echo '<input class="gmuw-sl-qr-code-value" type="hidden" value="'.$shortlink_url.'" />';
        echo '<div class="gmuw-sl-qr-code-output"></div>';
        echo '<div class="gmuw-sl-qr-code-controls">';
        echo '<button class="button button-primary gmuw-sl-qr-code-download-svg">Download SVG</button>';
        echo '<button class="button button-primary gmuw-sl-qr-code-download-png">Download PNG</button>';
        echo '<label class="gmuw-sl-qr-code-transparent-toggle-box"><input type="checkbox" class="gmuw-sl-qr-code-transparent-toggle"> Transparent Background</label>';
        echo '</div>';
        echo '</div>';        

    }

    //editing and user can edit
    if ($is_edit && gmuw_sl_current_user_can_edit_shortlink($redirect_id)) {

        //display edit form
        gmuw_sl_shortlink_edit_form($redirect_id);

        //display delete link
        //set up delete link
        $delete_url = wp_nonce_url(add_query_arg( 'action', 'delete' ), 'gmuw_sl_delete_shortlink_' . $redirect_id );

        //display delete link
        echo '<a style="margin-top:4em;" href="' . esc_url( $delete_url ) . '" class="button button-link-delete" onclick="return confirm(\'Are you sure you want to permanently delete this shortlink?\');">Delete Shortlink</a>';

    }

    //editing and user can't edit
    if ($is_edit && !gmuw_sl_current_user_can_edit_shortlink($redirect_id)) {

        echo '<div class="notice notice-error"><p>You are not allowed to edit this shortlink.</p></div>';

    }

    // Finish HTML output
    echo "</div>";

}

//function to generate data for the redirect export
function gmuw_sl_redirects_export_content() {

    ?>

    <p>Use the buttons below to download the full list of redirects in the appropriate format to either be <a href="https://wpengine.com/support/web-rules-engine/#Bulk_Import_Rewrite_Rules" target="_blank">bulk-imported into WPEngine's rewrite rules</a>, or to use as an Apache <code>.htaccess file</code>. </p>
    
    <p>Redirects are sorted in order of use, so redirects that have historically been used more frequently are at the top.</p>

    <div class="export-options" style="display: flex; gap: 20px; margin-top: 20px;">

        <div class="card" style="padding: 15px; border: 1px solid #ccd0d4; background: #fff; flex: 1;">
            <h3>WPEngine Bulk Import Format</h3>
            <p>Text file for <a href="https://wpengine.com/support/web-rules-engine/#Bulk_Import_Rewrite_Rules" target="_blank">bulk import of rewrite rules in the WPEngine User Portal</a>.</p>
            <a href="<?php echo esc_url(add_query_arg('action', 'download_redirect_export_wpe')); ?>" class="button button-primary">Download WPEngine Format</a>
        </div>

        <div class="card" style="padding: 15px; border: 1px solid #ccd0d4; background: #fff; flex: 1;">
            <h3>Apache .htaccess Format</h3>
            <p>Standard <code>Redirect 301</code> syntax for Apache servers. Rename to <code>.htaccess</code></p>
            <a href="<?php echo esc_url(add_query_arg('action', 'download_redirect_export_apache')); ?>" class="button button-primary">Download .htaccess Format</a>
        </div>

    </div>

    <?php

}
