<?php
/**
 * Contextual Related Posts Cache interface.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2019 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to clear the CRP Cache with Ajax.
 *
 * @since   1.8.10
 */
function crp_ajax_clearcache() {

	global $wpdb;

	$meta_keys = crp_cache_get_keys();
	$error     = false;

	foreach ( $meta_keys as $meta_key ) {

		$count = $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"
				DELETE FROM {$wpdb->postmeta}
				WHERE meta_key = %s
				",
				$meta_key
			)
		);

		if ( false === $count ) {
			$error = true;
		} else {
			$counter[] = $count;
		}
	}

	/**** Did an error occur? */
	if ( $error ) {
		exit(
			wp_json_encode(
				array(
					'success' => 0,
					'message' => __( 'An error occurred clearing the cache. Please contact your site administrator.\n\nError message:\n', 'contextual-related-posts' ) . $wpdb->print_error(),
				)
			)
		);
	} else {    // No error, return the number of.
		exit(
			wp_json_encode(
				array(
					'success' => 1,
					'message' => ( array_sum( $counter ) ) . __( ' cached row(s) cleared', 'contextual-related-posts' ),
				)
			)
		);
	}
}
add_action( 'wp_ajax_crp_clear_cache', 'crp_ajax_clearcache' );


/**
 * Delete the CRP cache.
 *
 * @since   2.2.0
 *
 * @param array $meta_keys Array of meta keys that hold the cache.
 */
function crp_cache_delete( $meta_keys = array() ) {

	$default_meta_keys = crp_cache_get_keys();

	if ( ! empty( $meta_keys ) ) {
		$meta_keys = array_intersect( $default_meta_keys, (array) $meta_keys );
	} else {
		$meta_keys = $default_meta_keys;
	}

	foreach ( $meta_keys as $meta_key ) {
		delete_post_meta_by_key( $meta_key );
	}
}


/**
 * Get the default meta keys used for the cache
 *
 * @since   2.2.0
 */
function crp_cache_get_keys() {

	$meta_keys = array(
		'crp_related_posts',
		'crp_related_posts_widget',
		'crp_related_posts_feed',
		'crp_related_posts_widget_feed',
		'crp_related_posts_manual',
		'crp_related_posts_block',
	);

	$meta_keys = array_merge( $meta_keys, crp_cache_get_meta_keys() );

	/**
	 * Filters the array containing the various cache keys.
	 *
	 * @since   1.9
	 *
	 * @param   array   $default_meta_keys  Array of meta keys
	 */
	return apply_filters( 'crp_cache_keys', $meta_keys );
}


/**
 * Get the _crp_cache keys.
 *
 * @since 2.7.0
 *
 * @return array Array of _crp_cache keys.
 */
function crp_cache_get_meta_keys() {
	global $wpdb;

	$keys = array();

	$sql = "
		SELECT meta_key
		FROM {$wpdb->postmeta}
		WHERE `meta_key` LIKE '_crp_cache_%'
	";

	$results = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

	$keys = wp_list_pluck( $results, 'meta_key' );

	/**
	 * Filter the array of _crp_cache keys.
	 *
	 * @since 2.7.0
	 *
	 * @return array Array of _crp_cache keys.
	 */
	return apply_filters( 'crp_cache_get_meta_keys', $keys );
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
	$default_meta_keys = crp_cache_get_keys();
	foreach ( $default_meta_keys as $meta_key ) {
		delete_post_meta( $post_id, $meta_key );
	}

}
add_action( 'crp_save_meta_box', 'crp_delete_cache_post_save' );

