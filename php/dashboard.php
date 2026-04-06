<?php

/**
 * Summary: php file which implements customizations related to the dashboard
 */


/**
 * adds custom meta boxes to WordPress admin dashboard
 */
add_action('wp_dashboard_setup', 'gmuw_sl_custom_dashboard_meta_boxes');
function gmuw_sl_custom_dashboard_meta_boxes() {

  // Declare global variables
  global $wp_meta_boxes;

  /* Add general meta box */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_index", "Redirection!", "gmuw_sl_custom_dashboard_meta_box_general", "dashboard","normal");

  /* Add 'add new' meta box */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_redirects_add", "Add Redirect", "gmuw_sl_custom_dashboard_meta_box_redirects_add", "dashboard","normal");

   /* all shortlinks */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_redirects", "All Shortlinks", "gmuw_sl_custom_dashboard_meta_box_redirects", "dashboard","normal");

   /* your shortlinks */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_redirects_current_user", "Your Shortlinks", "gmuw_sl_custom_dashboard_meta_box_redirects_current_user", "dashboard","normal",);

}




/**
 * Provides content for the dashboard general meta box
 */
function gmuw_sl_custom_dashboard_meta_box_general() {

  //Output content
  echo '<p><a href="/wp-admin/tools.php?page=redirection.php">Redirects</a></p>';
  echo '<p><a href="/wp-admin/tools.php?page=redirection.php&sub=groups">Redirection Groups</a></p>';
  echo '<div id="qrcode-container"></div>';

}

/**
 * Provides content for the dashboard 'add new' redirect meta box
 */
function gmuw_sl_custom_dashboard_meta_box_redirects_add() {

	//Output content
	?>
	<script>
		function gmuw_sl_validate_shortlink_add_form() {

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

	<form method="post" action="" onsubmit="return gmuw_sl_validate_shortlink_add_form();">
		<?php wp_nonce_field( 'gmuw_sl_shortlink_add', 'gmuw_sl_shortlink_add_nonce' ); ?>

		<p>
			<label for="shortlink_label">Shortlink label:</label><br>
			<input type="text" name="shortlink_label" id="shortlink_label" style="width:100%;">
			<label for="shortlink_target">Target:</label><br>
			<input type="text" name="shortlink_target" id="shortlink_target" style="width:100%;">
		</p>

		<p>
			<button type="submit" class="button button-primary">Submit</button>
		</p>
	</form>
	<?php

}

/**
 * Handle form submission.
 */
add_action( 'admin_init', 'gmuw_sl_handle_dashboard_form' );

function gmuw_sl_handle_dashboard_form() {
    if (
        isset( $_POST['gmuw_sl_shortlink_add_nonce'] ) &&
        wp_verify_nonce( $_POST['gmuw_sl_shortlink_add_nonce'], 'gmuw_sl_shortlink_add' )
    ) {
        if ( ! empty( $_POST['shortlink_label'] ) && ! empty( $_POST['shortlink_target'] )) {
            $shortlink_label = '/'.sanitize_text_field( $_POST['shortlink_label'] );
            $shortlink_target = sanitize_text_field( $_POST['shortlink_target'] );

            //create the redirection recod
			global $wpdb;

			// Insert the new row
			$result = $wpdb->insert(
			    "{$wpdb->prefix}redirection_items",
			    [
			        'url' => $shortlink_label,
			        'match_url' => $shortlink_label,
			        'position' => gmuw_sl_redirection_next_redirect_position(),
			        'group_id' => gmuw_sl_redirection_get_user_redirection_group_id(),
			        'action_type' => 'url',
			        'action_code' => '301',
			        'action_data' => $shortlink_target,
			        'match_type' => 'url',
			    ],
			    [ '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s' ]
			);

			//build output
			$output_text='Created shortlink: ' . esc_html( $shortlink_label ) .' -> '.esc_html( $shortlink_target );

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
}

//display custom dashboard meta box with a table of shortlinks
function gmuw_sl_custom_dashboard_meta_box_redirects() {


	//get redirects
	$redirects = gmuw_sl_get_redirects();

	//put into table
	echo gmuw_sl_dashboard_widget_redirects_table($redirects,true);

}

//display custom dashboard meta box with a table of current user's shortlinks
function gmuw_sl_custom_dashboard_meta_box_redirects_current_user() {


	//get redirects
	$redirects = gmuw_sl_get_redirects_current_user();

	//put into table
	echo gmuw_sl_dashboard_widget_redirects_table($redirects,true);

}

//function to display dashboard meta box datatables redirects table
function gmuw_sl_dashboard_widget_redirects_table($redirects,$compact=false){

	//initialize return variable
	$return_value='';

	if ($redirects) {
		$return_value.='<table class="data_table dashboardwidget">';
		$return_value.='<thead>';
		$return_value.='<tr>';
		$return_value.='<td>Label</td>';
		$return_value.='<td>Target</td>';
		$return_value.='<td>User</td>';
		$return_value.='<td>Hits</td>';
		$return_value.='<td></td>';
		//$return_value.='<td>QR Code</td>';	
		$return_value.='</tr>';
		$return_value.='</thead>';
		$return_value.='<tbody>';
		foreach ($redirects as $redirect) {
			$return_value.='<tr>';
			//label
			$return_value.='<td>';
			$return_value.='<a title="'.home_url().$redirect->url.'" href="'.home_url().$redirect->url.'" target="_blank">';
			$return_value.= $compact ? mb_strimwidth($redirect->url,0,25,'...') : $redirect->url;
			$return_value.='</a>';
			$return_value.='</td>';
			//target
			$return_value.='<td>';
			$return_value.='<a title="'.$redirect->action_data.'" href="'.$redirect->action_data.'" target="_blank">';
			$return_value.= $compact ? mb_strimwidth($redirect->action_data,0,25,'...') : $redirect->action_data;
			$return_value.='</a>';
			$return_value.='</td>';
			//user
			$return_value.='<td>';
			//if we have a user, show their login name
			$return_value.=gmuw_sl_get_username(gmuw_sl_redirect_user_id_by_redirect_id($redirect->id));
			$return_value.='</td>';
			//hits
			$return_value.='<td>';
				$return_value.=$redirect->last_count;
			$return_value.='</td>';
			//admin links
			$return_value.='<td>';
			//view link
			$return_value.='<a class="admin-icon admin-view" title="edit" href="/wp-admin/admin.php?page=gmuw_sl_shortlink_management&redirect_id='.$redirect->id.'" target="_blank"></a> ';
			//edit link, if the user can edit this redirect
			if (gmuw_sl_user_owns_redirect($redirect->id) || current_user_can('manage_options') ) {
				$return_value.='<a class="admin-icon admin-edit" title="edit" href="/wp-admin/admin.php?page=gmuw_sl_shortlink_management&redirect_id='.$redirect->id.'&mode=edit" target="_blank"></a>';
			}
			$return_value.='</td>';
			/*
			//qr code
			$return_value.='<td>';
            $return_value.='<div class="gmuw-sl-admin-list-qr-code">';
            $return_value.='<input class="gmuw-sl-qr-code-value" type="hidden" value="'.$redirect->action_data.'" />';
            $return_value.='<div class="gmuw-sl-qr-code-output" style="width:100px; height:100px;"></div>';
            $return_value.='<a class="gmuw-sl-qr-code-download" href="#" download="qrcode.png">Download</a>';
            $return_value.='</div>';
            $return_value.='</td>';
            */
			$return_value.='</tr>';
		}
		$return_value.='</tbody>';
		$return_value.='</table>';
	}

	//return value
	return $return_value;

}
