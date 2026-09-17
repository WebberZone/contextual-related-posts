<?php
/**
 * Contextual Related Posts Cache functions.
 *
 * @since 3.5.0
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Util;

use WebberZone\Contextual_Related_Posts\Frontend\Language_Handler;

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
	 * Posts saved this request whose related posts still need clearing, keyed by blog ID then post ID.
	 *
	 * The value is the set of post IDs the saved post was related to before it was saved, captured
	 * before its own cache is deleted.
	 *
	 * @since 4.5.0
	 *
	 * @var array
	 */
	private static $queue = array();

	/**
	 * Blogs whose whole cache should be flushed once this request ends, keyed by blog ID.
	 *
	 * @since 4.5.0
	 *
	 * @var array
	 */
	private static $flush_needed = array();

	/**
	 * Constructor class.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		Hook_Registry::add_action( 'wp_ajax_crp_clear_cache', array( $this, 'ajax_clearcache' ) );
	}

	/**
	 * Clear the saved post's own cache and queue its related posts for clearing on shutdown.
	 *
	 * @since 4.5.0
	 *
	 * @param  int           $post_id     Post ID.
	 * @param  \WP_Post|int  $post        Post object or post ID.
	 * @param  bool          $update      Whether this is an update.
	 * @param  \WP_Post|null $post_before Post object before the save, null for a new post.
	 * @return void
	 */
	public static function clear_cache_on_save( $post_id, $post, $update = false, $post_before = null ) {
		$post_id = absint( $post_id );
		$post    = $post instanceof \WP_Post ? $post : get_post( $post_id );

		if ( ! $post instanceof \WP_Post || ! self::is_cacheable_save( $post_id, $post ) ) {
			return;
		}

		$clear_related = self::should_clear_related( $post, $post_before );

		// An import leaves every existing post's related posts computed without the imported
		// candidates, so flush once when it goes quiet instead of reading and querying per post.
		$importing = $clear_related && self::is_importing();

		$blog_id = get_current_blog_id();

		// Once this blog has escalated to a whole-cache flush, every per-post record is redundant.
		$escalated = ! empty( self::$flush_needed[ $blog_id ] );

		// A post being inserted cannot have a cache of its own yet, so skip the read and the delete.
		$read_stale = $update && $clear_related && ! $importing && ! $escalated;
		$stale      = $read_stale ? self::get_cached_related_ids( $post_id ) : array();

		if ( $update ) {
			self::delete_by_post_id( $post_id );
		}

		if ( ! $clear_related ) {
			return;
		}

		if ( $importing || $escalated ) {
			// Recorded rather than scheduled here: scheduling on the first save would date the
			// event from the start of the import, letting cron fire before the import finishes.
			self::$flush_needed[ $blog_id ] = true;
			return;
		}

		// Merge rather than replace: a post saved twice in one request has already had its own
		// cache deleted by the first save, so the second save reads an empty stale set.
		self::$queue[ $blog_id ][ $post_id ] = array_merge(
			self::$queue[ $blog_id ][ $post_id ] ?? array(),
			$stale
		);

		self::maybe_escalate( $blog_id );
	}

	/**
	 * Drop this blog's queue and mark it for a flush once it holds more posts than the batch limit.
	 *
	 * Escalating here rather than waiting for the drain means the rest of the batch skips both the
	 * ledger read and the queue, instead of collecting records that the flush would discard.
	 *
	 * @since 4.5.0
	 *
	 * @param  int $blog_id Blog ID.
	 * @return void
	 */
	private static function maybe_escalate( int $blog_id ) {
		$batch_limit = self::get_batch_limit();

		if ( $batch_limit < 1 || count( self::$queue[ $blog_id ] ) <= $batch_limit ) {
			return;
		}

		unset( self::$queue[ $blog_id ] );
		self::$flush_needed[ $blog_id ] = true;
	}

	/**
	 * Number of posts saved in one request above which the whole cache is flushed instead.
	 *
	 * @since 4.5.0
	 *
	 * @return int
	 */
	private static function get_batch_limit(): int {
		/**
		 * Filters the number of posts saved in a single request above which the related posts'
		 * caches are not cleared one by one.
		 *
		 * Past this point the whole cache is stale anyway — an import leaves every existing post's
		 * related posts computed without the imported candidates — so a single debounced flush is
		 * both cheaper and more correct than thousands of individual queries. Set to 0 to never
		 * escalate.
		 *
		 * @since 4.5.0
		 *
		 * @param int $batch_limit Number of posts. Default 20.
		 */
		return (int) apply_filters( 'crp_related_cache_clear_batch_limit', 20 );
	}

	/**
	 * Whether a post type is one of WordPress' own internal types, which never hold CRP output.
	 *
	 * Deliberately a short list rather than an is_post_type_viewable() test: a non-public custom
	 * post type can still be rendered as a source by passing post_id to the shortcode, block or
	 * get_crp(), and its cache has to be cleared when it is saved.
	 *
	 * @since 4.5.0
	 *
	 * @param  string $post_type Post type name.
	 * @return bool
	 */
	private static function is_internal_post_type( string $post_type ): bool {
		$internal = array(
			'nav_menu_item',
			'customize_changeset',
			'custom_css',
			'oembed_cache',
			'user_request',
			'wp_block',
			'wp_global_styles',
			'wp_navigation',
			'wp_template',
			'wp_template_part',
			'wp_font_family',
			'wp_font_face',
		);

		/**
		 * Filters the post types whose saves never touch the CRP cache.
		 *
		 * These are WordPress' own bookkeeping post types, which cannot display related posts.
		 * Saving them skips the cache delete entirely, which keeps menu and template saves cheap.
		 *
		 * @since 4.5.0
		 *
		 * @param array $internal Array of post type names.
		 */
		$internal = (array) apply_filters( 'crp_internal_post_types', $internal );

		return in_array( $post_type, $internal, true );
	}

	/**
	 * Whether this request is a bulk import.
	 *
	 * @since 4.5.0
	 *
	 * @return bool
	 */
	private static function is_importing(): bool {
		/**
		 * Filters whether the current request is treated as a bulk import.
		 *
		 * When true, saving a post does not clear its related posts one by one; a single flush of
		 * the whole cache is scheduled once the request ends instead. Useful for importers that do
		 * not define the WP_IMPORTING constant.
		 *
		 * @since 4.5.0
		 *
		 * @param bool $importing Whether this is a bulk import. Defaults to the WP_IMPORTING constant.
		 */
		return (bool) apply_filters( 'crp_is_importing', defined( 'WP_IMPORTING' ) && WP_IMPORTING );
	}

	/**
	 * Whether a save should clear the saved post's own cache.
	 *
	 * Deliberately not gated on the `post_types` setting: that setting lists the post types CRP
	 * returns as related posts, but any post type can be a source with a cache of its own.
	 *
	 * @since 4.5.0
	 *
	 * @param  int      $post_id Post ID.
	 * @param  \WP_Post $post    Post object.
	 * @return bool
	 */
	private static function is_cacheable_save( int $post_id, \WP_Post $post ): bool {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return false;
		}

		if ( 'auto-draft' === $post->post_status ) {
			return false;
		}

		if ( self::is_internal_post_type( $post->post_type ) ) {
			return false;
		}

		return ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE );
	}

	/**
	 * Whether the posts related to a saved post should have their caches cleared.
	 *
	 * @since 4.5.0
	 *
	 * @param  \WP_Post      $post        Post object.
	 * @param  \WP_Post|null $post_before Post object before the save.
	 * @return bool
	 */
	private static function should_clear_related( \WP_Post $post, $post_before = null ): bool {
		// Only a post type CRP can return as a related post can appear in another post's list. The
		// previous type counts too: moving a post out of an eligible type has to clear the lists
		// that still show it, exactly like unpublishing does.
		$post_types = Helpers::parse_post_types( \crp_get_option( 'post_types' ) );

		/**
		 * Filters the post types treated as able to appear in another post's related posts.
		 *
		 * Defaults to the `post_types` setting. A shortcode or block using a per-instance
		 * `post_types` override can list a post type that the setting excludes; add it here so
		 * that saving such a post also clears the lists that show it.
		 *
		 * @since 4.5.0
		 *
		 * @param array    $post_types Array of post type names.
		 * @param \WP_Post $post       Post object being saved.
		 */
		$post_types = (array) apply_filters( 'crp_related_cache_clear_post_types', $post_types, $post );

		$is_candidate  = in_array( $post->post_type, (array) $post_types, true );
		$was_candidate = $post_before instanceof \WP_Post && in_array( $post_before->post_type, (array) $post_types, true );

		if ( ! $is_candidate && ! $was_candidate ) {
			return false;
		}

		// 'inherit' is a displayable status for attachments, which CRP_Core_Query queries alongside 'publish'.
		$visible     = array( 'publish', 'inherit' );
		$was_visible = $post_before instanceof \WP_Post && in_array( $post_before->post_status, $visible, true );
		$is_visible  = in_array( $post->post_status, $visible, true );

		if ( ! $is_visible && ! $was_visible ) {
			return false;
		}

		/**
		 * Filters whether saving a post clears the cache of the posts it is related to.
		 *
		 * Returning false leaves those posts serving their cached related posts until the cache
		 * expires, so a newly published post will not appear in their lists until then. Use this
		 * on very large sites, on a site that caches nothing, or around a migration that does not
		 * define WP_IMPORTING.
		 *
		 * @since 4.5.0
		 *
		 * @param bool     $clear   Whether to clear the related posts' cache. Default true.
		 * @param int      $post_id Post ID being saved.
		 * @param \WP_Post $post    Post object being saved.
		 */
		return (bool) apply_filters( 'crp_clear_related_cache_on_save', true, $post->ID, $post );
	}

	/**
	 * Read the post IDs a post was cached as being related to.
	 *
	 * @since 4.5.0
	 *
	 * @param  int $post_id Post ID.
	 * @return array Array of post IDs.
	 */
	private static function get_cached_related_ids( int $post_id ): array {
		$meta = get_post_meta( $post_id );

		if ( ! is_array( $meta ) ) {
			return array();
		}

		$ids = array();

		foreach ( $meta as $meta_key => $values ) {
			if ( 0 !== strpos( $meta_key, '_crp_cache_p_' ) ) {
				continue;
			}

			foreach ( (array) $values as $value ) {
				$value = maybe_unserialize( $value );
				if ( is_array( $value ) ) {
					$ids = array_merge( $ids, $value );
				}
			}
		}

		return array_map( 'absint', $ids );
	}

	/**
	 * Clear the cache of every post queued this request.
	 *
	 * Runs on shutdown so the query sees the fully synced index: on a REST save the terms are set
	 * after wp_after_insert_post has already fired.
	 *
	 * @since 4.5.0
	 *
	 * @return void
	 */
	public static function process_queue() {
		if ( empty( self::$queue ) && empty( self::$flush_needed ) ) {
			return;
		}

		$queue              = self::$queue;
		$flush_blogs        = self::$flush_needed;
		self::$queue        = array();
		self::$flush_needed = array();

		// Kept as a safety net: clear_cache_on_save() normally escalates before the queue gets here.
		$batch_limit = self::get_batch_limit();

		$current_blog = get_current_blog_id();

		foreach ( $queue as $blog_id => $posts ) {
			if ( $batch_limit > 0 && count( $posts ) > $batch_limit ) {
				$flush_blogs[ $blog_id ] = true;
				continue;
			}

			$switched = false;
			if ( is_multisite() && (int) $blog_id !== $current_blog ) {
				switch_to_blog( (int) $blog_id );
				$switched = true;
			}

			foreach ( $posts as $post_id => $stale_ids ) {
				self::clear_related_cache( (int) $post_id, (array) $stale_ids );
			}

			if ( $switched ) {
				restore_current_blog();
			}
		}

		// One cron write per blog, dated from the end of the request rather than the first save.
		foreach ( array_keys( $flush_blogs ) as $blog_id ) {
			$switched = false;
			if ( is_multisite() && (int) $blog_id !== $current_blog ) {
				switch_to_blog( (int) $blog_id );
				$switched = true;
			}

			self::schedule_deferred_flush();

			if ( $switched ) {
				restore_current_blog();
			}
		}
	}

	/**
	 * Clear the cache of the posts a saved post is, or was, related to.
	 *
	 * @since 4.5.0
	 *
	 * @param  int   $post_id   Post ID that was saved.
	 * @param  array $stale_ids Post IDs it was cached as being related to before the save.
	 * @return int Number of entries deleted.
	 */
	private static function clear_related_cache( int $post_id, array $stale_ids = array() ): int {
		$limit = (int) \crp_get_option( 'limit', 6 );

		/**
		 * Filters how many related posts are looked up when clearing their cache after a save.
		 *
		 * Relatedness is roughly symmetric but its ranking is not: the saved post may sit outside
		 * the top results of a post that still lists it. Raise this to catch more of them, at the
		 * cost of more rows deleted per save.
		 *
		 * @since 4.5.0
		 *
		 * @param int $limit   Number of related posts to look up. Defaults to the `limit` setting.
		 * @param int $post_id Post ID being saved.
		 */
		$limit = (int) apply_filters( 'crp_related_cache_clear_limit', $limit, $post_id );

		$fresh_ids = array();

		if ( $limit > 0 && function_exists( 'get_crp_posts' ) ) {
			$related = \get_crp_posts(
				array(
					'post_id'           => $post_id,
					'limit'             => $limit,
					'fields'            => 'ids',
					'cache'             => false,
					'cache_posts'       => false,
					'backlog_threshold' => 0,
				)
			);

			foreach ( (array) $related as $related_post ) {
					$fresh_ids[] = $related_post instanceof \WP_Post ? (int) $related_post->ID : absint( $related_post );
			}
		}

		$ids = array_diff( array_unique( array_merge( $stale_ids, $fresh_ids ) ), array( $post_id, 0 ) );

		/**
		 * Filters the post IDs whose cache is cleared after a related post is saved.
		 *
		 * @since 4.5.0
		 *
		 * @param array $ids     Array of post IDs.
		 * @param int   $post_id Post ID that was saved.
		 */
		$ids = (array) apply_filters( 'crp_related_cache_clear_ids', array_values( $ids ), $post_id );

		return self::delete_by_post_ids( $ids );
	}

	/**
	 * Schedule a single debounced flush of the entire cache.
	 *
	 * Each oversized request pushes the event further out, so a long import produces one flush once
	 * it goes quiet rather than one per batch.
	 *
	 * @since 4.5.0
	 *
	 * @return void
	 */
	private static function schedule_deferred_flush() {
		/**
		 * Filters how long after the last bulk save the deferred cache flush runs.
		 *
		 * @since 4.5.0
		 *
		 * @param int $delay Delay in seconds. Default 300.
		 */
		$delay = (int) apply_filters( 'crp_deferred_cache_flush_delay', 5 * MINUTE_IN_SECONDS );

		$scheduled = wp_next_scheduled( 'crp_deferred_cache_flush' );

		if ( $scheduled ) {
			wp_unschedule_event( $scheduled, 'crp_deferred_cache_flush' );
		}

		wp_schedule_single_event( time() + max( 60, $delay ), 'crp_deferred_cache_flush' );
	}

	/**
	 * Flush the entire cache. Callback for the deferred flush scheduled after a bulk save.
	 *
	 * @since 4.5.0
	 *
	 * @return void
	 */
	public static function deferred_flush() {
		self::delete();
	}

	/**
	 * Clear the CRP cache when a post is trashed or restored, if the option is enabled.
	 *
	 * @since 4.3.0
	 *
	 * @param  int $post_id Post ID being trashed or restored.
	 * @return void
	 */
	public static function maybe_clear_cache_on_trash( $post_id ) {  // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		static $cleared = false;

		if ( $cleared ) {
			return;
		}

		if ( ! \crp_get_option( 'clear_cache_on_trash', false ) ) {
			return;
		}

		self::delete();
		$cleared = true;
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

		$last_id = 0;
		$count   = 0;
		$like    = $wpdb->esc_like( '_crp_cache_' ) . '%';

		do {
			// Bound memory use and invalidate only posts whose metadata was deleted.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$post_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE post_id > %d AND meta_key LIKE %s ORDER BY post_id ASC LIMIT 500",
					$last_id,
					$like
				)
			);
			if ( empty( $post_ids ) ) {
				break;
			}

			$post_ids = array_map( 'absint', $post_ids );
			$deleted  = self::delete_meta_for_post_ids( $post_ids );
			if ( false === $deleted ) {
				break;
			}

			$count      += $deleted;
			$last_id     = max( $post_ids );
			$batch_count = count( $post_ids );
		} while ( 500 === $batch_count );

		return (int) ( $count / 2 );
	}

	/**
	 * Delete every CRP cache row belonging to a set of post IDs in a single query.
	 *
	 * @since 4.5.0
	 *
	 * @param  array $post_ids Array of post IDs. Assumed to be already sanitised.
	 * @return int|false Number of rows deleted, or false on database error.
	 */
	private static function delete_meta_for_post_ids( array $post_ids ) {
		global $wpdb;

		if ( empty( $post_ids ) ) {
			return 0;
		}

		$like    = $wpdb->esc_like( '_crp_cache_' ) . '%';
		$id_list = implode( ',', $post_ids );

     // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$deleted = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->postmeta} WHERE post_id IN ($id_list) AND meta_key LIKE %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- IDs are integers.
				$like
			)
		);

		if ( false === $deleted ) {
			return false;
		}

		wp_cache_delete_multiple( $post_ids, 'post_meta' );

		return (int) $deleted;
	}

	/**
	 * Delete the cache for an array of post IDs.
	 *
	 * @since 4.5.0
	 *
	 * @param  array $post_ids Array of post IDs.
	 * @return int Number of entries deleted.
	 */
	public static function delete_by_post_ids( array $post_ids ): int {
		$post_ids = array_values( array_unique( array_filter( array_map( 'absint', $post_ids ) ) ) );

		if ( empty( $post_ids ) ) {
			return 0;
		}

		$count = 0;

		foreach ( array_chunk( $post_ids, 500 ) as $chunk ) {
			$deleted = self::delete_meta_for_post_ids( $chunk );
			if ( false === $deleted ) {
				break;
			}
			$count += $deleted;
		}

		return (int) ( $count / 2 );
	}

	/**
	 * Format expiration time to human readable format.
	 *
	 * @since 4.2.0
	 *
	 * @param  int $seconds Expiration time in seconds.
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
			WHERE meta_key LIKE '_crp_cache_%'
			AND meta_key NOT LIKE '_crp_cache_expires_%'"
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
	 * @param  int $post_id Post ID. Optional.
	 * @return array Array of _crp_cache keys.
	 */
	public static function get_meta_keys( $post_id = 0 ): array {
		global $wpdb;

		// Only query the database for actual cache keys that exist.
		$sql = "SELECT DISTINCT meta_key FROM {$wpdb->postmeta}
			WHERE meta_key LIKE '_crp_cache_%'
			AND meta_key NOT LIKE '_crp_cache_expires_%'";

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
	 * @param  bool $dry_run Whether to perform a dry run.
	 * @return array Results array with 'cleaned' and 'scanned' counts.
	 */
	public static function cleanup_expired( bool $dry_run = false ): array {
		global $wpdb;

		$cleaned = 0;
		$scanned = 0;

		// Get all actual cache keys from database.
		$cache_keys = self::get_meta_keys();

		foreach ( $cache_keys as $cache_key ) {
			// Extract the raw key and cache type from the meta_key.
			if ( 0 === strpos( $cache_key, '_crp_cache_h_' ) ) {
				$key_name   = substr( $cache_key, strlen( '_crp_cache_h_' ) );
				$cache_type = 'html';
			} elseif ( 0 === strpos( $cache_key, '_crp_cache_p_' ) ) {
				$key_name   = substr( $cache_key, strlen( '_crp_cache_p_' ) );
				$cache_type = 'posts';
			} else {
				// Skip cache keys that don't match expected patterns (legacy or malformed keys).
				continue;
			}

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

				// Read expiration without get_cache(), which deletes expired entries.
				if ( 0 === self::get_cache_time( $key_name, $post_id ) ) {
					continue;
				}
				$expires_key = 'html' === $cache_type ? "_crp_cache_expires_h_{$key_name}" : "_crp_cache_expires_p_{$key_name}";
				$expires     = (int) get_post_meta( $post_id, $expires_key, true );
				if ( empty( $expires ) || $expires < time() ) {
					if ( $dry_run || self::delete_by_post_id_and_key( $post_id, $key_name, $cache_type ) ) {
						++$cleaned;
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
	 * @since 4.5.0 Delegates to self::delete_by_post_ids(). The return value now counts cache
	 *              entries rather than individual meta rows, so it is roughly half its old value.
	 *
	 * @param  int $post_id Post ID.
	 * @return int Number of entries deleted.
	 */
	public static function delete_by_post_id( $post_id ): int {
		return self::delete_by_post_ids( array( $post_id ) );
	}

	/**
	 * Get the cache key based on a list of parameters.
	 *
	 * @since 3.5.0
	 *
	 * @param  mixed $attr Array of attributes typically.
	 * @return string Cache meta key
	 */
	public static function get_key( $attr ): string {
		$args = (array) $attr;

		// A disabled recency boost has no effect on the query or its output. Drop its defaults so
		// upgrading an existing site does not mint a second cache entry for the unchanged ranking.
		$recency_weight = $args['weight_recency'] ?? null;
		if ( null === $recency_weight || ( is_scalar( $recency_weight ) && (float) $recency_weight <= 0 ) ) {
			unset( $args['weight_recency'], $args['recency_halflife'] );
		}

		static $setting_types = null;
		if ( null === $setting_types ) {
			$setting_types = function_exists( 'crp_get_registered_settings_types' ) ? crp_get_registered_settings_types() : array();
		}

		// Remove args that affect neither the query nor the cached HTML output. `cache` stores the
		// full rendered HTML (see Display::related_posts()), so any arg consumed by heading_title(),
		// list_link(), get_the_excerpt(), the before/after wrappers, or CRP_Core_Query's own query
		// building must stay in the key — leaving one out here causes two differently-configured
		// calls on the same post to collide on the same cache entry and silently reuse each other's
		// output (#86crp-bricks-cache; @since 4.4.0).
		$exclude_keys = array(
			'cache',
			'cache_posts',
			'className',
			'crp_query',
			'echo',
			'ignore_sticky_posts',
			'is_crp_query',
			'is_manual',
			'no_found_rows',
			'other_attributes',
			'post_id',
			'postid',
			'show_metabox',
			'show_metabox_admins',
			'suppress_filters',
		);

		foreach ( $exclude_keys as $key ) {
			unset( $args[ $key ] );
		}

		// Remove any keys ending in _header or _desc, or with type 'header'.
		foreach ( $args as $key => $value ) {
			// Numeric keys can leak in from shortcode_parse_atts() on malformed shortcode attributes.
			if ( ! is_string( $key ) ) {
				unset( $args[ $key ] );
				continue;
			}

			if ( '_header' === substr( $key, -7 ) || '_desc' === substr( $key, -5 ) ) {
				unset( $args[ $key ] );
				continue;
			}

			if ( isset( $setting_types[ $key ] ) && 'header' === $setting_types[ $key ] ) {
				unset( $args[ $key ] );
			}
		}

		// These lists affect output order; normalize their values without sorting.
		$ordered_keys = array( 'manual_related', 'include_post_ids', 'cornerstone_post_ids', 'post__in', 'post_name__in' );
		$ordered_args = array();
		foreach ( $ordered_keys as $key ) {
			if ( ! array_key_exists( $key, $args ) ) {
				continue;
			}
			$value = 'post_name__in' === $key ? wp_parse_list( $args[ $key ] ?? array() ) : wp_parse_id_list( $args[ $key ] ?? array() );
			$value = array_values( array_unique( array_filter( $value ) ) );
			if ( ! empty( $value ) ) {
				$ordered_args[ $key ] = $value;
			}
			unset( $args[ $key ] );
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
			'tag_slug__and',
			'tag_slug__in',
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

		$args = array_merge( $args, $ordered_args );

		// Sort top-level arguments.
		ksort( $args );

		// Remove any remaining empty strings or null values.
		foreach ( $args as $key => $value ) {
			if ( '' === $value || null === $value ) {
				unset( $args[ $key ] );
			}
		}

		// Generate cache key.
		// Version the format to retire previously shared HTML and colliding keys.
		return md5( '4.4.2|' . Language_Handler::get_cache_language() . '|' . wp_json_encode( $args ) );
	}

	/**
	 * Sets/updates the value of the CRP cache for a post.
	 *
	 * @since 3.5.0
	 *
	 * @param  int    $post_id    Post ID.
	 * @param  string $key        CRP Cache key.
	 * @param  mixed  $value      Metadata value. Must be serializable if non-scalar.
	 * @param  int    $expiration Time until expiration in seconds. Default CRP_CACHE_TIME (one month if not overridden).
	 * @param  string $cache_type Cache type: 'html' or 'posts'. Default: 'html'.
	 * @param  array  $args       Query arguments used to resolve a query-specific cache lifetime.
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure or if the value passed to the function
	 *                  is the same as the one that is already in the database.
	 */
	public static function set_cache( $post_id, $key, $value, $expiration = 0, string $cache_type = 'posts', array $args = array() ) {

		$expiration = (int) $expiration;

		// If expiration is not set, use the get_cache_time method.
		if ( 0 === $expiration ) {
			$expiration = self::get_cache_time( $key, $post_id, $args );
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
	 * @param  string $key     CRP Cache key.
	 * @param  int    $post_id Post ID.
	 * @param  array  $args    Query arguments used to resolve a query-specific cache lifetime.
	 * @return int Cache time in seconds.
	 */
	public static function get_cache_time( $key = '', $post_id = 0, array $args = array() ) {
		// Get default cache time from constant or use WEEK_IN_SECONDS.
		$default_cache_time = defined( 'CRP_CACHE_TIME' ) ? CRP_CACHE_TIME : WEEK_IN_SECONDS;

		// If CRP_CACHE_TIME is explicitly set to false, disable caching.
		if ( ! $default_cache_time ) {
			return 0;
		}

		// Get the cache time from settings. This takes priority over the default.
		$cache_time = \crp_get_option( 'cache_time', $default_cache_time );

		/**
		 * Filters the expiration of every CRP Cache key before its value is set.
		 *
		 * Unlike `crp_cache_time_{$key}` this fires for all keys, which are md5 hashes of the
		 * query arguments and so cannot be hooked individually ahead of time. It runs first, so a
		 * per-key filter still has the last word.
		 *
		 * @since 4.5.0
		 *
		 * @param int    $cache_time Time until expiration in seconds. Use 0 for no expiration.
		 * @param int    $post_id    Post ID.
		 * @param string $key        CRP Cache key name.
		 * @param array  $args       Query arguments used to resolve a query-specific cache lifetime.
		 */
		$cache_time = (int) apply_filters( 'crp_cache_time', $cache_time, $post_id, $key, $args );

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
	 * @param  int    $post_id    Post ID.
	 * @param  string $key        CRP Cache key.
	 * @param  string $cache_type Cache type: 'html' or 'posts'. Default: 'html'.
	 * @param  array  $args       Query arguments used to resolve a query-specific cache lifetime.
	 * @return mixed Value of the CRP cache or false if invalid, expired or unavailable.
	 */
	public static function get_cache( $post_id, $key, string $cache_type = 'posts', array $args = array() ) {
		$meta_key      = 'html' === $cache_type ? "_crp_cache_h_{$key}" : "_crp_cache_p_{$key}";
		$cache_expires = 'html' === $cache_type ? "_crp_cache_expires_h_{$key}" : "_crp_cache_expires_p_{$key}";

		if ( ! metadata_exists( 'post', $post_id, $meta_key ) ) {
			return false;
		}

		$value = get_post_meta( $post_id, $meta_key, true );

		// Get the cache time.
		$cache_time = self::get_cache_time( $key, $post_id, $args );

		// If cache time is 0, caching is disabled.
		if ( 0 === $cache_time ) {
			return $value;
		}

		$expires = (int) get_post_meta( $post_id, $cache_expires, true );
		if ( $expires < time() ) {
			self::delete_by_post_id_and_key( $post_id, $key, $cache_type );
			return false;
		}

		return $value;
	}

	/**
	 * Delete the value of the CRP cache for a post.
	 *
	 * @since 3.5.0
	 *
	 * @param  int    $post_id    Post ID.
	 * @param  string $key        CRP Cache key.
	 * @param  string $cache_type Cache type: 'html', 'posts', or 'all'. Default: 'all'.
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
	 * @param  string $key        CRP Cache key.
	 * @param  string $cache_type Cache type: 'html', 'posts', or 'all'. Default: 'all'.
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
