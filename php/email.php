<?php

/**
 * Summary: php file which implements customizations related to email
 */


//get notification email addresses
function gmuw_sl_get_notification_email_address_array(){

	//get data from plugin option
	$email_array=explode(',',get_option('gmuw_sl_options')['gmuw_sl_email_notification_addresses']);

	//return value
	return $email_array;

}

//send notification email when a shortlink is created
add_action('redirection_redirect_updated', 'gmuw_sl_send_email_on_shortlink_create');
function gmuw_sl_send_email_on_shortlink_create($data){

	//are we set to send an email on shortlink creation?
	if (get_option('gmuw_sl_options')['gmuw_sl_email_notification_shortlink_create']==1) {

		//build email data
		$output_message='Shortlink created by '.wp_get_current_user()->user_login.' ('.get_current_user_id().'): '.gmuw_sl_get_redirect_fields_by_id($data)['url'].' -> '.gmuw_sl_get_redirect_fields_by_id($data)['action_data'];

		//send notification email
		wp_mail(
			gmuw_sl_get_notification_email_address_array(), //email addresses
			$output_message, //email subject
			$output_message, //email body
		);

	}

}

//send notification email when a shortlink is deleted
add_action('redirection_redirect_deleted', 'gmuw_sl_send_email_on_shortlink_delete');
function gmuw_sl_send_email_on_shortlink_delete($data){

	//are we set to send an email on shortlink deletion?
	if (get_option('gmuw_sl_options')['gmuw_sl_email_notification_shortlink_delete']==1) {

		//build email data
		$output_message='Shortlink deleted by '.wp_get_current_user()->user_login.' ('.get_current_user_id().'): '.$data->get_url().' -> '.$data->get_action_data();

		//send notification email
		wp_mail(
			gmuw_sl_get_notification_email_address_array(), //email addresses
			$output_message, //email subject
			$output_message, //email body
		);

	}

}
