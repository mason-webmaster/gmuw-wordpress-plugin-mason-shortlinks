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

    // Page title
    echo "<h1>" . esc_html(get_admin_page_title()) . "</h1>";

    //shortlink deleted admin notice
    if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == 1 ) {
        echo '<div class="notice notice-success is-dismissible"><p>Shortlink successfully deleted.</p></div>';
    }

    //is this just a general listing? is no shortlink id specified?
    if (!isset($_GET['redirect_id']) || !$_GET['redirect_id']) {


        //display listing based on whether we are showing all shortlinks or just the current user's shortlinks
        $admin_page_link_base='/wp-admin/admin.php?page=gmuw_sl_shortlink_management';

        //current user redirects or all redirects?
        if (isset($_GET['displaymode']) && $_GET['displaymode']=='user'){

            //display link back to shorlink list in other mode
            echo '<p>You are viewing only your shortlinks. <a href="'.$admin_page_link_base.'&displaymode=all">View all shortlinks</a><br />&nbsp;</p>';

            //get redirects
            $my_redirects=gmuw_sl_get_redirects_current_user();

        } else {

            //display link back to shorlink list in other mode
            echo '<p>You are viewing all shortlinks. <a href="'.$admin_page_link_base.'&displaymode=user">View only your shortlinks</a><br />&nbsp;</p>';

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

    // heading
    if (!$is_edit) echo '<h2>'. $shortlink_label .' ('.$redirect_id.')</h2>';
    if ($is_edit) echo '<h2>Edit Shortlink</h2>';

    //display shortlink data
    echo '<table class="shortlink_data">';
    echo '<tr><th>Shortlink Label</th><td>'.$shortlink_label.'<td></tr>';
    echo '<tr><th>Target URL</th><td>'.$shortlink_target_url.'<td></tr>';
    echo '<tr><th>Hit Count</th><td>'.number_format($shortlink_hits) . '<td></tr>';
    echo '<tr><th>User</th><td>' . gmuw_sl_get_username(gmuw_sl_redirect_user_id_by_redirect_id($redirect_id)) . '<td></tr>';
    echo '</table>';

    //viewing
    if (!$is_edit) {

        //display edit link
        echo '<a href="'. esc_url( add_query_arg( 'mode', 'edit' ) ) .'" class="button button-primary">Edit Shortlink</a>';
        
        //shortlink display
        echo '<p class="shortlink_display"><a href="'.$shortlink_url.'" target="_blank">'.$shortlink_url.'</a> -> <a href="'.$shortlink_target_url.'" target="_blank">'.gmuw_sl_get_redirect_fields_by_id($redirect_id)['action_data'].'</a></span></p>';

        //qr code
        echo '<script src="https://cdn.jsdelivr.net/npm/qr-code-styling@1.6.0/lib/qr-code-styling.js"></script>';
        echo '<div class="gmuw-sl-admin-list-qr-code">';
        echo '<input class="gmuw-sl-qr-code-value" type="hidden" value="'.$shortlink_url.'" />';
        echo '<div class="gmuw-sl-qr-code-output"></div>';
        echo '<button class="gmuw-sl-qr-code-download">Download SVG</button>';
        echo '</div>';        

    }

    //editing and user can edit
    if ($is_edit && gmuw_sl_current_user_can_edit_shortlink($redirect_id)) {

        ?>
        <script>
            function gmuw_sl_validate_shortlink_edit_form() {

                const form = this; // "this" refers to the form

                const label  = form.shortlink_label.value.trim();
                const target = form.shortlink_target.value.trim();

                const labelPattern = /^[a-z0-9_-]+$/;

                if (label === '') {
                    alert('Shortlink label is required.');
                    return false;
                }

                if (!labelPattern.test(label)) {
                    alert('Shortlink label may only contain lowercase letters, numbers, underscores, and hyphens.');
                    return false;
                }

                try {
                    new URL(target);
                } catch (e) {
                    alert('Please enter a valid URL for the target.');
                    return false;
                }

                return confirm('Do you want to edit this shortlink?');
            }
        </script>

        <form method="post" action="" onsubmit="return gmuw_sl_validate_shortlink_edit_form();">
            <?php wp_nonce_field( 'gmuw_sl_shortlink_edit', 'gmuw_sl_shortlink_edit_nonce' ); ?>

            <input type="hidden" name="action" value="edit" />

            <input type="hidden" name="redirect_id" value="<?php echo $redirect_id ?>" />

            <?php if (current_user_can('manage_options')) : ?>
                <p>
                    <label for="redirect_group_id">User:</label><br>
                    <select name="redirect_group_id" id="redirect_group_id">
                        <?php echo gmuw_render_group_options(gmuw_sl_get_redirect_fields_by_id($redirect_id)['group_id']); ?>
                    </select>
                </p>
            <?php else: ?>

                <input type="hidden" name="redirect_group_id" value="<?php echo gmuw_sl_get_redirect_fields_by_id($redirect_id)['group_id']; ?>" />

            <?php endif; ?>

            <p>
                <label for="redirect_label">Label:</label><br>
                <input type="text" name="redirect_label" id="redirect_label" value="<?php echo ltrim(gmuw_sl_get_redirect_fields_by_id($redirect_id)['url'], '/') ?>">
            </p>
            <p>
                <label for="redirect_target">Target/URL:</label><br>
                <input type="text" name="redirect_target" id="redirect_target" value="<?php echo gmuw_sl_get_redirect_fields_by_id($redirect_id)['action_data'] ?>">
            </p>

            <p>
                <button type="submit" class="button button-primary">Submit</button>
                <?php echo '<a href="'. esc_url( remove_query_arg( 'mode' ) ) .'" class="button">Cancel</a>'; ?>

            </p>
        </form>
        <?php

        //display delete link
        //set up delete link
        $delete_url = wp_nonce_url(add_query_arg( 'action', 'delete' ), 'gmuw_sl_delete_shortlink_' . $redirect_id );

        //display delete link
        echo '<a style="margin-top:4em;" href="' . esc_url( $delete_url ) . '" class="button button-link-delete" onclick="return confirm(\'Are you sure you want to permanently delete this shortlink?\');">Delete Shortlink</a>';

    }

    // Finish HTML output
    echo "</div>";

}
