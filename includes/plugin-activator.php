<?php
/**
 * Fired during plugin activation
 *
 * @package   Contextual_Related_Posts
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
	crp_create_index();
}

/**
 * Fired for each blog when the plugin is deactivated.
 *
 * @since 2.9.3
 *
 * @param    boolean $network_wide    True if WPMU superadmin uses
 *                                    "Network Deactivate" action, false if
 *                                    WPMU is disabled or plugin is
 *                                    deactivated on an individual blog.
 */
function crp_deactivate( $network_wide ) {
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
			crp_single_deactivate();
		}

		// Switch back to the current blog.
		restore_current_blog();

	} else {
		crp_single_deactivate();
	}
}
register_deactivation_hook( CRP_PLUGIN_FILE, 'crp_deactivate' );

/**
 * Fired for each blog when the plugin is deactivated.
 *
 * @since 2.9.3
 */
function crp_single_deactivate() {
	$settings = get_option( 'crp_settings' );

	if ( ! empty( $settings['uninstall_indices_deactivate'] ) ) {
		crp_delete_index();
	}
}

/**
 * Fired when a new site is activated with a WPMU environment.
 *
 * @since 2.0.0
 *
 * @param int|WP_Site $blog WordPress 5.1 passes a WP_Site object.
 */
function crp_activate_new_site( $blog ) {

	if ( ! is_int( $blog ) ) {
		$blog = $blog->id;
	}

	switch_to_blog( $blog );
	crp_single_activate();
	restore_current_blog();
}
if ( version_compare( get_bloginfo( 'version' ), '5.1', '>=' ) ) {
	add_action( 'wp_initialize_site', 'crp_activate_new_site' );
} else {
	add_action( 'wpmu_new_blog', 'crp_activate_new_site' );
}
