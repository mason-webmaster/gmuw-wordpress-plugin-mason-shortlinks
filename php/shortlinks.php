<?php

/**
 * Summary: php file which implements customizations related to shortlinks
 */


//function to display dashboard meta box datatables redirects table
function gmuw_sl_shortlinks_table($redirects,$compact=false){

	//initialize return variable
	$return_value='';

	if ($redirects) {
		$return_value.='<table class="data_table">';

		//table header based on view type
		if (!$compact) {

			$return_value.='<thead>';
			$return_value.='<tr>';
			$return_value.='<td>Label</td>';
			$return_value.='<td>Target</td>';
			$return_value.='<td>User</td>';
			$return_value.='<td>Hits</td>';
			$return_value.='<td></td>';
			$return_value.='</tr>';
			$return_value.='</thead>';

		} else {

			$return_value.='<thead>';
			$return_value.='<tr>';
			$return_value.='<td>Shortlinks</td>';
			$return_value.='</tr>';
			$return_value.='</thead>';

		}


		$return_value.='<tbody>';
		foreach ($redirects as $redirect) {

			//get data
			$shortlink_url = home_url().$redirect->url;
			$shortlink_url_display = $compact ? mb_strimwidth(ltrim($redirect->url, '/'),0,35,'...') : ltrim($redirect->url, '/');
			$target_url = $redirect->action_data;
			$target_url_display = $compact ? mb_strimwidth(preg_replace('/^https?:\/\//', '', $redirect->action_data),0,50,'...') : $redirect->action_data;
			$redirect_user = gmuw_sl_get_username(gmuw_sl_redirect_user_id_by_redirect_id($redirect->id));
			$redirect_hits = $redirect->last_count;
			$view_url = '/wp-admin/admin.php?page=gmuw_sl_shortlink_management&redirect_id='.$redirect->id;
			$view_link = '<a class="admin-icon admin-view" title="edit" href="'.$view_url.'" target="_blank"></a> ';
			$edit_url = $view_url.'&mode=edit';
			$edit_link = '<a class="admin-icon admin-edit" title="edit" href="'.$edit_url.'" target="_blank"></a>';
			$copy_link = '<a class="admin-icon admin-copy" title="Copy Link" data-url="'. esc_url($shortlink_url) .'" href="javascript:void(0);"></a>';

			//start row
			$return_value.='<tr>';

			//set display
			if (!$compact) {

				//label
				$return_value.='<td>'.'<a style="font-weight:bold;" title="'.$shortlink_url.'" href="'.$view_url.'">'.$shortlink_url_display.'</a> '.$copy_link.'</td>';
				//target
				$return_value.='<td style="max-width:40em;">'.'<a title="'.$target_url.'" href="'.$target_url.'" target="_blank">'.$target_url_display.'</a>'.'</td>';
				//user
				$return_value.='<td>'.$redirect_user.'</td>';
				//hits
				$return_value.='<td>'.$redirect_hits.'</td>';
				//admin links
				$return_value.='<td>';
				//view link
				$return_value.=$view_link;
				//edit link, if the user can edit this redirect
				if (gmuw_sl_current_user_can_edit_shortlink($redirect->id)) {
					$return_value.=$edit_link;
				}
				$return_value.='</td>';

			} else {

				$return_value.='<td>';

				$return_value.='<div style="display:flex; justify-content:space-between;">';

				$return_value.='<div>';
				$return_value.='<a style="font-weight:bold;" title="'.$shortlink_url.'" href="'.$view_url.'">'.$shortlink_url_display.'</a>';
				$return_value.=' ';
				//copy link
				$return_value.=$copy_link;
				//edit link, if the user can edit this redirect
				if (gmuw_sl_current_user_can_edit_shortlink($redirect->id)) $return_value.=$edit_link;
				$return_value.='</div>';

				$return_value.='<div>';
				//user
				$return_value.='<span class="shortlink_user_display">'.$redirect_user . '&nbsp;</span>';
				//count
				$return_value.='<span class="highlight-metric">'.number_format($redirect_hits) . '</span>';
				$return_value.='</div>';

				$return_value.='</div>';

				$return_value.='<a style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, \'Liberation Mono\', \'Courier New\',  monospace; word-break:break-all;" title="'.$target_url.'" href="'.$target_url.'" target="_blank">'.$target_url_display.'</a>';

				$return_value.='</td>';

			}

            //end row
			$return_value.='</tr>';
		}
		$return_value.='</tbody>';
		$return_value.='</table>';
	}

	//return value
	return $return_value;

}

