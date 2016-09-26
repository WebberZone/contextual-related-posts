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

$option_name = 'ald_crp_settings';

if ( ! is_multisite() ) {

	$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related" );
	$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related_title" );
	$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related_content" );

	$wpdb->query( "
		DELETE FROM {$wpdb->postmeta}
		WHERE meta_key LIKE 'crp_related_posts%'
	" );

	delete_option( $option_name );

} else {

	// Get all blogs in the network and activate plugin on each one.
	$blog_ids = $wpdb->get_col( "
    	SELECT blog_id FROM $wpdb->blogs
		WHERE archived = '0' AND spam = '0' AND deleted = '0'
	" );

	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );

		$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related" );
		$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related_title" );
		$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related_content" );

		$wpdb->query( "
			DELETE FROM {$wpdb->postmeta}
			WHERE meta_key LIKE 'crp_related_posts%'
		" );

	    delete_option( $option_name );

	}

	// Switch back to the current blog.
	restore_current_blog();

}

