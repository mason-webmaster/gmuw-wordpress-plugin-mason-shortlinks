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

    //handle form submission, if any
    if (
        isset( $_POST['gmuw_sl_shortlink_edit_nonce'] ) &&
        wp_verify_nonce( $_POST['gmuw_sl_shortlink_edit_nonce'], 'gmuw_sl_shortlink_edit' )
    ) {
        if ( ! empty( $_POST['shortlink_label'] ) && ! empty( $_POST['shortlink_target'] )) {
            $shortlink_label = '/'.sanitize_text_field( $_POST['shortlink_label'] );
            $shortlink_target = sanitize_text_field( $_POST['shortlink_target'] );

            //edit the redirection recod
            global $wpdb;

            ////

            //build output
            $output_text='Edit shortlink: ' . esc_html( $shortlink_label ) .' -> '.esc_html( $shortlink_target );

            // log to simple history
            apply_filters(
                'simple_history_log',
                $output_text
            );

            //send email
            //are we set to send an email on shortlink creation?
            if (get_option('gmuw_sl_options')['gmuw_sl_email_notification_shortlink_create']==1) {

                //send notification email
                wp_mail(
                    gmuw_sl_get_notification_email_address_array(),
                    'Shortlink edited',
                    $output_text,
                );

            }           

            // admin notice
            add_action( 'admin_notices', function() use ( $shortlink_label, $shortlink_target, $output_text ) {
                echo '<div class="notice notice-success"><p>' . $output_text . '</p></div>';
            });
        }
    }    

    //is this just a general listing? is no shortlink id specified?
    if (!isset($_GET['redirect_id']) || !$_GET['redirect_id']) {

        //return redirect table
        echo gmuw_sl_dashboard_widget_redirects_table(gmuw_sl_get_redirects());

        return;

    }

    // is this an edit?
    $is_edit=false;
    if (isset($_GET['mode']) && $_GET['mode']=='edit') $is_edit=true;

    // heading
    if (!$is_edit) echo '<h2>View Shortlink</h2>'; 
    if ($is_edit) echo '<h2>Edit Shortlink</h2>';

    // get basic shortlink info
    $redirect_id = (int) $_GET['redirect_id'];
    $shortlink_url=home_url().gmuw_sl_get_redirect_fields_by_id($redirect_id)['url'];
    $target_url=gmuw_sl_get_redirect_fields_by_id($redirect_id)['action_data'];

    echo '<p>';
    echo 'Redirect ID: '.$redirect_id.'<br />';
    echo 'User: '.get_user_by('id', gmuw_sl_redirect_user_id_by_redirect_id($redirect_id))->user_login.'<br />';
    echo 'Shortlink URL: '.$shortlink_url.'<br />';
    echo 'Target URL: '.$target_url.'<br />';
    echo '</p>';

    //viewing
    if (!$is_edit) {
        
        //shortlink display
        echo '<p class="shortlink_display"><a href="'.$shortlink_url.'" target="_blank">'.gmuw_sl_get_redirect_fields_by_id($redirect_id)['url'].'</a> -> <a href="'.$target_url.'" target="_blank">'.gmuw_sl_get_redirect_fields_by_id($redirect_id)['action_data'].'</a></span></p>';

        //qr code
        echo '<script src="https://cdn.jsdelivr.net/npm/qr-code-styling@1.6.0/lib/qr-code-styling.js"></script>';
        echo '<div class="gmuw-sl-admin-list-qr-code">';
        echo '<input class="gmuw-sl-qr-code-value" type="hidden" value="'.$shortlink_url.'" />';
        echo '<div class="gmuw-sl-qr-code-output"></div>';
        echo '<button class="gmuw-sl-qr-code-download">Download SVG</button>';
        echo '</div>';        

    }

    //editing
    if ($is_edit) {

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

                return confirm('Do you want to add this shortlink?');
            }
        </script>

        <form method="post" action="" onsubmit="return gmuw_sl_validate_shortlink_edit_form();">
            <?php wp_nonce_field( 'gmuw_sl_shortlink_edit', 'gmuw_sl_shortlink_edit_nonce' ); ?>

            <input type="hidden" name="action" value="edit" />

            <input type="hidden" name="redirect_id" value="<?php echo $redirect_id ?>" />

            <p>
                <label for="shortlink_label">Shortlink label:</label><br>
                <input type="text" name="shortlink_label" id="shortlink_label" value="<?php echo ltrim(gmuw_sl_get_redirect_fields_by_id($redirect_id)['url'], '/') ?>">
            </p>
            <p>
                <label for="shortlink_target">Target:</label><br>
                <input type="text" name="shortlink_target" id="shortlink_target" value="<?php echo gmuw_sl_get_redirect_fields_by_id($redirect_id)['action_data'] ?>">
            </p>

            <p>
                <button type="submit" class="button button-primary">Submit</button>
            </p>
        </form>
        <?php

    }

    // Finish HTML output
    echo "</div>";

}