/**
 * Provides the add new shortlink form
 */
function gmuw_sl_shortlink_add_form() {

	//Output content
	?>

	<script>
		function gmuw_sl_validate_shortlink_add_form() {

			const form = this; // "this" refers to the form

			const label  = form.shortlink_label.value.trim();
			const target = form.shortlink_target.value.trim();

			const labelPattern = /^[a-z0-9_-]+$/;

			//ensure we have a label
			if (label === '') {
				alert('Shortlink label is required.');
				return false;
			}

			//ensure the label is valid
			if (!labelPattern.test(label)) {
				alert('Shortlink label may only contain lowercase letters, numbers, underscores, and hyphens.');
				return false;
			}

			//ensure the target is a valid URL
			try {
				new URL(target);
			} catch (e) {
				alert('Please enter a valid URL for the target.');
				return false;
			}

			//confirm
			return confirm('Do you want to add this shortlink?');
		}
	</script>

	<form method="post" action="" onsubmit="return gmuw_sl_validate_shortlink_add_form();">
		<?php wp_nonce_field( 'gmuw_sl_shortlink_add', 'gmuw_sl_shortlink_add_nonce' ); ?>

		<p>
			<label for="shortlink_label"><strong>Shortlink/Label</strong></label><br>
			<input type="text" name="shortlink_label" id="shortlink_label" style="width:100%;">
		</p>
		<p>
			<label for="shortlink_target"><strong>Target URL</strong></label><br>
			<input type="text" name="shortlink_target" id="shortlink_target" style="width:100%;">
		</p>

        <?php if (current_user_can('manage_options')) : ?>
            <p>
                <label for="redirect_group_id"><strong>User</strong></label><br>
                <select name="redirect_group_id" id="redirect_group_id">
                    <?php echo gmuw_render_user_group_options(gmuw_sl_redirection_get_user_redirection_group_id(get_current_user_id())); ?>
                </select>
            </p>
        <?php endif; ?>

        <?php if (gmuw_sl_get_user_dept_groups_array()) : ?>
            <p>
                <label for="shortlink_group_slug"><strong>Dept./Group</strong></label><br>
                <select name="shortlink_group_slug" id="shortlink_group_slug">
                    <?php echo gmuw_render_dept_group_options(); ?>
                </select>
            </p>
        <?php endif; ?>

		<p>
			<button type="submit" class="button button-primary">Submit</button>
		</p>

	</form>
	<?php

}

/**
 * Handle shortlink add form submission.
 */
add_action( 'admin_init', 'gmuw_sl_handle_form_shortlink_add' );
function gmuw_sl_handle_form_shortlink_add() {
    if (
        isset( $_POST['gmuw_sl_shortlink_add_nonce'] ) &&
        wp_verify_nonce( $_POST['gmuw_sl_shortlink_add_nonce'], 'gmuw_sl_shortlink_add' )
    ) {

		//is submitted shortlink data valid?
		if (!gmuw_sl_shortlink_data_is_valid($_POST['shortlink_label'],$_POST['shortlink_target'],'add')) {
			return;
		}

        //sanitize inputs
        //if we're an admin, get specified group, otherwise use the current users group
        $redirect_group_id = current_user_can('manage_options') ? sanitize_text_field($_POST['redirect_group_id']) : gmuw_sl_redirection_get_user_redirection_group_id();
        $shortlink_label = '/'.sanitize_text_field( $_POST['shortlink_label'] );
        $shortlink_target = sanitize_text_field( $_POST['shortlink_target'] );
        $shortlink_group_slug = sanitize_text_field( $_POST['shortlink_group_slug'] );

        //if user doesn't have permissions for this group, bail
        if (!in_array($shortlink_group_slug,gmuw_sl_get_user_dept_groups_array())) return; 

        //create the redirection recod
		global $wpdb;

		// Insert the new row
		$result = $wpdb->insert(
		    "{$wpdb->prefix}redirection_items",
		    [
		        'url' => $shortlink_label,
		        'match_url' => $shortlink_label,
		        'position' => gmuw_sl_redirection_next_redirect_position(),
		        'group_id' => $redirect_group_id,
		        'action_type' => 'url',
		        'action_code' => '301',
		        'action_data' => $shortlink_target,
		        'match_type' => 'url',
		    ],
		    [ '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s' ]
		);

		//get the newly-created redirect ID
		$new_redirect_id = $wpdb->insert_id;

		//add meta, if the insert was actually successful
		if ( $result && $new_redirect_id ) {

			update_redirect_meta( $new_redirect_id, 'when_created', current_time( 'mysql' ) );
			update_redirect_meta( $new_redirect_id, 'user_created', get_current_user_id() );
			update_redirect_meta( $new_redirect_id, 'when_last_edited', current_time( 'mysql' ) );
			update_redirect_meta( $new_redirect_id, 'user_last_edited', get_current_user_id() );
			update_redirect_meta( $new_redirect_id, 'gmuw_sl_group', $shortlink_group_slug );

		}

		//build output
		$output_text='Created shortlink: ' . esc_html( $shortlink_label ) .' -> '.esc_html( $shortlink_target ) . ' ('.get_user_by('id', gmuw_sl_redirect_user_id_by_group_id($redirect_group_id))->user_login.' / '.$shortlink_group_slug.')';

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
				'Shortlink created',
				$output_text,
			);

		}

        // admin notice
        add_action( 'admin_notices', function() use ( $shortlink_label, $shortlink_target, $output_text ) {
            echo '<div class="notice notice-success"><p>' . $output_text . '</p></div>';
        });

    }
}

