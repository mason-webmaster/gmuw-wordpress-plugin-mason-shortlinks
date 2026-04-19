<?php

/**
 * Summary: php file which sets up plugin settings
 */


/**
 * Register plugin settings
 */
add_action('admin_init', 'gmuw_sl_register_settings');
function gmuw_sl_register_settings() {
	
	/*
	Code reference:

	register_setting( 
		string   $option_group, // name of option group - should match the parameter used in the settings_fields function in the display_settings_page function
		string   $option_name, // name of the particular option
		callable $sanitize_callback = '' // function used to validate settings
	);

	add_settings_section( 
		string   $id, // section id
		string   $title, // title/heading of section
		callable $callback, // function that displays section
		string   $page // admin page (slug) on which this section should be displayed
	);

	add_settings_field(
    	string   $id, // setting id
		string   $title, // title of setting
		callable $callback, // outputs markup required to display the setting
		string   $page, // page on which setting should be displayed, same as menu slug of the menu item
		string   $section = 'default', // section id in which this setting is placed
		array    $args = [] // array the contains data to be passed to the callback function. by convention I pass back the setting id and label to make things easier
	);
	*/

	// Register serialized options setting to store this plugin's options
	register_setting(
		'gmuw_sl_options',
		'gmuw_sl_options',
		'gmuw_sl_callback_validate_options'
	);

	// Add section: content settings
	add_settings_section(
		'gmuw_sl_section_settings_content',
		'Content Settings',
		'gmuw_sl_callback_section_settings_content',
		'gmuw_sl'
	);

	// Add field: approved domains
	add_settings_field(
		'gmuw_sl_approved_domains',
		'Approved Domains',
		'gmuw_sl_callback_field_textarea',
		'gmuw_sl',
		'gmuw_sl_section_settings_content',
		['id' => 'gmuw_sl_approved_domains', 'label' => 'one per line, please']
	);

	// Add field: reserved labels
	add_settings_field(
		'gmuw_sl_reserved_labels',
		'Reserved Labels',
		'gmuw_sl_callback_field_textarea',
		'gmuw_sl',
		'gmuw_sl_section_settings_content',
		['id' => 'gmuw_sl_reserved_labels', 'label' => 'one per line, please']
	);

	// Add field: associated service ticket url
	add_settings_field(
		'gmuw_sl_ticket_url',
		'Associated Service Ticket URL',
		'gmuw_sl_callback_field_text',
		'gmuw_sl',
		'gmuw_sl_section_settings_content',
		['id' => 'gmuw_sl_ticket_url', 'label' => 'URL for associated service ticket page']
	);

	// Add section: email settings
	add_settings_section(
		'gmuw_sl_section_settings_email',
		'Email Settings',
		'gmuw_sl_callback_section_settings_email',
		'gmuw_sl'
	);

	// Add field: email addresses for notifications
	add_settings_field(
		'gmuw_sl_email_notification_addresses',
		'Email address(es) for notification emails',
		'gmuw_sl_callback_field_text',
		'gmuw_sl',
		'gmuw_sl_section_settings_email',
		['id' => 'gmuw_sl_email_notification_addresses', 'label' => 'comma-separated, please']
	);

	// Add field: turn on notification emails for shortlink creation
	add_settings_field(
		'gmuw_sl_email_notification_shortlink_create',
		'Send email notification on shortlink creation?',
		'gmuw_sl_callback_field_yesno',
		'gmuw_sl',
		'gmuw_sl_section_settings_email',
		['id' => 'gmuw_sl_email_notification_shortlink_create', 'label' => '']
	);

	// Add field: turn on notification emails for shortlink editing
	add_settings_field(
		'gmuw_sl_email_notification_shortlink_edit',
		'Send email notification on shortlink edit?',
		'gmuw_sl_callback_field_yesno',
		'gmuw_sl',
		'gmuw_sl_section_settings_email',
		['id' => 'gmuw_sl_email_notification_shortlink_edit', 'label' => '']
	);

	// Add field: turn on notification emails for shortlink deletion
	add_settings_field(
		'gmuw_sl_email_notification_shortlink_delete',
		'Send email notification on shortlink deletion?',
		'gmuw_sl_callback_field_yesno',
		'gmuw_sl',
		'gmuw_sl_section_settings_email',
		['id' => 'gmuw_sl_email_notification_shortlink_delete', 'label' => '']
	);

} 

/**
 * Generates the plugin settings page
 */
function gmuw_sl_plugin_settings_form() {
    
    // Only continue if this user has the 'manage options' capability
    if (!current_user_can('manage_options')) return;

    // heading
    //echo "<h2>Plugin Settings</h2>";

    // Begin form
    echo "<form action='options.php' method='post'>";

    // output settings fields - outputs required security fields - parameter specifes name of settings group
    settings_fields('gmuw_sl_options');

    // output setting sections - parameter specifies name of menu slug
    do_settings_sections('gmuw_sl');

    // submit button
    submit_button();

    // Close form
    echo "</form>";

}

/**
 * Generates content for email settings section
 */
function gmuw_sl_callback_section_settings_email() {

    //echo '<p>Email settings.</p>';

}

/**
 * Generates content for content section
 */
function gmuw_sl_callback_section_settings_content() {

    //echo '<p>Content settings.</p>';

}

/**
 * Generates text field for plugin settings option
 */
