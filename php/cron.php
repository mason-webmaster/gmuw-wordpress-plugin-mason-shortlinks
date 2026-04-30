<?php

/**
 * Summary: php file which implements customizations related to cron
 */


// Schedule the email redirect export event if it doesn't exist
if (!wp_next_scheduled('gmuw_sl_email_redirect_report')) {
    wp_schedule_event(time(), 'daily', 'gmuw_sl_email_redirect_report');
}

add_action('gmuw_sl_email_redirect_report', 'gmuw_sl_handle_automatic_email');
function gmuw_sl_handle_automatic_email() {
    gmuw_sl_email_redirect_export();
}
