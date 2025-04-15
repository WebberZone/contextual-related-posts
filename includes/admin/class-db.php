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
	 * Get the list of fulltext indexes to be created on the posts table.
	 *
	 * @since 4.0.1
	 *
	 * @return array Array of fulltext indexes with their respective columns.
	 */
	public static function get_fulltext_indexes() {
		$indexes = array(
			'crp_related'         => '(post_title, post_content)',
			'crp_related_title'   => '(post_title)',
			'crp_related_content' => '(post_content)',
		);

		/**
		 * Filter the fulltext indexes.
		 *
		 * @since 3.5.0
		 *
		 * @param array $indexes Array of fulltext indexes.
		 */
		return apply_filters( 'crp_fulltext_indexes', $indexes );
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

		$indexes = self::get_fulltext_indexes();

		foreach ( $indexes as $index => $columns ) {
			$index_exists = self::is_index_installed( $index );

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
	 * Check if a fulltext index already exists on the posts table.
	 *
	 * @since 4.0.1
	 *
	 * @param string $index Index name.
	 * @return bool True if the index exists, false otherwise.
	 */
	public static function is_index_installed( $index ) {
		global $wpdb;

		$index_exists = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SHOW INDEX FROM {$wpdb->posts} WHERE Key_name = %s",
				$index
			)
		);

		return (bool) $index_exists;
	}

	/**
	 * Check if all fulltext indexes are installed.
	 *
	 * @since 4.0.1
	 *
	 * @return bool True if all fulltext indexes are installed, false if any are missing.
	 */
	public static function is_fulltext_index_installed() {
		$indexes = self::get_fulltext_indexes();

		foreach ( $indexes as $index => $columns ) {
			if ( ! self::is_index_installed( $index ) ) {
				return false; // Return false if any index is missing.
			}
		}

		return true; // Return true if all indexes are installed.
	}

	/**
	 * Check the status of all fulltext indexes.
	 *
	 * @since 4.0.1
	 *
	 * @return array Array of index statuses indicating whether they are installed.
	 */
	public static function check_fulltext_indexes() {
		// Get the list of fulltext indexes.
		$indexes  = self::get_fulltext_indexes();
		$statuses = array();

		// Check if each index is installed and add to the report.
		foreach ( $indexes as $index => $columns ) {
			$statuses[ $index ] = self::is_index_installed( $index )
				? '<span style="color: #006400;">' . __( 'Installed', 'contextual-related-posts' ) . '</span>'
				: '<span style="color: #8B0000;">' . __( 'Not Installed', 'contextual-related-posts' ) . '</span>';
		}

		/**
		 * Filter the index statuses report.
		 *
		 * @since 4.0.1
		 *
		 * @param array $statuses Array of index statuses.
		 */
		return apply_filters( 'crp_fulltext_index_statuses', $statuses );
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
