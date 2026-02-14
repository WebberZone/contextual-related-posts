<?php
/**
 * Contextual Related Posts Cache functions.
 *
 * @since 3.5.0
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Util;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Cache Class.
 *
 * @since 3.5.0
 */
class Cache {

	/**
	 * Constructor class.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		Hook_Registry::add_action( 'wp_ajax_crp_clear_cache', array( $this, 'ajax_clearcache' ) );
	}

	/**
	 * Function to clear the CRP Cache with Ajax.
	 *
	 * @since 3.5.0
	 */
	public static function ajax_clearcache() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		check_ajax_referer( 'crp-admin', 'security' );

		$count = self::delete();

		exit(
			wp_json_encode(
				array(
					'success' => 1,
					'message' => sprintf(
						// translators: %d is the number of cache entries cleared.
						_n(
							'%d cache entry has been cleared',
							'%d cache entries have been cleared',
							$count,
							'contextual-related-posts'
						),
						$count
					),
					'count'   => $count,
				)
			)
		);
	}

	/**
	 * Delete the entire CRP cache.
	 *
	 * @since 3.5.0
	 * @since 4.0.0 Optimized with direct SQL for better performance.
	 *
	 * @return int Number of keys deleted.
	 */
	public static function delete(): int {
		global $wpdb;

		// Start transaction.
		$wpdb->query( 'START TRANSACTION' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		// Delete all cache entries and get count of deleted rows.
		$delete_sql = "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_crp_cache_%'";

		// Execute the deletion and get count of affected rows.
		$count = (int) $wpdb->query( $delete_sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared

		// Commit transaction.
		$wpdb->query( 'COMMIT' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared

		return intval( $count / 2 );
	}

	/**
	 * Format expiration time to human readable format.
	 *
	 * @since 4.2.0
	 *
	 * @param int $seconds Expiration time in seconds.
	 * @return string Human readable time format.
	 */
	private static function format_expiration_time( int $seconds ): string {
		// Map common time intervals to human readable format.
		$time_intervals = array(
			HOUR_IN_SECONDS      => '1 Hour',
			6 * HOUR_IN_SECONDS  => '6 Hours',
			12 * HOUR_IN_SECONDS => '12 Hours',
			DAY_IN_SECONDS       => '1 Day',
			2 * DAY_IN_SECONDS   => '2 Days',
			3 * DAY_IN_SECONDS   => '3 Days',
			WEEK_IN_SECONDS      => '1 Week',
			2 * WEEK_IN_SECONDS  => '2 Weeks',
			MONTH_IN_SECONDS     => '1 Month',
		);

		// Find exact match.
		if ( isset( $time_intervals[ $seconds ] ) ) {
			return $time_intervals[ $seconds ];
		}

		// For custom values, create a readable format.
		if ( $seconds < HOUR_IN_SECONDS ) {
			return $seconds . ' Minutes';
		} elseif ( $seconds < DAY_IN_SECONDS ) {
			$hours = round( $seconds / HOUR_IN_SECONDS );
			return $hours . ' Hours';
		} elseif ( $seconds < WEEK_IN_SECONDS ) {
			$days = round( $seconds / DAY_IN_SECONDS );
			return $days . ' Days';
		} elseif ( $seconds < MONTH_IN_SECONDS ) {
			$weeks = round( $seconds / WEEK_IN_SECONDS );
			return $weeks . ' Weeks';
		} else {
			$months = round( $seconds / MONTH_IN_SECONDS );
			return $months . ' Months';
		}
	}

	/**
	 * Get cache status information.
	 *
	 * @since 4.2.0
	 *
	 * @return array Cache status information.
	 */
	public static function get_status(): array {
		global $wpdb;

		// Count cache entries (excluding expiration entries).
		$cache_count = $wpdb->get_var(  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			"SELECT COUNT(*) FROM {$wpdb->postmeta} 
			WHERE (meta_key LIKE '_crp_cache_%' OR meta_key LIKE '_crp_cache_h_%' OR meta_key LIKE '_crp_cache_p_%')
			AND meta_key NOT LIKE '_crp_cache_expires_%'
			AND meta_key NOT LIKE '_crp_cache_expires_h_%'
			AND meta_key NOT LIKE '_crp_cache_expires_p_%'"
		);

		// Convert expiration time to human readable format.
		$expiration_seconds = \crp_get_option( 'cache_time', WEEK_IN_SECONDS );
		$expiration_human   = $expiration_seconds ? self::format_expiration_time( $expiration_seconds ) : 'No expiry';

		return array(
			'cache_count'      => (int) $cache_count,
			'enabled'          => \crp_get_option( 'cache', false ),
			'expiration'       => $expiration_seconds,
			'expiration_human' => $expiration_human,
		);
	}

	/**
	 * Get array of cache keys.
	 *
	 * @since 3.5.0
	 *
	 * @return array Array of cache keys.
	 */
	public static function get_keys(): array {

		$keys = self::get_meta_keys();

		/**
		 * Filters the array containing the various cache keys.
		 *
		 * @since 3.5.0
		 *
		 * @param array $keys Array of cache keys.
		 */
		return apply_filters( 'crp_cache_keys', $keys );
	}

	/**
	 * Get the _crp_cache keys.
	 *
	 * @since 3.5.0
	 *
	 * @param int $post_id Post ID. Optional.
	 * @return array Array of _crp_cache keys.
	 */
	public static function get_meta_keys( $post_id = 0 ): array {
		global $wpdb;

		// Only query the database for actual cache keys that exist.
		$sql = "SELECT meta_key FROM {$wpdb->postmeta} 
			WHERE (meta_key LIKE '_crp_cache_%' OR meta_key LIKE '_crp_cache_h_%' OR meta_key LIKE '_crp_cache_p_%')
			AND meta_key NOT LIKE '_crp_cache_expires_%'
			AND meta_key NOT LIKE '_crp_cache_expires_h_%'
			AND meta_key NOT LIKE '_crp_cache_expires_p_%'";

		if ( $post_id > 0 ) {
			$sql .= $wpdb->prepare( ' AND post_id = %d ', $post_id );
		}

		$results = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared

		$meta_keys = wp_list_pluck( $results, 'meta_key' );

		/**
		 * Filter the array of CRP cache meta keys.
		 *
		 * @since 2.7.0
		 *
		 * @param array $meta_keys Array of CRP cache meta keys.
		 */
		return apply_filters( 'crp_cache_get_meta_keys', $meta_keys );
	}

	/**
	 * Clean up expired cache entries.
	 *
	 * @since 4.2.0
	 *
	 * @param bool $dry_run Whether to perform a dry run.
	 * @return array Results array with 'cleaned' and 'scanned' counts.
	 */
	public static function cleanup_expired( bool $dry_run = false ): array {
		global $wpdb;

		$cleaned = 0;
		$scanned = 0;

		// Get all actual cache keys from database.
		$cache_keys = self::get_meta_keys();

		foreach ( $cache_keys as $cache_key ) {
			// Extract the key name from the meta_key.
			$key_name = str_replace( '_crp_cache_', '', $cache_key );

			// Get all posts that have this cache key.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$post_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s",
					$cache_key
				)
			);

			foreach ( $post_ids as $post_id ) {
				++$scanned;

				// Check if cache is expired by trying to get it.
				$cached_value = self::get_cache( $post_id, $key_name );

				// If get_cache returns false, it means the cache is expired or doesn't exist.
				if ( false === $cached_value ) {
					if ( $dry_run ) {
						++$cleaned;
					} else {
						// Delete the expired cache entry.
						$result = self::delete_by_post_id_and_key( $post_id, $key_name );
						if ( $result ) {
							++$cleaned;
						}
					}
				}
			}
		}

		return array(
			'cleaned' => $cleaned,
			'scanned' => $scanned,
		);
	}

	/**
	 * Delete cache by post ID.
	 *
	 * @since 3.4.0
	 *
	 * @param int $post_id Post ID.
	 * @return int Number of entries deleted.
	 */
	public static function delete_by_post_id( $post_id ): int {
		$meta_keys     = self::get_meta_keys( $post_id );
		$deleted_count = 0;

		foreach ( $meta_keys as $meta_key ) {
			$result = delete_post_meta( $post_id, $meta_key );
			if ( false !== $result ) {
				++$deleted_count;
			}
			// Also delete the corresponding expiration key.
			$expires_key    = str_replace( '_crp_cache_', '_crp_cache_expires_', $meta_key );
			$expires_key    = str_replace( '_crp_cache_h_', '_crp_cache_expires_h_', $expires_key );
			$expires_key    = str_replace( '_crp_cache_p_', '_crp_cache_expires_p_', $expires_key );
			$expires_result = delete_post_meta( $post_id, $expires_key );
			if ( false !== $expires_result ) {
				++$deleted_count;
			}
		}

		return $deleted_count;
	}

	/**
	 * Get the cache key based on a list of parameters.
	 *
	 * @since 3.5.0
	 *
	 * @param mixed $attr Array of attributes typically.
	 * @return string Cache meta key
	 */
	public static function get_key( $attr ): string {
		$args = (array) $attr;

		static $setting_types = null;
		if ( null === $setting_types ) {
			$setting_types = function_exists( 'crp_get_registered_settings_types' ) ? crp_get_registered_settings_types() : array();
		}

		// Remove args that don't affect query results.
		$exclude_keys = array(
			'after_list',
			'after_list_item',
			'before_list',
			'before_list_item',
			'blank_output',
			'blank_output_text',
			'cache',
			'cache_posts',
			'className',
			'crp_query',
			'echo',
			'excerpt_length',
			'extra_class',
			'heading',
			'ignore_sticky_posts',
			'is_block',
			'is_crp_query',
			'is_manual',
			'is_shortcode',
			'is_widget',
			'link_new_window',
			'link_nofollow',
			'more_link_text',
			'no_found_rows',
			'other_attributes',
			'post_types',
			'post_id',
			'postid',
			'same_post_type',
			'show_author',
			'show_credit',
			'show_date',
			'show_excerpt',
			'show_metabox',
			'show_metabox_admins',
			'suppress_filters',
			'title',
			'title_length',
		);

		foreach ( $exclude_keys as $key ) {
			unset( $args[ $key ] );
		}

		// Remove any keys ending in _header or _desc, or with type 'header'.
		foreach ( $args as $key => $value ) {
			if ( '_header' === substr( $key, -7 ) || '_desc' === substr( $key, -5 ) ) {
				unset( $args[ $key ] );
				continue;
			}

			if ( isset( $setting_types[ $key ] ) && 'header' === $setting_types[ $key ] ) {
				unset( $args[ $key ] );
			}
		}

		// Define categories of types for normalization.
		$id_array_types     = array( 'postids', 'numbercsv', 'taxonomies' );
		$string_array_types = array( 'posttypes', 'csv', 'multicheck' );
		$numeric_types      = array( 'number', 'checkbox', 'select', 'radio', 'radiodesc' );

		// Process arguments based on their registered types.
		foreach ( $args as $key => $value ) {
			$type = $setting_types[ $key ] ?? '';

			if ( in_array( $type, $numeric_types, true ) && is_numeric( $value ) ) {
				$args[ $key ] = (int) $value;
			} elseif ( in_array( $type, $id_array_types, true ) ) {
				$args[ $key ] = is_array( $value ) ? $value : wp_parse_id_list( $value );
				$args[ $key ] = array_unique( array_map( 'absint', $args[ $key ] ) );
				$args[ $key ] = array_filter( $args[ $key ] );
				sort( $args[ $key ] );
				if ( empty( $args[ $key ] ) ) {
					unset( $args[ $key ] );
				}
			} elseif ( in_array( $type, $string_array_types, true ) ) {
				if ( is_string( $value ) && strpos( $value, '=' ) !== false ) {
					parse_str( $value, $parsed );
					$value = array_keys( $parsed );
				} elseif ( is_string( $value ) ) {
					$value = explode( ',', $value );
				}
				$args[ $key ] = is_array( $value ) ? $value : array( $value );
				$args[ $key ] = array_unique( array_map( 'strval', $args[ $key ] ) );
				$args[ $key ] = array_filter( $args[ $key ] );
				sort( $args[ $key ] );
				if ( empty( $args[ $key ] ) ) {
					unset( $args[ $key ] );
				}
			}
		}

		// Fallback for known keys that might not be in $setting_types or need specific handling.
		$id_arrays = array(
			'author__in',
			'author__not_in',
			'category__and',
			'category__in',
			'category__not_in',
			'cornerstone_post_ids',
			'exclude_categories',
			'exclude_on_categories',
			'exclude_on_post_ids',
			'exclude_post_ids',
			'include_cat_ids',
			'include_post_ids',
			'manual_related',
			'post__in',
			'post__not_in',
			'post_parent__in',
			'post_parent__not_in',
			'tag__and',
			'tag__in',
			'tag__not_in',
			'tag_slug__and',
			'tag_slug__in',
		);

		foreach ( $id_arrays as $key ) {
			if ( array_key_exists( $key, $args ) && ! isset( $setting_types[ $key ] ) ) {
				if ( null !== $args[ $key ] ) {
					$args[ $key ] = is_array( $args[ $key ] ) ? $args[ $key ] : wp_parse_id_list( $args[ $key ] );
					$args[ $key ] = array_unique( array_map( 'absint', $args[ $key ] ) );
					$args[ $key ] = array_filter( $args[ $key ] );
					sort( $args[ $key ] );

					if ( empty( $args[ $key ] ) ) {
						unset( $args[ $key ] );
					}
				} else {
					unset( $args[ $key ] );
				}
			}
		}

		$string_arrays = array(
			'exclude_cat_slugs',
			'exclude_on_cat_slugs',
			'exclude_on_post_types',
			'post_name__in',
			'post_status',
			'post_type',
			'same_taxes',
		);

		foreach ( $string_arrays as $key ) {
			if ( array_key_exists( $key, $args ) && ! isset( $setting_types[ $key ] ) ) {
				if ( null !== $args[ $key ] ) {
					if ( is_string( $args[ $key ] ) && strpos( $args[ $key ], '=' ) !== false ) {
						parse_str( $args[ $key ], $parsed );
						$parsed_value = array_keys( $parsed );
					} elseif ( is_string( $args[ $key ] ) ) {
						$parsed_value = explode( ',', $args[ $key ] );
					} else {
						$parsed_value = $args[ $key ];
					}
					$args[ $key ] = is_array( $parsed_value ) ? $parsed_value : array( $parsed_value );
					$args[ $key ] = array_unique( array_map( 'strval', $args[ $key ] ) );
					$args[ $key ] = array_filter( $args[ $key ] );
					sort( $args[ $key ] );

					if ( empty( $args[ $key ] ) ) {
						unset( $args[ $key ] );
					}
				} else {
					unset( $args[ $key ] );
				}
			}
		}

		// Sort top-level arguments.
		ksort( $args );

		// Remove any remaining empty strings or null values.
		foreach ( $args as $key => $value ) {
			if ( '' === $value || null === $value ) {
				unset( $args[ $key ] );
			}
		}

		// Generate cache key.
		return md5( wp_json_encode( $args ) );
	}

	/**
	 * Sets/updates the value of the CRP cache for a post.
	 *
	 * @since 3.5.0
	 *
	 * @param int    $post_id    Post ID.
	 * @param string $key        CRP Cache key.
	 * @param mixed  $value      Metadata value. Must be serializable if non-scalar.
	 * @param int    $expiration Time until expiration in seconds. Default CRP_CACHE_TIME (one month if not overridden).
	 * @param string $cache_type Cache type: 'html' or 'posts'. Default: 'html'.
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure or if the value passed to the function
	 *                  is the same as the one that is already in the database.
	 */
	public static function set_cache( $post_id, $key, $value, $expiration = 0, string $cache_type = 'posts' ) {

		$expiration = (int) $expiration;

		// If expiration is not set, use the get_cache_time method.
		if ( 0 === $expiration ) {
			$expiration = self::get_cache_time( $key, $post_id );
		}

		/**
		 * Filters the expiration for a CRP Cache key before its value is set.
		 *
		 * The dynamic portion of the hook name, `$key`, refers to the CRP Cache key.
		 *
		 * @since 3.0.0
		 *
		 * @param int    $expiration Time until expiration in seconds. Use 0 for no expiration.
		 * @param int    $post_id    Post ID.
		 * @param string $key        CRP Cache key name.
		 * @param mixed  $value      New value of CRP Cache key.
		 */
		$expiration = apply_filters(
			"crp_cache_time_{$key}",
			$expiration,
			$post_id,
			$key,
			$value
		);

		$meta_key      = 'html' === $cache_type ? "_crp_cache_h_{$key}" : "_crp_cache_p_{$key}";
		$cache_expires = 'html' === $cache_type ? "_crp_cache_expires_h_{$key}" : "_crp_cache_expires_p_{$key}";

		$updated = update_post_meta( $post_id, $meta_key, $value, '' );
		update_post_meta( $post_id, $cache_expires, time() + $expiration, '' );

		return $updated;
	}

	/**
	 * Get the cache time to use.
	 *
	 * @since 4.0.0
	 *
	 * @param string $key CRP Cache key.
	 * @param int    $post_id Post ID.
	 * @return int Cache time in seconds.
	 */
	public static function get_cache_time( $key = '', $post_id = 0 ) {
		// Get default cache time from constant or use WEEK_IN_SECONDS.
		$default_cache_time = defined( 'CRP_CACHE_TIME' ) ? CRP_CACHE_TIME : WEEK_IN_SECONDS;

		// If CRP_CACHE_TIME is explicitly set to false, disable caching.
		if ( ! $default_cache_time ) {
			return 0;
		}

		// Get the cache time from settings. This takes priority over the default.
		$cache_time = \crp_get_option( 'cache_time', $default_cache_time );

		/**
		 * Filters the expiration for a CRP Cache key before its value is set.
		 *
		 * The dynamic portion of the hook name, `$key`, refers to the CRP Cache key.
		 *
		 * @since 3.0.0
		 * @since 4.0.0 Added $cache_time parameter.
		 *
		 * @param int    $cache_time Time until expiration in seconds. Use 0 for no expiration.
		 * @param int    $post_id    Post ID.
		 * @param string $key        CRP Cache key name.
		 */
		$cache_time = empty( $key ) ? $cache_time : apply_filters(
			"crp_cache_time_{$key}",
			$cache_time,
			$post_id,
			$key
		);

		return (int) $cache_time;
	}

	/**
	 * Get the value of the CRP cache for a post.
	 *
	 * @since 3.5.0
	 *
	 * @param int    $post_id   Post ID.
	 * @param string $key       CRP Cache key.
	 * @param string $cache_type Cache type: 'html' or 'posts'. Default: 'html'.
	 * @return mixed Value of the CRP cache or false if invalid, expired or unavailable.
	 */
	public static function get_cache( $post_id, $key, string $cache_type = 'posts' ) {
		$meta_key      = 'html' === $cache_type ? "_crp_cache_h_{$key}" : "_crp_cache_p_{$key}";
		$cache_expires = 'html' === $cache_type ? "_crp_cache_expires_h_{$key}" : "_crp_cache_expires_p_{$key}";

		$value = get_post_meta( $post_id, $meta_key, true );

		// Get the cache time.
		$cache_time = self::get_cache_time( $key, $post_id );

		// If cache time is 0, caching is disabled.
		if ( 0 === $cache_time ) {
			return $value;
		}

		if ( $value ) {
			$expires = (int) get_post_meta( $post_id, $cache_expires, true );
			if ( $expires < time() || empty( $expires ) ) {
				self::delete_by_post_id_and_key( $post_id, $meta_key );
				return false;
			} else {
				return $value;
			}
		} else {
			return false;
		}
	}

	/**
	 * Delete the value of the CRP cache for a post.
	 *
	 * @since 3.5.0
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     CRP Cache key.
	 * @param string $cache_type Cache type: 'html', 'posts', or 'all'. Default: 'all'.
	 * @return bool True on success, False on failure.
	 */
	public static function delete_by_post_id_and_key( $post_id, $key, string $cache_type = 'all' ): bool {
		$deleted = false;

		if ( 'all' === $cache_type || 'html' === $cache_type ) {
			$html_meta_key      = "_crp_cache_h_{$key}";
			$html_cache_expires = "_crp_cache_expires_h_{$key}";
			$result             = delete_post_meta( $post_id, $html_meta_key );
			if ( $result ) {
				delete_post_meta( $post_id, $html_cache_expires );
				$deleted = true;
			}
		}

		if ( 'all' === $cache_type || 'posts' === $cache_type ) {
			$posts_meta_key      = "_crp_cache_p_{$key}";
			$posts_cache_expires = "_crp_cache_expires_p_{$key}";
			$result              = delete_post_meta( $post_id, $posts_meta_key );
			if ( $result ) {
				delete_post_meta( $post_id, $posts_cache_expires );
				$deleted = true;
			}
		}

		return $deleted;
	}

	/**
	 * Delete the value of the CRP cache by cache key.
	 *
	 * @since 3.5.0
	 *
	 * @param string $key CRP Cache key.
	 * @param string $cache_type Cache type: 'html', 'posts', or 'all'. Default: 'all'.
	 * @return bool True on success, False on failure.
	 */
	public static function delete_by_key( $key, string $cache_type = 'all' ): bool {
		$deleted = false;

		if ( 'all' === $cache_type || 'html' === $cache_type ) {
			$html_meta_key      = "_crp_cache_h_{$key}";
			$html_cache_expires = "_crp_cache_expires_h_{$key}";
			$result             = delete_post_meta_by_key( $html_meta_key );
			delete_post_meta_by_key( $html_cache_expires );
			if ( $result ) {
				$deleted = true;
			}
		}

		if ( 'all' === $cache_type || 'posts' === $cache_type ) {
			$posts_meta_key      = "_crp_cache_p_{$key}";
			$posts_cache_expires = "_crp_cache_expires_p_{$key}";
			$result              = delete_post_meta_by_key( $posts_meta_key );
			delete_post_meta_by_key( $posts_cache_expires );
			if ( $result ) {
				$deleted = true;
			}
		}

		return $deleted;
	}
}
