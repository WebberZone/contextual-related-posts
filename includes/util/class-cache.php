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
		add_action( 'wp_ajax_crp_clear_cache', array( $this, 'ajax_clearcache' ) );
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

		self::delete();

		exit(
			wp_json_encode(
				array(
					'success' => 1,
					'message' => __( 'Cache has been cleared', 'contextual-related-posts' ),
				)
			)
		);
	}

	/**
	 * Delete the entire CRP cache.
	 *
	 * @since 3.5.0
	 *
	 * @param array $meta_keys  Array of meta keys that hold the cache.
	 * @return int Number of keys deleted.
	 */
	public static function delete( $meta_keys = array() ) {
		$loop = 0;

		$default_meta_keys = self::get_meta_keys();

		if ( ! empty( $meta_keys ) ) {
			$meta_keys = array_intersect( $default_meta_keys, (array) $meta_keys );
		} else {
			$meta_keys = $default_meta_keys;
		}

		foreach ( $meta_keys as $meta_key ) {
			$del_meta = self::delete_by_key( $meta_key );
			if ( $del_meta ) {
				++$loop;
			}
		}

		return $loop;
	}


	/**
	 * Get array of cache keys.
	 *
	 * @since 3.5.0
	 *
	 * @return array $keys Array of cache keys.
	 */
	public static function get_keys() {

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
	public static function get_meta_keys( $post_id = 0 ) {
		global $wpdb;

		$meta_keys = array(
			'crp_related_posts',
			'crp_related_posts_widget',
			'crp_related_posts_feed',
			'crp_related_posts_widget_feed',
			'crp_related_posts_manual',
			'crp_related_posts_block',
		);

		$keys = array();

		$sql = "
		SELECT meta_key
		FROM {$wpdb->postmeta}
		WHERE `meta_key` LIKE '_crp_cache_%'
		AND `meta_key` NOT LIKE '_crp_cache_expires_%'
		";

		if ( $post_id > 0 ) {
			$sql .= $wpdb->prepare( ' AND post_id = %d ', $post_id );
		}

		$results = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		$keys = wp_list_pluck( $results, 'meta_key' );

		$meta_keys = array_merge( $meta_keys, $keys );

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
	 * Delete cache by post ID.
	 *
	 * @since 3.4.0
	 *
	 * @param int $post_id Post ID.
	 */
	public static function delete_by_post_id( $post_id ) {
		$meta_keys = self::get_meta_keys( $post_id );
		foreach ( $meta_keys as $meta_key ) {
			delete_post_meta( $post_id, $meta_key );
		}
	}

	/**
	 * Get the meta key based on a list of parameters.
	 *
	 * @since 3.5.0
	 *
	 * @param mixed $attr Array of attributes typically.
	 * @return string Cache meta key
	 */
	public static function get_key( $attr ) {

		$meta_key = md5( wp_json_encode( $attr ) );

		return $meta_key;
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
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure or if the value passed to the function
	 *                  is the same as the one that is already in the database.
	 */
	public static function set_cache( $post_id, $key, $value, $expiration = 0 ) {

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
		$expiration = apply_filters( "crp_cache_time_{$key}", $expiration, $post_id, $key, $value );

		$meta_key      = '_crp_cache_' . $key;
		$cache_expires = '_crp_cache_expires_' . $key;

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
		// If CRP_CACHE_TIME is defined and is false, disable caching.
		if ( defined( 'CRP_CACHE_TIME' ) && false === CRP_CACHE_TIME ) {
			return 0;
		}

		// Get default cache time from constant or use MONTH_IN_SECONDS.
		$default_cache_time = defined( 'CRP_CACHE_TIME' ) ? CRP_CACHE_TIME : MONTH_IN_SECONDS;

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
		$cache_time = empty( $key ) ? $cache_time : apply_filters( "crp_cache_time_{$key}", $cache_time, $post_id, $key );

		return (int) $cache_time;
	}

	/**
	 * Get the value of the CRP cache for a post.
	 *
	 * @since 3.5.0
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     CRP Cache key.
	 * @return mixed Value of the CRP cache or false if invalid, expired or unavailable.
	 */
	public static function get_cache( $post_id, $key ) {
		$meta_key      = '_crp_cache_' . $key;
		$cache_expires = '_crp_cache_expires_' . $key;

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
	 * @return bool True on success, False on failure.
	 */
	public static function delete_by_post_id_and_key( $post_id, $key ) {
		$meta_key      = '_crp_cache_' . $key;
		$cache_expires = '_crp_cache_expires_' . $key;

		$result = delete_post_meta( $post_id, $meta_key );
		if ( $result ) {
			delete_post_meta( $post_id, $cache_expires );
		}

		return $result;
	}


	/**
	 * Delete the value of the CRP cache by cache key.
	 *
	 * @since 3.5.0
	 *
	 * @param string $key CRP Cache key.
	 * @return bool True on success, False on failure.
	 */
	public static function delete_by_key( $key ) {
		$key           = str_replace( '_crp_cache_expires_', '', $key );
		$key           = str_replace( '_crp_cache_', '', $key );
		$meta_key      = '_crp_cache_' . $key;
		$cache_expires = '_crp_cache_expires_' . $key;

		$result = delete_post_meta_by_key( $meta_key );
		delete_post_meta_by_key( $cache_expires );

		return $result;
	}
}
