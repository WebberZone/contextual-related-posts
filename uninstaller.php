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
		// Delete main plugin options.
		delete_option( 'ald_crp_settings' );
		delete_option( 'crp_settings' );

		// Delete wizard-related options.
		delete_option( 'crp_wizard_completed' );
		delete_option( 'crp_wizard_completed_date' );
		delete_option( 'crp_wizard_current_step' );
		delete_option( 'crp_show_wizard' );

		// Delete custom tables options.
		delete_option( 'wz_posts_custom_tables_ready' );

		// Delete block settings.
		delete_option( 'crp_related_posts_pro_blocks_settings' );

		// Delete post meta data.
		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
			"
			DELETE FROM {$wpdb->postmeta}
			WHERE `meta_key` LIKE 'crp_related_posts%'
			OR `meta_key` LIKE '_crp_cache_%'
			OR `meta_key` LIKE 'crp_post_meta%'
			OR `meta_key` LIKE '_crp_include_cat_%'
		"
		);

		// Delete all plugin transients.
		crp_delete_transients();
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

/**
 * Delete all plugin transients.
 *
 * @since 4.1.0
 */
function crp_delete_transients() {
	global $wpdb;

	// Delete specific known transients.
	delete_transient( 'crp_reindex_state' );
	delete_transient( 'crp_show_wizard_activation_redirect' );
	delete_transient( 'crp_deactivated_notice_id' );
	delete_transient( 'crp_reindex_scheduled' );

	// Delete all transients with crp_ prefix.
	$sql = "
		SELECT option_name
		FROM {$wpdb->options}
		WHERE `option_name` LIKE '_transient_crp_%'
		OR `option_name` LIKE '_transient_timeout_crp_%'
	";

	$results = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

	if ( is_array( $results ) ) {
		foreach ( $results as $result ) {
			if ( strpos( $result->option_name, '_transient_timeout_' ) === 0 ) {
				// Skip timeout options, they'll be deleted with the transient.
				continue;
			}
			$transient = str_replace( '_transient_', '', $result->option_name );
			delete_transient( $transient );
		}
	}

	// Delete site transients with crp_ prefix (for multisite).
	if ( is_multisite() ) {
		$sql = "
			SELECT meta_key
			FROM {$wpdb->sitemeta}
			WHERE `meta_key` LIKE '_site_transient_crp_%'
			OR `meta_key` LIKE '_site_transient_timeout_crp_%'
		";

		$results = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		if ( is_array( $results ) ) {
			foreach ( $results as $result ) {
				if ( strpos( $result->meta_key, '_site_transient_timeout_' ) === 0 ) {
					// Skip timeout options, they'll be deleted with the transient.
					continue;
				}
				$transient = str_replace( '_site_transient_', '', $result->meta_key );
				delete_site_transient( $transient );
			}
		}
	}
}