/**
 * Handle shortlink edit form submission.
 */
add_action( 'admin_init', 'gmuw_sl_handle_form_shortlink_edit' );
function gmuw_sl_handle_form_shortlink_edit() {
    if (
        isset( $_POST['gmuw_sl_shortlink_edit_nonce'] ) &&
        wp_verify_nonce( $_POST['gmuw_sl_shortlink_edit_nonce'], 'gmuw_sl_shortlink_edit' )
    ) {

        //sanitize inputs
		$redirect_id=(int)$_REQUEST['redirect_id'];
        $redirect_group_id = sanitize_text_field( $_POST['redirect_group_id'] );
        $redirect_label = sanitize_text_field( $_POST['redirect_label'] );
        $redirect_target = esc_url_raw( $_POST['redirect_target'] );
        $shortlink_group_slug = sanitize_text_field( $_POST['shortlink_group_slug'] );

		//is submitted shortlink data valid?
		if (!gmuw_sl_shortlink_data_is_valid($redirect_label,$redirect_target,'edit',$redirect_id)) {
			return;
		}

		//is the user not allowed to edit this short link?
		if (!gmuw_sl_current_user_can_edit_shortlink($redirect_id)) {

            // admin notice
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>This shortlink does not below to you. Nothing done.</p></div>';
            });

            return false;

		}

		//is the redirect not being assigned to the current user, and is the current user not an admin, and does the user not have permissions for this group?
		if ( !(gmuw_sl_redirect_user_id_by_group_id($redirect_group_id)==get_current_user_id()) && !current_user_can('manage_options') && !(in_array($shortlink_group_slug,gmuw_sl_get_user_dept_groups_array()))  ) {

            // admin notice
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>You cannot assign a shortlink to this user. Nothing done.</p></div>';
            });

            return false;

		}

        //if the group is non-blank, and the user doesn't have permissions for this group, bail
        if (!empty($shortlink_group_slug) && !in_array($shortlink_group_slug,gmuw_sl_get_user_dept_groups_array())) return; 

		//looks good; we will proceed

		//store the old data for reporting
		$old_redirect_data=gmuw_sl_get_redirect_fields_by_id($redirect_id);
		$old_redirect_group_id=$old_redirect_data['group_id'];
		$old_redirect_label=ltrim($old_redirect_data['url'],'/');
		$old_redirect_target=$old_redirect_data['action_data'];
		$old_shortlink_group_slug=get_redirect_meta($redirect_id,'gmuw_sl_group');

        //edit the redirection record
		global $wpdb;

		// Prepare the data for the Redirection table structure
		// Ensure the label has a leading slash
		$formatted_label = '/' . ltrim($redirect_label, '/');

		$table_items = $wpdb->prefix . 'redirection_items';

		$update_data = array(
			'url'         => $formatted_label,
			'match_url'   => $formatted_label,
			'action_data' => $redirect_target,
			'group_id'    => (int) $redirect_group_id,
		);

		$update_where = array(
			'id' => $redirect_id
		);

		// Perform the update
		$updated = $wpdb->update(
			$table_items,
			$update_data,
			$update_where,
			array('%s', '%s', '%s', '%d'), // Data formats
			array('%d')                    // Where format
		);

		//check if the database query actually succeeded
		if ( false === $updated ) {
			add_action( 'admin_notices', function() {
				echo '<div class="notice notice-error"><p>Database error: Could not update the shortlink.</p></div>';
			});
			return;
		}

		//update redirect meta
		update_redirect_meta( $redirect_id, 'when_last_edited', current_time( 'mysql' ) );
		update_redirect_meta( $redirect_id, 'user_last_edited', get_current_user_id() );
		update_redirect_meta( $redirect_id, 'gmuw_sl_group', $shortlink_group_slug );

		//build output
		$output_text="Edited shortlink:\n";
		$output_text.="From: ".esc_html( $old_redirect_label ) ." -> ".esc_html( $old_redirect_target ) . " (".get_user_by('id', gmuw_sl_redirect_user_id_by_group_id($old_redirect_group_id))->user_login.($old_shortlink_group_slug ? " / ". $old_shortlink_group_slug : '').")\n";
		$output_text.="To: ".esc_html( $redirect_label ) ." -> ".esc_html( $redirect_target ) . " (".get_user_by('id', gmuw_sl_redirect_user_id_by_group_id($redirect_group_id))->user_login.($shortlink_group_slug ? " / ".$shortlink_group_slug : '').")";

		// log to simple history
		apply_filters(
			'simple_history_log',
			$output_text
		);

		//send email
		//are we set to send an email on shortlink creation?
		if (get_option('gmuw_sl_options')['gmuw_sl_email_notification_shortlink_edit']==1) {

			//send notification email
			wp_mail(
				gmuw_sl_get_notification_email_address_array(),
				'Shortlink edited',
				$output_text,
			);

		}

        // admin notice
        add_action( 'admin_notices', function() use ( $redirect_label, $redirect_target, $output_text ) {
            echo '<div class="notice notice-success"><p>' . nl2br($output_text) . '</p></div>';
        });

    }
}

