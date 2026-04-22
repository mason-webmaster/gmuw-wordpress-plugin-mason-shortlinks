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
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_general", "General Information", "gmuw_sl_custom_dashboard_meta_box_general", "dashboard","normal");

  /* Add 'add shortlink' meta box */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlink_add", "Add New Shortlink:", "gmuw_sl_custom_dashboard_meta_box_shortlink_add", "dashboard","normal");

   /* all shortlinks */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlinks_all", "All Shortlinks", "gmuw_sl_custom_dashboard_meta_box_shortlinks_all", "dashboard","normal");

   /* top shortlinks */
  //add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlinks_top", "Top Shortlinks", "gmuw_sl_custom_dashboard_meta_box_shortlinks_top", "dashboard","normal");

   /* new shortlinks */
  //add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlinks_new", "Recently Added Shortlinks", "gmuw_sl_custom_dashboard_meta_box_shortlinks_new", "dashboard","normal");

   /* your shortlinks */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlinks_current_user", "Your Shortlinks", "gmuw_sl_custom_dashboard_meta_box_shortlinks_current_user", "dashboard","normal",);

   /* user groups shortlinks */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlinks_current_user_groups", "Shortlinks in Your Group(s)", "gmuw_sl_custom_dashboard_meta_box_shortlinks_current_user_groups", "dashboard","normal",);

}

/**
 * Provides content for the dashboard general meta box
 */
function gmuw_sl_custom_dashboard_meta_box_general() {

	?>
	<h3>About go.gmu.edu</h3>
	<p>go.gmu.edu is George Mason University's shortlink service.</p>

	<h3>About Shortlinks</h3>
	<p>Shortlinks allow you to create a (typically shorter, more convenient) Mason-branded custom link to be used as a substitute for another link which may be long, inconvenient, and/or non-Mason-branded.</p>
	<p>All shortlinks have a <strong>label</strong> and a <strong>target URL</strong>.</p>
	<p>For example, for the shortlink <code>go.gmu.edu/example</code>, which points to <code>https://www.gmu.edu/</code>:</p>
	<p>The <strong>label</strong> is <code>example</code> and the <strong>target URL</strong> is <code>https://www.gmu.edu/</code>.</p>


	<h3>Approved Affiliated Domains</h3>

	<p>Using a Mason‑branded shortlink signals an official connection to the university. To protect the George Mason brand and ensure appropriate representation, self‑service creation of shortlinks is limited to a set of approved affiliated domains.</p>
	<p>Approved affiliated domains include external services that George Mason regularly uses for university business (such as all gmu.edu domains, Salesforce, Qualtrics, Zoom, and others).</p> 
	<p>If you need a short link for a domain that isn't pre-approved for self‑service creation, you can <a href="<?php echo esc_url(get_option('gmuw_sl_options')['gmuw_sl_ticket_url']); ?>" target="_blank">submit a request ticket for review</a>.</p>
	<p>Shortlinks can be created without additional approval for the following pre-approved domains:</p>
	<p>
	<?php
	foreach (gmuw_sl_approved_domains_array() as $approved_domain) {
		echo '<code>'. $approved_domain . '</code><br />';
	}
	?>
	</p>

	<h3>Reserved Labels</h3>

	<p>Some shortlink labels are reserved and cannot be used without additional approval.</p>
	<p>If you would like to inquire about creating a shortlink using a reserved label, you can <a href="<?php echo esc_url(get_option('gmuw_sl_options')['gmuw_sl_ticket_url']); ?>" target="_blank">submit a request ticket for review</a>.</p>
	<p>The following shortlink labels are reserved:</p>

	<p>
	<?php
	foreach (gmuw_sl_reserved_labels_array() as $reserved_label) {
		echo '<code>'. $reserved_label . '</code><br />';
	}
	?>
	</p>

	<?php

}

/**
 * Provides content for the dashboard add new shortlink meta box
 */
function gmuw_sl_custom_dashboard_meta_box_shortlink_add() {

	//Output content
	?>

	<?php
	echo gmuw_sl_shortlink_add_form();

}

//display custom dashboard meta box with a table of shortlinks
function gmuw_sl_custom_dashboard_meta_box_shortlinks_all() {

	//link to full list
	echo '<p><a href="/wp-admin/admin.php?page=gmuw_sl_shortlink_management">View Full Details</a></p>';

	//get redirects
	$redirects = gmuw_sl_get_redirects();

	//put into table
	echo gmuw_sl_shortlinks_table($redirects,true);

}

//display custom dashboard meta box with a table of top shortlinks by count
function gmuw_sl_custom_dashboard_meta_box_shortlinks_top() {

	//get redirects
	$redirects = gmuw_sl_get_redirects('top');

	//put into table
	echo gmuw_sl_shortlinks_table($redirects,true);

}

//display custom dashboard meta box with a table of top shortlinks by newest
function gmuw_sl_custom_dashboard_meta_box_shortlinks_new() {

	//get redirects
	$redirects = gmuw_sl_get_redirects('new');

	//put into table
	echo gmuw_sl_shortlinks_table($redirects,true);

}

//display custom dashboard meta box with a table of current user's shortlinks
function gmuw_sl_custom_dashboard_meta_box_shortlinks_current_user() {

	//get redirects
	$redirects = gmuw_sl_get_redirects('user');

	//link to full list
	if ($redirects) {
		echo '<p><a href="/wp-admin/admin.php?page=gmuw_sl_shortlink_management&displaymode=user">View Full Details</a></p>';
	}

	//put into table
	echo gmuw_sl_shortlinks_table($redirects,true);

}

//display custom dashboard meta box with a table of current user's groups' shortlinks
function gmuw_sl_custom_dashboard_meta_box_shortlinks_current_user_groups() {

	//does the user have any groups
	if (!gmuw_sl_get_user_groups_array()) {
		echo '<p>You do not belong to any groups.';
		return;
	}

	//get redirects
	$redirects = gmuw_sl_get_redirects('user_groups');

	//users groups
	echo '<p>You belong to the following group(s): '.gmuw_sl_display_user_groups().'</p>';

	//link to full list
	if ($redirects) {
		echo '<p><a href="/wp-admin/admin.php?page=gmuw_sl_shortlink_management&displaymode=user_groups">View Full Details</a></p>';
	}

	//put into table
	echo gmuw_sl_shortlinks_table($redirects,true);

}
