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
	 *
	 * @param string $action Action to perform - create or delete.
	 */
	public static function fulltext_indexes( $action ) {
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

			if ( 'create' === $action && ! $index_exists ) {
				$index   = esc_sql( $index );
				$columns = esc_sql( $columns );
				$wpdb->query( "ALTER TABLE {$wpdb->posts} ADD FULLTEXT $index $columns" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			} elseif ( 'delete' === $action && $index_exists ) {
				$index = esc_sql( $index );
				$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX $index" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			}
		}
	}

	/**
	 * Create fulltext indexes on the posts table.
	 *
	 * @since 3.5.0
	 */
	public static function create_fulltext_indexes() {
		call_user_func( array( __CLASS__, 'fulltext_indexes' ), 'create' );
	}

	/**
	 * Delete the FULLTEXT index.
	 *
	 * @since 3.5.0
	 */
	public static function delete_fulltext_indexes() {
		call_user_func( array( __CLASS__, 'fulltext_indexes' ), 'delete' );
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