/**
 * Handle shortlink deletion.
 */
add_action( 'admin_init', 'gmuw_sl_handle_delete_shortlink' );
function gmuw_sl_handle_delete_shortlink() {

    // Check if we are actually trying to delete
    if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'delete' || ! isset( $_GET['redirect_id'] ) ) {
        return;
    }

    $redirect_id = (int) $_GET['redirect_id'];

    //verify nonce
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'gmuw_sl_delete_shortlink_' . $redirect_id ) ) {
        wp_die( 'Security check failed.' );
    }

    //permission check
    if ( ! gmuw_sl_current_user_can_edit_shortlink( $redirect_id ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p>You do not have permission to delete this shortlink.</p></div>';
        });
        return;
    }

    //get info for logging before we delete it
    $redirect_data = gmuw_sl_get_redirect_fields_by_id( $redirect_id );
	$redirect_group_id=$redirect_data['group_id'];
	$redirect_label=ltrim($redirect_data['url'],'/');
	$redirect_target=$redirect_data['action_data'];

    //perform deletion
    global $wpdb;
    $table_items = $wpdb->prefix . 'redirection_items';
    $deleted = $wpdb->delete( $table_items, array( 'id' => $redirect_id ), array( '%d' ) );

    if ( $deleted ) {

		//build output
		$output_text="Deleted shortlink:\n";
		$output_text.=esc_html( $redirect_label ) ." -> ".esc_html( $redirect_target ) . " (".get_user_by('id', gmuw_sl_redirect_user_id_by_group_id($redirect_group_id))->user_login.")";

        // Log to Simple History
        apply_filters( 'simple_history_log', $output_text );

		//send email
		//are we set to send an email on shortlink creation?
		if (get_option('gmuw_sl_options')['gmuw_sl_email_notification_shortlink_delete']==1) {

			//send notification email
			wp_mail(
				gmuw_sl_get_notification_email_address_array(),
				'Shortlink deleted',
				$output_text,
			);

		}

        // Redirect back to the main list with a success message
        // We redirect because the current page (viewing the ID) no longer exists
        wp_safe_redirect( admin_url( 'admin.php?page=gmuw_sl_shortlink_management&displaymode=user&deleted=1' ) );
        exit;
    }
}

