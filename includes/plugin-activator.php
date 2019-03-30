<?php
/**
 * Fired during plugin activation
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2019 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

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
		$blog_ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			"
        	SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0' AND deleted = '0'
			"
		);
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
register_activation_hook( CRP_PLUGIN_FILE, 'crp_activate' );


/**
 * Fired for each blog when the plugin is activated.
 *
 * @since 2.0.0
 */
function crp_single_activate() {
	global $wpdb;

	$wpdb->hide_errors();

	crp_create_index();

	$wpdb->show_errors();

}


/**
 * Fired when a new site is activated with a WPMU environment.
 *
 * @since 2.0.0
 *
 * @param    int $blog_id    ID of the new blog.
 */
function crp_activate_new_site( $blog_id ) {

	if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
		return;
	}

	switch_to_blog( $blog_id );
	crp_single_activate();
	restore_current_blog();

}
add_action( 'wpmu_new_blog', 'crp_activate_new_site' );

