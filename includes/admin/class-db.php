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
			'wz_title_content' => '(post_title, post_content)',
			'wz_title'         => '(post_title)',
			'wz_content'       => '(post_content)',
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
	 * Get the list of old fulltext indexes.
	 *
	 * @since 4.1.0
	 *
	 * @return array Array of fulltext indexes with their respective columns.
	 */
	public static function get_old_fulltext_indexes() {
		return array(
			'crp_related'         => '(post_title, post_content)',
			'crp_related_title'   => '(post_title)',
			'crp_related_content' => '(post_content)',
		);
	}

	/**
	 * Install a fulltext index on the posts table.
	 *
	 * @since 4.1.0
	 *
	 * @param string $index   Index name.
	 * @param string $columns Columns to be indexed.
	 * @return void
	 */
	public static function install_fulltext_index( $index, $columns ) {
		global $wpdb;

		// Install the fulltext index if it doesn't exist.
		$wpdb->query( "ALTER TABLE {$wpdb->posts} ADD FULLTEXT {$index} {$columns};" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Create fulltext indexes on the posts table.
	 *
	 * @since 3.5.0
	 */
	public static function create_fulltext_indexes() {
		// Get the list of fulltext indexes.
		$indexes = self::get_fulltext_indexes();

		// Loop through the indexes and create them if not exist.
		foreach ( $indexes as $index => $columns ) {
			if ( ! self::is_index_installed( $index ) ) {
				self::install_fulltext_index( $index, $columns );
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

		$indexes = array_merge( self::get_fulltext_indexes(), self::get_old_fulltext_indexes() );

		foreach ( $indexes as $index => $columns ) {
			if ( self::is_index_installed( $index ) ) {
				$index = esc_sql( $index );
				$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX $index" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			}
		}
	}

	/**
	 * Check if a fulltext index already exists on the posts table.
	 *
	 * @since 4.1.0
	 *
	 * @param string $index Index name.
	 * @return bool True if the index exists, false otherwise.
	 */
	public static function is_index_installed( $index ) {
		global $wpdb;

		$new_indexes = self::get_fulltext_indexes();
		$old_indexes = self::get_old_fulltext_indexes();

		// Find the corresponding old index name if the given index is a new one.
		$old_index_name = '';
		if ( in_array( $index, array_keys( $new_indexes ), true ) ) {
			$key            = array_search( $index, array_keys( $new_indexes ), true );
			$old_index_keys = array_keys( $old_indexes );
			if ( isset( $old_index_keys[ $key ] ) ) {
				$old_index_name = $old_index_keys[ $key ];
			}
		}

		$index_exists = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SHOW INDEX FROM {$wpdb->posts} WHERE Key_name = %s OR Key_name = %s",
				$index,
				$old_index_name
			)
		);

		return (bool) $index_exists;
	}

	/**
	 * Check if all fulltext indexes are installed.
	 *
	 * @since 4.1.0
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
	 * @since 4.1.0
	 *
	 * @return array Array of index statuses indicating whether they are installed.
	 */
	public static function check_fulltext_indexes() {
		// Get the list of fulltext indexes.
		$indexes  = self::get_fulltext_indexes();
		$statuses = array();

		// Check if each index is installed and add to the report.
		foreach ( $indexes as $index => $columns ) {
			$is_installed = self::is_index_installed( $index );

			$statuses[ $index ] = array(
				'columns' => $columns,
				'status'  => $is_installed
					? '<span style="color: #006400;">' . __( 'Installed', 'contextual-related-posts' ) . '</span>'
					: '<span style="color: #8B0000;">' . __( 'Not Installed', 'contextual-related-posts' ) . '</span>',
			);
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
