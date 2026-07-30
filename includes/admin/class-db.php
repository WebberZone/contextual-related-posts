<?php
/**
 * Database class
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

use WebberZone\Contextual_Related_Posts\Util\Helpers;

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
	 * FULLTEXT index schema version. Bump whenever the registered index set changes.
	 *
	 * @since 4.3.1
	 * @var int
	 */
	const INDEX_VERSION = 2;

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
			'crp_related_excerpt' => '(post_excerpt)',
		);
	}

	/**
	 * Get the map of current index names to the legacy index name each one replaces.
	 *
	 * The wz_ indexes are shared across WebberZone plugins, so a sibling plugin's index on the
	 * same columns satisfies ours.
	 *
	 * @since 4.3.1
	 *
	 * @return array Map of current index name => legacy index name.
	 */
	public static function get_legacy_index_aliases() {
		$aliases = array(
			'wz_title_content' => 'crp_related',
			'wz_title'         => 'crp_related_title',
			'wz_content'       => 'crp_related_content',
			'wz_excerpt'       => 'crp_related_excerpt',
		);

		/**
		 * Filter the map of current index names to legacy index names.
		 *
		 * @since 4.3.1
		 *
		 * @param array $aliases Map of current index name => legacy index name.
		 */
		return apply_filters( 'crp_legacy_index_aliases', $aliases );
	}

	/**
	 * Create any missing FULLTEXT indexes once per index schema version.
	 *
	 * Existing installs never re-run activation, so a newly registered index would otherwise
	 * wait on the user clicking through the missing-index notice.
	 *
	 * @since 4.3.1
	 */
	public static function maybe_heal_fulltext_indexes() {
		global $wpdb;

		if ( (int) get_option( 'crp_index_version', 0 ) >= self::INDEX_VERSION ) {
			return;
		}

		if ( Helpers::is_sqlite() ) {
			return;
		}

		$wpdb->hide_errors();
		self::create_fulltext_indexes();
		$wpdb->show_errors();

		// Only mark the version as healed if every index is actually in place, so a
		// failed ALTER is retried instead of being silently recorded as done.
		if ( self::is_fulltext_index_installed() ) {
			update_option( 'crp_index_version', self::INDEX_VERSION );
		}
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
		$wpdb->query( "ALTER TABLE {$wpdb->posts} ADD FULLTEXT {$index} {$columns};" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter 
	}

	/**
	 * Create fulltext indexes on the posts table.
	 *
	 * @since 3.5.0
	 */
	public static function create_fulltext_indexes() {
		if ( Helpers::is_sqlite() ) {
			return;
		}

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
			if ( self::index_exists( $index ) ) {
				$index = esc_sql( $index );
				$wpdb->query( "ALTER TABLE {$wpdb->posts} DROP INDEX $index" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
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
		if ( Helpers::is_sqlite() ) {
			return true;
		}

		$aliases = self::get_legacy_index_aliases();

		return self::index_exists( $index ) || self::index_exists( $aliases[ $index ] ?? '' );
	}

	/**
	 * Check if an index with this exact name exists on the posts table.
	 *
	 * Unlike is_index_installed(), this ignores legacy aliases - use it when acting on the
	 * index itself, e.g. building DROP statements.
	 *
	 * @since 4.3.1
	 *
	 * @param string $index Index name.
	 * @return bool True if the index exists, false otherwise.
	 */
	public static function index_exists( $index ) {
		if ( Helpers::is_sqlite() ) {
			return false;
		}

		global $wpdb;

		if ( '' === $index ) {
			return false;
		}

		$exists = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SHOW INDEX FROM {$wpdb->posts} WHERE Key_name = %s",
				$index
			)
		);

		return (bool) $exists;
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
	 * @return array Array of index statuses with 'installed' boolean flag and 'status' text.
	 */
	public static function check_fulltext_indexes() {
		// Get the list of fulltext indexes.
		$indexes  = self::get_fulltext_indexes();
		$statuses = array();

		// Check if each index is installed and add to the report.
		foreach ( $indexes as $index => $columns ) {
			$is_installed = self::is_index_installed( $index );

			$statuses[ $index ] = array(
				'columns'   => $columns,
				'installed' => $is_installed,
				'status'    => $is_installed
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
}
