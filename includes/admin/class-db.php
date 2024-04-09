<?php
/**
 * Database class
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Database class
 *
 * @since 3.5.0
 */
class Db {

	/**
	 * Constructor class.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
	}

	/**
	 * Create fulltext indexes on the posts table.
	 *
	 * @since 3.5.0
	 */
	public static function create_fulltext_indexes() {
		global $wpdb;

		$indexes = array(
			'crp_related'       => '(post_title, post_content)',
			'crp_related_title' => '(post_title)',
		);

		/**
		 * Filter the fulltext indexes.
		 *
		 * @since 3.5.0
		 *
		 * @param array $indexes Array of indexes.
		 */
		$indexes = apply_filters( 'crp_fulltext_indexes', $indexes );

		foreach ( $indexes as $index => $columns ) {
			$index_exists = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare(
					"SHOW INDEX FROM {$wpdb->posts} WHERE Key_name = %s",
					$index
				)
			);

			if ( ! $index_exists ) {
				$wpdb->query( 'ALTER TABLE ' . $wpdb->posts . ' ADD FULLTEXT ' . $index . ' ' . $columns . ';' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.NotPrepared
			}
		}
	}

	/**
	 * Delete the FULLTEXT index.
	 *
	 * @since 3.5.0
	 */
	public static function delete_fulltext_indexes() {
		global $wpdb;

		$wpdb->hide_errors();

		if ( $wpdb->get_results( "SHOW INDEX FROM {$wpdb->posts} where Key_name = 'crp_related'" ) ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
		}
		if ( $wpdb->get_results( "SHOW INDEX FROM {$wpdb->posts} where Key_name = 'crp_related_title'" ) ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related_title" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
		}
		if ( $wpdb->get_results( "SHOW INDEX FROM {$wpdb->posts} where Key_name = 'crp_related_content'" ) ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related_content" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
		}

		$wpdb->show_errors();
	}


	/**
	 * Get the table schema for the posts table.
	 *
	 * @since 3.5.0
	 */
	public static function get_posts_table_engine() {
		global $wpdb;

		$engine = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			"
		SELECT engine FROM INFORMATION_SCHEMA.TABLES
		WHERE table_schema=DATABASE()
		AND table_name = '{$wpdb->posts}'
		"
		);

		return $engine;
	}
}
