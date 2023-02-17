<?php
/**
 * Contextual Related Posts Cache interface.
 *
 * @package   Contextual_Related_Posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to clear the CRP Cache with Ajax.
 *
 * @since 1.8.10
 */
function crp_ajax_clearcache() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 0 );
	}
	check_ajax_referer( 'crp-admin', 'security' );

	crp_cache_delete();
	exit(
		wp_json_encode(
			array(
				'success' => 1,
				'message' => __( 'Cache has been cleared', 'contextual-related-posts' ),
			)
		)
	);
}
add_action( 'wp_ajax_crp_clear_cache', 'crp_ajax_clearcache' );


/**
 * Delete the entire CRP cache.
 *
 * @since 2.2.0
 *
 * @param array $meta_keys  Array of meta keys that hold the cache.
 * @return int Number of keys deleted.
 */
function crp_cache_delete( $meta_keys = array() ) {
	$loop = 0;

	$default_meta_keys = crp_cache_get_meta_keys();

	if ( ! empty( $meta_keys ) ) {
		$meta_keys = array_intersect( $default_meta_keys, (array) $meta_keys );
	} else {
		$meta_keys = $default_meta_keys;
	}

	foreach ( $meta_keys as $meta_key ) {
		$del_meta = delete_crp_cache_by_key( $meta_key );
		if ( $del_meta ) {
			$loop++;
		}
	}

	return $loop;
}


/**
 * Get array of cache keys.
 *
 * @since 2.2.0
 *
 * @return array $keys Array of cache keys.
 */
function crp_cache_get_keys() {

	$keys = crp_cache_get_meta_keys();

	/**
	 * Filters the array containing the various cache keys.
	 *
	 * @since 2.2.0
	 *
	 * @param array $keys Array of cache keys.
	 */
	return apply_filters( 'crp_cache_keys', $keys );
}


/**
 * Get the _crp_cache keys.
 *
 * @since 2.7.0
 * @since 3.0.0 Added $post_id parameter
 *
 * @param int $post_id Post ID. Optional.
 * @return array Array of _crp_cache keys.
 */
function crp_cache_get_meta_keys( $post_id = 0 ) {
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
 * Function to clear cache on post save.
 *
 * @since   2.5.0
 *
 * @param mixed $post_id Post ID.
 */
function crp_delete_cache_post_save( $post_id ) {

	// Bail if we're doing an auto save.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// If our nonce isn't there, or we can't verify it, bail.
	if ( ! isset( $_POST['crp_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['crp_meta_box_nonce'] ), 'crp_meta_box' ) ) {
		return;
	}

	// If our current user can't edit this post, bail.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Clear cache of current post.
	$meta_keys = crp_cache_get_meta_keys( $post_id );
	foreach ( $meta_keys as $meta_key ) {
		delete_post_meta( $post_id, $meta_key );
	}

}
add_action( 'crp_save_meta_box', 'crp_delete_cache_post_save' );


/**
 * Get the meta key based on a list of parameters.
 *
 * @since 2.7.0
 *
 * @param mixed $attr Array of attributes typically.
 * @return string Cache meta key
 */
function crp_cache_get_key( $attr ) {

	$meta_key = md5( wp_json_encode( $attr ) );

	return $meta_key;
}


/**
 * Sets/updates the value of the CRP cache for a post.
 *
 * @since 3.0.0
 *
 * @param int    $post_id    Post ID.
 * @param string $key        CRP Cache key.
 * @param mixed  $value      Metadata value. Must be serializable if non-scalar.
 * @param int    $expiration Time until expiration in seconds. Default CRP_CACHE_TIME (one month if not overridden).
 * @return int|bool Meta ID if the key didn't exist, true on successful update,
 *                  false on failure or if the value passed to the function
 *                  is the same as the one that is already in the database.
 */
function set_crp_cache( $post_id, $key, $value, $expiration = CRP_CACHE_TIME ) {

	$expiration = (int) $expiration;

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
 * Get the value of the CRP cache for a post.
 *
 * @since 3.0.0
 *
 * @param int    $post_id Post ID.
 * @param string $key     CRP Cache key.
 * @return mixed Value of the CRP cache or false if invalid, expired or unavailable.
 */
function get_crp_cache( $post_id, $key ) {
	$meta_key      = '_crp_cache_' . $key;
	$cache_expires = '_crp_cache_expires_' . $key;

	$value = get_post_meta( $post_id, $meta_key, true );

	if ( ! CRP_CACHE_TIME ) {
		return $value;
	}

	if ( $value ) {
		$expires = (int) get_post_meta( $post_id, $cache_expires, true );
		if ( $expires < time() || empty( $expires ) ) {
			delete_crp_cache( $post_id, $meta_key );
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
 * @since 3.0.0
 *
 * @param int    $post_id Post ID.
 * @param string $key     CRP Cache key.
 * @return bool True on success, False on failure.
 */
function delete_crp_cache( $post_id, $key ) {
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
 * @since 3.0.4
 *
 * @param string $key CRP Cache key.
 * @return bool True on success, False on failure.
 */
function delete_crp_cache_by_key( $key ) {
	$key           = str_replace( '_crp_cache_expires_', '', $key );
	$key           = str_replace( '_crp_cache_', '', $key );
	$meta_key      = '_crp_cache_' . $key;
	$cache_expires = '_crp_cache_expires_' . $key;

	$result = delete_post_meta_by_key( $meta_key );
	delete_post_meta_by_key( $cache_expires );

	return $result;
}