function gmuw_sl_callback_field_text($args) {
    
    //Get array of options. If the specified option does not exist, get default options from a function
    $options = get_option('gmuw_sl_options', gmuw_sl_options_default());
    
    //Extract field id and label from arguments array
    $id    = isset($args['id'])    ? $args['id']    : '';
    $label = isset($args['label']) ? $args['label'] : '';
    
    //Get setting value
    $value = isset($options[$id]) ? sanitize_text_field($options[$id]) : '';
    
    //Output field markup
    echo '<input id="gmuw_sl_options_'. $id .'" name="gmuw_sl_options['. $id .']" type="text" size="40" value="'. $value .'">';
    echo "<br />";
    echo '<label for="gmuw_sl_options_'. $id .'">'. $label .'</label>';
    
}

/**
 * Generates textarea field for plugin settings option
 */
function gmuw_sl_callback_field_textarea($args) {

    //Get array of options. If the specified option does not exist, get default options from a function
    $options = get_option('gmuw_sl_options', gmuw_sl_options_default());

    //Extract field id and label from arguments array
    $id    = isset($args['id'])    ? $args['id']    : '';
    $label = isset($args['label']) ? $args['label'] : '';

    //Get setting value
    $value = isset($options[$id]) ? sanitize_textarea_field($options[$id]) : '';

    //Output field markup
    echo '<textarea id="gmuw_sl_options_'. $id .'" name="gmuw_sl_options['. $id .']" type="text" style="width:60em; height:10em;">'. $value .'</textarea>';
    echo "<br />";
    echo '<label for="gmuw_sl_options_'. $id .'">'. $label .'</label>';

}

/**
 * Generates yes/no field for plugin settings options
 */
function gmuw_sl_callback_field_yesno($args) {

    //Get array of options. If the specified option does not exist, get default options from a function
    $options = get_option('gmuw_sl_options', gmuw_sl_options_default());

    //Extract field id and label from arguments array
    $id    = isset($args['id'])    ? $args['id']    : '';
    $label = isset($args['label']) ? $args['label'] : '';

    //Get setting value
    $value = isset($options[$id]) ? sanitize_text_field($options[$id]) : '';

    //Output field markup
    echo '<select id="gmuw_sl_options_'. $id .'" name="gmuw_sl_options['. $id .']">';
    echo '<option ';
    echo $value ? 'selected ' : '';
    echo 'value="1">Yes</option>';
    echo '<option ';
    echo !$value ? 'selected ' : '';
    echo 'value="0">No</option>';
    echo '</select>';
    echo "<br />";
    echo '<label for="gmuw_sl_options_'. $id .'">'. $label .'</label>';

}

/**
 * Sets default plugin options
 */
function gmuw_sl_options_default() {

    return array(
        'gmuw_sl_email_notification_shortlink_create' => '1',
        'gmuw_sl_email_notification_shortlink_delete' => '1',
    );

}

/**
 * Validate plugin options
 */
function gmuw_sl_callback_validate_options($input) {

    // gmuw_sl_ticket_url
    if (isset($input['gmuw_sl_ticket_url'])) {

	    //ensure the target is a valid URL
	    if (filter_var($input['gmuw_sl_ticket_url'], FILTER_VALIDATE_URL)==false) {

			//throw it out
			$input['gmuw_sl_ticket_url']='';

	    }

    }

    // gmuw_sl_email_notification_shortlink_create
    if (isset($input['gmuw_sl_email_notification_shortlink_create'])) {

		//sanitize input
        $input['gmuw_sl_email_notification_shortlink_create'] = sanitize_text_field($input['gmuw_sl_email_notification_shortlink_create']);

        //if not blank...
        if (!empty($input['gmuw_sl_email_notification_shortlink_create'])) {

			//if it's not an integer, throw it out
			if (!preg_match("/[01]/", $input['gmuw_sl_email_notification_shortlink_create'])) {
				$input['gmuw_sl_email_notification_shortlink_create']='';
			}

        }

    }

    // gmuw_sl_email_notification_shortlink_delete
    if (isset($input['gmuw_sl_email_notification_shortlink_delete'])) {

		//sanitize input
        $input['gmuw_sl_email_notification_shortlink_delete'] = sanitize_text_field($input['gmuw_sl_email_notification_shortlink_delete']);

        //if not blank...
        if (!empty($input['gmuw_sl_email_notification_shortlink_delete'])) {

			//if it's not an integer, throw it out
			if (!preg_match("/[01]/", $input['gmuw_sl_email_notification_shortlink_delete'])) {
				$input['gmuw_sl_email_notification_shortlink_delete']='';
			}

        }

    }

	// gmuw_sl_email_notification_addresses
	if (isset($input['gmuw_sl_email_notification_addresses'])) {

		//strip out spaces
		$input['gmuw_sl_email_notification_addresses']=str_replace(' ', '', $input['gmuw_sl_email_notification_addresses']);

		//split into array with commas
		$email_array=explode(',',$input['gmuw_sl_email_notification_addresses']);

		//is is an array, with anything in it?
		if (is_array($email_array) && count($email_array)>0) {

			//yes we have emails
			$have_emails=true;

			//assume that they are all good emails, unless we disprove it
			$all_emails_good=true;

			//loop through array
			foreach ($email_array as $email_address) {

				//is this a valid email
				if (!is_email($email_address)) {

					//this data is not an email, so the field is no good
					$all_emails_good=false;

				}

			}

		}

		//if anything was wrong, throw out the input
		if (!$have_emails || !$all_emails_good) {
			$input['gmuw_sl_email_notification_addresses']='';
		}

	}

    return $input;
    
}
