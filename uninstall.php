<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package     ST\Lms
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


// clean up after ourselves, that's a good plugin!

// Subscribe notice: stored subscriber email, lead sync state and per-user notice schedule.
delete_option( 'stlms_subscriber_email' );
delete_option( 'stlms_lead_sync' );
delete_option( 'stlms_site_hash' );
delete_metadata( 'user', 0, 'stlms_subscribe_notice_state', '', true );
