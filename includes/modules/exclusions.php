<?php
/**
 * Exclusion modules
 *
 * @package Contextual_Related_Posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to filter exclude post IDs.
 *
 * @since   2.3.0
 *
 * @param   array   $exclude_post_ids Original excluded post IDs.
 * @param   array   $args             Arguments array.
 * @param   WP_Post $post             Source post.
 * @return  array   Updated excluded post ID
 */
function crp_exclude_post_ids( $exclude_post_ids, $args, $post ) {
	global $wpdb;

	$exclude_post_ids = (array) $exclude_post_ids;

	$crp_post_metas = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE `meta_key` = 'crp_post_meta'", ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

	foreach ( $crp_post_metas as $crp_post_meta ) {
		$meta_value = maybe_unserialize( $crp_post_meta['meta_value'] );

		if ( isset( $meta_value['exclude_this_post'] ) && $meta_value['exclude_this_post'] ) {
			$exclude_post_ids[] = $crp_post_meta['post_id'];
		}
		if ( (int) $post->ID === (int) $crp_post_meta['post_id'] && isset( $meta_value['exclude_post_ids'] ) && $meta_value['exclude_post_ids'] ) {
			$exclude_post_ids = array_merge( $exclude_post_ids, explode( ',', $meta_value['exclude_post_ids'] ) );
		}
	}
	return $exclude_post_ids;

}
add_filter( 'crp_exclude_post_ids', 'crp_exclude_post_ids', 10, 3 );


/**
 * Processes exclusion settings to return if the related posts should not be displayed on the current post.
 *
 * @since 3.0.6
 *
 * @param int|WP_Post|null $post Post ID or post object. Defaults to global $post. Default null.
 * @param array            $args Parameters in a query string format.
 * @return bool True if any exclusion setting is matched.
 */
function crp_exclude_on( $post = null, $args = array() ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	// If this post ID is in the DO NOT DISPLAY list.
	$exclude_on_post_ids = isset( $args['exclude_on_post_ids'] ) ? $args['exclude_on_post_ids'] : crp_get_option( 'exclude_on_post_ids' );
	$exclude_on_post_ids = explode( ',', $exclude_on_post_ids );
	if ( in_array( $post->ID, $exclude_on_post_ids ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		return true;
	}

	// If this post type is in the DO NOT DISPLAY list.
	// If post_types is empty or contains a query string then use parse_str else consider it comma-separated.
	$exclude_on_post_types = isset( $args['exclude_on_post_types'] ) ? $args['exclude_on_post_types'] : crp_get_option( 'exclude_on_post_types' );
	if ( $exclude_on_post_types && false === strpos( $exclude_on_post_types, '=' ) ) {
		$exclude_on_post_types = explode( ',', $exclude_on_post_types );
	} else {
		parse_str( $exclude_on_post_types, $exclude_on_post_types );    // Save post types in $exclude_on_post_types variable.
	}

	if ( in_array( $post->post_type, $exclude_on_post_types, true ) ) {
		return true;
	}

	// If this post's category is in the DO NOT DISPLAY list.
	$exclude_on_categories = isset( $args['exclude_on_categories'] ) ? $args['exclude_on_categories'] : crp_get_option( 'exclude_on_categories' );
	$exclude_on_categories = explode( ',', $exclude_on_categories );
	$post_categories       = get_the_terms( $post->ID, 'category' );
	$categories            = array();
	if ( ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ) {
		$categories = wp_list_pluck( $post_categories, 'term_taxonomy_id' );
	}
	if ( ! empty( array_intersect( $exclude_on_categories, $categories ) ) ) {
		return true;
	}

	// If the DO NOT DISPLAY meta field is set.
	if ( ( isset( $args['is_shortcode'] ) && ! $args['is_shortcode'] ) &&
	( isset( $args['is_manual'] ) && ! $args['is_manual'] ) &&
	( isset( $args['is_block'] ) && ! $args['is_block'] ) ) {
		$crp_post_meta = get_post_meta( $post->ID, 'crp_post_meta', true );

		if ( isset( $crp_post_meta['crp_disable_here'] ) ) {
			$crp_disable_here = $crp_post_meta['crp_disable_here'];
		} else {
			$crp_disable_here = 0;
		}

		if ( $crp_disable_here ) {
			return true;
		}
	}

	return false;
}
