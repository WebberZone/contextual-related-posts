<?php
/**
 * Template functions for use in the plugin and theme.
 *
 * @package   Contextual_Related_Posts
 */

use WebberZone\Contextual_Related_Posts\Frontend\Display;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Main function to generate the related posts output
 *
 * @since 1.0.1
 *
 * @param string|array $args Parameters in a query string format.
 * @return string HTML formatted list of related posts
 */
function get_crp( $args = array() ) {
	return Display::related_posts( $args );
}

/**
 * Echos the related posts. Used for manual install
 *
 * @since 1.0.1
 *
 * @param array $args Array of arguments to control the output.
 */
function echo_crp( $args = array() ) {

	$defaults = array(
		'is_manual' => true,
	);

	// Parse incomming $args into an array and merge it with $defaults.
	$args = wp_parse_args( $args, $defaults );

	echo get_crp( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Retrieves an array of the related posts.
 *
 * The defaults are as follows:
 *
 * @since 1.8.6
 * @since 3.0.0 Parameters have been dropped for a single $args parameter.
 *
 * @see CRP_Query::prepare_query_args()
 *
 * @param array $args Optional. Arguments to retrieve posts. See WP_Query::parse_query() for all available arguments.
 * @return WP_Post[]|int[] Array of post objects or post IDs.
 */
function get_crp_posts( $args = array() ) {
	return Display::get_posts( $args );
}

/**
 * Backward compatibility helper for reading CRP meta keys.
 *
 * Prioritizes new individual _crp_* meta keys, falls back to crp_post_meta array.
 * Caches old meta to avoid repeated queries.
 *
 * @since 4.2.0
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key suffix (without _crp_ prefix).
 * @return mixed Meta value or empty string.
 */
function crp_get_meta( $post_id, $key ) {
	static $cached_old_meta = array();

	$post_id = absint( $post_id );
	$new_key = '_crp_' . $key;

	// Check new individual key first.
	$new_value = get_post_meta( $post_id, $new_key, true );
	if ( '' !== $new_value ) {
		return $new_value;
	}

	// Check cached old meta.
	if ( ! isset( $cached_old_meta[ $post_id ] ) ) {
		$cached_old_meta[ $post_id ] = get_post_meta( $post_id, 'crp_post_meta', true );
	}

	$old_meta = $cached_old_meta[ $post_id ];
	if ( is_array( $old_meta ) && isset( $old_meta[ $key ] ) ) {
		return $old_meta[ $key ];
	}

	return '';
}
