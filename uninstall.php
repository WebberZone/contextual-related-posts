<?php
/**
 * Fired when the plugin is uninstalled
 *
 * @package Contextual_Related_Posts
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


global $wpdb;

if ( is_multisite() ) {

	// Get all blogs in the network and activate plugin on each one.
	$blogids = $wpdb->get_col( //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		"
		SELECT blog_id FROM $wpdb->blogs
		WHERE archived = '0' AND spam = '0' AND deleted = '0'
	"
	);

	foreach ( $blogids as $blogid ) {
		switch_to_blog( $blogid );
		crp_delete_data();
		restore_current_blog();
	}
} else {
	crp_delete_data();
}


/**
 * Delete plugin data.
 *
 * @since 2.6.1
 */
function crp_delete_data() {
	global $wpdb;

	$settings = get_option( 'crp_settings' );

	if ( ! empty( $settings['uninstall_options'] ) ) {
		delete_option( 'ald_crp_settings' );
		delete_option( 'crp_settings' );

		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
			"
			DELETE FROM {$wpdb->postmeta}
			WHERE `meta_key` LIKE 'crp_related_posts%'
			OR `meta_key` LIKE '_crp_cache_%'
		"
		);
	}

	if ( ! empty( $settings['uninstall_indices'] ) ) {

		$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related_title" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related_content" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange

	}

}
