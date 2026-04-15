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
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_index", "General Information", "gmuw_sl_custom_dashboard_meta_box_general", "dashboard","normal");

  /* Add 'add shortlink' meta box */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlink_add", "Add Shortlink", "gmuw_sl_custom_dashboard_meta_box_shortlink_add", "dashboard","normal");

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
	echo '<h3>Approved Affiliated Domains</h3>';
	?>
	<p>Using a Mason‑branded shortlink signals an official connection to the university. To protect the George Mason brand and ensure appropriate representation, self‑service creation of shortlinks is limited to a set of approved affiliated domains.</p>
	<p>Approved affiliated domains include external services that George Mason regularly uses for university business (such as all gmu.edu domains, Salesforce, Qualtrics, Zoom, and others).</p> 
	<p>If you need a short link for a domain that isn't pre-approved for self‑service creation, you can <a href="<?php echo TICKET_URL; ?>" target="_blank">submit a request ticket for review</a>.</p> 
	<p>Shortlinks can be created immediately for the following approved domains:</p>
	<?php
	foreach (APPROVED_DOMAINS as $approved_domain) {
		echo $approved_domain . '<br />';
	}

}

/**
 * Provides content for the dashboard add new shortlink meta box
 */
function gmuw_sl_custom_dashboard_meta_box_shortlink_add() {

	//Output content
	?>

	<p>Add a new shortlink:</p>

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
        $shortlink_label = sanitize_text_field( $_POST['shortlink_label'] );
        $shortlink_target = sanitize_text_field( $_POST['shortlink_target'] );

		//is submitted shortlink data valid?
		if (!gmuw_sl_shortlink_data_is_valid($shortlink_label,$shortlink_target,'edit',$redirect_id)) {
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

        //edit the redirection recod
		global $wpdb;

		////


		//build output
		$output_text='Edited shortlink: ' . esc_html( $shortlink_label ) .' -> '.esc_html( $shortlink_target );

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
        add_action( 'admin_notices', function() use ( $shortlink_label, $shortlink_target, $output_text ) {
            echo '<div class="notice notice-success"><p>' . $output_text . '</p></div>';
        });

    }
}

//display custom dashboard meta box with a table of shortlinks
function gmuw_sl_custom_dashboard_meta_box_redirects() {

	//link to full list
	echo '<p style="text-align:right;"><a href="/wp-admin/admin.php?page=gmuw_sl_shortlink_management">View full list</a></p>';

	//get redirects
	$redirects = gmuw_sl_get_redirects();

	//put into table
	echo gmuw_sl_dashboard_widget_redirects_table($redirects,true);

}

//display custom dashboard meta box with a table of current user's shortlinks
function gmuw_sl_custom_dashboard_meta_box_redirects_current_user() {

	//link to full list
	echo '<p style="text-align:right;"><a href="/wp-admin/admin.php?page=gmuw_sl_shortlink_management&displaymode=user">View full list</a></p>';

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
			$target_url_display = $compact ? mb_strimwidth($redirect->action_data,0,35,'...') : $redirect->action_data;
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
				$return_value.='<td>'.'<a style="font-weight:bold;" class="admin-tooltip-only" title="'.$shortlink_url.'" href="#">'.$shortlink_url_display.'</a> '.$copy_link.'</td>';
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

				$return_value.='<div><a style="font-weight:bold;" class="admin-tooltip-only" title="'.$shortlink_url.'" href="#">'.$shortlink_url_display.'</a> '.$copy_link.'</div>';
				$return_value.='<a title="'.$target_url.'" href="'.$target_url.'" target="_blank">'.$target_url_display.'</a>';

				$return_value.='</div>';

				$return_value.='<div style="display:flex; justify-content:flex-end;">';

				$return_value.='<span>'.$redirect_user . '&nbsp;</span>';
				$return_value.='<span class="highlight-metric">'.number_format($redirect_hits) . '</span>';

				$return_value.='</div>';

				$return_value.='<div style="display:flex; justify-content:flex-end;">';

				//view link
				$return_value.=$view_link;
				//edit link, if the user can edit this redirect
				if (gmuw_sl_current_user_can_edit_shortlink($redirect->id)) {
					$return_value.=$edit_link;
				}

				$return_value.='</div>';

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
