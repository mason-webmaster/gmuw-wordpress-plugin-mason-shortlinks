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
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlink_add", "Add Shortlink", "gmuw_sl_custom_dashboard_meta_box_shortlink_add", "dashboard","normal");

   /* all shortlinks */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlinks_all", "All Shortlinks", "gmuw_sl_custom_dashboard_meta_box_shortlinks_all", "dashboard","normal");

   /* top shortlinks */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlinks_top", "Top Shortlinks", "gmuw_sl_custom_dashboard_meta_box_shortlinks_top", "dashboard","normal");

   /* new shortlinks */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlinks_new", "Recently Added Shortlinks", "gmuw_sl_custom_dashboard_meta_box_shortlinks_new", "dashboard","normal");

   /* your shortlinks */
  add_meta_box("gmuw_sl_custom_dashboard_meta_box_shortlinks_current_user", "Your Shortlinks", "gmuw_sl_custom_dashboard_meta_box_shortlinks_current_user", "dashboard","normal",);

}

/**
 * Provides content for the dashboard general meta box
 */
function gmuw_sl_custom_dashboard_meta_box_general() {

	?>
	<h3>About go.gmu.edu</h3>
	<p>go.gmu.edu is George Mason University's shortlink service.</p>

	<h3>About Shortlinks</h3>
	<p>Shortlinks allow you to create a (typically shorter, more convenient) Mason-branded custom link to be used as a substitue for another link which may be long, inconvenient, and/or non-Mason-branded.</p>
	<p>All shortlinks have a <strong>label</strong> and a <strong>target URL</strong>.</p>
	<p>For example, for the shortlink <code>go.gmu.edu/example</code>, which points to <code>https://www.gmu.edu/</code>:</p>
	<p>The <strong>label</strong> is <code>example</code> and the <strong>target URL</strong> is <code>https://www.gmu.edu/</code>.</p>


	<h3>Approved Affiliated Domains</h3>

	<p>Using a Mason‑branded shortlink signals an official connection to the university. To protect the George Mason brand and ensure appropriate representation, self‑service creation of shortlinks is limited to a set of approved affiliated domains.</p>
	<p>Approved affiliated domains include external services that George Mason regularly uses for university business (such as all gmu.edu domains, Salesforce, Qualtrics, Zoom, and others).</p> 
	<p>If you need a short link for a domain that isn't pre-approved for self‑service creation, you can <a href="<?php echo TICKET_URL; ?>" target="_blank">submit a request ticket for review</a>.</p> 
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
	<p>If you would like to inquire about creating a shortlink using a reserved label, you can <a href="<?php echo TICKET_URL; ?>" target="_blank">submit a request ticket for review</a>.</p>
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

	<p>Add a new shortlink:</p>

	<?php
	echo gmuw_sl_shortlink_add_form();

}

//display custom dashboard meta box with a table of shortlinks
function gmuw_sl_custom_dashboard_meta_box_shortlinks_all() {

	//link to full list
	echo '<p><a href="/wp-admin/admin.php?page=gmuw_sl_shortlink_management">View Full List</a></p>';

	//get redirects
	$redirects = gmuw_sl_get_redirects();

	//put into table
	echo gmuw_sl_shortlinks_table($redirects,true);

}

//display custom dashboard meta box with a table of top shortlinks by count
function gmuw_sl_custom_dashboard_meta_box_shortlinks_top() {

	//get redirects
	$redirects = gmuw_sl_get_redirects_top();

	//put into table
	echo gmuw_sl_shortlinks_table($redirects,true);

}

//display custom dashboard meta box with a table of top shortlinks by newest
function gmuw_sl_custom_dashboard_meta_box_shortlinks_new() {

	//get redirects
	$redirects = gmuw_sl_get_redirects_new();

	//put into table
	echo gmuw_sl_shortlinks_table($redirects,true);

}

//display custom dashboard meta box with a table of current user's shortlinks
function gmuw_sl_custom_dashboard_meta_box_shortlinks_current_user() {

	//link to full list
	echo '<p><a href="/wp-admin/admin.php?page=gmuw_sl_shortlink_management&displaymode=user">View Full List</a></p>';

	//get redirects
	$redirects = gmuw_sl_get_redirects_current_user();

	//put into table
	echo gmuw_sl_shortlinks_table($redirects,true);

}
