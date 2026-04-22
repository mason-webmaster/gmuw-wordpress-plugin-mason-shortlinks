<?php

/**
 * Summary: php file which implements simplehistory plugin customizations
 */


//disable IP address anonymyzation in simple history plugin logging
//https://simple-history.com/2018/simple-history-2-23-released-with-privacy-gdpr-related-logging/
add_filter( 'simple_history/privacy/anonymize_ip_address', '__return_false' );

//increase log retention duration
//https://simple-history.com/support/change-number-of-days-to-keep-log/
add_filter(
	"simple_history/db_purge_days_interval",
	function($days) {
		$days = 365;
		return $days;
	}
);
