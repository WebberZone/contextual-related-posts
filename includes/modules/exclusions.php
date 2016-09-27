<?php
/**
 * Exclusion modules
 *
 * @package Contextual_Related_Posts
 */

/**
 * Function to filter exclude post IDs.
 *
 * @since	2.3.0
 *
 * @param	array $exclude_post_ids   Original excluded post IDs.
 * @return	array	Updated excluded post ID
 */
function crp_exclude_post_ids( $exclude_post_ids ) {
	global $wpdb;

	$exclude_post_ids = (array) $exclude_post_ids;

	$crp_post_metas = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE `meta_key` = 'crp_post_meta'", ARRAY_A );

	foreach ( $crp_post_metas as $crp_post_meta ) {
		$meta_value = unserialize( $crp_post_meta['meta_value'] );

		if ( isset( $meta_value['exclude_this_post'] ) && $meta_value['exclude_this_post'] ) {
			$exclude_post_ids[] = $crp_post_meta['post_id'];
		}
	}
	return $exclude_post_ids;

}
add_filter( 'crp_exclude_post_ids', 'crp_exclude_post_ids' );

