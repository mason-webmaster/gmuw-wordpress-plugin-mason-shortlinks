<?php

/**
 * Summary: php file which implements the plugin WP admin page interface
 */


/**
 * Generates the plugin settings page
 */
function gmuw_sl_display_settings_page() {
	
	// Only continue if this user has the 'manage options' capability
	if (!current_user_can('manage_options')) return;

	// Begin HTML output
	echo "<div class='wrap'>";

	// Page title
	echo "<h1>" . esc_html(get_admin_page_title()) . "</h1>";

	// Output basic plugin info
	echo "<p>This plugin provides shortlink setup.</p>";

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

	//qr code
	echo '<input id="text" type="text" value="https://www.gmu.edu/test1" style="width:80%" /><br />';
	echo '<div id="qrcode" style="width:100px; height:100px; margin-top:15px;"></div>';
	echo '<div id="downloadControls">';
	echo '<p>Save output as CSV file: <button id="createOutputFile">Generate QR code image</button> <a id="downloadLink" href="#" download="qrcode.png" style="display:none;">Download QR code</a></p>';
	echo '</div>';

	// Finish HTML output
	echo "</div>";
	
}

/**
 * Generates content for caching settings section
 */
function gmuw_sl_callback_section_settings_general() {

	// Get plugin options
	$gmuj_mmi_options = get_option('gmuw_sl_options');

	// Provide section introductory information
	echo '<p>This section contains general settings.</p>';

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
 * Sets default plugin options
 */
function gmuw_sl_options_default() {

	return array(
		'setting_one'   => '',
	);

}

/**
 * Validate plugin options
 */
function gmuj_msi_callback_validate_options($input) {
	
	// setting_one

	// Filter the resulting value as a clean URL
	//$input['cache_clear_url'] = filter_var($input['cache_clear_url'], FILTER_SANITIZE_URL);

	// Return value
	return $input;
	
}
