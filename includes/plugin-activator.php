<?php
/**
 * Fired during plugin activation
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2015 Ajay D'Souza
 */

/**
 * Fired for each blog when the plugin is activated.
 *
 * @since 1.0.1
 *
 * @param    boolean $network_wide    True if WPMU superadmin uses
 *                                    "Network Activate" action, false if
 *                                    WPMU is disabled or plugin is
 *                                    activated on an individual blog.
 */
function crp_activate( $network_wide ) {
	global $wpdb;

	if ( is_multisite() && $network_wide ) {

		// Get all blogs in the network and activate plugin on each one.
		$blog_ids = $wpdb->get_col( "
        	SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0' AND deleted = '0'
		" );
		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			crp_single_activate();
		}

		// Switch back to the current blog.
		restore_current_blog();

	} else {
		crp_single_activate();
	}
}


/**
 * Fired for each blog when the plugin is activated.
 *
 * @since 2.0.0
 */
function crp_single_activate() {
	global $wpdb;

	crp_read_options();

	$wpdb->hide_errors();

	crp_delete_index();
	crp_create_index();

	$wpdb->show_errors();

}


