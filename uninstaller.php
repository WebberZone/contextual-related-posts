<?php
/**
 * Fired when the plugin is uninstalled
 *
 * @package Contextual_Related_Posts
 */

use WebberZone\Contextual_Related_Posts\Admin\Db;

defined( 'ABSPATH' ) || exit;

if ( ! ( defined( 'WP_UNINSTALL_PLUGIN' ) || defined( 'WP_FS__UNINSTALL_MODE' ) ) ) {
	exit;
}

if ( is_multisite() ) {

	$sites = get_sites(
		array(
			'archived' => 0,
			'spam'     => 0,
			'deleted'  => 0,
		)
	);

	foreach ( $sites as $site ) {
		switch_to_blog( (int) $site->blog_id );
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
			OR `meta_key` LIKE 'crp_post_meta%'
		"
		);
	}

	if ( ! empty( $settings['uninstall_indices'] ) ) {

		Db::delete_fulltext_indexes();

		delete_option( 'crp_db_version' );
	}

	if ( ! empty( $settings['uninstall_tables'] ) && class_exists( 'WebberZone\\Contextual_Related_Posts\\Pro\\Custom_Tables\\Table_Manager' ) ) {
		$table_manager = new \WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Table_Manager();
		$table_manager->drop_tables();
		delete_option( \WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Table_Manager::$db_version_option );
	}

	do_action( 'crp_delete_data' );
}