//function to return whether shortlink data is valid
function gmuw_sl_shortlink_data_is_valid($label,$target,$write_type,$redirection_id=null){

    //check for missing data for updates
    if ($write_type=='edit') {
        if ( empty($redirection_id)) {

            // admin notice
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>Missing redirection ID. Nothing done.</p></div>';
            });

            return false;

        }
    }

    //check for missing data
    if ( empty($label) || empty($target)) {

        // admin notice
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p>Missing input data. Nothing done.</p></div>';
        });

        return false;

    }

    //ensure the label is valid
    if (!preg_match("/^[a-z0-9_-]+$/", $label)) {

        // admin notice
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p>Shortlink label may only contain lowercase letters, numbers, underscores, and hyphens. Nothing done.</p></div>';
        });

        return false;

    }

    //ensure that the label is not already in use, and not by this exact redirect
    if (gmuw_sl_get_redirect_record_by_label($label) && gmuw_sl_get_redirect_record_by_label($label)->id != $redirection_id) {

        // admin notice
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p>Shortlink label is already in use. Nothing done.</p></div>';
        });

        return false;

    }

    //ensure the target is a valid URL
    if (filter_var($target, FILTER_VALIDATE_URL)==false) {

        // admin notice
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p>Please enter a valid URL for the target. Nothing done.</p></div>';
        });

        return false;

    }

    //ensure that the target uses an approved domain

    //get requested domain
    $requested_domain=wp_parse_url($target)['host'];

    //assume false
    $requested_domain_is_approved=false;

    //loop through all approved domains and check each one for a match
    foreach(gmuw_sl_approved_domains_array() as $approved_domain){

        //set pattern. there could be sub-domains
        $pattern = "/([a-z0-9-]+\.)*".$approved_domain."/i";

        //does the requested domain match the current approved domain from the list?
        if (preg_match($pattern, $requested_domain)){
            $requested_domain_is_approved=true;
        }

    }

    //if the requested domain is not approved...
    if (!$requested_domain_is_approved) {

        // admin notice
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p>You have specified an unapproved domain. Nothing done.</p></div>';
        });

        return false;

    }

    //ensure that the label is not a reserved label. check against reserved labels constant
    if (in_array($label, gmuw_sl_reserved_labels_array())) {

        // admin notice
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p>You have specified a reserved shortlink label. Nothing done.</p></div>';
        });

        return false;

    }

    //otherwise, we're good
    return true;

}

//function to return whether a user can edit a particular shortlink
function gmuw_sl_current_user_can_edit_shortlink($redirect_id){

    //return true is the user is an admin
	if (current_user_can('manage_options')) return true;

	//return true if the user id for this redirect matches the current user id
	if (gmuw_sl_redirect_user_id_by_redirect_id($redirect_id)==get_current_user_id()) return true; 

	//return true if the user has group permissions on the group this shortlink belongs to
	if (in_array(get_redirect_meta($redirect_id,'gmuw_sl_group'),gmuw_sl_get_user_dept_groups_array())) return true;

    //otherwise, return false
    return false;

}

/**
 * Return an array of approved domains from plugin settings
 */
function gmuw_sl_approved_domains_array() {

	// Get the full options array
	$options = get_option('gmuw_sl_options');

	// Return empty array if the key doesn't exist or is empty
	if ( empty($options['gmuw_sl_approved_domains']) ) {
		return array();
	}

	// Split by newline and filter out empty lines/whitespace
	return gmuw_sl_split_newline_string( $options['gmuw_sl_approved_domains'] );

}

/**
 * Return an array of reserved labels from plugin settings
 */
function gmuw_sl_reserved_labels_array() {

	// Get the full options array
	$options = get_option('gmuw_sl_options');

	// Return empty array if the key doesn't exist or is empty
	if ( empty($options['gmuw_sl_reserved_labels']) ) {
		return array();
	}

	// Split by newline and filter out empty lines/whitespace
	return gmuw_sl_split_newline_string( $options['gmuw_sl_reserved_labels'] );

}

/**
 * Helper to split a string by newline, trim whitespace, and remove empty values.
 */
function gmuw_sl_split_newline_string( $string ) {

    //Standardize line endings to \n
    $string = str_replace( array("\r\n", "\r"), "\n", $string );

    //Explode into an array
    $array = explode( "\n", $string );

    //Trim whitespace from each entry
    $array = array_map( 'trim', $array );

    //Remove any empty entries (e.g. accidental double newlines)
    return array_filter( $array );

}
